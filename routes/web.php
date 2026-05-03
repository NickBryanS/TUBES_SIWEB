<?php

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

Route::get('/checkout', function () {
    return view('checkout');
});

Route::get('/pembayaran', function () {
    return view('pembayaran');
});

Route::get('/konfirmasi', function () {
    return view('konfirmasi');
});

Route::get('/riwayat', function () {
    return view('riwayat');
});

Route::get('/pesanan/{id}', function ($id) {
    return view('pesanan-detail');
});
