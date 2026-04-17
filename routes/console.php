<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('make:admin', function () {
    $name = $this->ask('نام کاربر');
    $phone = $this->ask('شماره موبایل (مثال: 09123456789)');
    $password = $this->secret('رمز عبور');

    if (User::where('phone', $phone)->exists()) {
        $this->error('کاربر با این شماره موبایل قبلاً ثبت شده است.');

        return 1;
    }

    /** @var User $user */
    $user = User::create([
        'name' => $name,
        'phone' => $phone,
        'password' => Hash::make($password),
        'admin_panel_access' => 1,
    ]);

    $this->info("ادمین «{$user->name}» با موفقیت ایجاد شد.");
    $this->warn('آدرس پنل: ' . url('/admin'));
})->purpose('Create a new admin user for Filament panel');
