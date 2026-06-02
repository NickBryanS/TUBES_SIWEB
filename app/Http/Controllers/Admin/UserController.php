<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display user management page.
     */
    public function index(Request $request)
    {
        $query = User::where('peran', 'user');

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nomor_telepon', 'like', "%{$search}%");
            });
        }

        // Tab filter
        $tab = $request->input('tab', 'semua');
        if ($tab === 'aktif') {
            $query->where('status_akun', 'aktif');
        } elseif ($tab === 'diblokir') {
            $query->whereIn('status_akun', ['nonaktif', 'banned']);
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status_akun', $status);
        }

        // Verification filter
        if ($request->has('verifikasi')) {
            $query->where('status_verifikasi', $request->input('verifikasi'));
        }

        // Stats
        $totalPengguna   = User::where('peran', 'user')->count();
        $terverifikasi   = User::where('peran', 'user')->where('status_verifikasi', true)->count();
        $diblokir        = User::where('peran', 'user')->whereIn('status_akun', ['nonaktif', 'banned'])->count();
        $aktifHariIni    = User::where('peran', 'user')
                                ->where('updated_at', '>=', Carbon::today())
                                ->count();

        $users = $query->withCount('transactions')
                       ->orderBy('created_at', 'desc')
                       ->paginate(10)
                       ->appends($request->query());

        return view('admin.pengguna', compact(
            'users',
            'totalPengguna',
            'terverifikasi',
            'diblokir',
            'aktifHariIni',
            'tab'
        ));
    }

    /**
     * Get user detail via JSON (for modal).
     */
    public function show($id)
    {
        $user = User::withCount('transactions')
                     ->findOrFail($id);

        return response()->json([
            'id'                 => $user->id,
            'nama_lengkap'       => $user->nama_lengkap,
            'email'              => $user->email,
            'nomor_telepon'      => $user->nomor_telepon,
            'peran'              => $user->peran,
            'status_verifikasi'  => $user->status_verifikasi,
            'status_akun'        => $user->status_akun,
            'dokumen_identitas'  => $user->dokumen_identitas
                ? asset('storage/' . $user->dokumen_identitas)
                : null,
            'avatar'             => $user->avatar,
            'transactions_count' => $user->transactions_count,
            'created_at'         => $user->created_at->format('d M Y'),
            'updated_at'         => $user->updated_at->format('d M Y, H:i'),
        ]);
    }

    /**
     * Toggle user account status (aktif / banned).
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        if ($user->status_akun === 'aktif') {
            $user->status_akun = 'banned';
            $message = "Pengguna {$user->nama_lengkap} telah diblokir.";
            ActivityLog::catat('blokir_pengguna', 'Memblokir pengguna: ' . $user->nama_lengkap, 'User', $user->id);
        } else {
            $user->status_akun = 'aktif';
            $message = "Pengguna {$user->nama_lengkap} telah diaktifkan kembali.";
            ActivityLog::catat('aktifkan_pengguna', 'Mengaktifkan kembali pengguna: ' . $user->nama_lengkap, 'User', $user->id);
        }

        $user->save();

        return back()->with('success', $message);
    }

    /**
     * Toggle user verification status.
     */
    public function toggleVerifikasi($id)
    {
        $user = User::findOrFail($id);

        $user->status_verifikasi = !$user->status_verifikasi;
        $user->save();

        $status = $user->status_verifikasi ? 'terverifikasi' : 'belum terverifikasi';
        
        ActivityLog::catat('verifikasi_pengguna', 'Mengubah status verifikasi ' . $user->nama_lengkap . ' menjadi ' . $status, 'User', $user->id);

        return back()->with('success', "Pengguna {$user->nama_lengkap} sekarang {$status}.");
    }

    /**
     * Export all users data as print-friendly PDF (via browser print).
     */
    public function export()
    {
        $users = User::where('peran', 'user')
            ->withCount('transactions')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPengguna  = $users->count();
        $terverifikasi  = $users->where('status_verifikasi', true)->count();
        $diblokir       = $users->whereIn('status_akun', ['banned', 'nonaktif'])->count();
        $aktif          = $users->where('status_akun', 'aktif')->count();

        ActivityLog::catat('export_pengguna', 'Mengekspor data pelanggan/pengguna ke PDF');

        return view('admin.exports.pengguna-pdf', compact(
            'users', 'totalPengguna', 'terverifikasi', 'diblokir', 'aktif'
        ));
    }

    /**
     * Delete a user account.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $name = $user->nama_lengkap;
        $uid = $user->id;
        $user->delete();

        ActivityLog::catat('hapus_pengguna', 'Menghapus akun pengguna: ' . $name, 'User', $uid);

        return back()->with('success', "Pengguna {$name} telah dihapus.");
    }
}
