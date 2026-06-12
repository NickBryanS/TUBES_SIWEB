<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel settings untuk menyimpan konfigurasi toko secara dinamis.
     * Menggunakan pola key-value agar fleksibel tanpa perlu ubah schema.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed data awal pengaturan toko
        DB::table('settings')->insert([
            ['key' => 'nama_toko',        'value' => 'Gardakala Outdoor',                  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'singkatan_toko',   'value' => 'GKDL',                               'created_at' => now(), 'updated_at' => now()],
            ['key' => 'telepon_toko',     'value' => '+62 812 3456 7890',                  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'email_toko',       'value' => 'ops@gardakala.id',                   'created_at' => now(), 'updated_at' => now()],
            ['key' => 'alamat_toko',      'value' => 'Jl. Outdoor Raya No. 1, Bandung',    'created_at' => now(), 'updated_at' => now()],
            ['key' => 'denda_per_hari',   'value' => '50000',                              'created_at' => now(), 'updated_at' => now()],
            ['key' => 'min_sewa_hari',    'value' => '1',                                  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'max_dp_persen',    'value' => '50',                                 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
