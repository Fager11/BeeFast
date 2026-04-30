<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    
    public function index()
    {
        $user = Auth::user();
        $favorites = $user->favorites()->with('store')->latest('favorites.created_at')->get();
        return view('favorites.index', compact('favorites'));
    }

    
    public function toggle(Request $request, $productId)
    {
        $user = Auth::user();
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً لإضافة منتجات إلى السلة.');
        }
        $product = Product::findOrFail($productId);

        $wasFavorite = $user->favorites()->where('product_id', $productId)->exists();

        if ($wasFavorite) {
            $user->favorites()->detach($productId);
            
            session()->flash('success', 'تمت إزالة المنتج من المفضلة ❌');
            $status = false;
        } else {
            $user->favorites()->attach($productId);
            session()->flash('success', 'تمت إضافة المنتج إلى المفضلة ✅');
            $status = true;
        }

     
        if ($request->expectsJson()) {
            return response()->json(['status' => $status]);
        }

        return back();
    }

}