<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/katalog', function () {
    return view('katalog');
});

Route::get('/produk/{id}', function ($id) {
    return view('produk-detail');
});

Route::get('/keranjang', function () {
    return view('keranjang');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

/*
|--------------------------------------------------------------------------
| Order & Transaction Routes (OrderController)
|--------------------------------------------------------------------------
| TODO: Tambahkan middleware('auth') saat sistem login sudah tersedia.
| Contoh: Route::middleware('auth')->group(function () { ... });
|
*/

// Checkout
Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');

// Pembayaran
Route::get('/pembayaran/{id}', [OrderController::class, 'pembayaran'])->name('pembayaran');
Route::post('/pembayaran/{id}/upload', [OrderController::class, 'uploadBukti'])->name('pembayaran.upload');

// Konfirmasi
Route::get('/konfirmasi/{id}', [OrderController::class, 'konfirmasi'])->name('konfirmasi');

// Riwayat & Detail
Route::get('/riwayat', [OrderController::class, 'riwayat'])->name('riwayat');
Route::get('/pesanan/{id}', [OrderController::class, 'detail'])->name('pesanan.detail');
