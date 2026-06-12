<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ManajemenAdminController extends Controller
{
    /**
     * Halaman CRUD manajemen admin (staff).
     */
    public function index(Request $request)
    {
        $query = User::whereIn('peran', ['admin', 'superadmin']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $admins = $query->orderBy('created_at', 'desc')->get();

        return view('superadmin.manajemen-admin', compact('admins'));
    }

    /**
     * Tambah admin baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nomor_telepon' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $admin = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'nomor_telepon' => $request->nomor_telepon,
            'password' => Hash::make($request->password),
            'peran' => 'admin',
            'status_verifikasi' => true,
            'status_akun' => 'aktif',
        ]);

        ActivityLog::catat(
            'tambah_admin',
            'Menambahkan admin baru: ' . $admin->nama_lengkap . ' (' . $admin->email . ')',
            User::class,
            $admin->id
        );

        return redirect()->route('superadmin.admin.index')
            ->with('success', 'Admin "' . $admin->nama_lengkap . '" berhasil ditambahkan.');
    }

    /**
     * Update data admin.
     */
    public function update(Request $request, $id)
    {
        $admin = User::findOrFail($id);

        if ($admin->peran === 'superadmin') {
            return back()->with('error', 'Tidak bisa mengubah data Super Admin.');
        }

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($admin->id)],
            'nomor_telepon' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
        ]);

        $admin->update([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'nomor_telepon' => $request->nomor_telepon,
        ]);

        if ($request->filled('password')) {
            $admin->update(['password' => Hash::make($request->password)]);
        }

        ActivityLog::catat(
            'update_admin',
            'Mengubah data admin: ' . $admin->nama_lengkap,
            User::class,
            $admin->id
        );

        return redirect()->route('superadmin.admin.index')
            ->with('success', 'Data admin "' . $admin->nama_lengkap . '" berhasil diperbarui.');
    }

    /**
     * Hapus akun admin.
     */
    public function destroy($id)
    {
        $admin = User::findOrFail($id);

        if ($admin->peran === 'superadmin') {
            return back()->with('error', 'Tidak bisa menghapus akun Super Admin.');
        }

        $namaAdmin = $admin->nama_lengkap;

        ActivityLog::catat(
            'hapus_admin',
            'Menghapus akun admin: ' . $namaAdmin . ' (' . $admin->email . ')',
            User::class,
            $admin->id
        );

        $admin->delete();

        return redirect()->route('superadmin.admin.index')
            ->with('success', 'Admin "' . $namaAdmin . '" berhasil dihapus.');
    }

    /**
     * Toggle status aktif/nonaktif admin.
     */
    public function toggleStatus($id)
    {
        $admin = User::findOrFail($id);

        if ($admin->peran === 'superadmin') {
            return back()->with('error', 'Tidak bisa mengubah status Super Admin.');
        }

        $newStatus = $admin->status_akun === 'aktif' ? 'nonaktif' : 'aktif';
        $admin->update(['status_akun' => $newStatus]);

        ActivityLog::catat(
            'toggle_status_admin',
            'Mengubah status admin ' . $admin->nama_lengkap . ' menjadi ' . $newStatus,
            User::class,
            $admin->id
        );

        return redirect()->route('superadmin.admin.index')
            ->with('success', 'Status admin "' . $admin->nama_lengkap . '" diubah menjadi ' . ucfirst($newStatus) . '.');
    }
}
