<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provinces', function ($table) {
            $table->id();
            $table->string('name')->comment('نام استان');
            $table->string('slug');
            $table->timestamps();
            $table->softDeletes();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
        });

        Schema::create('cities', function ($table) {
            $table->id();
            $table->string('name')->comment('نام شهر');
            $table->string('slug');
            $table->foreignId('province_id')->constrained('provinces')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
        });

        if (DB::getDriverName() === 'mysql') {
            $path = base_path('pro-and-cities.sql');
            if (File::exists($path)) {
                $sql = File::get($path);
                DB::unprepared($sql);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
        Schema::dropIfExists('provinces');
    }
};
