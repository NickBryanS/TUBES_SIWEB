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
                'deskripsi'         => 'Tenda 4 musim dengan teknologi sirkulasi udara premium. Tahan terhadap hembusan angin kencang dan hujan lebat, cocok untuk pendakian gunung tinggi.',
                'spesifikasi_teknis'=> json_encode(['kapasitas' => '4 Orang', 'berat' => '3.8kg', 'material' => 'Double Layer']),
                'harga_sewa'        => 125000,
                'stok_tersedia'     => 5,
                'total_stok'        => 5,
                'url_gambar'        => 'images/tent-expedition.png',
            ],
            [
                'category_id'       => $carrier,
                'nama_produk'       => 'Nomad Elite 65L',
                'deskripsi'         => 'Tas carrier ergonomis dengan sistem suspensi udara untuk kenyamanan punggung. Kapasitas besar 65L cocok untuk perjalanan jauh di alam bebas.',
                'spesifikasi_teknis'=> json_encode(['kapasitas' => '65 Liter', 'fitur' => 'Rain Cover Inc.']),
                'harga_sewa'        => 85000,
                'stok_tersedia'     => 8,
                'total_stok'        => 8,
                'url_gambar'        => 'images/backpack-product.png',
            ],
            [
                'category_id'       => $alatMasak,
                'nama_produk'       => 'JetFire Ultra',
                'deskripsi'         => 'Kompor lipat ultra-ringan dengan efisiensi bahan bakar tinggi. Dirancang untuk memasak cepat dalam kondisi cuaca ekstrem sekalipun.',
                'spesifikasi_teknis'=> json_encode(['material' => 'Titanium', 'fitur' => 'Piezo Igniter']),
                'harga_sewa'        => 45000,
                'stok_tersedia'     => 10,
                'total_stok'        => 10,
                'url_gambar'        => 'images/stove-product.png',
            ],
            [
                'category_id'       => $alatTidur,
                'nama_produk'       => 'CloudRest Zero',
                'deskripsi'         => 'Sleeping bag bulu angsa sintetis yang memberikan kehangatan hingga suhu nol derajat Celcius. Ringan, lembut, dan mudah dikemas.',
                'spesifikasi_teknis'=> json_encode(['comfort' => 'DRC Comfort', 'material' => 'Ripstop Nylon']),
                'harga_sewa'        => 65000,
                'stok_tersedia'     => 6,
                'total_stok'        => 6,
                'url_gambar'        => 'images/sleepingbag-product.png',
            ],
            [
                'category_id'       => $alatTidur,
                'nama_produk'       => 'AeroCore Pro',
                'deskripsi'         => 'Matras angin otomatis dengan insulasi thermal tinggi. Memberikan kenyamanan tidur seperti di rumah di atas permukaan tanah berbatu.',
                'spesifikasi_teknis'=> json_encode(['fitur' => 'Anti selip/slip', 'r_value' => 'R-Value 4.2']),
                'harga_sewa'        => 40000,
                'stok_tersedia'     => 7,
                'total_stok'        => 7,
                'url_gambar'        => 'images/sleeping-bag-product.png',
            ],
            [
                'category_id'       => $aksesoris,
                'nama_produk'       => 'Lumina Beacon',
                'deskripsi'         => 'Lampu kemah multi fungsi dengan baterai tahan lama dan fitur powerbank terintegrasi. Dilengkapi dengan gantungan magnetis.',
                'spesifikasi_teknis'=> json_encode(['lumen' => '1000 Lumens', 'charging' => 'USB-C']),
                'harga_sewa'        => 30000,
                'stok_tersedia'     => 12,
                'total_stok'        => 12,
                'url_gambar'        => 'images/headlamp-product.png',
            ],
            [
                'category_id'       => $carrier,
                'nama_produk'       => 'Ranger Green 45L',
                'deskripsi'         => 'Tas carrier tangguh berkapasitas 45L dengan warna olive green yang maskulin. Desain ergonomis dengan busa tebal dan kompartemen taktis untuk pendakian singkat.',
                'spesifikasi_teknis'=> json_encode(['kapasitas' => '45 Liter', 'berat' => '1.8kg', 'material' => 'Ripstop Nylon']),
                'harga_sewa'        => 60000,
                'stok_tersedia'     => 10,
                'total_stok'        => 10,
                'url_gambar'        => 'images/backpack-green.png',
            ],
            [
                'category_id'       => $alatTidur,
                'nama_produk'       => 'Kootek Double Hammock',
                'deskripsi'         => 'Hammock berkualitas tinggi dari bahan 210T nylon parachute yang kuat dan nyaman. Dilengkapi dengan carabiner hitam dan strap gantung yang mudah disesuaikan.',
                'spesifikasi_teknis'=> json_encode(['kapasitas' => '2 Orang', 'berat' => '0.8kg', 'dimensi' => '300cm x 200cm']),
                'harga_sewa'        => 25000,
                'stok_tersedia'     => 15,
                'total_stok'        => 15,
                'url_gambar'        => 'images/hammock-kootek.png',
            ],
            [
                'category_id'       => $tenda,
                'nama_produk'       => 'Grand Pavilion 8P',
                'deskripsi'         => 'Tenda dome keluarga super luas untuk kapasitas hingga 8 orang. Dilengkapi dengan kanopi depan, pintu ganda, sekat ruang tidur, dan flysheet antiair.',
                'spesifikasi_teknis'=> json_encode(['kapasitas' => '8 Orang', 'berat' => '12kg', 'dimensi' => '430cm x 300cm']),
                'harga_sewa'        => 180000,
                'stok_tersedia'     => 4,
                'total_stok'        => 4,
                'url_gambar'        => 'images/tent-family.png',
            ],
            [
                'category_id'       => $aksesoris,
                'nama_produk'       => 'CampCraft Toolkit',
                'deskripsi'         => 'Set perkakas esensial dalam tas penyimpanan kokoh. Sangat berguna untuk mendirikan tenda, memotong tali, mengencangkan sekrup, dan perbaikan darurat lainnya.',
                'spesifikasi_teknis'=> json_encode(['isi' => 'Palu, Cutter, Obeng, Meteran, Tespen', 'berat' => '1.2kg']),
                'harga_sewa'        => 35000,
                'stok_tersedia'     => 8,
                'total_stok'        => 8,
                'url_gambar'        => 'images/toolkit-camp.png',
            ],
            [
                'category_id'       => $aksesoris,
                'nama_produk'       => 'Trailblazer Trekking Poles',
                'deskripsi'         => 'Sepasang tongkat mendaki berbahan aluminium alloy ultra-ringan dengan sistem penguncian teleskopik yang kuat. Membantu stabilitas langkah di jalur terjal.',
                'spesifikasi_teknis'=> json_encode(['isi' => '1 Pasang', 'material' => 'Aluminium Alloy', 'berat' => '0.6kg']),
                'harga_sewa'        => 20000,
                'stok_tersedia'     => 12,
                'total_stok'        => 12,
                'url_gambar'        => 'images/trekking-poles.png',
            ],
            [
                'category_id'       => $aksesoris,
                'nama_produk'       => 'Apex Foldable Chair',
                'deskripsi'         => 'Kursi lipat camping ultra-ringan dengan rangka aluminium kokoh dan kain mesh bernapas. Sangat praktis dibawa bepergian.',
                'spesifikasi_teknis'=> json_encode(['kapasitas' => '1 Orang/120kg', 'berat' => '0.95kg', 'material' => 'Aluminium 7075 & Oxford Fabric']),
                'harga_sewa'        => 20000,
                'stok_tersedia'     => 15,
                'total_stok'        => 15,
                'url_gambar'        => 'images/camping-chair.png',
            ],
            [
                'category_id'       => $alatMasak,
                'nama_produk'       => 'Alpinist Mess Kit',
                'deskripsi'         => 'Set alat masak lengkap dengan panci anti lengket, wajan, mangkuk plastik, sendok sup, dan spons pembersih. Cocok untuk pendakian solo atau berdua.',
                'spesifikasi_teknis'=> json_encode(['isi' => 'Panci, Wajan, 2 Mangkuk, Sendok', 'berat' => '0.45kg', 'material' => 'Anodized Aluminum']),
                'harga_sewa'        => 30000,
                'stok_tersedia'     => 12,
                'total_stok'        => 12,
                'url_gambar'        => 'images/cooking-set.png',
            ],
            [
                'category_id'       => $tenda,
                'nama_produk'       => 'Summit Dome 2P',
                'deskripsi'         => 'Tenda dome kapasitas 2 orang yang ringan dan sangat mudah didirikan. Dilengkapi dengan lapisan pelindung UV dan ventilasi jaring anti nyamuk.',
                'spesifikasi_teknis'=> json_encode(['kapasitas' => '2 Orang', 'berat' => '2.1kg', 'material' => 'Polyester 190T PU2000mm']),
                'harga_sewa'        => 75000,
                'stok_tersedia'     => 8,
                'total_stok'        => 8,
                'url_gambar'        => 'images/tent-product.png',
            ],
            [
                'category_id'       => $alatTidur,
                'nama_produk'       => 'Polar Shield Sleeping Bag',
                'deskripsi'         => 'Sleeping bag dengan insulasi thermal superior untuk suhu ekstrem hingga -5 derajat Celcius. Dilengkapi hoodie pelindung kepala.',
                'spesifikasi_teknis'=> json_encode(['limit_temp' => '-5C', 'berat' => '1.4kg', 'material' => 'Ripstop Polyester & Cotton Fill']),
                'harga_sewa'        => 55000,
                'stok_tersedia'     => 10,
                'total_stok'        => 10,
                'url_gambar'        => 'images/sleeping-bag-product.png',
            ],
            [
                'category_id'       => $alatMasak,
                'nama_produk'       => 'WindGuard Windproof Stove',
                'deskripsi'         => 'Kompor camping dengan pelindung angin built-in yang efisien. Mempercepat waktu mendidih dan menghemat penggunaan gas di puncak gunung.',
                'spesifikasi_teknis'=> json_encode(['fitur' => 'Windproof Shield', 'berat' => '0.3kg', 'bahan_bakar' => 'Butane/Propane']),
                'harga_sewa'        => 40000,
                'stok_tersedia'     => 14,
                'total_stok'        => 14,
                'url_gambar'        => 'images/stove-product.png',
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
