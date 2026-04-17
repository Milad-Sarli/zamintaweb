<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import { t } from '@/lib/utils';
    import AuthVisual from '@/components/AuthVisual.svelte';

    interface Props {
        status?: string | null;
        errors?: Record<string, string>;
    }

    let { status = null, errors = {} }: Props = $props();
    let csrfToken = $derived($page.props.csrf_token);

    let activeTab = $state<'otp' | 'password'>('otp');
</script>

<svelte:head>
    <title>{t('ui.login_title')}</title>
</svelte:head>

<div class="flex min-h-screen flex-col-reverse lg:flex-row" dir="rtl">
    <section class="flex w-full items-center justify-center px-4 py-12 lg:w-1/2 lg:px-10 xl:px-16">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center lg:text-right">
                <Link href="/" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
                    <svg class="h-4 w-4 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    بازگشت به صفحه اصلی
                </Link>
                <div class="mt-6 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 dark:bg-sky-500">
                        <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342" /></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900 dark:text-white">آکادمی آموزش وب</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">مسیر یادگیری منظم، تمرین واقعی و پشتیبانی آموزشی</p>
                    </div>
                </div>
                <h1 class="mt-8 text-2xl font-black text-slate-900 dark:text-white">خوش آمدید</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">برای ورود به حساب کاربری خود، روش ورود را انتخاب کنید</p>
            </div>

            <div class="flex gap-2 rounded-2xl bg-slate-100 p-1.5 dark:bg-zinc-800">
                <button
                    type="button"
                    class="flex-1 rounded-xl px-4 py-2.5 text-sm font-bold transition-all {activeTab === 'otp' ? 'bg-white text-slate-900 shadow-sm dark:bg-zinc-950 dark:text-white' : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'}"
                    onclick={() => activeTab = 'otp'}
                >
                    {t('ui.login_otp_tab')}
                </button>
                <button
                    type="button"
                    class="flex-1 rounded-xl px-4 py-2.5 text-sm font-bold transition-all {activeTab === 'password' ? 'bg-white text-slate-900 shadow-sm dark:bg-zinc-950 dark:text-white' : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'}"
                    onclick={() => activeTab = 'password'}
                >
                    {t('ui.login_password_tab')}
                </button>
            </div>

            {#if activeTab === 'otp'}
                <div class="mt-8">
                    <p class="text-sm leading-7 text-slate-600 dark:text-slate-300">{t('ui.login_otp_desc')}</p>
                    <div class="mt-6 grid gap-6">
                        <form method="POST" action="/login/send-otp" class="space-y-4">
                            <input type="hidden" name="_token" value={csrfToken} />
                            <div>
                                <label for="phone" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">{t('ui.phone')}</label>
                                <input id="phone" name="phone" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm transition-all focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20 dark:border-zinc-700 dark:bg-zinc-950" placeholder={t('ui.phone_placeholder')} required />
                            </div>
                            <button type="submit" class="w-full rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition-all hover:-translate-y-0.5 hover:shadow-lg dark:bg-sky-500 dark:hover:shadow-sky-500/20">{t('ui.send_code')}</button>
                        </form>

                        {#if status === 'code-sent'}
                            <form method="POST" action="/login/verify-otp" class="space-y-4 rounded-[1.5rem] border border-sky-200 bg-sky-50 p-5 dark:border-sky-500/20 dark:bg-sky-500/10">
                                <input type="hidden" name="_token" value={csrfToken} />
                                <div>
                                    <label for="verify_phone" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">{t('ui.phone')}</label>
                                    <input id="verify_phone" name="phone" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm transition-all focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20 dark:border-zinc-700 dark:bg-zinc-950" placeholder={t('ui.phone_placeholder')} required />
                                </div>
                                <div>
                                    <label for="otp" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">{t('ui.verification_code')}</label>
                                    <input id="otp" name="otp" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm tracking-[0.5em] transition-all focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20 dark:border-zinc-700 dark:bg-zinc-950" placeholder={t('ui.verify_code_placeholder')} required />
                                </div>
                                <button type="submit" class="w-full rounded-2xl bg-sky-600 px-5 py-3 text-sm font-bold text-white transition-all hover:-translate-y-0.5 hover:shadow-lg">{t('ui.verify_and_login')}</button>
                            </form>
                        {/if}
                    </div>
                    <p class="mt-6 text-center text-xs text-slate-400 dark:text-slate-500">
                        حساب کاربری ندارید؟
                        <Link href="/register" class="font-medium text-sky-700 hover:underline dark:text-sky-300">ثبت نام کنید</Link>
                    </p>
                </div>
            {:else}
                <div class="mt-8">
                    <p class="text-sm leading-7 text-slate-600 dark:text-slate-300">{t('ui.login_password_desc')}</p>
                    <form method="POST" action="/login" class="mt-6 space-y-4">
                        <input type="hidden" name="_token" value={csrfToken} />
                        <div>
                            <label for="email" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">{t('ui.username_or_email')}</label>
                            <input id="email" name="email" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm transition-all focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20 dark:border-zinc-700 dark:bg-zinc-950" placeholder="example@mail.com" required />
                            {#if errors.email}
                                <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{errors.email}</p>
                            {/if}
                        </div>
                        <div>
                            <label for="password" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">{t('ui.password')}</label>
                            <input id="password" name="password" type="password" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm transition-all focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20 dark:border-zinc-700 dark:bg-zinc-950" required />
                            {#if errors.password}
                                <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{errors.password}</p>
                            {/if}
                        </div>
                        <button type="submit" class="w-full rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition-all hover:-translate-y-0.5 hover:shadow-lg dark:bg-sky-500 dark:hover:shadow-sky-500/20">{t('ui.login_with_password')}</button>
                        <div class="flex items-center justify-between gap-4 text-sm">
                            <Link href="/forgot-password" class="font-medium text-sky-700 hover:underline dark:text-sky-300">{t('ui.forgot_password')}</Link>
                            <Link href="/register" class="font-medium text-sky-700 hover:underline dark:text-sky-300">{t('ui.register')}</Link>
                        </div>
                    </form>
                </div>
            {/if}

            <div class="mt-8 border-t border-slate-200 pt-6 dark:border-zinc-800">
                <p class="text-xs text-slate-400 dark:text-slate-500">
                    ادامه دادن به معنای پذیرش                     <span class="font-medium text-sky-700 dark:text-sky-300">قوانین</span> و <span class="font-medium text-sky-700 dark:text-sky-300">حریم خصوصی</span> است.
                </p>
            </div>
        </div>
    </section>

    <div class="relative flex h-64 w-full shrink-0 items-center justify-center overflow-hidden bg-gradient-to-br from-indigo-950 via-slate-900 to-purple-950 lg:h-auto lg:w-1/2">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,_rgba(99,102,241,0.15),_transparent_50%),radial-gradient(ellipse_at_bottom_right,_rgba(236,72,153,0.1),_transparent_50%)]"></div>
        <AuthVisual class="absolute inset-0 z-10 h-full w-full" />
        <div class="absolute inset-0 z-20 bg-gradient-to-t from-black/30 via-transparent to-transparent lg:bg-gradient-to-r lg:from-black/20"></div>
    </div>
</div>
