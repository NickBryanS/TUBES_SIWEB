<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Tampilkan halaman Pusat Notifikasi.
     * Notifikasi digenerate secara dinamis dari data transaksi, pembayaran, dan pengguna.
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'semua');

        $notifications = collect();

        // ── 1. Pesanan Baru (status menunggu / menunggu_admin) ──
        if (in_array($filter, ['semua', 'pesanan'])) {
            $pesananBaru = Transaction::with('user')
                ->whereIn('status_transaksi', ['menunggu', 'menunggu_admin'])
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($pesananBaru as $trx) {
                $notifications->push([
                    'id'         => 'trx-' . $trx->id,
                    'type'       => 'pesanan',
                    'user_name'  => $trx->user->nama_lengkap ?? 'Pelanggan',
                    'user_avatar' => $trx->user->avatar ?? null,
                    'user_initial' => strtoupper(substr($trx->user->nama_lengkap ?? 'P', 0, 1)),
                    'order_id'   => '#GK-' . now()->format('Y') . '-' . str_pad($trx->id, 4, '0', STR_PAD_LEFT),
                    'message'    => 'Pesanan Baru (' . $trx->details()->count() . ' Alat) — Menunggu verifikasi dokumen identitas pelanggan.',
                    'actions'    => [
                        ['label' => 'Setujui', 'type' => 'primary', 'route' => route('admin.transaksi.approve', $trx->id)],
                        ['label' => 'Lihat Detail', 'type' => 'secondary', 'route' => route('admin.transaksi.index', ['highlight' => $trx->id])],
                    ],
                    'time'       => $trx->created_at,
                    'read'       => false,
                ]);
            }
        }

        // ── 2. Upload KTP / Dokumen Identitas ──
        if (in_array($filter, ['semua', 'pesanan'])) {
            $ktpUploads = Transaction::with('user')
                ->whereNotNull('foto_ktp')
                ->whereIn('status_transaksi', ['menunggu', 'menunggu_admin'])
                ->orderBy('updated_at', 'desc')
                ->get();

            foreach ($ktpUploads as $trx) {
                $notifications->push([
                    'id'         => 'ktp-' . $trx->id,
                    'type'       => 'pesanan',
                    'user_name'  => $trx->user->nama_lengkap ?? 'Pelanggan',
                    'user_avatar' => $trx->user->avatar ?? null,
                    'user_initial' => strtoupper(substr($trx->user->nama_lengkap ?? 'P', 0, 1)),
                    'order_id'   => '#GK-' . now()->format('Y') . '-' . str_pad($trx->id, 4, '0', STR_PAD_LEFT),
                    'message'    => 'Foto Identitas (KTP) telah diunggah untuk Pesanan #GK-' . now()->format('Y') . '-' . str_pad($trx->id, 4, '0', STR_PAD_LEFT) . '.',
                    'actions'    => [
                        ['label' => 'Setujui', 'type' => 'primary', 'route' => route('admin.transaksi.approve', $trx->id)],
                        ['label' => 'Lihat Detail', 'type' => 'secondary', 'route' => route('admin.transaksi.index', ['highlight' => $trx->id])],
                    ],
                    'time'       => $trx->updated_at,
                    'read'       => false,
                ]);
            }
        }

        // ── 3. Pembayaran ──
        if (in_array($filter, ['semua', 'pembayaran'])) {
            $payments = Payment::with('transaction.user')
                ->whereIn('status_pembayaran', ['menunggu_verifikasi', 'pending'])
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($payments as $pay) {
                $trx = $pay->transaction;
                if (!$trx) continue;

                $metode = str_replace('_', ' ', ucfirst($pay->metode_pembayaran));
                $notifications->push([
                    'id'         => 'pay-' . $pay->id,
                    'type'       => 'pembayaran',
                    'user_name'  => $trx->user->nama_lengkap ?? 'Pelanggan',
                    'user_avatar' => $trx->user->avatar ?? null,
                    'user_initial' => strtoupper(substr($trx->user->nama_lengkap ?? 'P', 0, 1)),
                    'order_id'   => '#GK-' . now()->format('Y') . '-' . str_pad($trx->id, 4, '0', STR_PAD_LEFT),
                    'message'    => 'Pembayaran ' . $metode . ' untuk Pesanan #GK-' . now()->format('Y') . '-' . str_pad($trx->id, 4, '0', STR_PAD_LEFT) . '. Status diperbarui ke Dijadwalkan.',
                    'actions'    => [
                        ['label' => 'Lihat Kwitansi', 'type' => 'secondary', 'route' => route('admin.transaksi.index', ['highlight' => $trx->id])],
                    ],
                    'time'       => $pay->created_at,
                    'read'       => false,
                ]);
            }
        }

        // ── 4. Pengembalian (transaksi yang sedang berjalan mendekati/melewati tanggal selesai) ──
        if (in_array($filter, ['semua', 'pengembalian'])) {
            $perpanjangan = Transaction::with('user')
                ->where('status_perpanjangan', 'diminta')
                ->orderBy('updated_at', 'desc')
                ->get();

            foreach ($perpanjangan as $trx) {
                $durasi = $trx->perpanjangan_hari ?? 0;
                $notifications->push([
                    'id'         => 'ext-' . $trx->id,
                    'type'       => 'pengembalian',
                    'user_name'  => $trx->user->nama_lengkap ?? 'Pelanggan',
                    'user_avatar' => $trx->user->avatar ?? null,
                    'user_initial' => strtoupper(substr($trx->user->nama_lengkap ?? 'P', 0, 1)),
                    'order_id'   => '#GK-' . now()->format('Y') . '-' . str_pad($trx->id, 4, '0', STR_PAD_LEFT),
                    'message'    => 'Permintaan Perpanjangan Sewa (' . $durasi . ' Hari) untuk Pesanan #GK-' . now()->format('Y') . '-' . str_pad($trx->id, 4, '0', STR_PAD_LEFT) . '.',
                    'actions'    => [
                        ['label' => 'Konfirmasi', 'type' => 'primary', 'route' => route('perpanjangan.approve', $trx->id)],
                        ['label' => 'Lihat Kalender', 'type' => 'secondary', 'route' => route('admin.transaksi.index', ['highlight' => $trx->id])],
                    ],
                    'time'       => $trx->updated_at,
                    'read'       => false,
                ]);
            }

            // Barang yang sudah dikembalikan (selesai)
            $selesai = Transaction::with('user')
                ->where('status_transaksi', 'selesai')
                ->whereNotNull('tanggal_kembali_aktual')
                ->orderBy('tanggal_kembali_aktual', 'desc')
                ->take(5)
                ->get();

            foreach ($selesai as $trx) {
                $notifications->push([
                    'id'         => 'ret-' . $trx->id,
                    'type'       => 'pengembalian',
                    'user_name'  => $trx->user->nama_lengkap ?? 'Pelanggan',
                    'user_avatar' => $trx->user->avatar ?? null,
                    'user_initial' => strtoupper(substr($trx->user->nama_lengkap ?? 'P', 0, 1)),
                    'order_id'   => '#GK-' . now()->format('Y') . '-' . str_pad($trx->id, 4, '0', STR_PAD_LEFT),
                    'message'    => 'Barang telah dikembalikan untuk Pesanan #GK-' . now()->format('Y') . '-' . str_pad($trx->id, 4, '0', STR_PAD_LEFT) . '.',
                    'actions'    => [
                        ['label' => 'Cek Kondisi Barang', 'type' => 'secondary', 'route' => route('admin.transaksi.index', ['highlight' => $trx->id])],
                    ],
                    'time'       => $trx->tanggal_kembali_aktual,
                    'read'       => true,
                ]);
            }
        }

        // ── 5. Sistem (pengguna baru, dll.) ──
        if (in_array($filter, ['semua', 'sistem'])) {
            $newUsers = User::where('peran', 'pelanggan')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            foreach ($newUsers as $user) {
                $notifications->push([
                    'id'         => 'usr-' . $user->id,
                    'type'       => 'sistem',
                    'user_name'  => $user->nama_lengkap ?? 'Pengguna Baru',
                    'user_avatar' => $user->avatar ?? null,
                    'user_initial' => strtoupper(substr($user->nama_lengkap ?? 'P', 0, 1)),
                    'order_id'   => null,
                    'message'    => 'Pengguna baru terdaftar: ' . ($user->nama_lengkap ?? 'Unknown') . '.',
                    'actions'    => [
                        ['label' => 'Lihat Profil', 'type' => 'secondary', 'route' => route('admin.pengguna.show', $user->id)],
                    ],
                    'time'       => $user->created_at,
                    'read'       => true,
                ]);
            }
        }

        // Sort by time descending
        $notifications = $notifications->sortByDesc('time')->values();

        // Counts for tabs
        $counts = [
            'semua'        => $notifications->count(),
            'pesanan'      => $notifications->where('type', 'pesanan')->count(),
            'pembayaran'   => $notifications->where('type', 'pembayaran')->count(),
            'pengembalian' => $notifications->where('type', 'pengembalian')->count(),
            'sistem'       => $notifications->where('type', 'sistem')->count(),
        ];

        $unreadCount = $notifications->where('read', false)->count();

        return view('admin.notifikasi', compact('notifications', 'filter', 'counts', 'unreadCount'));
    }

    /**
     * Tandai semua notifikasi dibaca (placeholder — tanpa tabel notifikasi
     * ini hanya redirect dengan pesan sukses).
     */
    public function markAllRead()
    {
        return redirect()->route('admin.notifikasi.index')
            ->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}
