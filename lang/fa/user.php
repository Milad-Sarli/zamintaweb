<?php

return [
    'resource' => [
        'label' => 'کاربر',
        'plural_label' => 'کاربران',
        'navigation_group' => 'مدیریت سیستم',
    ],
    'fields' => [
        'name' => 'نام نمایشی',
        'first_name' => 'نام',
        'last_name' => 'نام خانوادگی',
        'national_id' => 'کد ملی',
        'phone' => 'شماره همراه',
        'email' => 'ایمیل',
        'password' => 'رمز عبور',
        'admin_panel_access' => 'دسترسی به پنل مدیریت',
        'email_verified_at' => 'تاریخ تایید ایمیل',
        'created_at' => 'تاریخ عضویت',
        'updated_at' => 'آخرین بروزرسانی',
        'two_factor_confirmed_at' => 'تاریخ تایید دو مرحله‌ای',
    ],
    'notifications' => [
        'created' => [
            'title' => 'کاربر ایجاد شد',
            'body' => 'کاربر جدید با موفقیت در سیستم ثبت گردید.',
        ],
    ],
    'filters' => [
        'admin_panel_access' => [
            'placeholder' => 'همه کاربران',
            'true' => 'مدیران',
            'false' => 'کاربران عادی',
        ],
    ],
    'sections' => [
        'system_info' => 'اطلاعات سیستمی',
    ],
];
