<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Product;
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
}
