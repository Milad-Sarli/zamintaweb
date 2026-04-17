<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { onMount } from 'svelte';
    import { gsap } from 'gsap';
    import PublicLayout from '@/layouts/PublicLayout.svelte';
    import { formatPrice, toPersianDigits } from '@/lib/utils';
    import AntigravityBg from '@/components/AntigravityBg.svelte';
    import SplitText from '@/components/SplitText.svelte';
    import ScrollReveal from '@/components/ScrollReveal.svelte';
    import TechMarquee from '@/components/TechMarquee.svelte';

    interface CourseCategory {
        id: number;
        title: string;
        slug: string;
        courses_count?: number;
    }

    interface Course {
        id: number;
        title: string;
        slug: string;
        instructor_name?: string | null;
        level?: string | null;
        duration?: string | null;
        price: number;
        short_description?: string | null;
        cover_image_url?: string | null;
        episodes_count?: number;
        category?: CourseCategory | null;
    }

    interface Props {
        featuredCourses: Course[];
        latestCourses: Course[];
        categories: CourseCategory[];
        stats: {
            courses: number;
            students: number;
            episodes: number;
        };
    }

    let { featuredCourses, latestCourses, categories, stats }: Props = $props();

    let statsEl: HTMLElement;
    let statCounted = $state(false);

    function fadeIn(node: Element, { delay = 0 }: { delay?: number }) {
        gsap.to(node, {
            opacity: 1,
            y: 0,
            duration: 0.8,
            delay,
            ease: 'power3.out',
            willChange: 'transform, opacity',
            force3D: true,
        } as gsap.TweenVars);
        return {};
    }

    onMount(() => {
        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting && !statCounted) {
                    statCounted = true;
                    const counters = entry.target.querySelectorAll('.stat-counter');
                    counters.forEach((el) => {
                        const target = parseInt(el.getAttribute('data-target') || '0', 10);
                        const val = { n: 0 };
                        gsap.to(val, {
                            n: target,
                            duration: 2,
                            ease: 'power2.out',
                            onUpdate() {
                                el.textContent = toPersianDigits(Math.round(val.n));
                            },
                        } as gsap.TweenVars);
                    });
                    observer.disconnect();
                }
            },
            { threshold: 0.3 }
        );
        if (statsEl) observer.observe(statsEl);
        return () => observer.disconnect();
    });
</script>

<svelte:head>
    <title>آکادمی آموزش طراحی و توسعه وب</title>
</svelte:head>

<PublicLayout>
    <section class="relative min-h-[720px] overflow-hidden bg-[radial-gradient(ellipse_at_center,_rgba(99,102,241,0.06),_transparent_75%)] bg-white text-slate-900 transition-colors duration-300 dark:bg-[radial-gradient(ellipse_at_center,_rgba(56,189,248,0.05),_transparent_65%)] dark:bg-zinc-950 dark:text-white">
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-[radial-gradient(#e2e8f0_1.2px,transparent_1.2px)] [background-size:32px_32px] opacity-65 dark:bg-[radial-gradient(#1e293b_1.2px,transparent_1.2px)] dark:opacity-30"></div>
            <AntigravityBg
                count={700}
                magnetRadius={15}
                ringRadius={12}
                waveSpeed={1.5}
                waveAmplitude={4}
                particleSize={1.6}
                autoAnimate={true}
                rotationSpeed={0.15}
                pulseSpeed={3}
                particleVariance={2}
                depthFactor={0.35}
                fieldStrength={12}
                class="h-full w-full"
            />
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-transparent to-white dark:to-zinc-950"></div>
        </div>

        <div class="absolute left-1/2 top-1/3 -z-10 h-96 w-[800px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-blue-500/5 blur-[120px] dark:bg-sky-500/10"></div>

        <div class="relative z-10 mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-28">
            <div class="space-y-8 lg:space-y-10">
                <div class="opacity-0" use:fadeIn={{ delay: 0.15 }}>
                        <span class="inline-flex items-center gap-2 rounded-full border border-slate-200/80 bg-slate-50/60 px-4 py-1.5 text-xs font-semibold tracking-wide text-slate-700 backdrop-blur-md dark:border-white/10 dark:bg-white/[0.06] dark:text-slate-300">
                        <span class="inline-block h-2 w-2 rounded-full bg-blue-600 shadow-[0_0_8px_rgba(37,99,235,0.4)] dark:bg-sky-400 dark:shadow-[0_0_8px_rgba(56,189,248,0.6)]"></span>
                        آموزش پروژه محور فرانت اند و لاراول به زبان فارسی
                    </span>
                </div>

                <div class="space-y-16 lg:space-y-24">
                    <SplitText
                        text="از صفر تا ساخت پروژه واقعی، توسعه وب رو حرفه‌ای یاد بگیر"
                        tag="h1"
                        class="text-4xl font-black leading-[1.5] tracking-tight text-slate-950 dark:text-white sm:text-5xl lg:text-6xl xl:text-7xl"
                        splitType="words"
                        y={40}
                        duration={1}
                        stagger={0.04}
                        rootMargin="-120px"
                    />
                    <div class="space-y-2">
                        <SplitText
                            text="دسترسی به دوره های ویدیویی، تمرین های پروژه محور، پشتیبانی آموزشی"
                            tag="p"
                            class="max-w-4xl text-base leading-8 text-slate-600 dark:text-slate-300 sm:text-lg sm:leading-8"
                            splitType="words"
                            y={15}
                            duration={0.7}
                            stagger={0.018}
                            delay={0.5}
                            rootMargin="-120px"
                        />
                        <SplitText
                            text="و مسیر یادگیری منظم — همه در یک پلتفرم"
                            tag="p"
                            class="max-w-4xl text-base leading-8 text-slate-600 dark:text-slate-300 sm:text-lg sm:leading-8"
                            splitType="words"
                            y={15}
                            duration={0.7}
                            stagger={0.018}
                            delay={0.55}
                            rootMargin="-120px"
                        />
                    </div>
                </div>

                <div class="flex flex-wrap gap-4 opacity-0" use:fadeIn={{ delay: 1.1 }}>
                    <Link href="/courses" class="group relative inline-flex items-center gap-2.5 overflow-hidden rounded-full bg-slate-950 px-7 py-3 text-sm font-bold text-white shadow-lg shadow-slate-950/10 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-slate-950/20 dark:bg-white dark:text-slate-950 dark:shadow-sky-500/10 dark:hover:shadow-sky-500/20">
                        <span class="relative z-10">مشاهده همه دوره ها</span>
                        <svg class="relative z-10 h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-white/0 via-white/10 to-white/0 transition-transform duration-500 group-hover:translate-x-full"></div>
                    </Link>
                    <Link href="/login" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/80 px-7 py-3 text-sm font-bold text-slate-700 shadow-sm backdrop-blur-md transition-all duration-300 hover:border-slate-300 hover:bg-slate-50 hover:-translate-y-0.5 dark:border-white/15 dark:bg-white/[0.04] dark:text-white dark:hover:border-white/25 dark:hover:bg-white/10">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        ورود یا ثبت نام هنرجو
                    </Link>
                </div>

                <div bind:this={statsEl} class="grid gap-px overflow-hidden rounded-[2rem] border border-slate-200/80 bg-white/60 shadow-[0_8px_30px_rgb(0,0,0,0.015)] backdrop-blur-md dark:border-white/10 dark:bg-white/[0.05] sm:grid-cols-3">
                    {#each [ { label: 'دوره منتشر شده', target: stats.courses }, { label: 'جلسه آموزشی', target: stats.episodes }, { label: 'ثبت نام تایید شده', target: stats.students } ] as stat}
                        <div class="px-6 py-6 text-center transition-colors hover:bg-slate-50/50 dark:hover:bg-white/[0.02] sm:px-8">
                            <div class="stat-counter text-4xl font-black tracking-tight text-slate-950 dark:text-white" data-target={stat.target}>۰</div>
                            <div class="mt-2 text-sm font-semibold text-slate-500 dark:text-slate-400">{stat.label}</div>
                        </div>
                    {/each}
                </div>
            </div>
        </div>
    </section>

    <TechMarquee />

    <ScrollReveal y={60} duration={0.8}>
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-black text-slate-900 dark:text-white">دسته بندی های آموزشی</h2>
                    <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">ساختار آموزشی خود را براساس مسیرهای واقعی یادگیری مدیریت کنید.</p>
                </div>
                <Link href="/courses" class="text-sm font-bold text-sky-700 dark:text-sky-300">آرشیو دوره ها</Link>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-3">
                {#each categories as category (category.id)}
                    <Link href={`/courses?category=${category.slug}`} class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="text-sm text-sky-700 dark:text-sky-300">{toPersianDigits(category.courses_count ?? 0)} دوره</div>
                        <h3 class="mt-3 text-xl font-bold text-slate-900 dark:text-white">{category.title}</h3>
                        <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">دوره های مرتبط با این مسیر آموزشی را ببینید.</p>
                    </Link>
                {/each}
            </div>
        </section>
    </ScrollReveal>

    <ScrollReveal y={60} duration={0.8} delay={0.1}>
        <section class="bg-slate-100 py-16 dark:bg-zinc-900/60">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-3xl font-black text-slate-900 dark:text-white">دوره های ویژه</h2>
                        <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">دوره هایی که برای شروع یا ارتقای مهارت پیشنهاد می شوند.</p>
                    </div>
                </div>

                <div class="mt-8 grid gap-6 lg:grid-cols-3">
                    {#each featuredCourses as course (course.id)}
                        <article class="rounded-[2rem] bg-white p-6 shadow-sm dark:bg-zinc-950">
                            <div class="flex items-center justify-between gap-3 text-sm text-slate-500 dark:text-slate-400">
                                <span>{course.category?.title ?? 'بدون دسته بندی'}</span>
                                <span>{toPersianDigits(course.episodes_count ?? 0)} جلسه</span>
                            </div>
                            <h3 class="mt-4 text-2xl font-black text-slate-900 dark:text-white">{course.title}</h3>
                            <p class="mt-4 text-sm leading-7 text-slate-600 dark:text-slate-300">{course.short_description ?? 'برای این دوره هنوز توضیح کوتاه ثبت نشده است.'}</p>
                            <div class="mt-6 flex items-center justify-between">
                                <div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">شهریه دوره</div>
                                    <div class="mt-1 text-xl font-black text-slate-900 dark:text-white">{formatPrice(course.price)} تومان</div>
                                </div>
                                <Link href={`/courses/${course.slug}`} class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white dark:bg-sky-500">
                                    جزئیات دوره
                                </Link>
                            </div>
                        </article>
                    {/each}
                </div>
            </div>
        </section>
    </ScrollReveal>

    <ScrollReveal y={60} duration={0.8} delay={0.2}>
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-black text-slate-900 dark:text-white">جدیدترین دوره ها</h2>
                    <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">لیست کامل دوره های قابل ثبت نام همراه با دسته بندی، مدرس و تعداد جلسات.</p>
                </div>
            </div>

            <div class="mt-8 overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
                <div class="grid gap-px bg-slate-200 dark:bg-zinc-800 md:grid-cols-2 xl:grid-cols-3">
                    {#each latestCourses as course (course.id)}
                        <article class="bg-white p-6 dark:bg-zinc-950">
                            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                                <span>{course.instructor_name ?? 'مدرس دوره ثبت نشده'}</span>
                                <span>{course.level ?? 'سطح عمومی'}</span>
                            </div>
                            <h3 class="mt-4 text-xl font-bold text-slate-900 dark:text-white">{course.title}</h3>
                            <p class="mt-4 text-sm leading-7 text-slate-600 dark:text-slate-300">{course.short_description ?? 'توضیحات این دوره به زودی تکمیل می شود.'}</p>
                            <div class="mt-6 flex items-center justify-between">
                                <div class="text-sm font-bold text-slate-900 dark:text-white">{formatPrice(course.price)} تومان</div>
                                <Link href={`/courses/${course.slug}`} class="text-sm font-bold text-sky-700 dark:text-sky-300">مشاهده دوره</Link>
                            </div>
                        </article>
                    {/each}
                </div>
            </div>
        </section>
    </ScrollReveal>
</PublicLayout>
