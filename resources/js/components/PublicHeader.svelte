<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import ThemeSwitcher from '@/components/ThemeSwitcher.svelte';
    import AppLogoIcon from '@/components/AppLogoIcon.svelte';

    let user = $derived($page.props.auth.user);
    let courseCategories = $derived($page.props.courseCategories || []);
</script>

<header class="sticky top-0 z-50 border-b border-slate-100 bg-white/80 backdrop-blur-md dark:border-zinc-900 dark:bg-zinc-950/80">
    <div class="mx-auto flex h-20 max-w-7xl items-center justify-between gap-6 px-4 sm:px-6 lg:px-8">
        <Link href="/" class="flex items-center gap-3">
            <div class="flex size-11 items-center justify-center rounded-2xl bg-slate-950 text-white dark:bg-sky-500">
                <AppLogoIcon class="size-6" />
            </div>
            <div>
                <div class="text-lg font-black text-slate-950 dark:text-white">آکادمی توسعه وب</div>
                <div class="text-xs text-slate-500 dark:text-slate-400">دوره های آموزشی فارسی</div>
            </div>
        </Link>

        <nav class="hidden items-center gap-6 lg:flex">
            <Link href="/" class="text-sm font-semibold text-slate-600 transition hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">خانه</Link>
            <Link href="/courses" class="text-sm font-semibold text-slate-600 transition hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">دوره ها</Link>
            <Link href="/about-me" class="text-sm font-semibold text-slate-600 transition hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">درباره من</Link>
            {#each courseCategories.slice(0, 4) as category (category.slug)}
                <Link href={`/courses?category=${category.slug}`} class="text-sm font-semibold text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-white">{category.title}</Link>
            {/each}
        </nav>

        <div class="flex items-center gap-4">
            <ThemeSwitcher />
            {#if user}
                <Link href="/dashboard" class="rounded-full bg-slate-950 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-100 shadow-[0_4px_12px_rgba(0,0,0,0.06)]">داشبورد</Link>
            {:else}
                <Link href="/login" class="rounded-full bg-slate-950 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-100 shadow-[0_4px_12px_rgba(0,0,0,0.06)]">ورود هنرجو</Link>
            {/if}
        </div>
    </div>
</header>
