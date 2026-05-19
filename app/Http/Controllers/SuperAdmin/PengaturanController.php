<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PaymentSetting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        $timAdmin = User::whereIn('peran', ['admin', 'superadmin'])
            ->orderBy('created_at', 'desc')->get();

        $paymentSettings = PaymentSetting::orderBy('created_at', 'desc')->get();

        return view('superadmin.pengaturan', compact('timAdmin', 'paymentSettings'));
    }

    /**
     * Simpan pengaturan toko.
     */
    public function update(Request $request)
    {
        // Placeholder: save settings to DB or config
        ActivityLog::catat('update_pengaturan', 'Memperbarui pengaturan toko.');
        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }

    /**
     * Tambah metode pembayaran (rekening).
     */
    public function storePayment(Request $request)
    {
        $request->validate([
            'nama_bank' => 'required|string|max:100',
            'nomor_rekening' => 'required|string|max:50',
            'atas_nama' => 'required|string|max:255',
        ]);

        $setting = PaymentSetting::create([
            'nama_bank' => strtoupper($request->nama_bank),
            'nomor_rekening' => $request->nomor_rekening,
            'atas_nama' => strtoupper($request->atas_nama),
            'is_active' => true,
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
            'nama_bank' => 'required|string|max:100',
            'nomor_rekening' => 'required|string|max:50',
            'atas_nama' => 'required|string|max:255',
        ]);

        $setting->update([
            'nama_bank' => strtoupper($request->nama_bank),
            'nomor_rekening' => $request->nomor_rekening,
            'atas_nama' => strtoupper($request->atas_nama),
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
        $label = $setting->nama_bank . ' - ' . $setting->nomor_rekening;

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
