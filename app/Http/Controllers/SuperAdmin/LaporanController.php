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
        if ($periode === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
        } else {
            [$start, $end] = $this->getDateRange($periode, $now);
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
                'label'  => $month->translatedFormat('M'),
                'actual' => $actual,
                'target' => $actual * 1.2,
            ];
        }

        // Metode Pembayaran breakdown
        $metodePembayaran = DB::table('payments')
            ->join('transactions', 'payments.transaction_id', '=', 'transactions.id')
            ->whereIn('transactions.status_transaksi', ['diproses', 'dikirim', 'selesai'])
            ->select('payments.metode_pembayaran', DB::raw('COUNT(*) as total'))
            ->groupBy('payments.metode_pembayaran')
            ->get();

        // Log Penyewaan Detail — tampilkan SEMUA data, bukan hanya periode ini
        $logs = Transaction::with(['user', 'details.product'])
            ->whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai', 'dibatalkan'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('superadmin.laporan', compact(
            'periode', 'totalPendapatan', 'rataRataPesanan', 'dendaTerkumpul',
            'chartBulanan', 'metodePembayaran', 'logs'
        ));
    }

    /**
     * Ekspor laporan ke PDF (halaman HTML printable).
     */
    public function exportPdf(Request $request)
    {
        $periode = $request->get('periode', 'bulanan');
        $now = Carbon::now();

        if ($periode === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            $periodeLabel = 'Custom (' . $start->format('d M Y') . ' - ' . $end->format('d M Y') . ')';
        } else {
            [$start, $end, $periodeLabel] = $this->getDateRangeWithLabel($periode, $now);
        }

        // Ambil SEMUA transaksi (tidak dibatasi periode agar data muncul)
        // Jika ada periode spesifik yang diminta, gunakan filter, tapi fallback ke semua data jika kosong
        $transactions = Transaction::with(['user', 'details.product', 'payment'])
            ->whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai', 'dibatalkan'])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get();

        // Jika tidak ada data dalam periode, ambil 50 transaksi terbaru
        if ($transactions->isEmpty()) {
            $transactions = Transaction::with(['user', 'details.product', 'payment'])
                ->whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai', 'dibatalkan'])
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();
            $periodeLabel .= ' (Semua Data)';
        }

        $totalPendapatan = $transactions->whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai'])->sum('total_biaya');
        $totalDenda      = $transactions->where('denda', '>', 0)->sum('denda');
        $jumlahTransaksi = $transactions->count();

        // Statistik tambahan
        $totalItem = $transactions->flatMap->details->sum('jumlah');
        $metodePembayaranSummary = $transactions->groupBy(fn($t) => $t->payment->metode_pembayaran ?? 'Lainnya')
            ->map(fn($group, $key) => ['label' => $key, 'count' => $group->count(), 'total' => $group->sum('total_biaya')]);

        $exportedBy = auth()->user()->nama_lengkap . ' (' . ucfirst(auth()->user()->peran) . ')';

        ActivityLog::catat('export_pdf', 'Mengekspor laporan PDF periode ' . $periodeLabel);

        return view('superadmin.exports.laporan-pdf', compact(
            'transactions', 'periodeLabel', 'totalPendapatan', 'totalDenda',
            'jumlahTransaksi', 'totalItem', 'metodePembayaranSummary', 'exportedBy'
        ));
    }

    /**
     * Ekspor laporan ke Excel (.xlsx via HTML table — dibuka Excel dengan benar).
     */
    public function exportExcel(Request $request)
    {
        $periode = $request->get('periode', 'bulanan');
        $now = Carbon::now();

        if ($periode === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            $periodeLabel = 'Custom (' . $start->format('d M Y') . ' - ' . $end->format('d M Y') . ')';
        } else {
            [$start, $end, $periodeLabel] = $this->getDateRangeWithLabel($periode, $now);
        }

        $transactions = Transaction::with(['user', 'details.product', 'payment'])
            ->whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai', 'dibatalkan'])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get();

        // Fallback ke semua data jika periode kosong
        if ($transactions->isEmpty()) {
            $transactions = Transaction::with(['user', 'details.product', 'payment'])
                ->whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai', 'dibatalkan'])
                ->orderBy('created_at', 'desc')
                ->limit(500)
                ->get();
        }

        $totalPendapatan = $transactions->whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai'])->sum('total_biaya');
        $totalDenda      = $transactions->where('denda', '>', 0)->sum('denda');
        $filename = 'laporan-keuangan-' . $periode . '-' . $now->format('Y-m-d') . '.xls';

        // Build HTML table — Excel membaca .xls HTML table dengan sempurna
        $html  = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        $html .= '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
        $html .= '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>';
        $html .= '<x:Name>Laporan Keuangan</x:Name>';
        $html .= '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>';
        $html .= '</x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>';
        $html .= '<body><table border="1" style="border-collapse:collapse;font-family:Arial,sans-serif;font-size:11px;">';

        // Title rows
        $exportedBy = auth()->user()->nama_lengkap . ' (' . ucfirst(auth()->user()->peran) . ')';
        $html .= '<tr><td colspan="11" style="background:#1a3a17;color:#fff;font-size:14px;font-weight:bold;padding:10px;text-align:center;">LAPORAN KEUANGAN GARDAKALA OUTDOOR</td></tr>';
        $html .= '<tr><td colspan="11" style="background:#f0f4f0;padding:6px;text-align:center;">Periode: ' . htmlspecialchars($periodeLabel) . ' | Dicetak: ' . $now->format('d/m/Y H:i') . ' WIB | Diekspor Oleh: ' . htmlspecialchars($exportedBy) . '</td></tr>';
        $html .= '<tr><td colspan="11" style="padding:4px;"></td></tr>';

        // Summary row
        $html .= '<tr>';
        $html .= '<td colspan="4" style="background:#e8f5e9;padding:8px;font-weight:bold;">TOTAL PENDAPATAN: Rp ' . number_format($totalPendapatan, 0, ',', '.') . '</td>';
        $html .= '<td colspan="4" style="background:#f3f4f6;padding:8px;font-weight:bold;">JUMLAH TRANSAKSI: ' . $transactions->count() . '</td>';
        $html .= '<td colspan="3" style="background:#fce4ec;padding:8px;font-weight:bold;">TOTAL DENDA: Rp ' . number_format($totalDenda, 0, ',', '.') . '</td>';
        $html .= '</tr>';
        $html .= '<tr><td colspan="11" style="padding:4px;"></td></tr>';

        // Header
        $html .= '<tr style="background:#1a3a17;color:#fff;font-weight:bold;">';
        $html .= '<td style="padding:8px;">No</td>';
        $html .= '<td style="padding:8px;">ID Transaksi</td>';
        $html .= '<td style="padding:8px;">Tanggal</td>';
        $html .= '<td style="padding:8px;">Pelanggan</td>';
        $html .= '<td style="padding:8px;">Email</td>';
        $html .= '<td style="padding:8px;">Produk</td>';
        $html .= '<td style="padding:8px;">Jumlah Item</td>';
        $html .= '<td style="padding:8px;">Total Biaya (Rp)</td>';
        $html .= '<td style="padding:8px;">Denda (Rp)</td>';
        $html .= '<td style="padding:8px;">Status</td>';
        $html .= '<td style="padding:8px;">Metode Pembayaran</td>';
        $html .= '</tr>';

        // Data rows
        $no = 1;
        foreach ($transactions as $trx) {
            $produkList = $trx->details->map(fn($d) => ($d->product->nama_produk ?? '-') . ' (x' . $d->jumlah . ')')->implode('; ');
            $totalItem  = $trx->details->sum('jumlah');
            $bgColor    = ($no % 2 === 0) ? '#fafaf8' : '#ffffff';
            $statusColors = [
                'selesai'    => '#e8f5e9',
                'diproses'   => '#e3f2fd',
                'dikirim'    => '#fff3e0',
                'dibatalkan' => '#fce4ec',
            ];
            $statusBg = $statusColors[$trx->status_transaksi] ?? '#f3f4f6';

            $html .= '<tr style="background:' . $bgColor . ';">';
            $html .= '<td style="padding:7px;text-align:center;">' . $no++ . '</td>';
            $html .= '<td style="padding:7px;font-weight:bold;">#GKD-' . str_pad($trx->id, 5, '0', STR_PAD_LEFT) . '</td>';
            $html .= '<td style="padding:7px;">' . $trx->created_at->format('d/m/Y H:i') . '</td>';
            $html .= '<td style="padding:7px;">' . htmlspecialchars($trx->user->nama_lengkap ?? '-') . '</td>';
            $html .= '<td style="padding:7px;">' . htmlspecialchars($trx->user->email ?? '-') . '</td>';
            $html .= '<td style="padding:7px;">' . htmlspecialchars($produkList) . '</td>';
            $html .= '<td style="padding:7px;text-align:center;">' . $totalItem . '</td>';
            $html .= '<td style="padding:7px;text-align:right;">' . number_format($trx->total_biaya, 0, ',', '.') . '</td>';
            $html .= '<td style="padding:7px;text-align:right;">' . number_format($trx->denda ?? 0, 0, ',', '.') . '</td>';
            $html .= '<td style="padding:7px;background:' . $statusBg . ';font-weight:bold;">' . strtoupper($trx->status_transaksi) . '</td>';
            $html .= '<td style="padding:7px;">' . htmlspecialchars($trx->payment->metode_pembayaran ?? '-') . '</td>';
            $html .= '</tr>';
        }

        // Total footer
        $html .= '<tr style="background:#e8f5e9;font-weight:bold;">';
        $html .= '<td colspan="7" style="padding:8px;text-align:right;">TOTAL</td>';
        $html .= '<td style="padding:8px;text-align:right;">' . number_format($totalPendapatan, 0, ',', '.') . '</td>';
        $html .= '<td style="padding:8px;text-align:right;">' . number_format($totalDenda, 0, ',', '.') . '</td>';
        $html .= '<td colspan="2" style="padding:8px;"></td>';
        $html .= '</tr>';

        $html .= '</table></body></html>';

        ActivityLog::catat('export_excel', 'Mengekspor laporan Excel periode ' . $periodeLabel);

        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function getDateRange(string $periode, Carbon $now): array
    {
        return match($periode) {
            'mingguan' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'tahunan'  => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default    => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };
    }

    private function getDateRangeWithLabel(string $periode, Carbon $now): array
    {
        return match($periode) {
            'mingguan' => [
                $now->copy()->startOfWeek(),
                $now->copy()->endOfWeek(),
                'Mingguan (' . $now->copy()->startOfWeek()->format('d M') . ' – ' . $now->copy()->endOfWeek()->format('d M Y') . ')',
            ],
            'tahunan' => [
                $now->copy()->startOfYear(),
                $now->copy()->endOfYear(),
                'Tahunan ' . $now->year,
            ],
            default => [
                $now->copy()->startOfMonth(),
                $now->copy()->endOfMonth(),
                'Bulanan – ' . $now->translatedFormat('F Y'),
            ],
        };
    }
}
