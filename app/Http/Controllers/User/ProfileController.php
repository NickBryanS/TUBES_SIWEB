<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil & pengaturan akun.
     */
    public function index()
    {
        $user = Auth::user();
        return view('user.profil', compact('user'));
    }

    /**
     * Update data profil (nama, telepon, foto).
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama_lengkap'  => 'required|string|max:100',
            'nomor_telepon' => 'nullable|string|max:20',
            'foto_profil'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nama_lengkap.max'      => 'Nama maksimal 100 karakter.',
            'nomor_telepon.max'     => 'Nomor telepon maksimal 20 karakter.',
            'foto_profil.image'     => 'File harus berupa gambar.',
            'foto_profil.mimes'     => 'Format gambar: JPG, JPEG, PNG, atau WebP.',
            'foto_profil.max'       => 'Ukuran foto maksimal 2MB.',
        ]);

        $user->nama_lengkap  = $request->nama_lengkap;
        $user->nomor_telepon = $request->nomor_telepon;

        // Upload foto profil
        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama jika ada
            if ($user->foto_profil && file_exists(public_path('uploads/profil/' . $user->foto_profil))) {
                unlink(public_path('uploads/profil/' . $user->foto_profil));
            }

            $file = $request->file('foto_profil');
            $filename = 'profil_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/profil'), $filename);
            $user->foto_profil = $filename;
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Hapus foto profil.
     */
    public function removeFoto()
    {
        $user = Auth::user();

        if ($user->foto_profil && file_exists(public_path('uploads/profil/' . $user->foto_profil))) {
            unlink(public_path('uploads/profil/' . $user->foto_profil));
        }

        $user->foto_profil = null;
        $user->save();

        return back()->with('success', 'Foto profil berhasil dihapus.');
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.min'              => 'Password baru minimal 6 karakter.',
            'password.confirmed'        => 'Konfirmasi password tidak cocok.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password berhasil diubah!');
    }
}
