<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartActivityLogger
{
    public function log(string $action, array $payload = [], ?Cart $cart = null)
    {
        // Try to find cart if not provided
        if (!$cart) {
            $user = Auth::user();
            if ($user) {
                $cart = Cart::where('user_id', $user->id)->first();
            } else {
                $sessionId = Session::getId();
                $cart = Cart::where('session_id', $sessionId)->first();
            }
        }

        CartActivity::create([
            'cart_id' => $cart?->id,
            'user_id' => Auth::id(),
            'session_id' => Session::getId(),
            'action' => $action,
            'payload' => $payload,
        ]);
    }
}
