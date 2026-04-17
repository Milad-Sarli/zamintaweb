<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:newsletters,email',
        ], [
            'email.required' => __('validation.required', ['attribute' => __('ui.email')]),
            'email.email' => __('validation.email', ['attribute' => __('ui.email')]),
            'email.unique' => __('ui.newsletter_already_subscribed'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        Newsletter::create([
            'email' => $request->email,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('ui.newsletter_subscribed_successfully'),
        ]);
    }
}
