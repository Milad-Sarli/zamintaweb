<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FavoriteController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get favorites
        $favorites = $user->favorites()
            ->with(['brand', 'unit'])
            ->latest('favorites.created_at')
            ->get();

        // Get recently viewed products
        $recentIds = session()->get('recently_viewed_products', []);
        $recentProducts = [];

        if (!empty($recentIds)) {
            $recentProducts = Product::whereIn('id', $recentIds)
                ->with(['brand', 'unit'])
                ->get()
                ->sortBy(function ($product) use ($recentIds) {
                    return array_search($product->id, $recentIds);
                })
                ->values();
        }

        return Inertia::render('Favorites', [
            'favorites' => $favorites,
            'recentProducts' => $recentProducts,
        ]);
    }

    public function store(Request $request, Product $product)
    {
        $product->favorites()->syncWithoutDetaching([Auth::id()]);

        return back()->with('success', 'به لیست علاقه‌مندی‌ها اضافه شد');
    }

    public function destroy(Product $product)
    {
        $product->favorites()->detach(Auth::id());

        return back()->with('success', 'از لیست علاقه‌مندی‌ها حذف شد');
    }
}
