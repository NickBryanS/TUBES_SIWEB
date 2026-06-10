<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\ActivityLog;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Tampilkan halaman Manajemen Transaksi.
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'details.product', 'payment']);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status_transaksi', $request->status);
        }

        // Filter berdasarkan pencarian (nama pelanggan / ID pesanan)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Stat cards
        $totalTransaksi = Transaction::count();
        $menungguVerifikasi = Transaction::whereIn('status_transaksi', ['menunggu', 'menunggu_admin'])->count();
        $sedangBerjalan = Transaction::whereIn('status_transaksi', ['diproses', 'dikirim'])->count();
        $selesai = Transaction::where('status_transaksi', 'selesai')->count();
        $dibatalkan = Transaction::where('status_transaksi', 'dibatalkan')->count();

        // Pendapatan (3 periode: hari ini, minggu ini, bulan ini)
        $revenueBase = Transaction::whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai']);

        $pendapatanHari = (clone $revenueBase)
            ->whereDate('created_at', Carbon::today())
            ->sum('total_biaya');

        $pendapatanMinggu = (clone $revenueBase)
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('total_biaya');

        $pendapatanBulan = (clone $revenueBase)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_biaya');

        // Periode aktif (dari query param, default: bulan)
        $periodePendapatan = $request->get('periode', 'bulan');

        $transactions = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.transaksi', compact(
            'transactions',
            'totalTransaksi',
            'menungguVerifikasi',
            'sedangBerjalan',
            'selesai',
            'dibatalkan',
            'pendapatanHari',
            'pendapatanMinggu',
            'pendapatanBulan',
            'periodePendapatan'
        ));
    }

    /**
     * Detail transaksi (JSON API untuk modal).
     */
    public function show($id)
    {
        $transaction = Transaction::with(['user', 'details.product', 'payment'])->findOrFail($id);

        return response()->json([
            'id' => $transaction->id,
            'user' => [
                'nama_lengkap' => $transaction->user->nama_lengkap ?? '-',
                'email' => $transaction->user->email ?? '-',
                'nomor_telepon' => $transaction->user->nomor_telepon ?? '-',
            ],
            'tanggal_mulai' => $transaction->tanggal_mulai->format('d M Y'),
            'tanggal_selesai' => $transaction->tanggal_selesai->format('d M Y'),
            'tanggal_kembali_aktual' => $transaction->tanggal_kembali_aktual?->format('d M Y'),
            'total_biaya' => $transaction->total_biaya,
            'denda' => $transaction->denda,
            'keterangan_denda' => $transaction->keterangan_denda,
            'perpanjangan_hari' => $transaction->perpanjangan_hari,
            'status_perpanjangan' => $transaction->status_perpanjangan,
            'status_transaksi' => $transaction->status_transaksi,
            'metode_pengambilan' => $transaction->metode_pengambilan,
            'alamat_pengiriman' => $transaction->alamat_pengiriman,
            'foto_ktp' => $transaction->foto_ktp ? asset('storage/' . $transaction->foto_ktp) : null,
            'jenis_jaminan' => $transaction->jenis_jaminan,
            'status_jaminan' => $transaction->status_jaminan,
            'rekening_pengembalian' => $transaction->rekening_pengembalian,
            'bank_pengembalian' => $transaction->bank_pengembalian,
            'atas_nama_pengembalian' => $transaction->atas_nama_pengembalian,
            'created_at' => $transaction->created_at->format('d M Y, H:i'),
            'details' => $transaction->details->map(function ($d) use ($transaction) {
                $hari = $transaction->tanggal_mulai->diffInDays($transaction->tanggal_selesai);
                return [
                    'nama_produk' => $d->product->nama_produk ?? '-',
                    'url_gambar' => $d->product->url_gambar ?? null,
                    'jumlah' => $d->jumlah,
                    'harga_sewa' => $d->product->harga_sewa ?? 0,
                    'hari' => $hari,
                    'subtotal' => ($d->product->harga_sewa ?? 0) * $d->jumlah * $hari,
                ];
            }),
            'payment' => $transaction->payment ? [
                'metode_pembayaran' => $transaction->payment->metode_pembayaran,
                'status_pembayaran' => $transaction->payment->status_pembayaran,
                'jumlah_bayar' => $transaction->payment->jumlah_bayar,
                'bukti_pembayaran' => $transaction->payment->bukti_pembayaran
                    ? asset('storage/' . $transaction->payment->bukti_pembayaran)
                    : null,
            ] : null,
        ]);
    }

    /**
     * Approve transaksi.
     */
    public function approve($id)
    {
        $transaction = Transaction::findOrFail($id);

        if (!in_array($transaction->status_transaksi, ['menunggu', 'menunggu_admin'])) {
            return redirect()->back()->with('error', 'Transaksi tidak bisa diproses.');
        }

        $transaction->update([
            'status_transaksi' => 'diproses',
            'status_jaminan' => 'verified',
        ]);

        if ($transaction->payment) {
            $transaction->payment->update(['status_pembayaran' => 'terverifikasi']);
        }

        $transaction->user->notify(new OrderStatusNotification($transaction, 'Pesanan Anda #WB-' . str_pad($id, 8, '0', STR_PAD_LEFT) . ' telah divalidasi dan sedang diproses.'));

        ActivityLog::catat('konfirmasi_transaksi', 'Menyetujui transaksi #WB-' . str_pad($id, 8, '0', STR_PAD_LEFT), 'Transaction', $transaction->id);

        return redirect()->route('admin.transaksi.index')
            ->with('success', 'Transaksi #WB-' . str_pad($id, 8, '0', STR_PAD_LEFT) . ' berhasil divalidasi.');
    }

    /**
     * Reject (tolak) transaksi.
     */
    public function reject($id)
    {
        $transaction = Transaction::with('details.product')->findOrFail($id);

        if (!in_array($transaction->status_transaksi, ['menunggu', 'menunggu_admin'])) {
            return redirect()->back()->with('error', 'Transaksi tidak bisa ditolak.');
        }

        // Kembalikan stok
        foreach ($transaction->details as $detail) {
            $detail->product->increment('stok_tersedia', $detail->jumlah);
        }

        $transaction->update([
            'status_transaksi' => 'dibatalkan',
            'status_jaminan' => 'rejected',
        ]);

        $transaction->user->notify(new OrderStatusNotification($transaction, 'Pesanan Anda #WB-' . str_pad($id, 8, '0', STR_PAD_LEFT) . ' telah ditolak dan dibatalkan.'));

        ActivityLog::catat('tolak_transaksi', 'Menolak transaksi #WB-' . str_pad($id, 8, '0', STR_PAD_LEFT), 'Transaction', $transaction->id);

        return redirect()->route('admin.transaksi.index')
            ->with('success', 'Transaksi #WB-' . str_pad($id, 8, '0', STR_PAD_LEFT) . ' ditolak.');
    }

    /**
     * Update status transaksi (misal: diproses → dikirim → selesai).
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:diproses,dikirim,selesai',
        ]);

        $transaction = Transaction::findOrFail($id);

        if ($request->status === 'selesai' && !$transaction->tanggal_kembali_aktual) {
            return redirect()->back()->with('error', 'Tidak bisa menyelesaikan transaksi: Barang belum dikembalikan oleh pelanggan.');
        }

        $transaction->update(['status_transaksi' => $request->status]);

        $label = str_replace('_', ' ', ucfirst($request->status));
        $transaction->user->notify(new OrderStatusNotification($transaction, 'Status pesanan Anda #WB-' . str_pad($id, 8, '0', STR_PAD_LEFT) . ' telah diperbarui menjadi ' . $label . '.'));

        ActivityLog::catat('update_status_transaksi', 'Mengubah status transaksi #WB-' . str_pad($id, 8, '0', STR_PAD_LEFT) . ' menjadi ' . strtoupper($request->status), 'Transaction', $transaction->id);

        return redirect()->route('admin.transaksi.index')
            ->with('success', "Status transaksi diubah ke \"{$label}\".");
    }

    /**
     * Set denda manual oleh admin (barang rusak/hilang).
     */
    public function setDenda(Request $request, $id)
    {
        $request->validate([
            'denda'            => 'required|numeric|min:0',
            'keterangan_denda' => 'required|string|max:500',
        ], [
            'denda.required'            => 'Nominal denda wajib diisi.',
            'denda.numeric'             => 'Nominal denda harus berupa angka.',
            'denda.min'                 => 'Nominal denda tidak boleh negatif.',
            'keterangan_denda.required' => 'Keterangan denda wajib diisi.',
            'keterangan_denda.max'      => 'Keterangan maksimal 500 karakter.',
        ]);

        $transaction = Transaction::findOrFail($id);

        $transaction->update([
            'denda'            => $request->denda,
            'keterangan_denda' => $request->keterangan_denda,
        ]);

        ActivityLog::catat('set_denda_transaksi', 'Menetapkan denda Rp ' . number_format($request->denda, 0, ',', '.') . ' pada transaksi #WB-' . str_pad($id, 8, '0', STR_PAD_LEFT), 'Transaction', $transaction->id);

        return response()->json([
            'success' => true,
            'message' => 'Denda berhasil disimpan sebesar Rp ' . number_format($request->denda, 0, ',', '.') . '.',
            'denda'   => $transaction->denda,
            'keterangan_denda' => $transaction->keterangan_denda,
        ]);
    }

    /**
     * Konfirmasi lunas pembayaran.
     */
    public function konfirmasiLunas($id)
    {
        $transaction = Transaction::with('payment')->findOrFail($id);

        if ($transaction->payment) {
            $transaction->payment->update(['status_pembayaran' => 'terverifikasi']);
        }

        if (in_array($transaction->status_transaksi, ['menunggu', 'menunggu_admin'])) {
            $transaction->update([
                'status_transaksi' => 'diproses',
                'status_jaminan' => 'verified',
            ]);
            $transaction->user->notify(new OrderStatusNotification($transaction, 'Pembayaran pesanan Anda #WB-' . str_pad($id, 8, '0', STR_PAD_LEFT) . ' telah dikonfirmasi dan pesanan sedang diproses.'));
        } else {
            $transaction->user->notify(new OrderStatusNotification($transaction, 'Pembayaran pesanan Anda #WB-' . str_pad($id, 8, '0', STR_PAD_LEFT) . ' telah dikonfirmasi.'));
        }

        ActivityLog::catat('konfirmasi_lunas_transaksi', 'Mengonfirmasi pelunasan transaksi #WB-' . str_pad($id, 8, '0', STR_PAD_LEFT), 'Transaction', $transaction->id);

        return redirect()->route('admin.transaksi.index')
            ->with('success', 'Pembayaran dikonfirmasi lunas.');
    }

    /**
     * Admin mencetak nota transaksi.
     */
    public function cetakNota($id)
    {
        $transaction = Transaction::with(['details.product', 'payment', 'user'])
            ->findOrFail($id);

        return view('user.nota', compact('transaction'));
    }
}
