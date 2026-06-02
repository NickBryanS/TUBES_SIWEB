<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use App\Models\User;
use App\Models\Review;
use App\Models\Payment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SuperAdminDashboardController extends Controller
{
    /**
     * Executive Dashboard — Pemilik GKDL
     * Fokus: Monitoring real-time & ringkasan bisnis hari ini.
     */
    public function index(Request $request)
    {
        $now   = Carbon::now();
        $today = Carbon::today();

        // ── STAT CARD 1: PENDAPATAN HARI INI ─────────────────────
        $pendapatanHariIni = Transaction::whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai'])
            ->whereDate('created_at', $today)->sum('total_biaya');

        $pendapatanKemarin = Transaction::whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai'])
            ->whereDate('created_at', $today->copy()->subDay())->sum('total_biaya');

        $persenHarian = $pendapatanKemarin > 0
            ? round((($pendapatanHariIni - $pendapatanKemarin) / $pendapatanKemarin) * 100, 1) : 0;

        // ── STAT CARD 2: PENYEWAAN AKTIF ─────────────────────────
        $penyewaanAktif = Transaction::whereIn('status_transaksi', ['diproses', 'dikirim'])->count();
        $totalAlatDisewa = TransactionDetail::whereHas('transaction', function ($q) {
            $q->whereIn('status_transaksi', ['diproses', 'dikirim']);
        })->sum('jumlah');

        // ── STAT CARD 3: PERLU PERHATIAN ─────────────────────────
        $menungguVerifikasi = Transaction::whereIn('status_transaksi', ['menunggu', 'menunggu_admin'])->count();
        $stokTipis = Product::whereColumn('stok_tersedia', '<', DB::raw('total_stok * 0.2'))->count();
        $pembayaranPending = Payment::whereIn('status_pembayaran', ['menunggu', 'menunggu_verifikasi', 'pending'])->count();
        $totalPerhatian = $menungguVerifikasi + $stokTipis + $pembayaranPending;

        // ── AKTIVITAS TERKINI (Timeline) ─────────────────────────
        $aktivitasTerkini = collect();

        // Pesanan baru (semua status terbaru)
        $recentOrders = Transaction::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(8)->get();

        foreach ($recentOrders as $trx) {
            $iconMap = [
                'menunggu'       => ['icon' => 'fa-clock',        'color' => '#e65100', 'label' => 'Pesanan Baru'],
                'menunggu_admin' => ['icon' => 'fa-user-shield',  'color' => '#7b1fa2', 'label' => 'Menunggu Verifikasi'],
                'diproses'       => ['icon' => 'fa-box-open',     'color' => '#1565c0', 'label' => 'Pesanan Diproses'],
                'dikirim'        => ['icon' => 'fa-truck',        'color' => '#00838f', 'label' => 'Sedang Dikirim'],
                'selesai'        => ['icon' => 'fa-check-circle', 'color' => '#2e7d32', 'label' => 'Pesanan Selesai'],
                'dibatalkan'     => ['icon' => 'fa-times-circle', 'color' => '#c62828', 'label' => 'Pesanan Dibatalkan'],
            ];
            $info = $iconMap[$trx->status_transaksi] ?? ['icon' => 'fa-circle', 'color' => '#999', 'label' => 'Update'];
            $itemCount = $trx->details()->sum('jumlah');

            $aktivitasTerkini->push([
                'icon'    => $info['icon'],
                'color'   => $info['color'],
                'label'   => $info['label'],
                'message' => ($trx->user->nama_lengkap ?? 'Pelanggan') . ' — ' . $itemCount . ' alat',
                'order_id' => '#GKD-' . str_pad($trx->id, 5, '0', STR_PAD_LEFT),
                'total'   => $trx->total_biaya,
                'time'    => $trx->created_at,
            ]);
        }

        $aktivitasTerkini = $aktivitasTerkini->sortByDesc('time')->take(6)->values();

        // ── CHART: ALIRAN KAS 7 HARI ─────────────────────────────
        $chartData = [];
        $hariLabel = ['SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB', 'MIN'];
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $chartData[] = [
                'label' => $hariLabel[$date->dayOfWeekIso - 1],
                'value' => (float) Transaction::whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai'])
                    ->whereDate('created_at', $date->toDateString())->sum('total_biaya'),
            ];
        }

        // ── BARANG TERLARIS ──────────────────────────────────────
        $barangTerlaris = TransactionDetail::select(
                'product_id', DB::raw('SUM(jumlah) as total_sewa')
            )
            ->groupBy('product_id')
            ->orderByDesc('total_sewa')
            ->limit(5)
            ->with('product.category')
            ->get();

        // ── JADWAL PENGEMBALIAN HARI INI ─────────────────────────
        $jadwalPengembalian = Transaction::with(['user', 'details.product'])
            ->whereIn('status_transaksi', ['diproses', 'dikirim'])
            ->whereDate('tanggal_selesai', $today)
            ->orderBy('tanggal_selesai', 'asc')
            ->get();

        // Juga ambil yang terlambat (tanggal selesai sudah lewat)
        $terlambatKembali = Transaction::with(['user', 'details.product'])
            ->whereIn('status_transaksi', ['diproses', 'dikirim'])
            ->whereDate('tanggal_selesai', '<', $today)
            ->orderBy('tanggal_selesai', 'asc')
            ->get();

        // ── TOTAL STAF AKTIF ─────────────────────────────────────
        $totalStaf = User::where('peran', 'admin')->count();

        // ── PENDAPATAN BULAN INI (untuk konteks) ─────────────────
        $pendapatanBulanIni = Transaction::whereIn('status_transaksi', ['diproses', 'dikirim', 'selesai'])
            ->whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->sum('total_biaya');

        return view('superadmin.dashboard', compact(
            'pendapatanHariIni', 'persenHarian', 'pendapatanKemarin',
            'penyewaanAktif', 'totalAlatDisewa',
            'totalPerhatian', 'menungguVerifikasi', 'stokTipis', 'pembayaranPending',
            'aktivitasTerkini',
            'chartData',
            'barangTerlaris',
            'jadwalPengembalian', 'terlambatKembali',
            'totalStaf', 'pendapatanBulanIni'
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
