<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import ThemeSwitcher from '@/components/ThemeSwitcher.svelte';
    import AppLogoIcon from '@/components/AppLogoIcon.svelte';
    import * as Sheet from '@/components/ui/sheet';
    import { Menu, House, BookOpen, User, Shapes, GraduationCap, LogIn } from 'lucide-svelte';

    let user = $derived($page.props.auth.user);
    let courseCategories = $derived($page.props.courseCategories || []);
    let mobileMenuOpen = $state(false);
</script>

<header class="sticky top-0 z-50 border-b border-slate-100 bg-white/80 backdrop-blur-md dark:border-zinc-900 dark:bg-zinc-950/80">
    <div class="mx-auto flex h-20 max-w-7xl items-center justify-between gap-6 px-4 sm:px-6 lg:px-8">
        <Link href="/" class="flex shrink-0 items-center gap-3">
            <AppLogoIcon class="size-11" />
            <div>
                <div class="text-lg font-black text-slate-950 dark:text-white">آکادمی Tekmil</div>
                <div class="text-xs text-slate-500 dark:text-slate-400">دوره های آموزش برنامه نویسی</div>
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

        <div class="flex items-center gap-2 sm:gap-3">
            <ThemeSwitcher />

            {#if user}
                <Link href="/dashboard" class="rounded-full bg-slate-950 px-4 py-2 text-xs font-bold text-white transition hover:bg-slate-800 sm:px-6 sm:py-2.5 sm:text-sm dark:bg-white dark:text-slate-950 dark:hover:bg-slate-100 shadow-[0_4px_12px_rgba(0,0,0,0.06)]">داشبورد</Link>
            {:else}
                <Link href="/login" class="flex size-10 items-center justify-center rounded-full bg-slate-950 text-white transition hover:bg-slate-800 lg:size-auto lg:px-6 lg:py-2.5 lg:text-sm lg:font-bold dark:bg-white dark:text-slate-950 dark:hover:bg-slate-100 shadow-[0_4px_12px_rgba(0,0,0,0.06)]">
                    <LogIn size={18} class="lg:hidden" />
                    <span class="hidden lg:inline">ورود هنرجو</span>
                </Link>
            {/if}

            <button
                onclick={() => mobileMenuOpen = true}
                class="inline-flex size-10 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 lg:hidden dark:text-slate-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-200"
                aria-label="منوی اصلی"
            >
                <Menu size={22} />
            </button>
        </div>
    </div>
</header>

<Sheet.Root bind:open={mobileMenuOpen}>
    <Sheet.Content side="left" class="w-[280px] bg-white p-0 shadow-2xl dark:bg-zinc-950 z-[100]">
        <div class="flex h-full flex-col">
            <div class="flex items-center gap-3 border-b px-5 py-5 dark:border-zinc-800">
                <AppLogoIcon class="size-10" />
                <div>
                    <div class="text-sm font-black text-slate-950 dark:text-white">آکادمی Tekmil</div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400">دوره های آموزش برنامه نویسی</div>
                </div>
            </div>

            <div class="flex-1 space-y-0.5 overflow-y-auto px-3 py-4">
                <Link
                    href="/"
                    onclick={() => mobileMenuOpen = false}
                    class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 transition-all hover:bg-slate-100 hover:pr-5 dark:text-zinc-300 dark:hover:bg-zinc-800"
                >
                    <House size={18} class="shrink-0 text-slate-400 group-hover:text-slate-600 dark:text-zinc-500 dark:group-hover:text-zinc-300" />
                    خانه
                </Link>
                <Link
                    href="/courses"
                    onclick={() => mobileMenuOpen = false}
                    class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 transition-all hover:bg-slate-100 hover:pr-5 dark:text-zinc-300 dark:hover:bg-zinc-800"
                >
                    <BookOpen size={18} class="shrink-0 text-slate-400 group-hover:text-slate-600 dark:text-zinc-500 dark:group-hover:text-zinc-300" />
                    دوره ها
                </Link>
                <Link
                    href="/about-me"
                    onclick={() => mobileMenuOpen = false}
                    class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 transition-all hover:bg-slate-100 hover:pr-5 dark:text-zinc-300 dark:hover:bg-zinc-800"
                >
                    <User size={18} class="shrink-0 text-slate-400 group-hover:text-slate-600 dark:text-zinc-500 dark:group-hover:text-zinc-300" />
                    درباره من
                </Link>

                {#if courseCategories.length > 0}
                    <div class="pt-4 pb-1">
                        <div class="flex items-center gap-2 px-4 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                            <Shapes size={14} />
                            دسته‌بندی دوره‌ها
                        </div>
                        {#each courseCategories as category (category.slug)}
                            <Link
                                href={`/courses?category=${category.slug}`}
                                onclick={() => mobileMenuOpen = false}
                                class="group flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-600 transition-all hover:bg-slate-100 hover:pr-5 dark:text-zinc-400 dark:hover:bg-zinc-800"
                            >
                                <GraduationCap size={16} class="shrink-0 text-slate-400 group-hover:text-slate-600 dark:text-zinc-500 dark:group-hover:text-zinc-300" />
                                {category.title}
                            </Link>
                        {/each}
                    </div>
                {/if}
            </div>

            <div class="border-t p-4 dark:border-zinc-800">
                {#if user}
                    <Link
                        href="/dashboard"
                        onclick={() => mobileMenuOpen = false}
                        class="flex w-full items-center justify-center gap-2 rounded-full bg-slate-950 px-6 py-3 text-sm font-bold text-white transition-all hover:bg-slate-800 hover:shadow-lg dark:bg-white dark:text-slate-950 dark:hover:bg-slate-100"
                    >
                        داشبورد
                    </Link>
                {:else}
                    <Link
                        href="/login"
                        onclick={() => mobileMenuOpen = false}
                        class="flex w-full items-center justify-center gap-2 rounded-full bg-slate-950 px-6 py-3 text-sm font-bold text-white transition-all hover:bg-slate-800 hover:shadow-lg dark:bg-white dark:text-slate-950 dark:hover:bg-slate-100"
                    >
                        ورود هنرجو
                    </Link>
                {/if}
            </div>
        </div>
    </Sheet.Content>
</Sheet.Root>
