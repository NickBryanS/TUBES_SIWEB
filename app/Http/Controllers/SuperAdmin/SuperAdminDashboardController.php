<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SuperAdminDashboardController extends Controller
{
    /**
     * Executive Dashboard — Pemilik GKDL
     */
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now();

        // Stat Cards
        $pendapatanHariIni = Transaction::whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai'])
            ->whereDate('created_at', $today)->sum('total_biaya');

        $prevDay = Transaction::whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai'])
            ->whereDate('created_at', $today->copy()->subDay())->sum('total_biaya');
        $persenPendapatan = $prevDay > 0 ? round((($pendapatanHariIni - $prevDay) / $prevDay) * 100) : 0;

        $totalTransaksiAktif = Transaction::whereIn('status_transaksi', ['menunggu', 'menunggu_admin', 'diproses', 'dikirim'])->count();

        $totalStaf = User::where('peran', 'admin')->count();
        $totalBarang = Product::sum('total_stok');
        $stokTipis = Product::whereColumn('stok_tersedia', '<', DB::raw('total_stok * 0.2'))->count();

        // Chart Data: Pendapatan 7 hari terakhir
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartData[] = [
                'label' => $date->translatedFormat('D'),
                'value' => (float) Transaction::whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai'])
                    ->whereDate('created_at', $date)->sum('total_biaya'),
            ];
        }

        // Top 5 Produk Terlaris
        $topProduk = DB::table('transaction_details')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->select('products.nama_produk', 'products.url_gambar', DB::raw('SUM(transaction_details.jumlah) as total_sewa'))
            ->groupBy('products.id', 'products.nama_produk', 'products.url_gambar')
            ->orderByDesc('total_sewa')
            ->limit(5)->get();

        // Status Armada (transaksi dengan metode deliver yang sedang aktif)
        $armadaAktif = Transaction::with('user')
            ->where('metode_pengambilan', 'deliver')
            ->where('status_transaksi', 'dikirim')
            ->limit(3)->get();

        // Peringatan Inventaris (stok hampir habis)
        $peringatanStok = Product::whereColumn('stok_tersedia', '<', DB::raw('total_stok * 0.3'))
            ->where('stok_tersedia', '>', 0)
            ->orderBy('stok_tersedia')->limit(3)->get();

        // Statistik tambahan untuk dashboard
        $totalPesananBulan = Transaction::whereMonth('created_at', $thisMonth->month)
            ->whereYear('created_at', $thisMonth->year)->count();
        $pendapatanBulan = Transaction::whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai'])
            ->whereMonth('created_at', $thisMonth->month)
            ->whereYear('created_at', $thisMonth->year)->sum('total_biaya');

        // Aktivitas terakhir (untuk ringkasan)
        $recentActivities = ActivityLog::with('user')->orderBy('created_at', 'desc')->limit(5)->get();

        return view('superadmin.dashboard', compact(
            'pendapatanHariIni', 'persenPendapatan', 'totalTransaksiAktif',
            'totalStaf', 'totalBarang', 'stokTipis',
            'chartData', 'topProduk', 'armadaAktif', 'peringatanStok',
            'totalPesananBulan', 'pendapatanBulan', 'recentActivities'
        ));
    }

    /**
     * Backup database (download SQL dump).
     */
    public function backupDatabase()
    {
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port', '3306');

        $filename = 'backup-' . $dbName . '-' . date('Y-m-d_His') . '.sql';
        $tempPath = storage_path('app/' . $filename);

        // Build mysqldump command
        $command = sprintf(
            'mysqldump --user=%s --host=%s --port=%s %s %s > %s',
            escapeshellarg($dbUser),
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            $dbPass ? '--password=' . escapeshellarg($dbPass) : '',
            escapeshellarg($dbName),
            escapeshellarg($tempPath)
        );

        $result = null;
        $output = [];
        exec($command . ' 2>&1', $output, $result);

        if ($result !== 0 || !file_exists($tempPath) || filesize($tempPath) === 0) {
            // Fallback: generate SQL from PHP
            return $this->backupDatabaseFallback($filename);
        }

        ActivityLog::catat('backup_database', 'Melakukan backup database: ' . $filename);

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/sql',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Fallback backup: Generate SQL-like dump from PHP.
     */
    private function backupDatabaseFallback(string $filename)
    {
        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $key = 'Tables_in_' . $dbName;

        $sql = "-- Backup Database: {$dbName}\n";
        $sql .= "-- Tanggal: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Generated by Gardakala Super Admin\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $tableName = $table->$key;

            // Skip sessions table
            if ($tableName === 'sessions') continue;

            // CREATE TABLE statement
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            if (!empty($createTable)) {
                $sql .= "-- Struktur tabel `{$tableName}`\n";
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $createKey = 'Create Table';
                $sql .= $createTable[0]->$createKey . ";\n\n";
            }

            // INSERT statements
            $rows = DB::table($tableName)->get();
            if ($rows->count() > 0) {
                $sql .= "-- Data untuk tabel `{$tableName}`\n";
                foreach ($rows as $row) {
                    $values = collect((array) $row)->map(function ($val) {
                        if (is_null($val)) return 'NULL';
                        return "'" . addslashes($val) . "'";
                    })->implode(', ');
                    $sql .= "INSERT INTO `{$tableName}` VALUES ({$values});\n";
                }
                $sql .= "\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        ActivityLog::catat('backup_database', 'Melakukan backup database (PHP): ' . $filename);

        return response($sql)
            ->header('Content-Type', 'application/sql')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
