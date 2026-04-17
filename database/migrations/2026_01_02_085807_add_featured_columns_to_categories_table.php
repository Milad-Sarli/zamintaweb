<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('is_visible');
            $table->string('featured_title')->nullable()->after('is_featured');
            $table->string('featured_subtitle')->nullable()->after('featured_title');
            $table->string('featured_image')->nullable()->after('featured_subtitle');
            $table->string('featured_color')->nullable()->after('featured_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'featured_title', 'featured_subtitle', 'featured_image', 'featured_color']);
        });
    }
};
