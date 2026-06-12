<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY COLUMN peran ENUM('user', 'admin', 'superadmin') DEFAULT 'user'");
        }
    }

    /**
     * Kembalikan enum ke nilai semula.
     */
    public function down(): void
    {
        // Revert superadmin users back to admin first
        DB::table('users')->where('peran', 'superadmin')->update(['peran' => 'admin']);

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY COLUMN peran ENUM('user', 'admin') DEFAULT 'user'");
        }
    }
};
