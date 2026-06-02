<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = bcrypt('password');

        User::firstOrCreate(
            ['email' => 'miladsarli@gmail.com'],
            [
                'name' => 'milad',
                'email' => 'miladsarli@gmail.com',
                'password' => $password,
                'admin_panel_access' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@zaminta.com'],
            [
                'name' => 'مدیر سایت',
                'email' => 'admin@zaminta.com',
                'password' => $password,
                'admin_panel_access' => true,
            ]
        );

        User::firstOrCreate(
            ['phone' => '09120000000'],
            [
                'name' => 'مدیر تست',
                'phone' => '09120000000',
                'password' => $password,
                'admin_panel_access' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'miladsarli4444@gmail.com'],
            [
                'name' => 'سارلی',
                'username' => 'Mili',
                'email' => 'miladsarli4444@gmail.com',
                'password' => $password,
                'admin_panel_access' => false,
            ]
        );
    }
}
