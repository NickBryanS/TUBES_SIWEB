<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SuperAdminDashboardController extends Controller
{
    /**
     * Executive Dashboard — Pemilik GKDL
     */
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now();

        // Stat Cards
        $pendapatanHariIni = Transaction::whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai'])
            ->whereDate('created_at', $today)->sum('total_biaya');

        $prevDay = Transaction::whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai'])
            ->whereDate('created_at', $today->copy()->subDay())->sum('total_biaya');
        $persenPendapatan = $prevDay > 0 ? round((($pendapatanHariIni - $prevDay) / $prevDay) * 100) : 0;

        $totalTransaksiAktif = Transaction::whereIn('status_transaksi', ['menunggu', 'menunggu_admin', 'diproses', 'dikirim'])->count();

        $totalStaf = User::where('peran', 'admin')->count();
        $totalBarang = Product::sum('total_stok');
        $stokTipis = Product::whereColumn('stok_tersedia', '<', DB::raw('total_stok * 0.2'))->count();

        // Chart Data: Pendapatan 7 hari terakhir
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartData[] = [
                'label' => $date->translatedFormat('D'),
                'value' => (float) Transaction::whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai'])
                    ->whereDate('created_at', $date)->sum('total_biaya'),
            ];
        }

        // Top 5 Produk Terlaris
        $topProduk = DB::table('transaction_details')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->select('products.nama_produk', 'products.url_gambar', DB::raw('SUM(transaction_details.jumlah) as total_sewa'))
            ->groupBy('products.id', 'products.nama_produk', 'products.url_gambar')
            ->orderByDesc('total_sewa')
            ->limit(5)->get();

        // Status Armada (transaksi dengan metode deliver yang sedang aktif)
        $armadaAktif = Transaction::with('user')
            ->where('metode_pengambilan', 'deliver')
            ->where('status_transaksi', 'dikirim')
            ->limit(3)->get();

        // Peringatan Inventaris (stok hampir habis)
        $peringatanStok = Product::whereColumn('stok_tersedia', '<', DB::raw('total_stok * 0.3'))
            ->where('stok_tersedia', '>', 0)
            ->orderBy('stok_tersedia')->limit(3)->get();

        return view('superadmin.dashboard', compact(
            'pendapatanHariIni', 'persenPendapatan', 'totalTransaksiAktif',
            'totalStaf', 'totalBarang', 'stokTipis',
            'chartData', 'topProduk', 'armadaAktif', 'peringatanStok'
        ));
    }
}
