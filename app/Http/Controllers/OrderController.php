<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Notifications\OrderStatusUpdated;
use App\Notifications\RentalReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
                $cart->product->decrement('stok_tersedia', $cart->quantity);
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

            // Update status transaksi jika bukti sudah diupload
            if ($buktiPath) {
                $transaction->update(['status_transaksi' => 'menunggu_admin']);
            }

            return $transaction;
        });

        // Kosongkan keranjang dan session checkout setelah berhasil
        \App\Models\Cart::where('user_id', $userId)->delete();
        $request->session()->forget('checkout_data');

        $transaction->user->notify(new OrderStatusUpdated($transaction));

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

        $transaction->user->notify(new OrderStatusUpdated($transaction));

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

    /**
     * User membatalkan pesanan.
     */
    public function batalkanPesanan($id)
    {
        $transaction = Transaction::with('details.product', 'user')->findOrFail($id);

        $userId = $this->getUserId();
        if ($transaction->user_id !== $userId) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        if (!in_array($transaction->status_transaksi, ['menunggu', 'menunggu_admin'])) {
            return redirect()->back()->with('error', 'Pesanan ini tidak dapat dibatalkan.');
        }

        // Kembalikan stok
        foreach ($transaction->details as $detail) {
            $detail->product->increment('stok_tersedia', $detail->jumlah);
        }

        $transaction->update([
            'status_transaksi' => 'dibatalkan',
        ]);

        $transaction->user->notify(new OrderStatusUpdated($transaction));

        return redirect()->route('pesanan.detail', $transaction->id)
                         ->with('success', 'Pesanan berhasil dibatalkan.');
    }

    // =========================================================================
    // FR-USR-034: LOGIKA KALKULASI DENDA KETERLAMBATAN
    // =========================================================================

    /**
     * Helper: Hitung denda keterlambatan berdasarkan selisih hari.
     * Rumus: 50% x harga_sewa_harian x jumlah_item x jumlah_hari_telat
     *
     * @param Transaction $transaction
     * @param Carbon $tanggalKembali
     * @return float
     */
    private function hitungDenda(Transaction $transaction, Carbon $tanggalKembali): float
    {
        $tanggalSelesai = Carbon::parse($transaction->tanggal_selesai);

        // Jika dikembalikan tepat waktu atau lebih awal, tidak ada denda
        if ($tanggalKembali->lte($tanggalSelesai)) {
            return 0;
        }

        // Hitung jumlah hari keterlambatan
        $hariTelat = $tanggalKembali->diffInDays($tanggalSelesai);

        // Hitung total denda dari semua item
        $totalDenda = 0;
        $transaction->load('details.product');

        foreach ($transaction->details as $detail) {
            $hargaHarian = $detail->product->harga_sewa;
            // Denda = 50% dari harga sewa harian per item per hari keterlambatan
            $dendaPerItem = ($hargaHarian * 0.5) * $detail->jumlah * $hariTelat;
            $totalDenda += $dendaPerItem;
        }

        return $totalDenda;
    }

    /**
     * Konfirmasi pengembalian barang (POST).
     * Mencatat tanggal kembali aktual dan menghitung denda otomatis.
     */
    public function konfirmasiPengembalian(Request $request, $id)
    {
        $request->validate([
            'tanggal_kembali_aktual' => 'required|date|after_or_equal:' . now()->format('Y-m-d'),
        ]);

        $transaction = Transaction::with('details.product')->findOrFail($id);

        // Pastikan transaksi dalam status yang benar (sedang berjalan)
        if (!in_array($transaction->status_transaksi, ['diproses', 'dikirim'])) {
            return redirect()->back()->with('error', 'Pesanan ini tidak dalam status yang bisa dikembalikan.');
        }

        $tanggalKembali = Carbon::parse($request->tanggal_kembali_aktual);
        $denda = $this->hitungDenda($transaction, $tanggalKembali);

        $transaction->update([
            'tanggal_kembali_aktual' => $tanggalKembali,
            'denda'                  => $denda,
            'status_transaksi'       => 'selesai',
        ]);

        $transaction->user->notify(new OrderStatusUpdated($transaction));

        $message = 'Pengembalian barang berhasil dicatat.';
        if ($denda > 0) {
            $message .= ' Denda keterlambatan: Rp ' . number_format($denda, 0, ',', '.');
        }

        return redirect()->route('pesanan.detail', $transaction->id)
                         ->with('success', $message);
    }

    // =========================================================================
    // FR-USR-033: FITUR PERPANJANGAN SEWA
    // =========================================================================

    /**
     * Tampilkan form perpanjangan sewa.
     */
    public function formPerpanjangan($id)
    {
        $transaction = Transaction::with(['details.product', 'payment'])
            ->findOrFail($id);

        // Hanya bisa diperpanjang jika status masih aktif
        if (!in_array($transaction->status_transaksi, ['diproses', 'dikirim'])) {
            return redirect()->back()->with('error', 'Pesanan ini tidak bisa diperpanjang.');
        }

        // Jika sudah ada pengajuan pending, tampilkan pesan
        if ($transaction->status_perpanjangan === 'pending') {
            return redirect()->back()->with('info', 'Pengajuan perpanjangan Anda sedang menunggu persetujuan admin.');
        }

        return view('perpanjangan', compact('transaction'));
    }

    /**
     * User mengajukan perpanjangan sewa (POST).
     * Menyimpan jumlah hari tambahan yang diminta ke database.
     */
    public function ajukanPerpanjangan(Request $request, $id)
    {
        $request->validate([
            'perpanjangan_hari' => 'required|integer|min:1|max:30',
        ]);

        $transaction = Transaction::findOrFail($id);

        // Pastikan transaksi milik user yang login
        $userId = $this->getUserId();
        if ($transaction->user_id !== $userId) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        // Pastikan status masih aktif
        if (!in_array($transaction->status_transaksi, ['diproses', 'dikirim'])) {
            return redirect()->back()->with('error', 'Pesanan ini tidak bisa diperpanjang.');
        }

        // Simpan pengajuan perpanjangan
        $transaction->update([
            'perpanjangan_hari'    => $request->perpanjangan_hari,
            'status_perpanjangan'  => 'pending',
        ]);

        return redirect()->route('pesanan.detail', $transaction->id)
                         ->with('success', 'Pengajuan perpanjangan ' . $request->perpanjangan_hari . ' hari berhasil dikirim. Menunggu persetujuan admin.');
    }

    /**
     * Admin menyetujui perpanjangan sewa (POST).
     * Mengupdate tanggal_selesai dan total_biaya sesuai hari tambahan.
     */
    public function approvePerpanjangan($id)
    {
        $transaction = Transaction::with('details.product')->findOrFail($id);

        if ($transaction->status_perpanjangan !== 'pending') {
            return redirect()->back()->with('error', 'Tidak ada pengajuan perpanjangan yang menunggu.');
        }

        $hariTambahan = $transaction->perpanjangan_hari;

        // Hitung biaya tambahan dari semua item
        $biayaTambahan = 0;
        foreach ($transaction->details as $detail) {
            $biayaTambahan += $detail->product->harga_sewa * $detail->jumlah * $hariTambahan;
        }

        // Update tanggal selesai dan total biaya
        $tanggalSelesaiBaru = Carbon::parse($transaction->tanggal_selesai)
                                    ->addDays($hariTambahan);

        $transaction->update([
            'tanggal_selesai'      => $tanggalSelesaiBaru,
            'total_biaya'          => $transaction->total_biaya + $biayaTambahan,
            'status_perpanjangan'  => 'approved',
        ]);

        return redirect()->route('pesanan.detail', $transaction->id)
                         ->with('success', 'Perpanjangan ' . $hariTambahan . ' hari disetujui. Biaya tambahan: Rp ' . number_format($biayaTambahan, 0, ',', '.'));
    }

    /**
     * Admin menolak perpanjangan sewa (POST).
     */
    public function rejectPerpanjangan($id)
    {
        $transaction = Transaction::findOrFail($id);

        if ($transaction->status_perpanjangan !== 'pending') {
            return redirect()->back()->with('error', 'Tidak ada pengajuan perpanjangan yang menunggu.');
        }

        $transaction->update([
            'perpanjangan_hari'    => 0,
            'status_perpanjangan'  => 'rejected',
        ]);

        return redirect()->route('pesanan.detail', $transaction->id)
                         ->with('info', 'Pengajuan perpanjangan ditolak.');
    }
}
