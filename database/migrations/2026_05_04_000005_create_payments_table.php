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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->enum('metode_pembayaran', [
                'transfer_bank',
                'qris',
                'bayar_di_toko',
            ]);
            $table->enum('status_pembayaran', [
                'menunggu',
                'menunggu_verifikasi',
                'terverifikasi',
                'ditolak',
            ])->default('menunggu');
            $table->decimal('jumlah_bayar', 12, 2)->default(0);
            $table->string('bukti_pembayaran')->nullable(); // path ke file bukti transfer
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
