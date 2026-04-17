<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CartService
{
    public function getCart()
    {
        if (Auth::check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

            // Merge session cart if exists
            $sessionId = Session::get('cart_session_id');
            if ($sessionId) {
                $sessionCart = Cart::where('session_id', $sessionId)->first();
                if ($sessionCart) {
                    $this->mergeCarts($sessionCart, $cart);
                    $sessionCart->delete();
                    Session::forget('cart_session_id');
                }
            }
        } else {
            $sessionId = Session::get('cart_session_id');
            if (!$sessionId) {
                $sessionId = Str::uuid()->toString();
                Session::put('cart_session_id', $sessionId);
            }
            $cart = Cart::firstOrCreate(['session_id' => $sessionId]);
        }

        return $cart->load(['items.product']);
    }

    public function addToCart($productId, $quantity = 1)
    {
        $cart = $this->getCart();
        $product = Product::findOrFail($productId);

        $cartItem = $cart->items()->where('product_id', $productId)->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
        } else {
            $cart->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        return $cart;
    }

    public function updateQuantity($itemId, $quantity)
    {
        $cart = $this->getCart();
        $item = $cart->items()->findOrFail($itemId);

        if ($quantity <= 0) {
            $item->delete();
        } else {
            $item->update(['quantity' => $quantity]);
        }

        return $cart;
    }

    public function removeItem($itemId)
    {
        $cart = $this->getCart();
        $cart->items()->where('id', $itemId)->delete();

        return $cart;
    }

    public function clear()
    {
        $cart = $this->getCart();
        $cart->items()->delete();

        return $cart;
    }

    protected function mergeCarts(Cart $source, Cart $target)
    {
        foreach ($source->items as $item) {
            $targetItem = $target->items()->where('product_id', $item->product_id)->first();

            if ($targetItem) {
                $targetItem->increment('quantity', $item->quantity);
            } else {
                $target->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                ]);
            }
        }

        // Move activities from source cart to target cart
        // $source->activities()->update(['cart_id' => $target->id]);
    }

    public function getCartDetails()
    {
        $cart = $this->getCart();
        $items = $cart->items->filter(function ($item) {
            return $item->product !== null;
        });

        $total = $items->sum(function ($item) {
            return $item->quantity * ($item->product->current_price ?? 0);
        });

        return [
            'id' => $cart->id,
            'items' => $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'name' => $item->product->name,
                    'slug' => $item->product->slug,
                    'image' => $item->product->images[0] ?? null,
                    'price' => $item->product->current_price,
                    'original_price' => $item->product->price,
                    'is_on_sale' => $item->product->is_on_sale,
                    'discount_percentage' => $item->product->discount_percentage,
                    'quantity' => $item->quantity,
                    'total' => $item->quantity * ($item->product->current_price ?? 0),
                ];
            }),
            'total_price' => $total,
            'count' => $items->sum('quantity'),
        ];
    }
}
