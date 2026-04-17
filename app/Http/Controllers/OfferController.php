<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;

class OfferController extends Controller
{
    public function index()
    {
        $offers = Product::amazingOffers()
            ->with(['brand', 'categories'])
            ->latest()
            ->paginate(12)
            ->withQueryString()
            ->through(fn($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->price / 10,
                'formatted_price' => number_format($product->price / 10) . ' تومان',
                'sale_price' => $product->sale_price / 10,
                'formatted_sale_price' => number_format($product->sale_price / 10) . ' تومان',
                'discount_percentage' => $product->discount_percentage,
                'sale_end_at' => $product->sale_end_at,
                'images' => $product->images,
                'brand' => $product->brand ? $product->brand->name : null,
                'categories' => $product->categories->map(fn($cat) => [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                ]),
            ]);

        \Log::info('Offers count: ' . count($offers->items()));

        return Inertia::render('Offers/Index', [
            'offers' => $offers,
        ]);
    }
}
