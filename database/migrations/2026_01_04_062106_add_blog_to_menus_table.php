<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \App\Models\Menu::firstOrCreate(
            ['url' => '/blog', 'location' => 'header'],
            [
                'title' => 'مجله',
                'target' => '_self',
                'is_visible' => true,
                'parent_id' => -1,
                'order' => 10,
            ]
        );

        \App\Models\Menu::firstOrCreate(
            ['url' => '/blog', 'location' => 'footer_quick_links'],
            [
                'title' => 'مجله پارس طب',
                'target' => '_self',
                'is_visible' => true,
                'parent_id' => -1,
                'order' => 5,
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\Menu::where('url', '/blog')->delete();
    }
};
