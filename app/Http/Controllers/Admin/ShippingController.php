<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShippingController extends Controller
{
    /**
     * Halaman utama Manajemen Pengiriman.
     * Menampilkan antrean pengiriman (transaksi dengan metode deliver)
     * beserta stat cards dan fitur filter.
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'details.product', 'payment'])
            ->where('metode_pengambilan', 'deliver')
            ->whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai']);

        // Filter status pengiriman
        if ($request->filled('status')) {
            $statusMap = [
                'menunggu'  => 'diproses',
                'dikirim'   => 'dikirim',
                'selesai'   => 'selesai',
            ];
            if (isset($statusMap[$request->status])) {
                $query->where('status_transaksi', $statusMap[$request->status]);
            }
        }

        // Pencarian (nama pelanggan / ID pesanan)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('no_telepon', 'like', "%{$search}%");
                })->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Stat Cards
        $totalDikirim = Transaction::where('metode_pengambilan', 'deliver')
            ->whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai'])
            ->count();

        $dalamPengiriman = Transaction::where('metode_pengambilan', 'deliver')
            ->where('status_transaksi', 'dikirim')
            ->count();

        $selesaiDikirim = Transaction::where('metode_pengambilan', 'deliver')
            ->where('status_transaksi', 'selesai')
            ->count();

        $menungguKirim = Transaction::where('metode_pengambilan', 'deliver')
            ->where('status_transaksi', 'diproses')
            ->count();

        $shipments = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.pengiriman', compact(
            'shipments',
            'totalDikirim',
            'dalamPengiriman',
            'selesaiDikirim',
            'menungguKirim'
        ));
    }

    /**
     * API: Detail satu pengiriman (untuk modal).
     */
    public function show($id)
    {
        $transaction = Transaction::with(['user', 'details.product', 'payment'])
            ->where('metode_pengambilan', 'deliver')
            ->findOrFail($id);

        $items = $transaction->details->map(function ($d) {
            return [
                'nama'    => $d->product->nama_produk ?? 'Produk',
                'gambar'  => $d->product->url_gambar ?? null,
                'spek'    => $d->product->spesifikasi_teknis ?? '-',
                'jumlah'  => $d->jumlah,
            ];
        });

        return response()->json([
            'id'                 => $transaction->id,
            'user_nama'          => $transaction->user->nama_lengkap ?? 'User',
            'user_telepon'       => $transaction->user->no_telepon ?? '-',
            'alamat_pengiriman'  => $transaction->alamat_pengiriman ?? '-',
            'tanggal_mulai'      => Carbon::parse($transaction->tanggal_mulai)->translatedFormat('d M Y'),
            'tanggal_selesai'    => Carbon::parse($transaction->tanggal_selesai)->translatedFormat('d M Y'),
            'total_biaya'        => $transaction->total_biaya,
            'status_transaksi'   => $transaction->status_transaksi,
            'items'              => $items,
            'created_at'         => $transaction->created_at->translatedFormat('d M, H:i'),
        ]);
    }

    /**
     * Update status pengiriman: siapkan → kirim → selesai
     */
    public function updateStatus(Request $request, $id)
    {
        $transaction = Transaction::where('metode_pengambilan', 'deliver')->findOrFail($id);

        $action = $request->input('action');

        if ($action === 'siapkan' && $transaction->status_transaksi === 'diproses') {
            // Siapkan pesanan — tetap diproses, tapi tandai siap kirim
            // (opsional: bisa tambah kolom 'siap_kirim' nanti)
            return back()->with('success', 'Pesanan #GK-' . str_pad($id, 4, '0', STR_PAD_LEFT) . ' siap dikirim.');
        }

        if ($action === 'kirim' && in_array($transaction->status_transaksi, ['diproses'])) {
            $transaction->update(['status_transaksi' => 'dikirim']);
            return back()->with('success', 'Pesanan #GK-' . str_pad($id, 4, '0', STR_PAD_LEFT) . ' sedang dalam pengiriman.');
        }

        if ($action === 'selesai' && $transaction->status_transaksi === 'dikirim') {
            $request->validate([
                'nama_penerima' => 'required|string|max:255',
            ]);

            $transaction->update(['status_transaksi' => 'selesai']);

            // Handle foto bukti pengiriman jika ada
            if ($request->hasFile('bukti_pengiriman')) {
                $path = $request->file('bukti_pengiriman')->store('bukti-pengiriman', 'public');
                // Simpan path jika ada kolom — jika belum, skip saja
            }

            return back()->with('success', 'Pengiriman #GK-' . str_pad($id, 4, '0', STR_PAD_LEFT) . ' dikonfirmasi selesai. Penerima: ' . $request->nama_penerima);
        }

        return back()->with('error', 'Aksi tidak valid untuk status saat ini.');
    }
}
