<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // حذف محصولات قبلی (شامل سافت دیلیت شده‌ها)
        \App\Models\Product::withTrashed()->forceDelete();

        // دریافت دسته‌بندی‌های موجود
        $categories = \App\Models\Category::all();

        if ($categories->isEmpty()) {
            $this->command->error('هیچ دسته‌بندی در دیتابیس یافت نشد.');

            return;
        }

        // دریافت یا ایجاد برندهای فارسی
        $brands = \App\Models\Brand::all();
        if ($brands->isEmpty()) {
            $brandNames = ['صاایران', 'پارس خزر', 'رونیکس', 'توسن'];
            $brands = collect();
            foreach ($brandNames as $name) {
                $brands->push(\App\Models\Brand::create([
                    'name' => $name,
                    'slug' => \Illuminate\Support\Str::slug($name) ?: \Illuminate\Support\Str::random(8),
                    'logo' => 'brands/default.png',
                ]));
            }
        }

        // ایجاد واحدهای اندازه‌گیری فارسی
        $units = \App\Models\Unit::all();
        if ($units->isEmpty()) {
            $unitNames = ['عدد', 'بسته', 'کارتن', 'جین'];
            $units = collect();
            foreach ($unitNames as $name) {
                $units->push(\App\Models\Unit::create(['name' => $name]));
            }
        }

        $fakerFa = \Faker\Factory::create('fa_IR');
        $medicalProducts = [
            'دستگاه فشارخون دیجیتال', 'تب‌سنج لیزری', 'گوشی پزشکی لیتمن', 'تخت بیمارستانی مکانیکی',
            'ویلچر ارتوپدی', 'دستگاه اکسیژن‌ساز ۵ لیتری', 'مانیتور علائم حیاتی', 'پالس اکسیمتر انگشتی',
            'ساکشن رومیزی', 'چراغ سیالیتیک اتاق عمل', 'ترازو و قدسنج دیجیتال', 'کیف کمک‌های اولیه',
            'ماسک N95 فیلتردار', 'دستکش جراحی لاتکس', 'سرنگ ۵ سی‌سی لوئرلاک', 'آنژیوکت آبی',
            'ست سرم خارجی', 'الکل طبی ۷۰ درصد', 'باند گچی ارتوپدی', 'آتل انگشتی',
        ];

        for ($i = 0; $i < 100; $i++) {
            $name = $fakerFa->randomElement($medicalProducts) . ' ' . ($i + 100);
            $product = \App\Models\Product::create([
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name) ?: \Illuminate\Support\Str::random(10),
                'description' => $fakerFa->realText(200),
                'images' => [
                    'https://picsum.photos/seed/' . rand(1, 1000) . '/600/600',
                    'https://picsum.photos/seed/' . rand(1, 1000) . '/600/600',
                ],
                'price' => rand(10000, 5000000) * 10,
                'quantity' => rand(5, 50),
                'is_active' => true,
                'brand_id' => $brands->random()->id,
                'unit_id' => $units->random()->id,
            ]);

            $product->categories()->attach(
                $categories->random(rand(1, min(2, $categories->count())))->pluck('id')->toArray()
            );
        }
    }

    private $faker;

    public function __construct()
    {
        $this->faker = \Faker\Factory::create();
    }
}
