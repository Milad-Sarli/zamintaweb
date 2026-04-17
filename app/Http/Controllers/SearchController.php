<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return Inertia::render('Search', [
                'products' => [],
                'posts' => [],
                'brands' => [],
                'query' => '',
            ]);
        }

        $products = Product::query()
            ->with(['brand', 'categories'])
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            })
            ->limit(12)
            ->get();

        $posts = Post::query()
            ->published()
            ->with(['category'])
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('summary', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%");
            })
            ->limit(6)
            ->get();

        $brands = Brand::query()
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit(6)
            ->get();

        return Inertia::render('Search', [
            'products' => $products,
            'posts' => $posts,
            'brands' => $brands,
            'query' => $query,
        ]);
    }
}
