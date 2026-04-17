<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import PublicLayout from '@/layouts/PublicLayout.svelte';
    import { formatPrice, toPersianDigits } from '@/lib/utils';
    import ScrollReveal from '@/components/ScrollReveal.svelte';

    interface Category {
        id: number;
        title: string;
        slug: string;
    }

    interface Course {
        id: number;
        title: string;
        slug: string;
        instructor_name?: string | null;
        level?: string | null;
        short_description?: string | null;
        price: number;
        episodes_count?: number;
        category?: Category | null;
    }

    interface Pagination<T> {
        data: T[];
        links: { url: string | null; label: string; active: boolean }[];
    }

    interface Props {
        courses: Pagination<Course>;
        categories: Category[];
        selectedCategory?: string;
    }

    let { courses, categories, selectedCategory = '' }: Props = $props();
</script>

<svelte:head>
    <title>دوره های آموزشی</title>
</svelte:head>

<PublicLayout>
    <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
    <ScrollReveal y={40} duration={0.8}>
        <div class="rounded-[2rem] bg-slate-900 px-8 py-10 text-white">
            <div class="max-w-3xl">
                <h1 class="text-4xl font-black">آرشیو دوره های آموزشی</h1>
                <p class="mt-4 text-sm leading-7 text-slate-200">دوره ها را بر اساس دسته بندی انتخاب کنید و بعد از پرداخت، فیش را برای فعال سازی دستی ارسال کنید.</p>
            </div>
        </div>
    </ScrollReveal>

    <ScrollReveal y={20} duration={0.6} delay={0.15}>
        <div class="mt-8 flex flex-wrap gap-3">
            <Link href="/courses" class={`rounded-full px-5 py-2 text-sm font-bold transition ${selectedCategory === '' ? 'bg-sky-600 text-white' : 'bg-slate-100 text-slate-700 dark:bg-zinc-900 dark:text-slate-200'}`}>
                همه دوره ها
            </Link>
            {#each categories as category (category.id)}
                <Link href={`/courses?category=${category.slug}`} class={`rounded-full px-5 py-2 text-sm font-bold transition ${selectedCategory === category.slug ? 'bg-sky-600 text-white' : 'bg-slate-100 text-slate-700 dark:bg-zinc-900 dark:text-slate-200'}`}>
                    {category.title}
                </Link>
            {/each}
        </div>
    </ScrollReveal>

    <ScrollReveal y={50} duration={0.7} delay={0.3}>
        <div class="mt-10 grid gap-6 lg:grid-cols-3">
            {#each courses.data as course (course.id)}
                <article class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
                    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>{course.category?.title ?? 'بدون دسته بندی'}</span>
                        <span>{toPersianDigits(course.episodes_count ?? 0)} جلسه</span>
                    </div>
                    <h2 class="mt-4 text-2xl font-black text-slate-900 dark:text-white">{course.title}</h2>
                    <p class="mt-4 text-sm leading-7 text-slate-600 dark:text-slate-300">{course.short_description ?? 'برای این دوره توضیح کوتاه ثبت نشده است.'}</p>
                    <div class="mt-6 space-y-2 text-sm text-slate-500 dark:text-slate-400">
                        <div>مدرس: {course.instructor_name ?? 'ثبت نشده'}</div>
                        <div>سطح: {course.level ?? 'عمومی'}</div>
                    </div>
                    <div class="mt-6 flex items-center justify-between">
                        <div class="text-lg font-black text-slate-900 dark:text-white">{formatPrice(course.price)} تومان</div>
                        <Link href={`/courses/${course.slug}`} class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-bold text-white dark:bg-sky-500">جزئیات</Link>
                    </div>
                </article>
            {/each}
        </div>
    </ScrollReveal>

    <ScrollReveal y={20} duration={0.5} delay={0.5}>
        <div class="mt-10 flex flex-wrap gap-3">
            {#each courses.links as link (link.label)}
                {#if link.url}
                    <Link href={link.url} class={`rounded-xl px-4 py-2 text-sm font-bold ${link.active ? 'bg-sky-600 text-white' : 'bg-slate-100 text-slate-700 dark:bg-zinc-900 dark:text-slate-200'}`}>
                        {link.label}
                    </Link>
                {/if}
            {/each}
        </div>
    </ScrollReveal>
    </section>
</PublicLayout>
