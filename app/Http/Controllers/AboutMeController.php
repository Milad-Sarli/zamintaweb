<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class AboutMeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('AboutMe', [
            'profile' => [
                'name' => 'میلاد سرلی',
                'title' => 'توسعه‌دهنده فرانت‌اند و لاراول',
                'avatar' => '/images/me.jpg',
                'bio' => 'من یک توسعه‌دهنده وب با بیش از ۵ سال تجربه در طراحی و پیاده‌سازی پروژه‌های تحت وب هستم. تخصص اصلی من در فریمورک لاراول برای توسعه بک‌اند و Svelte/Vue.js در فرانت‌اند است. به نوشتن کد تمیز، معماری مقیاس‌پذیر و تجربه کاربری روان اعتقاد دارم. در کنار برنامه‌نویسی، به آموزش و به اشتراک‌گذاری دانش علاقه دارم.',
                'shortBio' => 'لاراول | Svelte | Vue.js | TypeScript | کدنویس خوش ذوق و عاشق آموزش',
                'skills' => ['PHP', 'Laravel', 'Svelte', 'Vue.js', 'TypeScript', 'Tailwind CSS', 'MySQL', 'Docker', 'Git', 'REST API'],
                'socials' => [
                    ['name' => 'گیت‌هاب', 'icon' => 'github', 'url' => 'https://github.com/miladsarli'],
                    ['name' => 'لینکدین', 'icon' => 'linkedin', 'url' => 'https://linkedin.com/in/miladsarli'],
                    ['name' => 'تلگرام', 'icon' => 'telegram', 'url' => 'https://t.me/miladsarli'],
                ],
            ],
            'projects' => [
                [
                    'title' => 'ZaminTaWeb',
                    'description' => 'پلتفرم آموزش طراحی و توسعه وب با قابلیت فروش دوره، سیستم اقساط، پنل ادمین حرفه‌ای با Filament',
                    'tags' => ['Laravel', 'Svelte', 'Filament', 'Inertia'],
                    'image' => '/images/projects/zamintaweb.jpg',
                    'link' => 'https://zamintaweb.test',
                ],
                [
                    'title' => 'فروشگاه کالای پزشکی',
                    'description' => 'فروشگاه اینترنتی تخصصی تجهیزات پزشکی با سبد خرید، درگاه پرداخت، مدیریت سفارشات و تخفیف‌ها',
                    'tags' => ['Laravel', 'Vue.js', 'Tailwind', 'MySQL'],
                    'image' => '/images/projects/medical.jpg',
                    'link' => '#',
                ],
                [
                    'title' => 'سیستم مدیریت تیکت',
                    'description' => 'سیستم پشتیبانی و تیکتینگ هوشمند با قابلیت اولویت‌بندی، پیوست فایل و اعلان‌های لحظه‌ای',
                    'tags' => ['PHP', 'Laravel', 'Livewire', 'Alpine.js'],
                    'image' => '/images/projects/ticket.jpg',
                    'link' => '#',
                ],
            ],
        ]);
    }
}
