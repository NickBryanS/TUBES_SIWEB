<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Review;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard admin.
     * Menyediakan semua data statistik untuk dashboard.
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'bulanan'); // mingguan | bulanan | tahunan

        // ── DATE RANGE ───────────────────────────────────────────
        $now = Carbon::now();
        [$dateFrom, $dateTo] = match($period) {
            'mingguan' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'tahunan'  => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default    => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()], // bulanan
        };

        [$prevFrom, $prevTo] = match($period) {
            'mingguan' => [$now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek()],
            'tahunan'  => [$now->copy()->subYear()->startOfYear(), $now->copy()->subYear()->endOfYear()],
            default    => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()],
        };

        // ── TOTAL PENDAPATAN ─────────────────────────────────────
        $totalPendapatan = Transaction::whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('total_biaya');

        $prevPendapatan = Transaction::whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai'])
            ->whereBetween('created_at', [$prevFrom, $prevTo])
            ->sum('total_biaya');

        $persenPerubahan = $prevPendapatan > 0
            ? round((($totalPendapatan - $prevPendapatan) / $prevPendapatan) * 100, 1)
            : 0;

        // ── PESANAN SELESAI ──────────────────────────────────────
        $pesananSelesai = Transaction::where('status_transaksi', 'selesai')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->count();

        // Tingkat kepuasan (dari ulasan bintang >= 4)
        $totalUlasan = \App\Models\Review::whereBetween('created_at', [$dateFrom, $dateTo])->count();
        $ulasanPositif = \App\Models\Review::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('rating', '>=', 4)->count();
        $tingkatKepuasan = $totalUlasan > 0 ? round(($ulasanPositif / $totalUlasan) * 100) : 98;

        // ── SALDO TERSEDIA (kas masuk periode ini) ───────────────
        $saldoTersedia = Transaction::where('status_transaksi', 'selesai')
            ->sum('total_biaya');

        // ── PENYEWAAN AKTIF & STATUS ─────────────────────────────
        $penyewaanAktif    = Transaction::whereIn('status_transaksi', ['diproses', 'dikirim'])->count();
        $menungguPesanan   = Transaction::where('status_transaksi', 'menunggu')->count();
        $menungguVerifikasi = Transaction::where('status_transaksi', 'menunggu_admin')->count();
        $stokTipis         = Product::where('stok_tersedia', '<=', 3)->count();

        // ── CHART: ALIRAN KAS ─────────────────────────────────────
        $chartData = [];
        $hariLabel = ['SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB', 'MIN'];
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $dayOfWeek = $date->dayOfWeekIso;
            $pendapatan = Transaction::whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai'])
                ->whereDate('created_at', $date->toDateString())
                ->sum('total_biaya');
            $chartData[] = [
                'label' => $hariLabel[$dayOfWeek - 1],
                'value' => (float) $pendapatan,
                'date'  => $date->format('d M'),
            ];
        }

        // ── BARANG TERLARIS ─────────────────────────────────────
        $barangTerlaris = TransactionDetail::select(
                'product_id', DB::raw('SUM(jumlah) as total_sewa')
            )
            ->groupBy('product_id')
            ->orderByDesc('total_sewa')
            ->limit(4)
            ->with('product')
            ->get();

        // ── RINCIAN TRANSAKSI (recent) ───────────────────────────
        $recentTransaksi = Transaction::with(['user', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        // ── METODE PEMBAYARAN BREAKDOWN ──────────────────────────
        $totalPayments = \App\Models\Payment::count() ?: 1;
        $paymentBreakdown = [
            [
                'label'   => 'Transfer Bank',
                'count'   => \App\Models\Payment::where('metode_pembayaran', 'transfer_bank')->count(),
                'fill'    => '',
            ],
            [
                'label'   => 'Tunai di Toko',
                'count'   => \App\Models\Payment::where('metode_pembayaran', 'bayar_di_toko')->count(),
                'fill'    => 'fill-2',
            ],
            [
                'label'   => 'QRIS / DP',
                'count'   => \App\Models\Payment::where('metode_pembayaran', 'qris')->count(),
                'fill'    => 'fill-3',
            ],
        ];
        foreach ($paymentBreakdown as &$pb) {
            $pb['pct'] = round(($pb['count'] / $totalPayments) * 100);
        }
        unset($pb);

        // ── TRANSAKSI PERLU TINDAKAN (untuk modal quick-action) ──
        $transaksiMenunggu = Transaction::with(['user', 'details.product'])
            ->whereIn('status_transaksi', ['menunggu', 'menunggu_admin'])
            ->orderBy('created_at', 'desc')
            ->limit(5)->get();

        // ── JADWAL PENGEMBALIAN ───────────────────────────────────
        $today = Carbon::today();
        $jadwalPengembalian = Transaction::with(['user', 'details.product', 'payment'])
            ->whereIn('status_transaksi', ['diproses', 'dikirim'])
            ->whereDate('tanggal_selesai', $today)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.dashboard', compact(
            'period',
            'totalPendapatan',
            'persenPerubahan',
            'pesananSelesai',
            'tingkatKepuasan',
            'saldoTersedia',
            'penyewaanAktif',
            'menungguPesanan',
            'menungguVerifikasi',
            'stokTipis',
            'chartData',
            'barangTerlaris',
            'recentTransaksi',
            'paymentBreakdown',
            'transaksiMenunggu',
            'jadwalPengembalian'
        ));
    }

    /**
     * Approve transaksi (ubah status dari menunggu_admin ke diproses).
     */
    public function approveTransaksi($id)
    {
        $transaction = Transaction::findOrFail($id);

        if (!in_array($transaction->status_transaksi, ['menunggu', 'menunggu_admin'])) {
            return redirect()->back()->with('error', 'Transaksi tidak bisa diproses.');
        }

        $transaction->update(['status_transaksi' => 'diproses']);

        // Juga update status pembayaran jika ada
        if ($transaction->payment) {
            $transaction->payment->update(['status_pembayaran' => 'terverifikasi']);
        }

        // Kirim notifikasi ke user
        $transaction->user->notify(new OrderStatusNotification(
            $transaction,
            'Pesanan Anda #TRX-' . str_pad($id, 4, '0', STR_PAD_LEFT) . ' telah disetujui dan sedang diproses.'
        ));

        return redirect()->route('admin.dashboard')->with('success', 'Transaksi #TRX-' . str_pad($id, 4, '0', STR_PAD_LEFT) . ' berhasil disetujui.');
    }

    /**
     * Reject transaksi (ubah status ke dibatalkan).
     */
    public function rejectTransaksi($id)
    {
        $transaction = Transaction::with('details.product')->findOrFail($id);

        if (!in_array($transaction->status_transaksi, ['menunggu', 'menunggu_admin'])) {
            return redirect()->back()->with('error', 'Transaksi tidak bisa ditolak.');
        }

        // Kembalikan stok
        foreach ($transaction->details as $detail) {
            $detail->product->increment('stok_tersedia', $detail->jumlah);
        }

        $transaction->update(['status_transaksi' => 'dibatalkan']);

        // Kirim notifikasi ke user
        $transaction->user->notify(new OrderStatusNotification(
            $transaction,
            'Pesanan Anda #TRX-' . str_pad($id, 4, '0', STR_PAD_LEFT) . ' telah ditolak dan dibatalkan.'
        ));

        return redirect()->route('admin.dashboard')->with('success', 'Transaksi #TRX-' . str_pad($id, 4, '0', STR_PAD_LEFT) . ' ditolak.');
    }
}
