<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users   = User::where('peran', 'user')->pluck('id')->toArray();
        $products = Product::pluck('id')->toArray();

        if (empty($users) || empty($products)) return;

        $samples = [
            ['rating' => 5, 'ulasan' => 'Alat sangat bagus dan bersih, sangat memuaskan! Pasti sewa lagi.'],
            ['rating' => 4, 'ulasan' => 'Kondisi alat baik, proses sewa mudah. Recommended!'],
            ['rating' => 5, 'ulasan' => 'Tenda yang disewa sangat nyaman dan berkualitas. Terima kasih Garkadala!'],
            ['rating' => 3, 'ulasan' => 'Alat cukup baik, namun pengiriman agak lambat. Semoga bisa diperbaiki.'],
            ['rating' => 2, 'ulasan' => 'Kondisi alat kurang memuaskan, ada beberapa bagian yang rusak kecil.'],
            ['rating' => 1, 'ulasan' => 'Sangat mengecewakan!! Alat kotor dan bau, tidak layak sewa!!!'],
            ['rating' => 5, 'ulasan' => 'Carrier yang saya sewa sangat nyaman dipakai mendaki. Kualitas top!'],
            ['rating' => 4, 'ulasan' => 'Pelayanan ramah dan cepat. Alat dalam kondisi prima.'],
            ['rating' => 1, 'ulasan' => 'PENIPU!!! Alat tidak sesuai foto. JANGAN SEWA DI SINI!!!'],
            ['rating' => 3, 'ulasan' => 'Lumayan, tapi harga sedikit mahal dibanding tempat lain.'],
            ['rating' => 5, 'ulasan' => 'Alat kompor camping yang saya sewa sangat bagus dan menyala dengan sempurna.'],
            ['rating' => 2, 'ulasan' => 'Sleeping bag bau apek, perlu dibersihkan lebih baik sebelum disewakan.'],
        ];

        foreach ($samples as $i => $data) {
            Review::create([
                'user_id'    => $users[array_rand($users)],
                'product_id' => $products[$i % count($products)],
                'rating'     => $data['rating'],
                'ulasan'     => $data['ulasan'],
            ]);
        }
    }
}
