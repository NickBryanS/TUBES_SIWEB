<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Helper: Ambil user ID yang sedang login.
     * Jika belum ada sistem auth, gunakan user pertama sebagai fallback (development only).
     * TODO: Hapus fallback saat sistem login sudah tersedia.
     */
    private function getUserId()
    {
        return Auth::id() ?? \App\Models\User::first()?->id;
    }

    /**
     * Tampilkan halaman checkout.
     * Ambil data keranjang dari session dan tampilkan item beserta ringkasan.
     */
    public function checkout()
    {
        // Ambil data keranjang dari session (format: [product_id => jumlah])
        $cart = session('cart', []);

        // Ambil data produk yang ada di keranjang
        $items = Product::whereIn('id', array_keys($cart))->get();

        // Hitung subtotal
        $subtotal = 0;
        foreach ($items as $item) {
            $item->jumlah = $cart[$item->id] ?? 1;
            $subtotal += $item->harga_sewa * $item->jumlah;
        }

        return view('checkout', compact('items', 'subtotal'));
    }

    /**
     * Simpan transaksi baru (POST dari halaman checkout).
     * Menyimpan data transaksi, detail item, dan membuat record pembayaran.
     */
    public function store(Request $request)
    {
        // Validasi input dari form checkout
        $request->validate([
            'tanggal_mulai'      => 'required|date|after_or_equal:today',
            'tanggal_selesai'    => 'required|date|after:tanggal_mulai',
            'metode_pengambilan' => 'required|in:pickup,deliver',
            'alamat_pengiriman'  => 'nullable|required_if:metode_pengambilan,deliver|string',
            'metode_pembayaran'  => 'required|in:transfer_bank,qris,bayar_di_toko',
        ]);

        // Ambil keranjang dari session
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->back()->with('error', 'Keranjang belanja kosong.');
        }

        $userId = $this->getUserId();

        // Gunakan DB transaction untuk menjaga konsistensi data
        $transaction = DB::transaction(function () use ($request, $cart, $userId) {

            // Ambil produk dari database
            $products = Product::whereIn('id', array_keys($cart))->get();

            // Hitung total biaya
            $totalBiaya = 0;
            foreach ($products as $product) {
                $jumlah = $cart[$product->id] ?? 1;
                $totalBiaya += $product->harga_sewa * $jumlah;
            }

            // 1. Simpan transaksi utama
            $transaction = Transaction::create([
                'user_id'            => $userId,
                'tanggal_mulai'      => $request->tanggal_mulai,
                'tanggal_selesai'    => $request->tanggal_selesai,
                'total_biaya'        => $totalBiaya,
                'status_transaksi'   => 'menunggu',
                'metode_pengambilan' => $request->metode_pengambilan,
                'alamat_pengiriman'  => $request->alamat_pengiriman,
            ]);

            // 2. Simpan detail transaksi (setiap item di keranjang)
            foreach ($products as $product) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id'     => $product->id,
                    'jumlah'         => $cart[$product->id] ?? 1,
                ]);
            }

            // 3. Buat record pembayaran awal
            Payment::create([
                'transaction_id'    => $transaction->id,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status_pembayaran' => 'menunggu',
                'jumlah_bayar'      => $totalBiaya,
            ]);

            return $transaction;
        });

        // Kosongkan keranjang setelah checkout berhasil
        session()->forget('cart');

        // Redirect ke halaman konfirmasi
        return redirect()->route('konfirmasi', $transaction->id)
                         ->with('success', 'Pesanan berhasil dibuat!');
    }

    /**
     * Tampilkan halaman pembayaran untuk transaksi tertentu.
     */
    public function pembayaran($id)
    {
        $transaction = Transaction::with(['details.product', 'payment'])
            ->findOrFail($id);

        return view('pembayaran', compact('transaction'));
    }

    /**
     * Upload bukti pembayaran (POST).
     */
    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $transaction = Transaction::findOrFail($id);
        $payment = $transaction->payment;

        if (!$payment) {
            return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        // Simpan file bukti pembayaran
        $path = $request->file('bukti_pembayaran')
                        ->store('bukti-pembayaran', 'public');

        // Update payment record
        $payment->update([
            'bukti_pembayaran'  => $path,
            'status_pembayaran' => 'menunggu_verifikasi',
        ]);

        // Update status transaksi
        $transaction->update([
            'status_transaksi' => 'menunggu_admin',
        ]);

        return redirect()->route('konfirmasi', $transaction->id)
                         ->with('success', 'Bukti pembayaran berhasil diunggah!');
    }

    /**
     * Tampilkan halaman konfirmasi pesanan.
     */
    public function konfirmasi($id)
    {
        $transaction = Transaction::with(['details.product', 'payment'])
            ->findOrFail($id);

        return view('konfirmasi', compact('transaction'));
    }

    /**
     * Tampilkan halaman riwayat transaksi user.
     */
    public function riwayat()
    {
        $transactions = Transaction::with(['details.product', 'payment'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('riwayat', compact('transactions'));
    }

    /**
     * Tampilkan detail pesanan berdasarkan ID.
     */
    public function detail($id)
    {
        $transaction = Transaction::with(['details.product', 'payment', 'user'])
            ->findOrFail($id);

        return view('pesanan-detail', compact('transaction'));
    }
}
