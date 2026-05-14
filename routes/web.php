<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ShippingController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google OAuth (Socialite)
Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);

/*
|--------------------------------------------------------------------------
| Public Routes
| Admin yang sudah login akan otomatis redirect ke admin dashboard
|--------------------------------------------------------------------------
*/
Route::middleware('redirect_if_admin')->group(function () {
    Route::get('/', function () {
        return view('home');
    });

    Route::get('/katalog', function () {
        $products = Product::with('category')->get();
        return view('katalog', compact('products'));
    });

    // Product Detail page mapping
    Route::get('/produk/{product}', function (Product $product) {
        return view('produk-detail', compact('product'));
    })->name('produk.detail');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (dilindungi middleware auth)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'redirect_if_admin'])->group(function () {
    Route::get('/dashboard', [OrderController::class, 'dashboard'])->name('dashboard');

    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang/{product}', [CartController::class, 'store'])->name('cart.store');
    Route::post('/keranjang/{product}/checkout', [CartController::class, 'directCheckout'])->name('cart.directCheckout');
    Route::put('/keranjang/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/keranjang/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Step 1: Checkout (Pemesanan)
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');

    // Step 2: Pembayaran (Metode Pembayaran)
    Route::get('/pembayaran', [OrderController::class, 'pembayaran'])->name('pembayaran');
    Route::post('/pembayaran', [OrderController::class, 'storePembayaran'])->name('pembayaran.store');

    // Upload bukti pembayaran (dari halaman terpisah / upload ulang)
    Route::post('/pembayaran/{id}/upload', [OrderController::class, 'uploadBukti'])->name('pembayaran.upload');

    // Step 3: Konfirmasi
    Route::get('/konfirmasi/{id}', [OrderController::class, 'konfirmasi'])->name('konfirmasi');

    // Riwayat & Detail
    Route::get('/riwayat', [OrderController::class, 'riwayat'])->name('riwayat');
    Route::get('/pesanan/{id}', [OrderController::class, 'detail'])->name('pesanan.detail');
    Route::get('/pesanan/{id}/nota', [OrderController::class, 'downloadNota'])->name('pesanan.nota');
    Route::post('/pesanan/{id}/batal', [OrderController::class, 'batalkanPesanan'])->name('pesanan.batal');

    // Perpanjangan Sewa (FR-USR-033)
    Route::get('/pesanan/{id}/perpanjangan', [OrderController::class, 'formPerpanjangan'])->name('perpanjangan.form');
    Route::post('/pesanan/{id}/perpanjangan', [OrderController::class, 'ajukanPerpanjangan'])->name('perpanjangan.store');
    Route::post('/pesanan/{id}/perpanjangan/approve', [OrderController::class, 'approvePerpanjangan'])->name('perpanjangan.approve');
    Route::post('/pesanan/{id}/perpanjangan/reject', [OrderController::class, 'rejectPerpanjangan'])->name('perpanjangan.reject');

    // Konfirmasi Pengembalian & Denda (FR-USR-034)
    Route::post('/pesanan/{id}/pengembalian', [OrderController::class, 'konfirmasiPengembalian'])->name('pesanan.pengembalian');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (terpisah dari user)
|--------------------------------------------------------------------------
*/
Route::middleware('auth', 'is_admin')->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/transaksi/{id}/approve', [AdminDashboardController::class, 'approveTransaksi'])->name('admin.transaksi.approve');
    Route::post('/transaksi/{id}/reject', [AdminDashboardController::class, 'rejectTransaksi'])->name('admin.transaksi.reject');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    // Inventory Routes (export & bulk-delete harus sebelum resource agar tidak konflik)
    Route::get('/inventory/export', [InventoryController::class, 'export'])->name('admin.inventory.export');
    Route::post('/inventory/bulk-delete', [InventoryController::class, 'bulkDelete'])->name('admin.inventory.bulk-delete');
    Route::resource('inventory', InventoryController::class)->names('admin.inventory');

    // Transaksi Routes
    Route::get('/transaksi', [TransactionController::class, 'index'])->name('admin.transaksi.index');
    Route::get('/transaksi/{id}', [TransactionController::class, 'show'])->name('admin.transaksi.show');
    Route::post('/transaksi/{id}/approve', [TransactionController::class, 'approve'])->name('admin.transaksi.approve');
    Route::post('/transaksi/{id}/reject', [TransactionController::class, 'reject'])->name('admin.transaksi.reject');
    Route::post('/transaksi/{id}/status', [TransactionController::class, 'updateStatus'])->name('admin.transaksi.status');
    Route::post('/transaksi/{id}/lunas', [TransactionController::class, 'konfirmasiLunas'])->name('admin.transaksi.lunas');

    // Pengguna Routes
    Route::get('/pengguna', [UserController::class, 'index'])->name('admin.pengguna.index');
    Route::get('/pengguna/{id}', [UserController::class, 'show'])->name('admin.pengguna.show');
    Route::post('/pengguna/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('admin.pengguna.toggle-status');
    Route::post('/pengguna/{id}/verifikasi', [UserController::class, 'toggleVerifikasi'])->name('admin.pengguna.verifikasi');
    Route::delete('/pengguna/{id}', [UserController::class, 'destroy'])->name('admin.pengguna.destroy');

    // Notifikasi Routes
    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('admin.notifikasi.index');
    Route::post('/notifikasi/mark-all-read', [NotificationController::class, 'markAllRead'])->name('admin.notifikasi.markAllRead');

    // Pengiriman Routes
    Route::get('/pengiriman', [ShippingController::class, 'index'])->name('admin.pengiriman.index');
    Route::get('/pengiriman/{id}', [ShippingController::class, 'show'])->name('admin.pengiriman.show');
    Route::post('/pengiriman/{id}/status', [ShippingController::class, 'updateStatus'])->name('admin.pengiriman.status');
});
