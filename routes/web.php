<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\AddressController;
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

// Lupa & Reset Password
Route::get('/lupa-password', [PasswordResetController::class, 'showForgotForm'])->name('password.forgot');
Route::post('/lupa-password', [PasswordResetController::class, 'sendResetToken'])->name('password.send-token');
Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.reset');

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| Authenticated Routes (dilindungi middleware auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('user.dashboard');
    })->name('dashboard');

    // ─── USER: Profil & Pengaturan ───
    Route::get('/user/profil', [ProfileController::class, 'index'])->name('user.profil');
    Route::put('/user/profil', [ProfileController::class, 'update'])->name('user.profil.update');
    Route::delete('/user/profil/foto', [ProfileController::class, 'removeFoto'])->name('user.profil.remove-foto');
    Route::put('/user/profil/password', [ProfileController::class, 'updatePassword'])->name('user.profil.password');

    // ─── USER: Manajemen Alamat ───
    Route::get('/user/alamat', [AddressController::class, 'index'])->name('user.alamat');
    Route::post('/user/alamat', [AddressController::class, 'store'])->name('user.alamat.store');
    Route::put('/user/alamat/{address}', [AddressController::class, 'update'])->name('user.alamat.update');
    Route::put('/user/alamat/{address}/utama', [AddressController::class, 'setUtama'])->name('user.alamat.set-utama');
    Route::delete('/user/alamat/{address}', [AddressController::class, 'destroy'])->name('user.alamat.destroy');

    // ─── Cart ───
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang/{product}', [CartController::class, 'store'])->name('cart.store');
    Route::post('/keranjang/{product}/checkout', [CartController::class, 'directCheckout'])->name('cart.directCheckout');
    Route::put('/keranjang/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/keranjang/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');

    // ─── Wishlist ───
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
});
