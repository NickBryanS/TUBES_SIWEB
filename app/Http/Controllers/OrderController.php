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
     * Tampilkan halaman checkout (Step 1 - PEMESANAN).
     * Ambil data keranjang dari session dan tampilkan item beserta ringkasan.
     */
    public function checkout()
    {
        $userId = $this->getUserId();
        $carts = \App\Models\Cart::where('user_id', $userId)->with('product')->get();

        $subtotal = 0;
        foreach ($carts as $cart) {
            $subtotal += $cart->product->harga_sewa * $cart->quantity * $cart->days;
        }

        return view('checkout', compact('carts', 'subtotal'));
    }

    /**
     * Simpan data checkout ke session dan redirect ke halaman pembayaran (Step 2).
     * Belum membuat transaksi — hanya menyimpan pilihan user sementara.
     */
    public function store(Request $request)
    {
        // Validasi input dari form checkout
        $request->validate([
            'tanggal_mulai'      => 'required|date|after_or_equal:today',
            'tanggal_selesai'    => 'required|date|after:tanggal_mulai',
            'metode_pengambilan' => 'required|in:pickup,deliver',
            'alamat_pengiriman'  => 'nullable|required_if:metode_pengambilan,deliver|string',
        ]);

        // Simpan data checkout ke session (belum buat transaksi)
        $request->session()->put('checkout_data', [
            'tanggal_mulai'      => $request->tanggal_mulai,
            'tanggal_selesai'    => $request->tanggal_selesai,
            'metode_pengambilan' => $request->metode_pengambilan,
            'alamat_pengiriman'  => $request->alamat_pengiriman,
        ]);

        // Redirect ke halaman pembayaran (Step 2)
        return redirect()->route('pembayaran');
    }

    /**
     * Tampilkan halaman pembayaran (Step 2 - PEMBAYARAN).
     * Data diambil dari cart user + session checkout_data.
     */
    public function pembayaran(Request $request)
    {
        // Pastikan user sudah melalui step 1
        if (!$request->session()->has('checkout_data')) {
            return redirect()->route('checkout')->with('error', 'Silakan isi data pemesanan terlebih dahulu.');
        }

        $userId = $this->getUserId();
        $carts = \App\Models\Cart::where('user_id', $userId)->with('product')->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong.');
        }

        $subtotal = 0;
        foreach ($carts as $cart) {
            $subtotal += $cart->product->harga_sewa * $cart->quantity * $cart->days;
        }

        $biayaAdmin = 2500;
        $total = $subtotal + $biayaAdmin;

        $checkoutData = $request->session()->get('checkout_data');

        return view('pembayaran', compact('carts', 'subtotal', 'biayaAdmin', 'total', 'checkoutData'));
    }

    /**
     * Proses pembayaran dan buat transaksi (POST dari halaman pembayaran Step 2).
     * Di sinilah transaksi + detail + payment record benar-benar dibuat.
     */
    public function storePembayaran(Request $request)
    {
        // Validasi metode pembayaran
        $request->validate([
            'metode_pembayaran'  => 'required|in:transfer_bank,qris,bayar_di_toko',
            'bukti_pembayaran'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // Pastikan data checkout ada di session
        $checkoutData = $request->session()->get('checkout_data');
        if (!$checkoutData) {
            return redirect()->route('checkout')->with('error', 'Sesi checkout telah berakhir. Silakan ulangi pemesanan.');
        }

        // Ambil keranjang dari database
        $userId = $this->getUserId();
        $carts = \App\Models\Cart::where('user_id', $userId)->with('product')->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong.');
        }

        // Gunakan DB transaction untuk menjaga konsistensi data
        $transaction = DB::transaction(function () use ($request, $carts, $userId, $checkoutData) {

            // Hitung total biaya
            $totalBiaya = 0;
            foreach ($carts as $cart) {
                $totalBiaya += $cart->product->harga_sewa * $cart->quantity * $cart->days;
            }
            $totalBiaya += 2500; // Biaya admin

            // 1. Simpan transaksi utama
            $transaction = Transaction::create([
                'user_id'            => $userId,
                'tanggal_mulai'      => $checkoutData['tanggal_mulai'],
                'tanggal_selesai'    => $checkoutData['tanggal_selesai'],
                'total_biaya'        => $totalBiaya,
                'status_transaksi'   => 'menunggu',
                'metode_pengambilan' => $checkoutData['metode_pengambilan'],
                'alamat_pengiriman'  => $checkoutData['alamat_pengiriman'],
            ]);

            // 2. Simpan detail transaksi (setiap item di keranjang)
            foreach ($carts as $cart) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id'     => $cart->product_id,
                    'jumlah'         => $cart->quantity,
                ]);
            }

            // 3. Upload bukti pembayaran jika ada
            $buktiPath = null;
            if ($request->hasFile('bukti_pembayaran')) {
                $buktiPath = $request->file('bukti_pembayaran')
                                     ->store('bukti-pembayaran', 'public');
            }

            // 4. Buat record pembayaran
            $statusPembayaran = 'menunggu';
            if ($buktiPath) {
                $statusPembayaran = 'menunggu_verifikasi';
            }
            if ($request->metode_pembayaran === 'bayar_di_toko') {
                $statusPembayaran = 'menunggu';
            }

            Payment::create([
                'transaction_id'    => $transaction->id,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status_pembayaran' => $statusPembayaran,
                'jumlah_bayar'      => $totalBiaya,
                'bukti_pembayaran'  => $buktiPath,
            ]);

            return $transaction;
        });

        // Kosongkan keranjang dan session checkout setelah berhasil
        \App\Models\Cart::where('user_id', $userId)->delete();
        $request->session()->forget('checkout_data');

        // Redirect ke halaman konfirmasi (Step 3)
        return redirect()->route('konfirmasi', $transaction->id)
                         ->with('success', 'Pesanan berhasil dibuat!');
    }

    /**
     * Upload bukti pembayaran (POST) — untuk upload ulang dari halaman terpisah.
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
     * Tampilkan halaman konfirmasi pesanan (Step 3 - SELESAI).
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
