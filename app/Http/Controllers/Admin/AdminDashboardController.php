<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard admin.
     * Menyediakan semua data statistik untuk dashboard.
     */
    public function index()
    {
        // ── STAT CARDS ──────────────────────────────────────────

        // Total Pendapatan (dari transaksi yang sudah selesai/diproses)
        $totalPendapatan = Transaction::whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai'])
            ->sum('total_biaya');

        // Persentase perubahan pendapatan (bandingkan minggu ini vs minggu lalu)
        $pendapatanMingguIni = Transaction::whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai'])
            ->where('created_at', '>=', Carbon::now()->startOfWeek())
            ->sum('total_biaya');
        $pendapatanMingguLalu = Transaction::whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai'])
            ->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->startOfWeek()])
            ->sum('total_biaya');
        $persenPerubahan = $pendapatanMingguLalu > 0
            ? round((($pendapatanMingguIni - $pendapatanMingguLalu) / $pendapatanMingguLalu) * 100)
            : 0;

        // Penyewaan Aktif
        $penyewaanAktif = Transaction::whereIn('status_transaksi', ['diproses', 'dikirim'])->count();
        $menungguPesanan = Transaction::where('status_transaksi', 'menunggu')->count();

        // Menunggu Verifikasi
        $menungguVerifikasi = Transaction::where('status_transaksi', 'menunggu_admin')->count();

        // Stok Tipis (produk dengan stok_tersedia <= 3)
        $stokTipis = Product::where('stok_tersedia', '<=', 3)->count();

        // ── CHART: TREN PENDAPATAN 7 HARI ───────────────────────
        $chartData = [];
        $hariLabel = ['SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB', 'MIN'];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayOfWeek = $date->dayOfWeekIso; // 1=Monday ... 7=Sunday
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
                'product_id',
                DB::raw('SUM(jumlah) as total_sewa')
            )
            ->groupBy('product_id')
            ->orderByDesc('total_sewa')
            ->limit(4)
            ->with('product')
            ->get();

        // ── TRANSAKSI PERLU TINDAKAN ────────────────────────────
        $transaksiMenunggu = Transaction::with(['user', 'details.product'])
            ->whereIn('status_transaksi', ['menunggu', 'menunggu_admin'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ── JADWAL PENGEMBALIAN HARI INI ────────────────────────
        $today = Carbon::today();
        $jadwalPengembalian = Transaction::with(['user', 'details.product', 'payment'])
            ->whereIn('status_transaksi', ['diproses', 'dikirim'])
            ->whereDate('tanggal_selesai', $today)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.dashboard', compact(
            'totalPendapatan',
            'persenPerubahan',
            'penyewaanAktif',
            'menungguPesanan',
            'menungguVerifikasi',
            'stokTipis',
            'chartData',
            'barangTerlaris',
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

        return redirect()->route('admin.dashboard')->with('success', 'Transaksi #TRX-' . str_pad($id, 4, '0', STR_PAD_LEFT) . ' ditolak.');
    }
}
