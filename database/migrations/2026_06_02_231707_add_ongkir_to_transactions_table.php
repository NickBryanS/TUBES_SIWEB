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
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('jarak_tempuh', 8, 2)->nullable()->after('alamat_pengiriman');
            $table->decimal('ongkos_kirim', 15, 2)->default(0)->after('jarak_tempuh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['jarak_tempuh', 'ongkos_kirim']);
        });
    }
};
