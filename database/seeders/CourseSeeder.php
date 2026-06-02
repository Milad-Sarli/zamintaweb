<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $category = CourseCategory::firstOrCreate(
            ['slug' => 'web-fullstack'],
            [
                'title' => 'فول استک وب',
                'description' => 'برنامه نویسی فول استک تحت وب',
                'sort_order' => 0,
                'is_active' => true,
            ]
        );

        Course::firstOrCreate(
            ['slug' => 'zamintaweb'],
            [
                'course_category_id' => $category->id,
                'title' => 'زمین تا وب',
                'instructor_name' => 'میلاد سارلی',
                'level' => 'مبتدی تا پیشفرته',
                'duration' => '۶ تا ۸ ماه',
                'price' => 16000000,
                'prepayment_amount' => 6000000,
                'installment_months' => 5,
                'installment_monthly_amount' => 2000000,
                'support_channel' => 'شماره تماس : ۰۹۹۰۴۷۶۲۷۹۱',
                'description' => '',
                'sort_order' => 0,
                'is_published' => true,
                'is_featured' => false,
            ]
        );
    }
}
