<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $userId = Auth::id() ?? 1;
        $carts = Cart::where('user_id', $userId)->with('product')->get();
        return view('keranjang', compact('carts'));
    }

    public function store(Request $request, Product $product)
    {
        $userId = Auth::id() ?? 1;
        
        // Cek quantity, jika tidak ada dikirim, set default 1
        $quantity = $request->input('quantity', 1);

        $cart = Cart::where('user_id', $userId)->where('product_id', $product->id)->first();

        if ($cart) {
            $cart->update([
                'quantity' => $cart->quantity + $quantity
            ]);
        } else {
            Cart::create([
                'user_id' => $userId,
                'product_id' => $product->id,
                'quantity' => $quantity
            ]);
        }

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function update(Request $request, Cart $cart)
    {
        $userId = Auth::id() ?? 1;

        if ($cart->user_id !== $userId) {
            abort(403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart->update([
            'quantity' => $request->quantity
        ]);

        return redirect()->back()->with('success', 'Jumlah produk berhasil diupdate!');
    }

    public function destroy(Cart $cart)
    {
        $userId = Auth::id() ?? 1;

        if ($cart->user_id !== $userId) {
            abort(403);
        }

        $cart->delete();

        return redirect()->back()->with('success', 'Produk berhasil dihapus dari keranjang!');
    }
}
