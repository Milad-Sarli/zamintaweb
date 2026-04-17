<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fakerFa = \Faker\Factory::create('fa_IR');

        $medicalProducts = [
            'دستگاه فشارخون دیجیتال', 'تب‌سنج لیزری', 'گوشی پزشکی لیتمن', 'تخت بیمارستانی مکانیکی',
            'ویلچر ارتوپدی', 'دستگاه اکسیژن‌ساز ۵ لیتری', 'مانیتور علائم حیاتی', 'پالس اکسیمتر انگشتی',
            'ساکشن رومیزی', 'چراغ سیالیتیک اتاق عمل', 'ترازو و قدسنج دیجیتال', 'کیف کمک‌های اولیه',
            'ماسک N95 فیلتردار', 'دستکش جراحی لاتکس', 'سرنگ ۵ سی‌سی لوئرلاک', 'آنژیوکت آبی',
            'ست سرم خارجی', 'الکل طبی ۷۰ درصد', 'باند گچی ارتوپدی', 'آتل انگشتی',
        ];

        $name = $fakerFa->randomElement($medicalProducts) . ' ' . $fakerFa->unique()->numberBetween(100, 999);

        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'description' => $fakerFa->realText(200),
            'price' => $this->faker->numberBetween(10000, 5000000) * 10, // ریال
            'quantity' => $this->faker->numberBetween(5, 50),
            'is_active' => true,
            'brand_id' => null, // Will be set in Seeder
            'unit_id' => null,  // Will be set in Seeder
        ];
    }
}
