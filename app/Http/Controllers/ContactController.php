<?php

namespace App\Http\Controllers;

use App\Models\ContactSetting;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        // Force getting the latest record from DB
        $settings = ContactSetting::first() ?? new ContactSetting;

        return Inertia::render('Contact', [
            'contactSettings' => $settings->toArray(),
        ]);
    }
}
