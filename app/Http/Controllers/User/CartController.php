<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $carts = Cart::where('user_id', $userId)->with('product')->get();
        return view('user.keranjang', compact('carts'));
    }

    public function store(Request $request, Product $product)
    {
        $userId = Auth::id();
        
        $quantity = $request->input('quantity', 1);
        $days = $request->input('days', 1);

        // Validate stock availability
        $currentCartQty = Cart::where('user_id', $userId)->where('product_id', $product->id)->value('quantity') ?? 0;
        if (($currentCartQty + $quantity) > $product->stok_tersedia) {
            return redirect()->back()->with('error', "Stok tidak mencukupi. Hanya tersedia {$product->stok_tersedia} unit.");
        }

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
        $userId = Auth::id();
        
        $quantity = $request->input('quantity', 1);
        $days = $request->input('days', 1);

        // Validate stock availability
        $currentCartQty = Cart::where('user_id', $userId)->where('product_id', $product->id)->value('quantity') ?? 0;
        if (($currentCartQty + $quantity) > $product->stok_tersedia) {
            return redirect()->back()->with('error', "Stok tidak mencukupi. Hanya tersedia {$product->stok_tersedia} unit.");
        }

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
        $userId = Auth::id();

        if ($cart->user_id !== $userId) {
            abort(403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'days' => 'nullable|integer|min:1'
        ]);

        // Validate stock availability
        $product = $cart->product;
        if ($request->quantity > $product->stok_tersedia) {
            return redirect()->back()->with('error', "Stok tidak mencukupi. Hanya tersedia {$product->stok_tersedia} unit.");
        }

        $updateData = ['quantity' => $request->quantity];
        if ($request->has('days')) {
            $updateData['days'] = $request->days;
        }

        $cart->update($updateData);

        return redirect()->back()->with('success', 'Jumlah produk berhasil diupdate!');
    }

    public function destroy(Cart $cart)
    {
        $userId = Auth::id();

        if ($cart->user_id !== $userId) {
            abort(403);
        }

        $cart->delete();

        return redirect()->back()->with('success', 'Produk berhasil dihapus dari keranjang!');
    }
}
