<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->decimal('total_biaya', 12, 2)->default(0);
            $table->enum('status_transaksi', [
                'menunggu',
                'menunggu_admin',
                'diproses',
                'dikirim',
                'selesai',
                'dibatalkan',
            ])->default('menunggu');
            $table->enum('metode_pengambilan', ['pickup', 'deliver'])->default('pickup');
            $table->text('alamat_pengiriman')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
