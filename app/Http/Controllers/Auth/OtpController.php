<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SMS\IPPanelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class OtpController extends Controller
{
    public function showLogin()
    {
        if (!session()->has('url.intended')) {
            $prev = url()->previous();
            if ($prev && $prev !== route('login') && $prev !== url()->current()) {
                session(['url.intended' => $prev]);
            }
        }

        return Inertia::render('auth/Login', [
            'status' => session('status'),
            'phone' => session('phone'),
        ]);
    }

    public function sendOtp(Request $request, IPPanelService $smsService)
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^09[0-9]{9}$/'],
        ]);

        $phone = $request->phone;

        // Generate 5 digit OTP
        $otp = rand(10000, 99999);

        // Cache OTP for 2 minutes
        Cache::put("otp_{$phone}", $otp, now()->addMinutes(2));

        // Log for local dev
        if (app()->isLocal()) {
            Log::info("OTP for {$phone}: {$otp}");
        }

        // Send SMS
        $smsService->send('otp', $phone, [
            'code' => (string) $otp,
        ]);

        return back()->with('status', 'code-sent')->with('phone', $phone);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^09[0-9]{9}$/'],
            'otp' => ['required', 'string', 'size:5'],
        ]);

        $phone = $request->phone;
        $otp = $request->otp;

        $cachedOtp = Cache::get("otp_{$phone}");

        if (!$cachedOtp || $cachedOtp != $otp) {
            throw ValidationException::withMessages([
                'otp' => [__('auth.failed')], // Use standard auth failed message or custom
            ]);
        }

        // Find or Create User
        $user = User::firstOrCreate(
            ['phone' => $phone],
            [
                'name' => 'کاربر ' . substr($phone, -4),
                'password' => Hash::make(Str::random(16)),
            ]
        );

        Auth::login($user, true);

        Cache::forget("otp_{$phone}");

        $request->session()->regenerate();

        // Return Inertia render so frontend shows welcome message then redirects
        return Inertia::render('auth/Login', [
            'status' => 'just_logged_in',
            'phone' => $phone,
        ]);
    }
}
