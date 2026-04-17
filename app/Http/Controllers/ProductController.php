<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->with(['brand', 'categories', 'unit'])
            ->where('is_active', true);

        // Search
        $query->when($request->search, function ($q, $search) {
            $q->where(function ($subQ) use ($search) {
                $subQ->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        });

        // Category Filter
        $query->when($request->category, function ($q, $slug) {
            $q->whereHas('categories', function ($catQ) use ($slug) {
                $catQ->where('slug', $slug);
            });
        });

        // Brand Filter
        $query->when($request->brand, function ($q, $slug) {
            $q->whereHas('brand', function ($brandQ) use ($slug) {
                $brandQ->where('slug', $slug);
            });
        });

        // Price Filter
        $query->when($request->min_price, function ($q, $price) {
            $q->where('price', '>=', $price);
        });
        $query->when($request->max_price, function ($q, $price) {
            $q->where('price', '<=', $price);
        });

        // Sorting
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'filters' => $request->only(['search', 'category', 'brand', 'min_price', 'max_price', 'sort']),
            'categories' => Category::where('is_visible', true)->select('id', 'name', 'slug')->get(),
            'brands' => Brand::where('is_active', true)->select('id', 'name', 'slug')->get(),
        ]);
    }

    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        // Increment views
        $product->increment('views');

        // Track recently viewed products
        $recentlyViewed = session()->get('recently_viewed_products', []);
        if (!in_array($product->id, $recentlyViewed)) {
            array_unshift($recentlyViewed, $product->id);
            $recentlyViewed = array_slice($recentlyViewed, 0, 10); // Keep last 10
            session()->put('recently_viewed_products', $recentlyViewed);
        }

        $product->load(['brand', 'categories', 'unit', 'colors']);

        // Add URL for SEO
        $product->url = route('products.show', $product);

        // Related Products
        $relatedProducts = Product::query()
            ->with(['brand', 'unit'])
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->whereHas('categories', function ($q) use ($product) {
                $q->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->take(4)
            ->get();

        return Inertia::render('Products/Show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'isFavorited' => auth()->check() ? $product->isFavoritedBy(auth()->user()) : false,
            'reviews' => $product->reviews()->where('status', 'approved')->with('user')->latest()->get(),
        ]);
    }
}
