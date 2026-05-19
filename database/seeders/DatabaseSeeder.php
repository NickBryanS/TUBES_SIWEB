<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'nama_lengkap' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Buat Admin User
        User::create([
            'nama_lengkap' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'peran' => 'admin',
            'status_verifikasi' => true,
            'status_akun' => 'aktif',
        ]);

        // Buat Super Admin (Pemilik)
        User::create([
            'nama_lengkap' => 'Pemilik GKDL',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('super123'),
            'peran' => 'superadmin',
            'status_verifikasi' => true,
            'status_akun' => 'aktif',
        ]);

        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
