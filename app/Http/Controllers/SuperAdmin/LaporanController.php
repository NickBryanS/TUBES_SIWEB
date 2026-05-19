<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $periode = $request->get('periode', 'bulanan');
        $now = Carbon::now();

        // Periode filter
        if ($periode === 'mingguan') {
            $start = $now->copy()->startOfWeek();
            $end = $now->copy()->endOfWeek();
        } elseif ($periode === 'tahunan') {
            $start = $now->copy()->startOfYear();
            $end = $now->copy()->endOfYear();
        } else {
            $start = $now->copy()->startOfMonth();
            $end = $now->copy()->endOfMonth();
        }

        // Stat Cards
        $totalPendapatan = Transaction::whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai'])
            ->whereBetween('created_at', [$start, $end])->sum('total_biaya');

        $rataRataPesanan = Transaction::whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai'])
            ->whereBetween('created_at', [$start, $end])->avg('total_biaya') ?? 0;

        $dendaTerkumpul = Transaction::whereBetween('created_at', [$start, $end])
            ->where('denda', '>', 0)->sum('denda');

        // Chart: Pendapatan vs Target (6 bulan terakhir)
        $chartBulanan = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $actual = (float) Transaction::whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai'])
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('total_biaya');
            $chartBulanan[] = [
                'label' => $month->translatedFormat('M'),
                'actual' => $actual,
                'target' => $actual * 1.2, // Target 20% lebih tinggi
            ];
        }

        // Metode Pembayaran breakdown
        $metodePembayaran = DB::table('payments')
            ->join('transactions', 'payments.transaction_id', '=', 'transactions.id')
            ->whereIn('transactions.status_transaksi', ['diproses', 'dikirim', 'selesai'])
            ->select('payments.metode_pembayaran', DB::raw('COUNT(*) as total'))
            ->groupBy('payments.metode_pembayaran')
            ->get();

        // Log Penyewaan Detail
        $logs = Transaction::with(['user', 'details.product'])
            ->whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai', 'dibatalkan'])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->limit(10)->get();

        return view('superadmin.laporan', compact(
            'periode', 'totalPendapatan', 'rataRataPesanan', 'dendaTerkumpul',
            'chartBulanan', 'metodePembayaran', 'logs'
        ));
    }

    /**
     * Ekspor laporan ke PDF.
     */
    public function exportPdf(Request $request)
    {
        $periode = $request->get('periode', 'bulanan');
        $now = Carbon::now();

        if ($periode === 'mingguan') {
            $start = $now->copy()->startOfWeek();
            $end = $now->copy()->endOfWeek();
            $periodeLabel = 'Mingguan (' . $start->format('d M') . ' - ' . $end->format('d M Y') . ')';
        } elseif ($periode === 'tahunan') {
            $start = $now->copy()->startOfYear();
            $end = $now->copy()->endOfYear();
            $periodeLabel = 'Tahunan ' . $now->year;
        } else {
            $start = $now->copy()->startOfMonth();
            $end = $now->copy()->endOfMonth();
            $periodeLabel = 'Bulanan - ' . $now->translatedFormat('F Y');
        }

        $transactions = Transaction::with(['user', 'details.product', 'payment'])
            ->whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai', 'dibatalkan'])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPendapatan = $transactions->whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai'])->sum('total_biaya');
        $totalDenda = $transactions->where('denda', '>', 0)->sum('denda');

        ActivityLog::catat('export_pdf', 'Mengekspor laporan PDF periode ' . $periodeLabel);

        // Generate PDF menggunakan view HTML
        $html = view('superadmin.exports.laporan-pdf', compact(
            'transactions', 'periodeLabel', 'totalPendapatan', 'totalDenda'
        ))->render();

        return response($html)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="laporan-' . $periode . '.pdf"');
    }

    /**
     * Ekspor laporan ke Excel (CSV).
     */
    public function exportExcel(Request $request)
    {
        $periode = $request->get('periode', 'bulanan');
        $now = Carbon::now();

        if ($periode === 'mingguan') {
            $start = $now->copy()->startOfWeek();
            $end = $now->copy()->endOfWeek();
            $periodeLabel = 'Mingguan';
        } elseif ($periode === 'tahunan') {
            $start = $now->copy()->startOfYear();
            $end = $now->copy()->endOfYear();
            $periodeLabel = 'Tahunan';
        } else {
            $start = $now->copy()->startOfMonth();
            $end = $now->copy()->endOfMonth();
            $periodeLabel = 'Bulanan';
        }

        $transactions = Transaction::with(['user', 'details.product', 'payment'])
            ->whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai', 'dibatalkan'])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get();

        ActivityLog::catat('export_excel', 'Mengekspor laporan Excel periode ' . $periodeLabel);

        $filename = 'laporan-' . $periode . '-' . $now->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($file, [
                'ID Transaksi',
                'Tanggal',
                'Pelanggan',
                'Email',
                'Produk',
                'Jumlah Item',
                'Total Biaya',
                'Denda',
                'Status Transaksi',
                'Metode Pembayaran',
                'Status Pembayaran',
            ]);

            foreach ($transactions as $trx) {
                $produkList = $trx->details->map(fn($d) => ($d->product->nama_produk ?? '-') . ' (x' . $d->jumlah . ')')->implode(', ');
                $totalItem = $trx->details->sum('jumlah');

                fputcsv($file, [
                    'WB-' . str_pad($trx->id, 8, '0', STR_PAD_LEFT),
                    $trx->created_at->format('d/m/Y H:i'),
                    $trx->user->nama_lengkap ?? '-',
                    $trx->user->email ?? '-',
                    $produkList,
                    $totalItem,
                    $trx->total_biaya,
                    $trx->denda ?? 0,
                    strtoupper($trx->status_transaksi),
                    $trx->payment->metode_pembayaran ?? '-',
                    $trx->payment->status_pembayaran ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
