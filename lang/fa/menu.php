<?php

return [
    'resource' => [
        'label' => 'منو',
        'plural_label' => 'منوها',
        'navigation_group' => 'تنظیمات وب‌سایت',
        'tree' => 'مدیریت درختی منوها',
    ],
    'fields' => [
        'title' => 'عنوان منو',
        'url' => 'آدرس (URL)',
        'target' => 'نحوه باز شدن',
        'icon' => 'آیکون',
        'location' => 'موقعیت منو',
        'is_visible' => 'نمایش در سایت',
        'parent' => 'منوی مادر',
        'order' => 'ترتیب نمایش',
    ],
    'targets' => [
        'self' => 'پنجره فعلی',
        'blank' => 'پنجره جدید',
    ],
    'locations' => [
        'header' => 'هدر (بالای سایت)',
        'footer' => 'فوتر (پایین سایت)',
        'footer_quick_links' => 'لینک‌های سریع فوتر',
        'sidebar' => 'سایدبار (کنار سایت)',
    ],
];
