<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Seed the categories table.
     */
    public function run(): void
    {
        $categories = [
            ['nama_kategori' => 'Tenda'],
            ['nama_kategori' => 'Tas/Carrier'],
            ['nama_kategori' => 'Alat Masak'],
            ['nama_kategori' => 'Alat Tidur'],
            ['nama_kategori' => 'Aksesoris'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['nama_kategori' => $category['nama_kategori']],
                $category
            );
        }
    }
}
