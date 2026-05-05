<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/katalog', function () {
    $products = Product::with('category')->get();
    return view('katalog', compact('products'));
});



Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
Route::post('/keranjang/{product}', [CartController::class, 'store'])->name('cart.store');
Route::post('/keranjang/{product}/checkout', [CartController::class, 'directCheckout'])->name('cart.directCheckout');
Route::put('/keranjang/{cart}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/keranjang/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');

Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

// Product Detail page mapping
Route::get('/produk/{product}', function (Product $product) {
    return view('produk-detail', compact('product'));
})->name('produk.detail');

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
