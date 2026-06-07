<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Tampilkan daftar kategori produk.
     */
    public function index(Request $request)
    {
        $query = Category::withCount('products');

        // Search berdasarkan nama_kategori
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_kategori', 'like', "%$search%");
        }

        // Pagination - 10 per halaman
        $categories = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Statistik
        $totalKategori = Category::count();
        $kategoriAktif = Category::whereHas('products')->count();
        $kategoriKosong = $totalKategori - $kategoriAktif;

        return view('admin.kategori', compact(
            'categories',
            'totalKategori',
            'kategoriAktif',
            'kategoriKosong'
        ));
    }

    /**
     * Simpan kategori baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:categories',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.max' => 'Nama kategori maksimal 255 karakter.',
            'nama_kategori.unique' => 'Nama kategori sudah ada.',
        ]);

        $category = Category::create($validated);

        ActivityLog::catat('tambah_kategori', 'Menambahkan kategori produk: ' . $category->nama_kategori, 'Category', $category->id);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan!',
            'category' => $category,
        ], 201);
    }

    /**
     * Tampilkan detail kategori dalam JSON.
     */
    public function show(Category $kategori)
    {
        $kategori->loadCount('products');
        return response()->json($kategori);
    }

    /**
     * Update kategori.
     */
    public function update(Request $request, Category $kategori)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:categories,nama_kategori,' . $kategori->id,
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.max' => 'Nama kategori maksimal 255 karakter.',
            'nama_kategori.unique' => 'Nama kategori sudah ada.',
        ]);

        $kategori->update($validated);

        ActivityLog::catat('update_kategori', 'Memperbarui kategori produk: ' . $kategori->nama_kategori, 'Category', $kategori->id);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui!',
            'category' => $kategori,
        ]);
    }

    /**
     * Hapus kategori.
     */
    public function destroy(Category $kategori)
    {
        // Cek apakah kategori masih punya produk
        if ($kategori->products()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak dapat dihapus karena masih memiliki produk terkait!',
            ], 422);
        }

        $nama = $kategori->nama_kategori;
        $id = $kategori->id;
        $kategori->delete();

        ActivityLog::catat('hapus_kategori', 'Menghapus kategori produk: ' . $nama, 'Category', $id);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus!',
        ]);
    }
}
