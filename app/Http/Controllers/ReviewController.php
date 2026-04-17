<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = auth()->user()->reviews()
            ->with('product')
            ->latest()
            ->paginate(10);

        return Inertia::render('Dashboard/Reviews/Index', [
            'reviews' => $reviews,
        ]);
    }

    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $product->reviews()->create([
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => 'pending',
        ]);

        return back()->with('success', 'دیدگاه شما ثبت شد و پس از تایید نمایش داده می‌شود.');
    }
}
