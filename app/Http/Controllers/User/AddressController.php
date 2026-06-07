<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * Tampilkan halaman daftar alamat.
     */
    public function index()
    {
        $addresses = Auth::user()->addresses()->orderByDesc('is_utama')->latest()->get();
        return view('user.alamat', compact('addresses'));
    }

    /**
     * Simpan alamat baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'label'           => 'required|string|max:50',
            'nama_penerima'   => 'required|string|max:100',
            'nomor_telepon'   => 'required|string|max:20',
            'alamat_lengkap'  => 'required|string|max:500',
            'kota'            => 'required|string|max:100',
            'provinsi'        => 'required|string|max:100',
            'kode_pos'        => 'required|string|max:10',
        ], [
            'label.required'          => 'Label alamat wajib diisi.',
            'nama_penerima.required'  => 'Nama penerima wajib diisi.',
            'nomor_telepon.required'  => 'Nomor telepon wajib diisi.',
            'alamat_lengkap.required' => 'Alamat lengkap wajib diisi.',
            'kota.required'           => 'Kota wajib diisi.',
            'provinsi.required'       => 'Provinsi wajib diisi.',
            'kode_pos.required'       => 'Kode pos wajib diisi.',
        ]);

        $user = Auth::user();

        // Jika ini alamat pertama, jadikan utama
        $isFirst = $user->addresses()->count() === 0;

        // Jika request set sebagai utama, reset semua yang lain
        if ($request->boolean('is_utama') || $isFirst) {
            $user->addresses()->update(['is_utama' => false]);
        }

        $user->addresses()->create([
            'label'          => $request->label,
            'nama_penerima'  => $request->nama_penerima,
            'nomor_telepon'  => $request->nomor_telepon,
            'alamat_lengkap' => $request->alamat_lengkap,
            'kota'           => $request->kota,
            'provinsi'       => $request->provinsi,
            'kode_pos'       => $request->kode_pos,
            'is_utama'       => $request->boolean('is_utama') || $isFirst,
        ]);

        return back()->with('success', 'Alamat baru berhasil ditambahkan!');
    }

    /**
     * Update alamat.
     */
    public function update(Request $request, Address $address)
    {
        // Pastikan alamat milik user yang login
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'label'           => 'required|string|max:50',
            'nama_penerima'   => 'required|string|max:100',
            'nomor_telepon'   => 'required|string|max:20',
            'alamat_lengkap'  => 'required|string|max:500',
            'kota'            => 'required|string|max:100',
            'provinsi'        => 'required|string|max:100',
            'kode_pos'        => 'required|string|max:10',
        ]);

        $user = Auth::user();

        // Jika request set sebagai utama, reset semua yang lain
        if ($request->boolean('is_utama')) {
            $user->addresses()->where('id', '!=', $address->id)->update(['is_utama' => false]);
        }

        $address->update([
            'label'          => $request->label,
            'nama_penerima'  => $request->nama_penerima,
            'nomor_telepon'  => $request->nomor_telepon,
            'alamat_lengkap' => $request->alamat_lengkap,
            'kota'           => $request->kota,
            'provinsi'       => $request->provinsi,
            'kode_pos'       => $request->kode_pos,
            'is_utama'       => $request->boolean('is_utama'),
        ]);

        return back()->with('success', 'Alamat berhasil diperbarui!');
    }

    /**
     * Set alamat sebagai utama.
     */
    public function setUtama(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $user = Auth::user();
        $user->addresses()->update(['is_utama' => false]);
        $address->update(['is_utama' => true]);

        return back()->with('success', 'Alamat utama berhasil diubah!');
    }

    /**
     * Hapus alamat.
     */
    public function destroy(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $wasUtama = $address->is_utama;
        $address->delete();

        // Jika yang dihapus adalah alamat utama, set alamat pertama sebagai utama
        if ($wasUtama) {
            $firstAddress = Auth::user()->addresses()->first();
            if ($firstAddress) {
                $firstAddress->update(['is_utama' => true]);
            }
        }

        return back()->with('success', 'Alamat berhasil dihapus.');
    }
}
