<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { t, toPersianDigits } from '@/lib/utils';
    import ScrollReveal from '@/components/ScrollReveal.svelte';

    interface Episode {
        id: number;
        title: string;
        duration?: string | null;
        video_url?: string | null;
    }

    interface Course {
        title: string;
        slug: string;
        price: number;
        category?: { title: string } | null;
        episodes?: Episode[];
    }

    interface Enrollment {
        course: Course;
        status: string;
        admin_note?: string | null;
        approved_at?: string | null;
        receipt_reference?: string | null;
    }

    interface Props {
        approvedEnrollments: Enrollment[];
        pendingEnrollments: Enrollment[];
        rejectedEnrollments: Enrollment[];
        dashboardStats: {
            approvedCourses: number;
            pendingRequests: number;
            availableEpisodes: number;
        };
    }

    let { approvedEnrollments, pendingEnrollments, rejectedEnrollments, dashboardStats }: Props = $props();

    let activeSection = $derived($page.url.split('ui.#')[1] || 'overview');

    const sections = [
        { id: 'overview', label: t('ui.overview') },
        { id: 'courses', label: t('ui.active_courses') },
        { id: 'videos', label: t('ui.educational_videos') },
        { id: 'payments', label: t('ui.payment_status') },
    ];
</script>

<svelte:head>
    <title>{t('ui.dashboard')}</title>
</svelte:head>

<AppLayout>
    <div class="space-y-8 p-4 md:p-8" dir="rtl">
        <ScrollReveal y={30} duration={0.7}>
            <section class="rounded-[2rem] bg-[linear-gradient(135deg,_#0f172a,_#1d4ed8)] p-8 text-white">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black">{t('ui.dashboard')}</h1>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-200">
                        {t('ui.dashboard_desc')}
                    </p>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-2">
                {#each sections as section (section.id)}
                    <Link
                        href={`/dashboard#${section.id}`}
                        class="rounded-xl px-4 py-2 text-sm font-bold transition-colors {activeSection === section.id
                            ? 'bg-white text-slate-900'
                            : 'bg-white/10 text-slate-200 hover:bg-white/20'}"
                    >
                        {section.label}
                    </Link>
                {/each}
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-3">
                <div class="rounded-2xl bg-white/10 p-5">
                    <div class="text-sm text-slate-200">{t('ui.active_courses_count')}</div>
                    <div class="mt-2 text-3xl font-black">{toPersianDigits(dashboardStats.approvedCourses)}</div>
                </div>
                <div class="rounded-2xl bg-white/10 p-5">
                    <div class="text-sm text-slate-200">{t('ui.pending_requests')}</div>
                    <div class="mt-2 text-3xl font-black">{toPersianDigits(dashboardStats.pendingRequests)}</div>
                </div>
                <div class="rounded-2xl bg-white/10 p-5">
                    <div class="text-sm text-slate-200">{t('ui.available_videos')}</div>
                    <div class="mt-2 text-3xl font-black">{toPersianDigits(dashboardStats.availableEpisodes)}</div>
                </div>
            </div>
        </section>
    </ScrollReveal>

        {#if activeSection === 'overview'}
            <ScrollReveal y={40} duration={0.7} delay={0.1}>
            <section class="grid gap-8 xl:grid-cols-[1.2fr_0.8fr]">
                <div class="space-y-6">
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
                        <div class="flex items-center justify-between gap-4">
                            <h2 class="text-2xl font-black text-slate-900 dark:text-white">{t('ui.your_active_courses')}</h2>
                            <Link href="/courses" class="text-sm font-bold text-sky-700 dark:text-sky-300">{t('ui.view_all_courses')}</Link>
                        </div>
                        <div class="mt-6 space-y-5">
                            {#each approvedEnrollments.slice(0, 3) as enrollment (enrollment.course.slug)}
                                <article class="rounded-[1.5rem] border border-slate-200 p-5 dark:border-zinc-800">
                                    <div class="flex flex-wrap items-start justify-between gap-4">
                                        <div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{enrollment.course.category?.title ?? t('ui.no_category')}</div>
                                            <h3 class="mt-2 text-xl font-bold text-slate-900 dark:text-white">{enrollment.course.title}</h3>
                                        </div>
                                        <div class="text-sm font-bold text-emerald-700 dark:text-emerald-300">{t('ui.active')}</div>
                                    </div>
                                </article>
                            {:else}
                                <div class="rounded-2xl bg-slate-50 p-5 text-sm leading-7 text-slate-600 dark:bg-zinc-900 dark:text-slate-300">
                                    {t('ui.no_active_courses_desc')}
                                </div>
                            {/each}
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-[2rem] border border-amber-200 bg-amber-50 p-6 shadow-sm dark:border-amber-500/20 dark:bg-amber-500/10">
                        <h2 class="text-xl font-black text-amber-800 dark:text-amber-200">{t('ui.pending_requests_title')}</h2>
                        <div class="mt-5 space-y-4 text-sm">
                            {#each pendingEnrollments as enrollment (enrollment.course.slug)}
                                <div class="rounded-2xl bg-white/80 p-4 dark:bg-zinc-950/50">
                                    <div class="font-bold text-slate-900 dark:text-white">{enrollment.course.title}</div>
                                    {#if enrollment.receipt_reference}
                                        <div class="mt-2 text-slate-600 dark:text-slate-300">{t('ui.tracking_number')}: {enrollment.receipt_reference}</div>
                                    {/if}
                                    <div class="mt-3 text-amber-700 dark:text-amber-300">{t('ui.pending_request_desc')}</div>
                                </div>
                            {:else}
                                <div class="text-slate-600 dark:text-slate-300">{t('ui.no_pending_requests')}</div>
                            {/each}
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
                        <h2 class="text-xl font-black text-slate-900 dark:text-white">{t('ui.activation_guide_title')}</h2>
                        <ul class="mt-5 space-y-3 text-sm leading-7 text-slate-600 dark:text-slate-300">
                            <li>{t('ui.activation_step_1')}</li>
                            <li>{t('ui.activation_step_2')}</li>
                            <li>{t('ui.activation_step_3')}</li>
                        </ul>
                    </div>
                </div>
            </section>
        </ScrollReveal>
        {/if}

        {#if activeSection === 'courses'}
            <ScrollReveal y={40} duration={0.7} delay={0.1}>
            <section>
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white">{t('ui.your_active_courses')}</h2>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{t('ui.dashboard_courses_desc')}</p>
                    <div class="mt-6 space-y-5">
                        {#each approvedEnrollments as enrollment (enrollment.course.slug)}
                            <article class="rounded-[1.5rem] border border-slate-200 p-5 dark:border-zinc-800">
                                <div class="flex flex-wrap items-start justify-between gap-4">
                                    <div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">{enrollment.course.category?.title ?? t('ui.no_category')}</div>
                                        <h3 class="mt-2 text-xl font-bold text-slate-900 dark:text-white">{enrollment.course.title}</h3>
                                    </div>
                                    <div class="text-sm font-bold text-emerald-700 dark:text-emerald-300">{t('ui.active')}</div>
                                </div>
                                <div class="mt-5 grid gap-3">
                                    {#each enrollment.course.episodes ?? [] as episode, index (episode.id)}
                                        <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-slate-50 px-4 py-3 text-sm dark:bg-zinc-900">
                                            <div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400">{t('ui.session')} {toPersianDigits(index + 1)}</div>
                                                <div class="mt-1 font-bold text-slate-900 dark:text-white">{episode.title}</div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                {#if episode.duration}
                                                    <span class="text-xs text-slate-500 dark:text-slate-400">{episode.duration}</span>
                                                {/if}
                                                {#if episode.video_url}
                                                    <!-- eslint-disable-next-line svelte/no-navigation-without-resolve -->
                                                    <a href={episode.video_url} target="_blank" rel="noopener noreferrer" class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white dark:bg-sky-500">{t('ui.watch_video')}</a>
                                                {/if}
                                            </div>
                                        </div>
                                    {/each}
                                </div>
                            </article>
                        {:else}
                            <div class="rounded-2xl bg-slate-50 p-5 text-sm leading-7 text-slate-600 dark:bg-zinc-900 dark:text-slate-300">
                                {t('ui.no_active_courses_desc')}
                            </div>
                        {/each}
                    </div>
                </div>
            </section>
        </ScrollReveal>
        {/if}

        {#if activeSection === 'videos'}
            <ScrollReveal y={40} duration={0.7} delay={0.1}>
            <section>
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white">{t('ui.educational_videos')}</h2>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{t('ui.videos_desc')}</p>
                    <div class="mt-6 space-y-6">
                        {#each approvedEnrollments as enrollment (enrollment.course.slug)}
                            {#if enrollment.course.episodes && enrollment.course.episodes.length > 0}
                                <div>
                                    <h3 class="mb-3 text-lg font-bold text-slate-900 dark:text-white">{enrollment.course.title}</h3>
                                    <div class="grid gap-3">
                                        {#each enrollment.course.episodes as episode, index (episode.id)}
                                            <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-slate-50 px-4 py-3 text-sm dark:bg-zinc-900">
                                                <div class="flex items-center gap-3">
                                                    <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-sky-100 text-xs font-bold text-sky-700 dark:bg-sky-900 dark:text-sky-300">{toPersianDigits(index + 1)}</span>
                                                    <div>
                                                        <div class="font-bold text-slate-900 dark:text-white">{episode.title}</div>
                                                        {#if episode.duration}
                                                            <div class="text-xs text-slate-500 dark:text-slate-400">{episode.duration}</div>
                                                        {/if}
                                                    </div>
                                                </div>
                                                {#if episode.video_url}
                                                    <!-- eslint-disable-next-line svelte/no-navigation-without-resolve -->
                                                    <a href={episode.video_url} target="_blank" rel="noopener noreferrer" class="rounded-xl bg-sky-500 px-4 py-2 text-xs font-bold text-white hover:bg-sky-600">{t('ui.watch_video')}</a>
                                                {/if}
                                            </div>
                                        {/each}
                                    </div>
                                </div>
                            {/if}
                        {:else}
                            <div class="rounded-2xl bg-slate-50 p-5 text-sm leading-7 text-slate-600 dark:bg-zinc-900 dark:text-slate-300">
                                {t('ui.no_active_courses_desc')}
                            </div>
                        {/each}
                    </div>
                </div>
            </section>
        </ScrollReveal>
        {/if}

        {#if activeSection === 'payments'}
            <ScrollReveal y={40} duration={0.7} delay={0.1}>
            <section class="grid gap-8 xl:grid-cols-2">
                <div class="rounded-[2rem] border border-amber-200 bg-amber-50 p-6 shadow-sm dark:border-amber-500/20 dark:bg-amber-500/10">
                    <h2 class="text-xl font-black text-amber-800 dark:text-amber-200">{t('ui.pending_requests_title')}</h2>
                    <p class="mt-2 text-sm text-amber-700 dark:text-amber-300">{t('ui.dashboard_payments_desc')}</p>
                    <div class="mt-5 space-y-4 text-sm">
                        {#each pendingEnrollments as enrollment (enrollment.course.slug)}
                            <div class="rounded-2xl bg-white/80 p-4 dark:bg-zinc-950/50">
                                <div class="font-bold text-slate-900 dark:text-white">{enrollment.course.title}</div>
                                <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">{toPersianDigits(enrollment.course.price)} تومان</div>
                                {#if enrollment.receipt_reference}
                                    <div class="mt-2 text-slate-600 dark:text-slate-300">{t('ui.tracking_number')}: {enrollment.receipt_reference}</div>
                                {/if}
                                <div class="mt-3 text-amber-700 dark:text-amber-300">{t('ui.pending_request_desc')}</div>
                            </div>
                        {:else}
                            <div class="text-slate-600 dark:text-slate-300">{t('ui.no_pending_requests')}</div>
                        {/each}
                    </div>
                </div>

                <div class="rounded-[2rem] border border-rose-200 bg-rose-50 p-6 shadow-sm dark:border-rose-500/20 dark:bg-rose-500/10">
                    <h2 class="text-xl font-black text-rose-800 dark:text-rose-200">{t('ui.rejected_requests_title')}</h2>
                    <p class="mt-2 text-sm text-rose-700 dark:text-rose-300">{t('ui.dashboard_payments_desc')}</p>
                    <div class="mt-5 space-y-4 text-sm">
                        {#each rejectedEnrollments as enrollment (enrollment.course.slug)}
                            <div class="rounded-2xl bg-white/80 p-4 dark:bg-zinc-950/50">
                                <div class="font-bold text-slate-900 dark:text-white">{enrollment.course.title}</div>
                                <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">{toPersianDigits(enrollment.course.price)} تومان</div>
                                <div class="mt-3 leading-7 text-rose-700 dark:text-rose-300">{enrollment.admin_note ?? t('ui.rejected_default_note')}</div>
                                <Link href={`/courses/${enrollment.course.slug}`} class="mt-4 inline-flex font-bold text-rose-800 dark:text-rose-200">{t('ui.edit_and_resubmit')}</Link>
                            </div>
                        {:else}
                            <div class="text-slate-600 dark:text-slate-300">{t('ui.no_rejected_requests')}</div>
                        {/each}
                    </div>
                </div>
            </section>
        </ScrollReveal>
        {/if}
    </div>
</AppLayout>