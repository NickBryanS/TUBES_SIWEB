<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Seed the products table.
     */
    public function run(): void
    {
        // Ambil category IDs
        $tenda     = Category::where('nama_kategori', 'Tenda')->first()?->id;
        $carrier   = Category::where('nama_kategori', 'Tas/Carrier')->first()?->id;
        $alatMasak = Category::where('nama_kategori', 'Alat Masak')->first()?->id;
        $alatTidur = Category::where('nama_kategori', 'Alat Tidur')->first()?->id;
        $aksesoris = Category::where('nama_kategori', 'Aksesoris')->first()?->id;

        $products = [
            [
                'category_id'       => $tenda,
                'nama_produk'       => 'Apex Summit 4P',
                'deskripsi'         => 'Tenda 4 musim dengan teknologi sirkulasi udara...',
                'spesifikasi_teknis'=> json_encode(['kapasitas' => '4 Orang', 'berat' => '3.8kg', 'material' => 'Double Layer']),
                'harga_sewa'        => 125000,
                'stok_tersedia'     => 5,
                'total_stok'        => 5,
                'url_gambar'        => 'images/tent-expedition.png',
            ],
            [
                'category_id'       => $carrier,
                'nama_produk'       => 'Nomad Elite 65L',
                'deskripsi'         => 'Tas carrier ergonomis dengan sistem suspensi udara untuk...',
                'spesifikasi_teknis'=> json_encode(['kapasitas' => '65 Liter', 'fitur' => 'Rain Cover Inc.']),
                'harga_sewa'        => 85000,
                'stok_tersedia'     => 8,
                'total_stok'        => 8,
                'url_gambar'        => 'images/backpack-product.png',
            ],
            [
                'category_id'       => $alatMasak,
                'nama_produk'       => 'JetFire Ultra',
                'deskripsi'         => 'Kompor lipat ultra-ringan dengan efisiensi bahan bakar...',
                'spesifikasi_teknis'=> json_encode(['material' => 'Titanium', 'fitur' => 'Piezo Igniter']),
                'harga_sewa'        => 45000,
                'stok_tersedia'     => 10,
                'total_stok'        => 10,
                'url_gambar'        => 'images/stove-product.png',
            ],
            [
                'category_id'       => $alatTidur,
                'nama_produk'       => 'CloudRest Zero',
                'deskripsi'         => 'Sleeping bag bulu angsa sintetis yang memberikan...',
                'spesifikasi_teknis'=> json_encode(['comfort' => 'DRC Comfort', 'material' => 'Ripstop Nylon']),
                'harga_sewa'        => 65000,
                'stok_tersedia'     => 6,
                'total_stok'        => 6,
                'url_gambar'        => 'images/sleepingbag-product.png',
            ],
            [
                'category_id'       => $carrier,
                'nama_produk'       => 'AeroCore Pro',
                'deskripsi'         => 'Matras angin otomatis dengan insulasi thermal tinggi...',
                'spesifikasi_teknis'=> json_encode(['fitur' => 'Anti selip/slip', 'r_value' => 'R-Value 4.2']),
                'harga_sewa'        => 40000,
                'stok_tersedia'     => 7,
                'total_stok'        => 7,
                'url_gambar'        => 'images/carrier-product.png',
            ],
            [
                'category_id'       => $aksesoris,
                'nama_produk'       => 'Lumina Beacon',
                'deskripsi'         => 'Lampu kemah multi fungsi dengan baterai tahan lama...',
                'spesifikasi_teknis'=> json_encode(['lumen' => '1000 Lumens', 'charging' => 'USB-C']),
                'harga_sewa'        => 30000,
                'stok_tersedia'     => 12,
                'total_stok'        => 12,
                'url_gambar'        => 'images/headlamp-product.png',
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['nama_produk' => $product['nama_produk']],
                $product
            );
        }
    }
}
