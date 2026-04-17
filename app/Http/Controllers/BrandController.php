<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Inertia\Inertia;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($brand) {
                return [
                    'id' => $brand->id,
                    'name' => $brand->name,
                    'slug' => $brand->slug,
                    'logo' => $brand->logo ? asset('storage/' . $brand->logo) : null,
                    'description' => $brand->description,
                ];
            });

        return Inertia::render('Brands/Index', [
            'brands' => $brands,
        ]);
    }

    public function show(Brand $brand)
    {
        if (! $brand->is_active) {
            abort(404);
        }

        $products = Product::query()
            ->with(['brand'])
            ->where('is_active', true)
            ->where('brand_id', $brand->id)
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Brands/Show', [
            'brand' => [
                'id' => $brand->id,
                'name' => $brand->name,
                'slug' => $brand->slug,
                'logo' => $brand->logo ? asset('storage/' . $brand->logo) : null,
                'description' => $brand->description,
                'products_count' => $brand->products()->where('is_active', true)->count(),
            ],
            'products' => $products,
        ]);
    }
}
