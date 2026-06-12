<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tabel payment_settings untuk menyimpan pengaturan metode pembayaran toko.
     */
    public function up(): void
    {
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bank');
            $table->string('nomor_rekening');
            $table->string('atas_nama');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed default payment setting
        DB::table('payment_settings')->insert([
            'nama_bank' => 'BCA',
            'nomor_rekening' => '8832xxxx99',
            'atas_nama' => 'SUMMIT PEAK ADVENTURE',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
