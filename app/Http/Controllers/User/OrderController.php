<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{

    /**
     * Tampilkan halaman dashboard user.
     * Menampilkan stats, sewa aktif, dan transaksi terakhir dari database.
     */
    public function dashboard()
    {
        $userId = Auth::id();

        // Query semua transaksi milik user
        $allTransactions = Transaction::where('user_id', $userId)
            ->with(['details.product', 'payment'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Stats
        $sewaAktif = $allTransactions->whereIn('status_transaksi', ['diproses', 'dikirim'])->count();
        $totalPesanan = $allTransactions->count();
        $selesai = $allTransactions->where('status_transaksi', 'selesai')->count();
        $menungguBayar = $allTransactions->where('status_transaksi', 'menunggu')->count();

        // Sewa aktif pertama (untuk section "Sedang Disewa")
        $activeRental = $allTransactions->whereIn('status_transaksi', ['diproses', 'dikirim'])->first();

        // Semua transaksi aktif (untuk tombol perpanjang)
        $activeRentals = $allTransactions->whereIn('status_transaksi', ['diproses', 'dikirim']);

        // 5 transaksi terakhir
        $recentTransactions = $allTransactions->take(5);

        return view('user.dashboard', compact(
            'sewaAktif', 'totalPesanan', 'selesai', 'menungguBayar',
            'activeRental', 'activeRentals', 'recentTransactions'
        ));
    }

    /**
     * Tampilkan halaman checkout (Step 1 - PEMESANAN).
     * Ambil data keranjang dari session dan tampilkan item beserta ringkasan.
     */
    public function checkout()
    {
        $userId = Auth::id();
        $carts = \App\Models\Cart::where('user_id', $userId)->with('product')->get();

        $subtotal = 0;
        foreach ($carts as $cart) {
            $subtotal += $cart->product->harga_sewa * $cart->quantity * $cart->days;
        }

        return view('user.checkout', compact('carts', 'subtotal'));
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
            'nama_penerima'      => 'required|string|max:255',
            'telepon_penerima'   => 'required|string|max:30',
            'alamat_pengiriman'  => 'nullable|required_if:metode_pengambilan,deliver|string',
            'jarak_tempuh'       => 'nullable|required_if:metode_pengambilan,deliver|numeric|min:0',
            'foto_ktp'           => 'nullable|required_if:metode_pengambilan,deliver|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $fotoKtpPath = null;
        if ($request->hasFile('foto_ktp')) {
            $fotoKtpPath = $request->file('foto_ktp')->store('jaminan', 'public');
        }

        // Simpan data checkout ke session (belum buat transaksi)
        $request->session()->put('checkout_data', [
            'tanggal_mulai'      => $request->tanggal_mulai,
            'tanggal_selesai'    => $request->tanggal_selesai,
            'metode_pengambilan' => $request->metode_pengambilan,
            'nama_penerima'      => $request->nama_penerima,
            'telepon_penerima'   => $request->telepon_penerima,
            'alamat_pengiriman'  => $request->alamat_pengiriman,
            'jarak_tempuh'       => $request->jarak_tempuh,
            'foto_ktp'           => $fotoKtpPath,
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

        $userId = Auth::id();
        $carts = \App\Models\Cart::where('user_id', $userId)->with('product')->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong.');
        }

        $subtotal = 0;
        foreach ($carts as $cart) {
            $subtotal += $cart->product->harga_sewa * $cart->quantity * $cart->days;
        }

        $biayaAdmin = 0;
        $checkoutData = $request->session()->get('checkout_data');
        
        $ongkosKirim = 0;
        if (isset($checkoutData['metode_pengambilan']) && $checkoutData['metode_pengambilan'] === 'deliver' && isset($checkoutData['jarak_tempuh'])) {
            $ongkosKirim = $checkoutData['jarak_tempuh'] * 5000;
        }

        $total = $subtotal + $biayaAdmin + $ongkosKirim;

        // Ambil daftar rekening aktif dari pengaturan admin
        $paymentSettings = \App\Models\PaymentSetting::where('is_active', true)->get();

        return view('user.pembayaran', compact('carts', 'subtotal', 'biayaAdmin', 'ongkosKirim', 'total', 'checkoutData', 'paymentSettings'));
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

        if ($request->metode_pembayaran === 'bayar_di_toko' && isset($checkoutData['metode_pengambilan']) && $checkoutData['metode_pengambilan'] === 'deliver') {
            return redirect()->back()->with('error', 'Pembayaran di toko tidak tersedia untuk metode pengiriman ke alamat.');
        }

        // Ambil keranjang dari database
        $userId = Auth::id();
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
            $totalBiaya += 0; // Biaya admin dihapus

            $ongkosKirim = 0;
            if ($checkoutData['metode_pengambilan'] === 'deliver' && isset($checkoutData['jarak_tempuh'])) {
                $ongkosKirim = $checkoutData['jarak_tempuh'] * 5000;
            }
            $totalBiaya += $ongkosKirim;

            // 1. Simpan transaksi utama
            $transactionData = [
                'user_id'            => $userId,
                'tanggal_mulai'      => $checkoutData['tanggal_mulai'],
                'tanggal_selesai'    => $checkoutData['tanggal_selesai'],
                'total_biaya'        => $totalBiaya,
                'status_transaksi'   => 'menunggu',
                'metode_pengambilan' => $checkoutData['metode_pengambilan'],
                'alamat_pengiriman'  => $checkoutData['alamat_pengiriman'],
                'jarak_tempuh'       => $checkoutData['jarak_tempuh'] ?? null,
                'ongkos_kirim'       => $ongkosKirim,
                'nama_penerima'      => $checkoutData['nama_penerima'],
                'telepon_penerima'   => $checkoutData['telepon_penerima'],
            ];

            if (isset($checkoutData['foto_ktp'])) {
                $transactionData['foto_ktp'] = $checkoutData['foto_ktp'];
                $transactionData['jenis_jaminan'] = 'ktp';
                $transactionData['status_jaminan'] = 'pending';
            }

            $transaction = Transaction::create($transactionData);

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

        if ($request->expectsJson()) {
            $snapToken = $this->getOrCreateSnapToken($transaction);
            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'redirect_url' => route('konfirmasi', $transaction->id)
            ]);
        }

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

        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

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

        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        // Fallback untuk Localhost: Sinkronisasi status dari Midtrans jika ada indikasi redirect
        if (request()->has('order_id') && request()->has('transaction_status')) {
            try {
                $this->initMidtrans();
                $statusRes = \Midtrans\Transaction::status(request()->order_id);
                
                if (in_array($statusRes->transaction_status, ['settlement', 'capture'])) {
                    if ($transaction->status_transaksi === 'menunggu') {
                        $transaction->update([
                            'status_transaksi' => 'diproses',
                            'status_jaminan'   => 'verified'
                        ]);
                        if ($transaction->payment) {
                            $transaction->payment->update(['status_pembayaran' => 'terverifikasi']);
                        }
                        // Refresh agar data terbaru dimuat
                        $transaction->refresh();
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Localhost Midtrans Sync Error: ' . $e->getMessage());
            }
        }

        $snapToken = $this->getOrCreateSnapToken($transaction);

        return view('user.konfirmasi', compact('transaction', 'snapToken'));
    }

    /**
     * Tampilkan halaman riwayat transaksi user.
     */
    public function riwayat()
    {
        $transactions = Transaction::with(['details.product', 'payment'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.riwayat', compact('transactions'));
    }

    /**
     * Tampilkan detail pesanan berdasarkan ID.
     */
    public function detail($id)
    {
        $transaction = Transaction::with(['details.product', 'payment', 'user'])
            ->findOrFail($id);

        // Pastikan transaksi hanya bisa dilihat oleh pemiliknya
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $snapToken = $this->getOrCreateSnapToken($transaction);

        return view('user.pesanan-detail', compact('transaction', 'snapToken'));
    }

    /**
     * Tampilkan nota digital (receipt) yang bisa di-print.
     */
    public function downloadNota($id)
    {
        $transaction = Transaction::with(['details.product', 'payment', 'user'])
            ->findOrFail($id);

        // Pastikan transaksi milik user yang login
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        return view('user.nota', compact('transaction'));
    }

    /**
     * User membatalkan pesanan.
     */
    public function batalkanPesanan(Request $request, $id)
    {
        $transaction = Transaction::with('details.product', 'user')->findOrFail($id);

        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        if (!in_array($transaction->status_transaksi, ['menunggu', 'menunggu_admin'])) {
            return redirect()->back()->with('error', 'Pesanan ini tidak dapat dibatalkan.');
        }

        // Validasi input form refund jika status menunggu_admin
        if ($transaction->status_transaksi === 'menunggu_admin') {
            $request->validate([
                'bank_pengembalian' => 'required|string|max:100',
                'rekening_pengembalian' => 'required|string|max:100',
                'atas_nama_pengembalian' => 'required|string|max:255',
            ], [
                'bank_pengembalian.required' => 'Nama Bank / E-Wallet wajib diisi.',
                'rekening_pengembalian.required' => 'Nomor Rekening wajib diisi.',
                'atas_nama_pengembalian.required' => 'Nama Pemilik Rekening wajib diisi.',
            ]);
        }

        // Kembalikan stok
        foreach ($transaction->details as $detail) {
            $detail->product->increment('stok_tersedia', $detail->jumlah);
        }

        $transaction->update([
            'status_transaksi'       => 'dibatalkan',
            'bank_pengembalian'      => $request->bank_pengembalian,
            'rekening_pengembalian'  => $request->rekening_pengembalian,
            'atas_nama_pengembalian' => $request->atas_nama_pengembalian,
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
        $transaction = \App\Models\Transaction::findOrFail($id);
        if ($transaction->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

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

        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        // Hanya bisa diperpanjang jika status masih aktif
        if (!in_array($transaction->status_transaksi, ['diproses', 'dikirim'])) {
            return redirect()->back()->with('error', 'Pesanan ini tidak bisa diperpanjang.');
        }

        // Jika sudah ada pengajuan pending, tampilkan pesan
        if ($transaction->status_perpanjangan === 'pending') {
            return redirect()->back()->with('info', 'Pengajuan perpanjangan Anda sedang menunggu persetujuan admin.');
        }

        return view('user.perpanjangan', compact('transaction'));
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
        if ($transaction->user_id !== Auth::id()) {
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

        // Notify user
        try {
            $transaction->user->notify(new \App\Notifications\OrderStatusUpdated($transaction));
        } catch (\Exception $e) {}

        // Redirect: admin ke notifikasi, user ke detail pesanan
        if (auth()->user()->peran === 'admin' || auth()->user()->peran === 'superadmin') {
            return redirect()->route('admin.notifikasi.index')
                             ->with('success', 'Perpanjangan ' . $hariTambahan . ' hari disetujui. Biaya tambahan: Rp ' . number_format($biayaTambahan, 0, ',', '.'));
        }

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

        // Redirect: admin ke notifikasi, user ke detail pesanan
        if (auth()->user()->peran === 'admin' || auth()->user()->peran === 'superadmin') {
            return redirect()->route('admin.notifikasi.index')
                             ->with('info', 'Pengajuan perpanjangan ditolak.');
        }

        return redirect()->route('pesanan.detail', $transaction->id)
                         ->with('info', 'Pengajuan perpanjangan ditolak.');
    }

    /**
     * User mengonfirmasi bahwa pesanan (pengantaran) telah diterima.
     */
    public function terimaPesanan($id)
    {
        $transaction = Transaction::findOrFail($id);

        // Pastikan transaksi milik user yang login
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        if ($transaction->status_transaksi === 'dikirim' && $transaction->metode_pengambilan === 'deliver') {
            $transaction->update([
                'barang_diterima' => true
            ]);
            return redirect()->route('pesanan.detail', $transaction->id)
                             ->with('success', 'Terima kasih, Anda telah mengonfirmasi penerimaan barang.');
        }

        return redirect()->back()->with('error', 'Status pesanan tidak valid untuk tindakan ini.');
    }

    /**
     * Inisialisasi konfigurasi Midtrans.
     */
    private function initMidtrans()
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Dapatkan snap token yang sudah ada atau buat baru jika belum ada.
     */
    private function getOrCreateSnapToken(Transaction $transaction)
    {
        $payment = $transaction->payment;
        if (!$payment || $payment->metode_pembayaran !== 'qris') {
            return null;
        }

        if ($payment->snap_token) {
            return $payment->snap_token;
        }

        // Buat snap token baru
        $this->initMidtrans();
        
        $params = [
            'transaction_details' => [
                'order_id' => 'GK-' . str_pad($transaction->id, 4, '0', STR_PAD_LEFT) . '-' . time(),
                'gross_amount' => (int) $payment->jumlah_bayar,
            ],
            'customer_details' => [
                'first_name' => $transaction->nama_penerima,
                'email' => Auth::user()->email,
                'phone' => $transaction->telepon_penerima,
            ],
            'callbacks' => [
                'finish' => route('konfirmasi', $transaction->id)
            ]
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $payment->update([
                'snap_token' => $snapToken
            ]);
            return $snapToken;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Midtrans Snap Error: ' . $e->getMessage());
            return null;
        }
    }
}
