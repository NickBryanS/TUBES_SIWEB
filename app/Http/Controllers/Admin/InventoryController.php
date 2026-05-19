<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Tampilkan daftar inventaris produk.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Filter berdasarkan kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter berdasarkan status stok
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'tersedia') {
                $query->where('stok_tersedia', '>', 3);
            } elseif ($status === 'habis') {
                $query->where('stok_tersedia', '<=', 0);
            } elseif ($status === 'stok_tipis') {
                $query->where('stok_tersedia', '>', 0)
                    ->where('stok_tersedia', '<=', 3);
            }
        }

        // Search produk
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%$search%")
                  ->orWhere('deskripsi', 'like', "%$search%");
            });
        }

        // Pagination - 5 per halaman sesuai referensi
        $products = $query->paginate(5)->withQueryString();
        $categories = Category::all();

        // Hitung statistik menggunakan sum agar akurat
        $totalStokSum = (int) Product::sum('total_stok');
        $tersediaSum = (int) Product::sum('stok_tersedia');
        $disewaSum = $totalStokSum - $tersediaSum;
        $diperbaiki = 0; // Bisa ditambahkan field status perbaikan nanti

        return view('admin.inventory', compact(
            'products',
            'categories',
            'totalStokSum',
            'tersediaSum',
            'disewaSum',
            'diperbaiki'
        ));
    }

    /**
     * Tampilkan form tambah produk (AJAX).
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.inventory.form', compact('categories'));
    }

    /**
     * Simpan produk baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'harga_sewa' => 'required|numeric|min:0',
            'total_stok' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
            'spesifikasi_teknis' => 'nullable|string',
            'url_gambar' => 'nullable|url',
        ]);

        // Set stok tersedia = total stok saat membuat produk baru
        $validated['stok_tersedia'] = $validated['total_stok'];

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan!',
            'product' => $product,
        ], 201);
    }

    /**
     * Tampilkan form edit produk (AJAX).
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.inventory.form', compact('product', 'categories'));
    }

    /**
     * Tampilkan detail produk dalam JSON.
     */
    public function show(Product $product)
    {
        $product->load('category');
        return response()->json($product);
    }

    /**
     * Update produk.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'harga_sewa' => 'required|numeric|min:0',
            'total_stok' => 'required|integer|min:1',
            'stok_tersedia' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'spesifikasi_teknis' => 'nullable|string',
            'url_gambar' => 'nullable|url',
        ]);

        // Validasi: stok_tersedia tidak boleh melebihi total_stok
        if ($validated['stok_tersedia'] > $validated['total_stok']) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tersedia tidak boleh melebihi total stok!',
            ], 422);
        }

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diperbarui!',
            'product' => $product,
        ]);
    }

    /**
     * Hapus produk.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus!',
        ]);
    }

    /**
     * Bulk delete produk.
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        if (!is_array($ids) || empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih produk terlebih dahulu!',
            ], 422);
        }

        Product::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => count($ids) . ' produk berhasil dihapus!',
        ]);
    }

    /**
     * Export inventaris ke CSV.
     */
    public function export()
    {
        $products = Product::with('category')->get();
        
        $filename = 'inventaris_' . date('Y-m-d_H-i-s') . '.csv';
        
        $handle = fopen('php://memory', 'w');
        
        // Header
        fputcsv($handle, [
            'ID', 'Nama Alat', 'Kategori', 'Harga/Hari', 'Total Stok', 
            'Tersedia', 'Disewa', 'Rusak', 'Deskripsi'
        ]);
        
        // Data
        foreach ($products as $product) {
            fputcsv($handle, [
                $product->id,
                $product->nama_produk,
                $product->category->nama_kategori ?? '-',
                $product->harga_sewa,
                $product->total_stok,
                $product->stok_tersedia,
                $product->total_stok - $product->stok_tersedia,
                0,
                $product->deskripsi,
            ]);
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);
        
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
}

