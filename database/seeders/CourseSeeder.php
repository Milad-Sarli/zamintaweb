<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseEnrollment;
use App\Models\CourseEpisode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = $this->seedCategories();
        $instructor = 'میلاد سارلی';

        $coursesData = [
            [
                'title' => 'HTML & CSS — ساختار و استایل وب',
                'slug' => 'html-css-complete',
                'category' => 'frontend',
                'level' => 'مبتدی تا پیشرفته',
                'duration' => '۲ ماه',
                'price' => 1980000,
                'prepayment' => 780000,
                'installment_months' => 3,
                'installment_monthly' => 400000,
                'short_desc' => 'از صفر تا صد HTML5 و CSS3 — یادگیری Semantic HTML، Flexbox، Grid، Responsive Design، Animation و پروژه‌های عملی',
                'is_featured' => true,
                'episodes' => 24,
                'support' => 'گروه تلگرام پشتیبانی',
            ],
            [
                'title' => 'JavaScript — زبان اصلی وب',
                'slug' => 'javascript-mastery',
                'category' => 'frontend',
                'level' => 'مقدماتی تا پیشرفته',
                'duration' => '۳ ماه',
                'price' => 4480000,
                'prepayment' => 1480000,
                'installment_months' => 4,
                'installment_monthly' => 750000,
                'short_desc' => 'تسلط بر JavaScript مدرن: ES6+، DOM Manipulation، Async/Await، Fetch API، Module System، Closure، Prototype و پروژه‌های عملی',
                'is_featured' => true,
                'episodes' => 36,
                'support' => 'گروه تلگرام پشتیبانی',
            ],
            [
                'title' => 'Tailwind CSS — طراحی مدرن رابط کاربری',
                'slug' => 'tailwind-css-modern',
                'category' => 'frontend',
                'level' => 'مقدماتی تا پیشرفته',
                'duration' => '۱.۵ ماه',
                'price' => 1680000,
                'prepayment' => 680000,
                'installment_months' => 2,
                'installment_monthly' => 500000,
                'short_desc' => 'طراحی حرفه‌ای با Tailwind CSS v4: Utility-First، Responsive، Dark Mode، Custom Theme، Animation و پیاده‌سازی طرح‌های واقعی',
                'is_featured' => false,
                'episodes' => 16,
                'support' => 'گروه تلگرام پشتیبانی',
            ],
            [
                'title' => 'React.js — ساخت اپلیکیشن‌های مدرن',
                'slug' => 'react-modern-apps',
                'category' => 'react',
                'level' => 'پیشرفته',
                'duration' => '۳.۵ ماه',
                'price' => 6480000,
                'prepayment' => 2480000,
                'installment_months' => 5,
                'installment_monthly' => 800000,
                'short_desc' => 'React 19 با رویکرد پروژه محور: Hooks، State Management (Zustand)، React Router، Suspense، Lazy Loading، TypeScript و پروژه فروشگاه اینترنتی',
                'is_featured' => true,
                'episodes' => 40,
                'support' => 'گروه تلگرام پشتیبانی',
            ],
            [
                'title' => 'shadcn/ui — کامپوننت‌های زیبا و مدرن',
                'slug' => 'shadcn-ui-components',
                'category' => 'react',
                'level' => 'پیشرفته',
                'duration' => '۱ ماه',
                'price' => 1480000,
                'prepayment' => 480000,
                'installment_months' => 1,
                'installment_monthly' => 1000000,
                'short_desc' => 'پیاده‌سازی رابط کاربری با shadcn/ui: نصب و تنظیم، سفارشی‌سازی تم، کامپوننت‌های فرم، Dialog، Table، Chart و ترکیب با React Hook Form + Zod',
                'is_featured' => false,
                'episodes' => 12,
                'support' => 'گروه تلگرام پشتیبانی',
            ],
            [
                'title' => 'Svelte 5 — فریمورک مدرن فرانت‌اند',
                'slug' => 'svelte-5-modern',
                'category' => 'svelte',
                'level' => 'مقدماتی تا پیشرفته',
                'duration' => '۲.۵ ماه',
                'price' => 3980000,
                'prepayment' => 1480000,
                'installment_months' => 3,
                'installment_monthly' => 834000,
                'short_desc' => 'Svelte 5 با Runes ($state، $derived، $props)، Snippets، Stores، Transitions، SvelteKit و ساخت SPA و SSR',
                'is_featured' => true,
                'episodes' => 28,
                'support' => 'گروه تلگرام پشتیبانی',
            ],
            [
                'title' => 'Laravel 12 — از صفر تا پروژه واقعی',
                'slug' => 'laravel-12-from-zero',
                'category' => 'laravel',
                'level' => 'مبتدی تا پیشرفته',
                'duration' => '۴ ماه',
                'price' => 8980000,
                'prepayment' => 3480000,
                'installment_months' => 6,
                'installment_monthly' => 917000,
                'short_desc' => 'آموزش کامل لاراول ۱۲: Routing، Eloquent ORM، Authentication، Authorization، Testing، Queue، Event، Notification، Filament و ساخت پروژه واقعی سیستم مدیریت محتوا',
                'is_featured' => true,
                'episodes' => 54,
                'support' => 'گروه تلگرام پشتیبانی',
            ],
            [
                'title' => 'MySQL — طراحی و مدیریت دیتابیس',
                'slug' => 'mysql-database-design',
                'category' => 'database',
                'level' => 'مبتدی تا پیشرفته',
                'duration' => '۲ ماه',
                'price' => 2980000,
                'prepayment' => 1000000,
                'installment_months' => 3,
                'installment_monthly' => 660000,
                'short_desc' => 'SQL از پایه تا پیشرفته: Database Design، Normalization، Joins، Subqueries، Indexing، Stored Procedures، Migration با Laravel و بهینه‌سازی کوئری',
                'is_featured' => false,
                'episodes' => 22,
                'support' => 'گروه تلگرام پشتیبانی',
            ],
            [
                'title' => 'Git & GitHub — کنترل نسخه حرفه‌ای',
                'slug' => 'git-github-professional',
                'category' => 'git',
                'level' => 'مبتدی',
                'duration' => '۱ ماه',
                'price' => 1280000,
                'prepayment' => 480000,
                'installment_months' => 1,
                'installment_monthly' => 800000,
                'short_desc' => 'گیت از پایه تا پیشرفته: Init، Commit، Branch، Merge، Rebase، Pull Request، GitHub Flow، Conflict Resolution و کار تیمی',
                'is_featured' => false,
                'episodes' => 14,
                'support' => 'گروه تلگرام پشتیبانی',
            ],
            [
                'title' => 'مبانی کامپیوتر و تفکر برنامه‌نویسی',
                'slug' => 'computer-fundamentals',
                'category' => 'fundamentals',
                'level' => 'مبتدی',
                'duration' => '۱.۵ ماه',
                'price' => 980000,
                'prepayment' => 380000,
                'installment_months' => 1,
                'installment_monthly' => 600000,
                'short_desc' => 'مفاهیم پایه کامپیوتر: سخت‌افزار و نرم‌افزار، سیستم عامل، فایل‌سیستم، الگوریتم و فلوچارت، تفکر محاسباتی و منطق برنامه‌نویسی',
                'is_featured' => false,
                'episodes' => 14,
                'support' => 'گروه تلگرام پشتیبانی',
            ],
        ];

        foreach ($coursesData as $data) {
            $category = $categories[$data['category']];

            $course = Course::firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'course_category_id' => $category->id,
                    'title' => $data['title'],
                    'instructor_name' => $instructor,
                    'level' => $data['level'],
                    'duration' => $data['duration'],
                    'price' => $data['price'],
                    'prepayment_amount' => $data['prepayment'],
                    'installment_months' => $data['installment_months'],
                    'installment_monthly_amount' => $data['installment_monthly'],
                    'short_description' => $data['short_desc'],
                    'support_channel' => $data['support'],
                    'sort_order' => 0,
                    'is_published' => true,
                    'is_featured' => $data['is_featured'],
                ]
            );

            for ($i = 1; $i <= $data['episodes']; $i++) {
                CourseEpisode::firstOrCreate(
                    [
                        'course_id' => $course->id,
                        'sort_order' => $i,
                    ],
                    [
                        'title' => "جلسه {$i} — {$this->getEpisodeTopic($data['slug'], $i, $data['episodes'])}",
                        'description' => "آموزش گام به گام {$this->getEpisodeTopic($data['slug'], $i, $data['episodes'])} در دوره {$data['title']}",
                        'duration' => rand(12, 48) . ':' . str_pad((string) rand(0, 59), 2, '0', STR_PAD_LEFT),
                        'is_preview' => $i <= 3,
                        'is_published' => true,
                    ]
                );
            }
        }

        $this->seedEnrollments();
    }

    private function getEpisodeTopic(string $slug, int $i, int $total): string
    {
        $topics = [
            'html-css-complete' => [
                'مقدمه و آشنایی با HTML',
                'ساختار یک صفحه HTML',
                'تگ‌های متنی و Heading',
                'لیست‌ها و لینک‌ها',
                'تصاویر و Media',
                'جدول‌ها در HTML',
                'فرم‌ها و Inputها',
                'HTML5 Semantic Tags',
                'مقدمه CSS و Selectorها',
                'Box Model و Display',
                'Typography و Fontها',
                'Background و Border',
                'Flexbox — قسمت اول',
                'Flexbox — قسمت دوم',
                'CSS Grid',
                'Positioning',
                'Responsive Design با Media Query',
                'CSS Animation و Transition',
                'متغیرهای CSS',
                'پروژه عملی — طراحی صفحه فرود',
                'پروژه عملی — طراحی وبلاگ',
                'بهینه‌سازی و اصول Accessibility',
                'جمع‌بندی و مسیر یادگیری',
                '—',
            ],
            'javascript-mastery' => [
                'تاریخچه و جایگاه JavaScript',
                'متغیرها و انواع داده',
                'عملگرها و عبارات',
                'شرط‌ها و تصمیم‌گیری',
                'حلقه‌ها و تکرار',
                'توابع — تعریف و فراخوانی',
                'Scope و Hoisting',
                'آرایه‌ها و متدهای پرکاربرد',
                'اشیاء و Propertyها',
                'DOM — دستکاری صفحه',
                'رویدادها (Events)',
                'LocalStorage و SessionStorage',
                'توابع پیشرفته — Callback و Closure',
                'برنامه‌نویسی Async — Promise',
                'Async/Await',
                'Fetch API و درخواست HTTP',
                'Error Handling',
                'ES6+ — Destructuring و Spread',
                'ماژول‌ها (Module System)',
                'API و کار با JSON',
                'Web APIها — Geolocation، Notification',
                'پروژه — To-Do اپلیکیشن',
                'پروژه — پیش‌بینی آب و هوا',
                'This و Binding',
                'Prototype و Inheritance',
                'Classها در ES6',
                'Set و Map',
                'Regular Expression',
                'Performance و Debouncing',
                'Testing با Jest — مقدمه',
                'ساختار پروژه واقعی',
                'Webpack و Vite — مقدمه',
                'TypeScript — آشنایی اولیه',
                'نکات پیشرفته و Best Practice',
                'پروژه نهایی — اپلیکیشن مدیریت tasks',
                'جمع‌بندی و مسیر ادامه',
            ],
        ];

        $generic = [
            'معرفی و مقدمه دوره',
            'نصب و راه‌اندازی ابزارها',
            'مفاهیم پایه و پیش‌نیازها',
            'آموزش گام به گام مبحث اصلی',
            'تمرین عملی و پروژه',
            'اشکال‌زدایی و رفع خطا',
            'بهینه‌سازی و Best Practice',
            'تکنیک‌های پیشرفته',
            'پروژه عملی — بخش اول',
            'پروژه عملی — بخش دوم',
            'نکات تکمیلی و منابع',
            'ارزیابی و آزمون',
            'جمع‌بندی و مسیر پیشرو',
            'پرسش و پاسخ',
        ];

        $courseTopics = $topics[$slug] ?? $generic;

        return $courseTopics[($i - 1) % count($courseTopics)];
    }

    private function seedCategories(): array
    {
        $categories = [
            'frontend' => ['title' => 'فرانت اند', 'desc' => 'HTML، CSS، JavaScript، Tailwind و طراحی رابط کاربری', 'sort' => 1],
            'react' => ['title' => 'React.js', 'desc' => 'React، Next.js و اکوسیستم مدرن فرانت اند', 'sort' => 2],
            'svelte' => ['title' => 'SvelteJS', 'desc' => 'Svelte 5 و SvelteKit', 'sort' => 3],
            'laravel' => ['title' => 'لاراول', 'desc' => 'Laravel، Filament و توسعه بک اند حرفه‌ای', 'sort' => 4],
            'database' => ['title' => 'دیتابیس', 'desc' => 'MySQL، طراحی دیتابیس و بهینه‌سازی کوئری', 'sort' => 5],
            'git' => ['title' => 'گیت و گیت‌هاب', 'desc' => 'کنترل نسخه، کار تیمی و GitHub', 'sort' => 6],
            'fundamentals' => ['title' => 'مبانی کامپیوتر', 'desc' => 'مفاهیم پایه، الگوریتم و تفکر برنامه‌نویسی', 'sort' => 7],
        ];

        $result = [];
        foreach ($categories as $slug => $data) {
            $cat = CourseCategory::firstOrCreate(
                ['slug' => $slug],
                [
                    'title' => $data['title'],
                    'description' => $data['desc'],
                    'sort_order' => $data['sort'],
                    'is_active' => true,
                ]
            );
            $result[$slug] = $cat;
        }

        return $result;
    }

    private function seedEnrollments(): void
    {
        $existingUser = User::where('phone', '09904762791')->first();
        if (!$existingUser) {
            $existingUser = User::create([
                'name' => 'میلاد سارلی',
                'phone' => '09904762791',
                'email' => 'milad@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
        }

        // Create some fake users and enrollments for stats
        $fakerUsers = [
            ['name' => 'علی محمدی', 'phone' => '09121234567'],
            ['name' => 'سارا احمدی', 'phone' => '09131234567'],
            ['name' => 'محمد رضایی', 'phone' => '09141234567'],
            ['name' => 'نرگس حسینی', 'phone' => '09151234567'],
            ['name' => 'امیر کریمی', 'phone' => '09161234567'],
            ['name' => 'زهرا موسوی', 'phone' => '09171234567'],
            ['name' => 'رضا صادقی', 'phone' => '09181234567'],
        ];

        $allCourses = Course::published()->get();

        foreach ($fakerUsers as $i => $userData) {
            $user = User::firstOrCreate(
                ['phone' => $userData['phone']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make(Str::random(16)),
                ]
            );

            // Each user enrolls in 1-3 courses
            $enrollCount = rand(1, 3);
            $userCourses = $allCourses->random($enrollCount);

            foreach ($userCourses as $course) {
                $createdAt = now()->subDays(rand(10, 180));
                CourseEnrollment::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'course_id' => $course->id,
                    ],
                    [
                        'status' => 'approved',
                        'amount' => $course->price,
                        'paid_at' => $createdAt,
                        'approved_at' => $createdAt,
                    ]
                );
            }
        }

        // Enroll the main user in featured courses
        $featuredCourses = $allCourses->where('is_featured', true);
        foreach ($featuredCourses as $course) {
            $createdAt = now()->subDays(rand(5, 30));
            CourseEnrollment::firstOrCreate(
                [
                    'user_id' => $existingUser->id,
                    'course_id' => $course->id,
                ],
                [
                    'status' => 'approved',
                    'amount' => $course->price,
                    'paid_at' => $createdAt,
                    'approved_at' => $createdAt,
                ]
            );
        }
    }
}
