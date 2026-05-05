<?php

use App\Models\Category;
use App\Models\Product;

$category = Category::firstOrCreate(['nama_kategori' => 'Tenda', 'deskripsi' => 'Alat Kemah']);

$products = [
    ['nama_produk' => 'Apex Summit 4P', 'harga_sewa' => 125000, 'stok_tersedia' => 10, 'total_stok' => 10, 'url_gambar' => 'images/tent-expedition.png', 'deskripsi' => 'Tenda 4 musim dengan teknologi sirkulasi udara'],
    ['nama_produk' => 'Nomad Elite 65L', 'harga_sewa' => 85000, 'stok_tersedia' => 5, 'total_stok' => 5, 'url_gambar' => 'images/backpack-product.png', 'deskripsi' => 'Tas carrier ergonomis dengan sistem suspensi udara'],
    ['nama_produk' => 'JetFire Ultra', 'harga_sewa' => 45000, 'stok_tersedia' => 20, 'total_stok' => 20, 'url_gambar' => 'images/stove-product.png', 'deskripsi' => 'Kompor lipat ultra-ringan'],
    ['nama_produk' => 'CloudRest Zero', 'harga_sewa' => 65000, 'stok_tersedia' => 15, 'total_stok' => 15, 'url_gambar' => 'images/sleepingbag-product.png', 'deskripsi' => 'Sleeping bag bulu angsa'],
    ['nama_produk' => 'AeroCore Pro', 'harga_sewa' => 40000, 'stok_tersedia' => 12, 'total_stok' => 12, 'url_gambar' => 'images/carrier-product.png', 'deskripsi' => 'Matras angin otomatis'],
    ['nama_produk' => 'Lumina Beacon', 'harga_sewa' => 30000, 'stok_tersedia' => 30, 'total_stok' => 30, 'url_gambar' => 'images/headlamp-product.png', 'deskripsi' => 'Lampu kemah multi fungsi'],
];

foreach ($products as $p) {
    Product::firstOrCreate(
        ['nama_produk' => $p['nama_produk']],
        array_merge($p, ['category_id' => $category->id])
    );
}

echo "Seeded 6 products!\n";
