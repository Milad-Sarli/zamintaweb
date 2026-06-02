<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProvinceCitySeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('provinces')->count() > 0) {
            return;
        }

        $path = base_path('pro-and-cities.sql');
        if (File::exists($path)) {
            $sql = File::get($path);
            DB::unprepared($sql);
        }
    }
}
