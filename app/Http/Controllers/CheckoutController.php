<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    protected $activityLogger;

    public function __construct(CartActivityLogger $activityLogger)
    {
        $this->activityLogger = $activityLogger;
    }

    public function shipping()
    {
        $user = Auth::user();
        $addresses = $user->addresses()->with(['province', 'city'])->get();

        // Check if cart is empty
        $cart = Cart::where('user_id', Auth::id())->first();
        if (!$cart || $cart->items()->count() == 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $this->activityLogger->log('checkout_shipping_viewed', [], $cart);

        return Inertia::render('Checkout/Shipping', [
            'addresses' => $addresses,
        ]);
    }

    public function storeShipping(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
        ]);

        $address = Auth::user()->addresses()->with(['province', 'city'])->findOrFail($request->address_id);

        session(['checkout.address_id' => $address->id]);

        $this->activityLogger->log('checkout_address_set', [
            'address_id' => $address->id,
            'address_text' => "{$address->province->name}، {$address->city->name}، {$address->address}",
            'postal_code' => $address->postal_code,
        ]);

        return redirect()->route('checkout.payment');
    }

    public function payment()
    {
        $this->activityLogger->log('checkout_payment_viewed');
        $addressId = session('checkout.address_id');
        if (!$addressId) {
            return redirect()->route('checkout.shipping');
        }

        $address = Auth::user()->addresses()->with(['province', 'city'])->findOrFail($addressId);

        $cart = Cart::where('user_id', Auth::id())->with(['items.product'])->first();
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $subtotal = $cart->items->sum(function ($item) {
            // Check if product has discount
            // For now assuming simple price
            $price = $item->product->price;

            // If product has discount logic, apply it here
            return $item->quantity * $price;
        });

        $couponCode = session('checkout.coupon_code');
        $discountAmount = 0;
        $coupon = null;

        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->is_active) {
                // Check expiry
                if ($coupon->expires_at && $coupon->expires_at->isPast()) {
                    session()->forget('checkout.coupon_code');
                    $coupon = null;
                } else {
                    if ($coupon->type == 'percentage') {
                        $discountAmount = $subtotal * ($coupon->value / 100);
                        if ($coupon->max_discount_amount) {
                            $discountAmount = min($discountAmount, $coupon->max_discount_amount);
                        }
                    } else {
                        $discountAmount = $coupon->value;
                    }
                }
            } else {
                session()->forget('checkout.coupon_code');
                $coupon = null;
            }
        }

        $total = $subtotal - $discountAmount;
        if ($total < 0) {
            $total = 0;
        }

        return Inertia::render('Checkout/Payment', [
            'address' => $address,
            'cart' => $cart,
            'subtotal' => $subtotal,
            'discountAmount' => $discountAmount,
            'total' => $total,
            'coupon' => $coupon,
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $coupon = Coupon::where('code', $request->code)->first();

        if (!$coupon || !$coupon->is_active) {
            return back()->withErrors(['code' => 'Invalid coupon code']);
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return back()->withErrors(['code' => 'Coupon expired']);
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return back()->withErrors(['code' => 'Coupon usage limit reached']);
        }

        session(['checkout.coupon_code' => $coupon->code]);

        $this->activityLogger->log('coupon_applied', [
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
        ]);

        return back()->with('success', 'Coupon applied successfully');
    }

    public function removeCoupon()
    {
        session()->forget('checkout.coupon_code');

        return back();
    }

    public function placeOrder(Request $request)
    {
        $addressId = session('checkout.address_id');
        $cart = Cart::where('user_id', Auth::id())->with(['items.product'])->first();

        if (!$addressId || !$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $address = Auth::user()->addresses()->with(['province', 'city'])->findOrFail($addressId);

        // Recalculate totals (security)
        $subtotal = $cart->items->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        $couponCode = session('checkout.coupon_code');
        $discountAmount = 0;
        $couponId = null;

        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->where('is_active', true)->first();
            if ($coupon) {
                if ($coupon->type == 'percentage') {
                    $discountAmount = $subtotal * ($coupon->value / 100);
                    if ($coupon->max_discount_amount) {
                        $discountAmount = min($discountAmount, $coupon->max_discount_amount);
                    }
                } else {
                    $discountAmount = $coupon->value;
                }
                $couponId = $coupon->id;
            }
        }

        $total = $subtotal - $discountAmount;
        if ($total < 0) {
            $total = 0;
        }

        DB::beginTransaction();
        try {
            // Create Order
            $order = Order::create([
                'user_id' => Auth::id(),
                'shipping_address' => $address->toArray(), // Save snapshot
                'total_price' => $subtotal,
                'discount_amount' => $discountAmount,
                'final_price' => $total,
                'status' => 'pending',
                'payment_method' => 'online', // Default for now
                'payment_status' => 'pending',
            ]);

            // Create Order Items
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'total_price' => $item->quantity * $item->product->price,
                    // 'color_id' => ... if cart item has color
                ]);
            }

            // Update Coupon Usage
            if ($couponId) {
                Coupon::where('id', $couponId)->increment('used_count');
            }

            // Clear Cart
            $cart->items()->delete();
            $cart->delete();

            session()->forget(['checkout.address_id', 'checkout.coupon_code']);

            DB::commit();

            $this->activityLogger->log('checkout_order_placed', [
                'order_id' => $order->id,
                'final_price' => $order->final_price,
                'items_count' => $cart->items->count(),
                'coupon_used' => $couponCode ?? 'none',
            ]);

            // Redirect to bank (placeholder)
            return redirect()->route('dashboard')->with('success', 'Order placed successfully! Redirecting to bank...');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->activityLogger->log('checkout_order_failed', ['error' => $e->getMessage()]);

            return back()->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }
}
