<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambah kolom-kolom baru untuk fitur:
     * - Denda keterlambatan (FR-USR-034)
     * - Perpanjangan sewa (FR-USR-033)
     * - Sistem jaminan & KTP (FR-USR-030, 031, 032)
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // --- Kolom Denda (FR-USR-034) ---
            $table->decimal('denda', 12, 2)->default(0)->after('total_biaya');
            $table->date('tanggal_kembali_aktual')->nullable()->after('tanggal_selesai');

            // --- Kolom Perpanjangan Sewa (FR-USR-033) ---
            $table->unsignedInteger('perpanjangan_hari')->default(0)->after('denda');
            $table->enum('status_perpanjangan', [
                'none',
                'pending',
                'approved',
                'rejected',
            ])->default('none')->after('perpanjangan_hari');

            // --- Kolom Jaminan & KTP (FR-USR-030, 031, 032) ---
            $table->string('foto_ktp')->nullable()->after('alamat_pengiriman');
            $table->enum('jenis_jaminan', [
                'ktp',
                'deposit_uang',
                'ktp_dan_deposit',
            ])->default('ktp')->after('foto_ktp');
            $table->enum('status_jaminan', [
                'pending',
                'verified',
                'rejected',
            ])->default('pending')->after('jenis_jaminan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'denda',
                'tanggal_kembali_aktual',
                'perpanjangan_hari',
                'status_perpanjangan',
                'foto_ktp',
                'jenis_jaminan',
                'status_jaminan',
            ]);
        });
    }
};
