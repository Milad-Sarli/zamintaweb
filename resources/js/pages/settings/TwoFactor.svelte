<script lang="ts">
        import AppLayout from '@/layouts/AppLayout.svelte';
</script>

<svelte:head>
    <title>ورود دو مرحله ای</title>
</svelte:head>

<AppLayout>
    <div class="p-6 md:p-8" dir="rtl">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">ورود دو مرحله ای</h1>
            <p class="mt-4 text-sm leading-7 text-slate-600 dark:text-slate-300">احراز هویت اصلی این سامانه بر پایه پیامک است. اگر بخواهید امنیت بیشتری داشته باشید، در مرحله بعدی می توان این صفحه را برای TOTP یا کدهای بکاپ تکمیل کرد.</p>
        </div>
    </div>
</AppLayout>
