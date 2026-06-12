<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'ulasan' => 'nullable|string|max:1000',
        ]);

        $product = Product::findOrFail($productId);

        // Validasi: user hanya bisa review produk yang pernah disewa dan sudah selesai
        $pernakSewa = Transaction::where('user_id', Auth::id())
            ->where('status_transaksi', 'selesai')
            ->whereHas('details', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            })->exists();

        if (!$pernakSewa) {
            return redirect()->back()
                ->with('error', 'Anda hanya bisa mengulas produk yang sudah pernah disewa dan selesai.');
        }

        // Optional: Cek apakah user sudah pernah mereview produk ini
        $existingReview = Review::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if ($existingReview) {
            $existingReview->update([
                'rating' => $request->rating,
                'ulasan' => $request->ulasan,
            ]);
            return redirect()->back()->with('success', 'Ulasan berhasil diperbarui.');
        }

        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $productId,
            'rating' => $request->rating,
            'ulasan' => $request->ulasan,
        ]);

        return redirect()->back()->with('success', 'Ulasan berhasil ditambahkan.');
    }
}
