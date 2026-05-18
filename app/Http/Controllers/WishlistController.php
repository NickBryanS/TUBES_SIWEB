<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $userId = Auth::id() ?? 1;
        $wishlists = Wishlist::where('user_id', $userId)->with('product')->get();
        return view('user.wishlist', compact('wishlists'));
    }

    public function toggle(Request $request, Product $product)
    {
        $userId = Auth::id() ?? 1;
        $wishlist = Wishlist::where('user_id', $userId)->where('product_id', $product->id)->first();

        if ($wishlist) {
            $wishlist->delete();
            return redirect()->back()->with('success', 'Produk dihapus dari wishlist!');
        } else {
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $product->id
            ]);
            return redirect()->back()->with('success', 'Produk ditambahkan ke wishlist!');
        }
    }
}
