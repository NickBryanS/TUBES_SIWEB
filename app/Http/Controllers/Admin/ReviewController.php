<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Tampilkan semua ulasan untuk moderasi.
     */
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product']);

        // Search berdasarkan isi ulasan atau nama user/produk
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ulasan', 'like', "%$search%")
                  ->orWhereHas('user', fn($u) => $u->where('nama_lengkap', 'like', "%$search%"))
                  ->orWhereHas('product', fn($p) => $p->where('nama_produk', 'like', "%$search%"));
            });
        }

        // Filter berdasarkan rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Pagination 10 per halaman, terbaru dulu
        $reviews = $query->latest()->paginate(10)->withQueryString();

        // Statistik
        $totalUlasan    = Review::count();
        $ulasanPositif  = Review::where('rating', '>=', 4)->count();
        $ulasanNegatif  = Review::where('rating', '<=', 2)->count();
        $rataRating     = round(Review::avg('rating') ?? 0, 1);

        return view('admin.ulasan', compact(
            'reviews',
            'totalUlasan',
            'ulasanPositif',
            'ulasanNegatif',
            'rataRating'
        ));
    }

    /**
     * Hapus ulasan (moderasi spam/kasar).
     */
    public function destroy(Review $ulasan)
    {
        $namaUser = $ulasan->user->nama_lengkap ?? 'Anonim';
        $namaProduk = $ulasan->product->nama_produk ?? 'Produk';
        $id = $ulasan->id;
        $ulasan->delete();

        ActivityLog::catat('hapus_ulasan', 'Menghapus ulasan dari ' . $namaUser . ' pada produk ' . $namaProduk, 'Review', $id);

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil dihapus.',
        ]);
    }
}
