<script lang="ts">
    import { Link } from '@inertiajs/svelte';
</script>

<footer class="border-t border-slate-200 bg-slate-950 text-slate-300 dark:border-zinc-800">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-3 lg:px-8">
        <div>
            <h3 class="text-2xl font-black text-white">آکادمی Tekmil</h3>
            <p class="mt-4 text-sm leading-8 text-slate-400">
                دوره های آموزش برنامه نویسی با پشتیبانی کامل، جلسات ویدیویی با کیفیت و مسیر یادگیری منظم.
            </p>
        </div>
        <div>
            <h4 class="text-lg font-bold text-white">لینک های کاربردی</h4>
            <div class="mt-5 grid gap-3 text-sm">
                <Link href="/" class="transition hover:text-white">خانه</Link>
                <Link href="/courses" class="transition hover:text-white">همه دوره ها</Link>
                <Link href="/login" class="transition hover:text-white">ورود هنرجو</Link>
                <Link href="/admin" class="transition hover:text-white">پنل مدیریت</Link>
            </div>
        </div>
        <div>
            <h4 class="text-lg font-bold text-white">نحوه فعال سازی دوره</h4>
            <ul class="mt-5 space-y-3 text-sm leading-7 text-slate-400">
                <li>۱. انتخاب دوره مناسب از صفحه دوره ها</li>
                <li>۲. پرداخت آفلاین و ثبت تصویر فیش</li>
                <li>۳. بررسی توسط ادمین و فعال سازی دسترسی</li>
            </ul>
        </div>
    </div>
</footer>
