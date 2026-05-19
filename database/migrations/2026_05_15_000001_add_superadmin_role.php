<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Menambah role 'superadmin' ke enum peran di tabel users.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN peran ENUM('user', 'admin', 'superadmin') DEFAULT 'user'");
    }

    /**
     * Kembalikan enum ke nilai semula.
     */
    public function down(): void
    {
        // Revert superadmin users back to admin first
        DB::table('users')->where('peran', 'superadmin')->update(['peran' => 'admin']);

        DB::statement("ALTER TABLE users MODIFY COLUMN peran ENUM('user', 'admin') DEFAULT 'user'");
    }
};
