<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        Menu::firstOrCreate(
            ['title' => 'مجله', 'location' => 'header'],
            [
                'url' => '/blog',
                'target' => '_self',
                'location' => 'header',
                'is_visible' => true,
                'parent_id' => -1,
                'order' => 10,
            ]
        );

        Menu::firstOrCreate(
            ['title' => 'مجله پارس طب', 'location' => 'footer_quick_links'],
            [
                'url' => '/blog',
                'target' => '_self',
                'location' => 'footer_quick_links',
                'is_visible' => true,
                'parent_id' => -1,
                'order' => 5,
            ]
        );
    }
}
