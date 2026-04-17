<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import PublicLayout from '@/layouts/PublicLayout.svelte';
    import { formatPrice, toPersianDigits } from '@/lib/utils';
    import { JalaliDatePicker } from '@/components/ui/jalali-date-picker';
    import ScrollReveal from '@/components/ScrollReveal.svelte';

    interface Episode {
        id: number;
        title: string;
        description?: string | null;
        duration?: string | null;
        video_url?: string | null;
        is_preview: boolean;
    }

    interface Course {
        title: string;
        slug: string;
        instructor_name?: string | null;
        level?: string | null;
        duration?: string | null;
        price: number;
        short_description?: string | null;
        description?: string | null;
        category?: { title: string } | null;
        episodes: Episode[];
    }

    interface Enrollment {
        status: string;
        amount?: number | null;
        receipt_reference?: string | null;
        admin_note?: string | null;
        approved_at?: string | null;
    }

    interface Props {
        course: Course;
        enrollment?: Enrollment | null;
        relatedCourses: Course[];
    }

    let { course, enrollment = null, relatedCourses }: Props = $props();
    let user = $derived($page.props.auth.user);
    let csrfToken = $derived($page.props.csrf_token);
    let flash = $derived($page.props.flash || {});
    let paidAt = $state('');
</script>

<svelte:head>
    <title>{course.title}</title>
</svelte:head>

<PublicLayout>
    <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
            <div class="space-y-8">
                <ScrollReveal y={40} duration={0.8}>
                    <div class="rounded-[2rem] bg-slate-900 p-8 text-white">
                        <div class="flex flex-wrap items-center gap-3 text-sm text-sky-200">
                            <span>{course.category?.title ?? 'بدون دسته بندی'}</span>
                            <span>•</span>
                            <span>{course.level ?? 'سطح عمومی'}</span>
                            <span>•</span>
                            <span>{toPersianDigits(course.episodes.length)} جلسه</span>
                        </div>
                        <h1 class="mt-5 text-4xl font-black leading-tight">{course.title}</h1>
                        <p class="mt-5 text-sm leading-8 text-slate-200">{course.short_description ?? 'برای این دوره هنوز توضیح کوتاه نوشته نشده است.'}</p>
                        <div class="mt-8 grid gap-4 sm:grid-cols-3">
                            <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                                <div class="text-xs text-slate-300">مدرس</div>
                                <div class="mt-2 font-bold">{course.instructor_name ?? 'ثبت نشده'}</div>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                                <div class="text-xs text-slate-300">مدت دوره</div>
                                <div class="mt-2 font-bold">{course.duration ?? 'به زودی'}</div>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                                <div class="text-xs text-slate-300">هزینه ثبت نام</div>
                                <div class="mt-2 font-bold">{formatPrice(course.price)} تومان</div>
                            </div>
                        </div>
                    </div>
                </ScrollReveal>

                <ScrollReveal y={30} duration={0.7} delay={0.1}>
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white">توضیحات کامل دوره</h2>
                        <div class="prose prose-slate mt-6 max-w-none leading-8 dark:prose-invert">
                            <!-- eslint-disable-next-line svelte/no-at-html-tags -->
                            {@html course.description ?? '<p>توضیحات کامل این دوره هنوز تکمیل نشده است.</p>'}
                        </div>
                    </div>
                </ScrollReveal>

                <ScrollReveal y={30} duration={0.7} delay={0.2}>
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
                        <div class="flex items-center justify-between gap-4">
                            <h2 class="text-2xl font-black text-slate-900 dark:text-white">جلسات دوره</h2>
                            <span class="text-sm text-slate-500 dark:text-slate-400">{toPersianDigits(course.episodes.length)} جلسه</span>
                        </div>
                        <div class="mt-6 space-y-4">
                            {#each course.episodes as episode, index (episode.id)}
                                <div class="rounded-2xl border border-slate-200 p-5 dark:border-zinc-800">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">جلسه {toPersianDigits(index + 1)}</div>
                                            <h3 class="mt-2 text-lg font-bold text-slate-900 dark:text-white">{episode.title}</h3>
                                        </div>
                                        <div class="flex flex-wrap gap-2 text-xs">
                                            {#if episode.duration}
                                                <span class="rounded-full bg-slate-100 px-3 py-1 dark:bg-zinc-800">{episode.duration}</span>
                                            {/if}
                                            {#if episode.is_preview}
                                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">پیش نمایش رایگان</span>
                                            {/if}
                                        </div>
                                    </div>
                                    {#if episode.description}
                                        <p class="mt-4 text-sm leading-7 text-slate-600 dark:text-slate-300">{episode.description}</p>
                                    {/if}
                                    {#if episode.is_preview && episode.video_url}
                                        <!-- eslint-disable-next-line svelte/no-navigation-without-resolve -->
                                        <a href={episode.video_url} target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex text-sm font-bold text-sky-700 dark:text-sky-300">مشاهده ویدیوی نمونه</a>
                                    {/if}
                                </div>
                            {/each}
                        </div>
                    </div>
                </ScrollReveal>
            </div>

            <aside class="space-y-6">
                <ScrollReveal y={30} duration={0.7} delay={0.15}>
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
                        <div class="text-sm text-slate-500 dark:text-slate-400">شهریه این دوره</div>
                        <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{formatPrice(course.price)} تومان</div>
                        <p class="mt-4 text-sm leading-7 text-slate-600 dark:text-slate-300">
                            پس از واریز مبلغ، تصویر فیش و شماره پیگیری را از همین بخش ارسال کنید تا ادمین دسترسی دوره را فعال کند.
                        </p>

                        {#if flash.success}
                            <div class="mt-5 rounded-2xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">{flash.success}</div>
                        {/if}

                        {#if enrollment}
                            <div class={`mt-5 rounded-2xl px-4 py-4 text-sm ${enrollment.status === 'approved' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : enrollment.status === 'rejected' ? 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300'}`}>
                                {#if enrollment.status === 'approved'}
                                    دسترسی این دوره برای شما فعال شده است.
                                {:else if enrollment.status === 'rejected'}
                                    درخواست قبلی شما رد شده است. لطفا فیش جدید یا توضیح کامل تر ارسال کنید.
                                {:else}
                                    درخواست شما ثبت شده و در انتظار بررسی ادمین است.
                                {/if}
                                {#if enrollment.admin_note}
                                    <div class="mt-3 border-t border-current/20 pt-3 text-xs leading-6">یادداشت ادمین: {enrollment.admin_note}</div>
                                {/if}
                            </div>
                        {/if}

                        {#if user}
                            <form method="POST" action={`/courses/${course.slug}/enrollments`} enctype="multipart/form-data" class="mt-6 space-y-4">
                                <input type="hidden" name="_token" value={csrfToken} />
                                <div>
                                    <label for="amount" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">مبلغ پرداختی</label>
                                    <input id="amount" name="amount" type="number" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-900" placeholder="مثلا ۳۵۰۰۰۰۰" />
                                </div>
                                <div>
                                    <label for="receipt_reference" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">شماره پیگیری</label>
                                    <input id="receipt_reference" name="receipt_reference" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-900" placeholder="شماره مرجع یا کد رهگیری" />
                                </div>
                                <div>
                                    <label for="paid_at" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">تاریخ پرداخت</label>
                                    <input id="paid_at" name="paid_at" type="hidden" value={paidAt} />
                                    <JalaliDatePicker bind:value={paidAt} />
                                </div>
                                <div>
                                    <label for="receipt_image" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">تصویر فیش</label>
                                    <input id="receipt_image" name="receipt_image" type="file" accept="image/*" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-900" required />
                                </div>
                                <div>
                                    <label for="user_note" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">توضیحات تکمیلی</label>
                                    <textarea id="user_note" name="user_note" rows="4" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-900" placeholder="اگر نکته ای برای بررسی دارید اینجا بنویسید."></textarea>
                                </div>
                                <button type="submit" class="w-full rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white dark:bg-sky-500">
                                    ارسال فیش و درخواست فعال سازی
                                </button>
                            </form>
                        {:else}
                            <Link href="/login" class="mt-6 inline-flex w-full justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white dark:bg-sky-500">
                                برای ثبت درخواست ابتدا وارد شوید
                            </Link>
                        {/if}
                    </div>
                </ScrollReveal>

                <ScrollReveal y={30} duration={0.7} delay={0.25}>
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
                        <h3 class="text-xl font-black text-slate-900 dark:text-white">دوره های مرتبط</h3>
                        <div class="mt-5 space-y-4">
                            {#each relatedCourses as related (related.id)}
                                <Link href={`/courses/${related.slug}`} class="block rounded-2xl border border-slate-200 p-4 transition hover:border-sky-400 dark:border-zinc-800">
                                    <div class="text-xs text-slate-500 dark:text-slate-400">{related.category?.title ?? 'بدون دسته بندی'}</div>
                                    <div class="mt-2 font-bold text-slate-900 dark:text-white">{related.title}</div>
                                    <div class="mt-3 text-sm text-sky-700 dark:text-sky-300">{formatPrice(related.price)} تومان</div>
                                </Link>
                            {/each}
                        </div>
                    </div>
                </ScrollReveal>
            </aside>
        </div>
    </section>
</PublicLayout>
