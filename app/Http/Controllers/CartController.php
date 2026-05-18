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
        return view('user.keranjang', compact('carts'));
    }

    public function store(Request $request, Product $product)
    {
        $userId = Auth::id() ?? 1;
        
        $quantity = $request->input('quantity', 1);
        $days = $request->input('days', 1);

        $cart = Cart::where('user_id', $userId)->where('product_id', $product->id)->first();

        if ($cart) {
            $cart->update([
                'quantity' => $cart->quantity + $quantity,
                'days' => $days // Update to latest requested days
            ]);
        } else {
            Cart::create([
                'user_id' => $userId,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'days' => $days
            ]);
        }

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function directCheckout(Request $request, Product $product)
    {
        $userId = Auth::id() ?? 1;
        
        $quantity = $request->input('quantity', 1);
        $days = $request->input('days', 1);

        $cart = Cart::where('user_id', $userId)->where('product_id', $product->id)->first();

        if ($cart) {
            $cart->update([
                'quantity' => $cart->quantity + $quantity,
                'days' => $days
            ]);
        } else {
            Cart::create([
                'user_id' => $userId,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'days' => $days
            ]);
        }

        return redirect()->route('checkout');
    }

    public function update(Request $request, Cart $cart)
    {
        $userId = Auth::id() ?? 1;

        if ($cart->user_id !== $userId) {
            abort(403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'days' => 'nullable|integer|min:1'
        ]);

        $updateData = ['quantity' => $request->quantity];
        if ($request->has('days')) {
            $updateData['days'] = $request->days;
        }

        $cart->update($updateData);

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
