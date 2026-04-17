<?php

namespace App\Http\Controllers;

use App\Services\CartActivityLogger;
use App\Services\CartService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    protected $cartService;

    protected $activityLogger;

    public function __construct(CartService $cartService, CartActivityLogger $activityLogger)
    {
        $this->cartService = $cartService;
        $this->activityLogger = $activityLogger;
    }

    public function index()
    {
        $this->activityLogger->log('view_cart');

        return Inertia::render('Cart/Index', [
            'cart' => $this->cartService->getCartDetails(),
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $product = \App\Models\Product::find($request->product_id);

        $this->cartService->addToCart(
            $request->product_id,
            $request->quantity ?? 1
        );

        $this->activityLogger->log('item_added', [
            'product_id' => $request->product_id,
            'product_name' => $product?->name,
            'price' => $product?->current_price ?? $product?->price,
            'quantity' => $request->quantity ?? 1,
        ]);

        return back()->with('success', 'محصول به سبد خرید اضافه شد.');
    }

    public function update(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $cartItem = \App\Models\CartItem::with('product')->find($itemId);

        $this->cartService->updateQuantity($itemId, $request->quantity);

        $this->activityLogger->log('item_updated', [
            'item_id' => $itemId,
            'product_name' => $cartItem?->product?->name,
            'old_quantity' => $cartItem?->quantity,
            'new_quantity' => $request->quantity,
        ]);

        return back()->with('success', 'سبد خرید بروزرسانی شد.');
    }

    public function remove($itemId)
    {
        $cartItem = \App\Models\CartItem::with('product')->find($itemId);

        $this->cartService->removeItem($itemId);

        $this->activityLogger->log('item_removed', [
            'item_id' => $itemId,
            'product_name' => $cartItem?->product?->name,
            'price' => $cartItem?->product?->current_price ?? $cartItem?->product?->price,
            'quantity' => $cartItem?->quantity,
        ]);

        return back()->with('success', 'محصول از سبد خرید حذف شد.');
    }

    public function clear()
    {
        $this->cartService->clear();

        $this->activityLogger->log('cart_cleared');

        return back()->with('success', 'سبد خرید خالی شد.');
    }
}
