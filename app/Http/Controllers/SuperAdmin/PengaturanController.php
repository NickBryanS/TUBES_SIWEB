<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PaymentSetting;
use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        $timAdmin = User::whereIn('peran', ['admin', 'superadmin'])
            ->orderBy('created_at', 'desc')->get();

        $paymentSettings = PaymentSetting::orderBy('created_at', 'desc')->get();

        // Ambil semua settings toko
        $settings = Setting::allAsArray();

        return view('superadmin.pengaturan', compact('timAdmin', 'paymentSettings', 'settings'));
    }

    /**
     * Simpan pengaturan toko ke database.
     */
    public function update(Request $request)
    {
        $request->validate([
            'nama_toko'      => 'required|string|max:255',
            'singkatan_toko' => 'nullable|string|max:20',
            'telepon_toko'   => 'nullable|string|max:20',
            'email_toko'     => 'nullable|email|max:255',
            'alamat_toko'    => 'nullable|string|max:500',
            'denda_per_hari' => 'nullable|numeric|min:0',
            'min_sewa_hari'  => 'nullable|integer|min:1',
            'max_dp_persen'  => 'nullable|integer|min:0|max:100',
        ]);

        $fields = [
            'nama_toko', 'singkatan_toko', 'telepon_toko',
            'email_toko', 'alamat_toko', 'denda_per_hari',
            'min_sewa_hari', 'max_dp_persen',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                Setting::set($field, $request->input($field));
            }
        }

        ActivityLog::catat('update_pengaturan', 'Memperbarui pengaturan toko: ' . ($request->nama_toko ?? '-'));

        return back()->with('success', 'Pengaturan toko berhasil disimpan.');
    }

    /**
     * Tambah metode pembayaran (rekening).
     */
    public function storePayment(Request $request)
    {
        $request->validate([
            'nama_bank'      => 'required|string|max:100',
            'nomor_rekening' => 'required|string|max:50',
            'atas_nama'      => 'required|string|max:255',
        ]);

        $setting = PaymentSetting::create([
            'nama_bank'      => strtoupper($request->nama_bank),
            'nomor_rekening' => $request->nomor_rekening,
            'atas_nama'      => strtoupper($request->atas_nama),
            'is_active'      => true,
        ]);

        ActivityLog::catat(
            'tambah_rekening',
            'Menambah rekening baru: ' . $setting->nama_bank . ' - ' . $setting->nomor_rekening,
            PaymentSetting::class,
            $setting->id
        );

        return back()->with('success', 'Rekening ' . $setting->nama_bank . ' berhasil ditambahkan.');
    }

    /**
     * Update metode pembayaran.
     */
    public function updatePayment(Request $request, $id)
    {
        $setting = PaymentSetting::findOrFail($id);

        $request->validate([
            'nama_bank'      => 'required|string|max:100',
            'nomor_rekening' => 'required|string|max:50',
            'atas_nama'      => 'required|string|max:255',
        ]);

        $setting->update([
            'nama_bank'      => strtoupper($request->nama_bank),
            'nomor_rekening' => $request->nomor_rekening,
            'atas_nama'      => strtoupper($request->atas_nama),
        ]);

        ActivityLog::catat(
            'update_rekening',
            'Mengubah data rekening: ' . $setting->nama_bank . ' - ' . $setting->nomor_rekening,
            PaymentSetting::class,
            $setting->id
        );

        return back()->with('success', 'Rekening berhasil diperbarui.');
    }

    /**
     * Hapus metode pembayaran.
     */
    public function destroyPayment($id)
    {
        $setting = PaymentSetting::findOrFail($id);
        $label   = $setting->nama_bank . ' - ' . $setting->nomor_rekening;

        ActivityLog::catat(
            'hapus_rekening',
            'Menghapus rekening: ' . $label,
            PaymentSetting::class,
            $setting->id
        );

        $setting->delete();

        return back()->with('success', 'Rekening ' . $label . ' berhasil dihapus.');
    }

    /**
     * Toggle status aktif/nonaktif rekening.
     */
    public function togglePayment($id)
    {
        $setting = PaymentSetting::findOrFail($id);
        $setting->update(['is_active' => !$setting->is_active]);

        $statusLabel = $setting->is_active ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLog::catat(
            'toggle_rekening',
            'Rekening ' . $setting->nama_bank . ' ' . $statusLabel,
            PaymentSetting::class,
            $setting->id
        );

        return back()->with('success', 'Rekening ' . $setting->nama_bank . ' berhasil ' . $statusLabel . '.');
    }
}
