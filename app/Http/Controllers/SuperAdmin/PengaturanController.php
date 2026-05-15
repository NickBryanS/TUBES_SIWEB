<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        $timAdmin = User::whereIn('peran', ['admin', 'superadmin'])
            ->orderBy('created_at', 'desc')->get();

        return view('superadmin.pengaturan', compact('timAdmin'));
    }

    /**
     * Simpan pengaturan toko (placeholder — bisa dihubungkan ke settings table nanti)
     */
    public function update(Request $request)
    {
        // Placeholder: save settings to DB or config
        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
