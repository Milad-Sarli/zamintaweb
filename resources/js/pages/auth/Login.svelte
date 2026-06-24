<script lang="ts">
    import { Link, page, router } from '@inertiajs/svelte';
    import { t } from '@/lib/utils';
    import AuthVisual from '@/components/AuthVisual.svelte';
    import * as Dialog from '@/components/ui/dialog';
    import { Smartphone, ShieldCheck, Clock, ArrowLeft, RotateCw, Loader2, Check } from 'lucide-svelte';

    interface Props {
        status?: string | null;
        phone?: string | null;
        errors?: Record<string, string>;
    }

    let { status = null, phone = null, errors = {} }: Props = $props();
    let csrfToken = $derived($page.props.csrf_token);

    let activeTab = $state<'otp' | 'password'>('otp');
    let isSending = $derived($page.processing);
    let showOtpModal = $state(false);
    let otpValue = $state('');
    let countdown = $state(120);
    let timerInterval: ReturnType<typeof setInterval> | null = null;

    function handleSendOtp(e: Event) {
        e.preventDefault();
        const form = e.currentTarget as HTMLFormElement;
        const data = new FormData(form);
        router.post('/login/send-otp', data);
    }

    function handleVerifyOtp(e: Event) {
        e.preventDefault();
        const form = e.currentTarget as HTMLFormElement;
        const data = new FormData(form);
        router.post('/login/verify-otp', data);
    }

    function handlePasswordLogin(e: Event) {
        e.preventDefault();
        const form = e.currentTarget as HTMLFormElement;
        const data = new FormData(form);
        router.post('/login', data);
    }

    $effect(() => {
        if (status === 'code-sent' && activeTab === 'otp') {
            showOtpModal = true;
            otpValue = '';
            countdown = 120;
            if (timerInterval) clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                countdown -= 1;
                if (countdown <= 0) {
                    if (timerInterval) clearInterval(timerInterval);
                    timerInterval = null;
                }
            }, 1000);
        }
    });

    $effect(() => {
        if (status === 'just_logged_in') {
            showOtpModal = true;
            if (timerInterval) clearInterval(timerInterval);
            timerInterval = null;
            const t = setTimeout(() => {
                router.visit('/');
            }, 2500);
            return () => clearTimeout(t);
        }
    });

    function maskPhone(p: string): string {
        if (!p || p.length < 7) return p;
        return p.slice(0, 4) + '***' + p.slice(-4);
    }

    function formatTime(s: number): string {
        const m = Math.floor(s / 60);
        const sec = s % 60;
        return `${m}:${sec.toString().padStart(2, '0')}`;
    }
</script>

<svelte:head>
    <title>{t('ui.login_title')}</title>
</svelte:head>

<div class="flex min-h-screen flex-col-reverse lg:flex-row" dir="rtl">
    <section class="flex w-full items-center justify-center px-4 py-12 lg:w-1/2 lg:px-10 xl:px-16">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center lg:text-right">
                <Link href="/" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
                    <ArrowLeft size={16} />
                    بازگشت به صفحه اصلی
                </Link>
                <div class="mt-6 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 dark:bg-sky-500">
                        <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342" /></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900 dark:text-white">آکادمی Tekmil</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">دوره های آموزش برنامه نویسی</p>
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
                        <form onsubmit={handleSendOtp} class="space-y-4">
                            <input type="hidden" name="_token" value={csrfToken} />
                            <div>
                                <label for="phone" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">{t('ui.phone')}</label>
                                <input id="phone" name="phone" type="tel" inputmode="numeric" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm transition-all focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20 dark:border-zinc-700 dark:bg-zinc-950" placeholder={t('ui.phone_placeholder')} required />
                            </div>
                            <button type="submit" disabled={isSending} class="w-full rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition-all hover:-translate-y-0.5 hover:shadow-lg disabled:translate-y-0 disabled:opacity-60 disabled:shadow-none dark:bg-sky-500 dark:hover:shadow-sky-500/20">
                                {#if isSending}
                                    <span class="inline-flex items-center gap-2"><Loader2 size={16} class="animate-spin" /> در حال ارسال</span>
                                {:else}
                                    {t('ui.send_code')}
                                {/if}
                            </button>
                        </form>
                    </div>
                    <p class="mt-6 text-center text-xs text-slate-400 dark:text-slate-500">
                        حساب کاربری ندارید؟
                        <Link href="/register" class="font-medium text-sky-700 hover:underline dark:text-sky-300">ثبت نام کنید</Link>
                    </p>
                </div>
            {:else}
                <div class="mt-8">
                    <p class="text-sm leading-7 text-slate-600 dark:text-slate-300">{t('ui.login_password_desc')}</p>
                    <form onsubmit={handlePasswordLogin} class="mt-6 space-y-4">
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
                        <button type="submit" disabled={isSending} class="w-full rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition-all hover:-translate-y-0.5 hover:shadow-lg disabled:translate-y-0 disabled:opacity-60 disabled:shadow-none dark:bg-sky-500 dark:hover:shadow-sky-500/20">
                            {#if isSending}
                                <span class="inline-flex items-center gap-2"><Loader2 size={16} class="animate-spin" /> در حال ورود</span>
                            {:else}
                                {t('ui.login_with_password')}
                            {/if}
                        </button>
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

<Dialog.Root bind:open={showOtpModal}>
    <Dialog.Content class="max-w-sm rounded-2xl p-0 sm:max-w-sm" showCloseButton={false}>
        <div class="px-6 pt-8 pb-6 text-center">
            <div class="mx-auto mb-4 flex size-14 items-center justify-center rounded-2xl bg-sky-100 dark:bg-sky-500/20">
                <ShieldCheck size={28} class="text-sky-600 dark:text-sky-400" />
            </div>
            <h3 class="text-lg font-black text-slate-900 dark:text-white">کد تایید</h3>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                کد تایید ۵ رقمی به شماره
                <span class="font-bold text-slate-700 dark:text-slate-300" dir="ltr">{phone ? maskPhone(phone) : ''}</span>
                ارسال شد
            </p>
        </div>

        {#if status === 'just_logged_in'}
            <div class="px-6 pt-10 pb-10 text-center">
                <div class="mx-auto mb-5 flex size-16 items-center justify-center rounded-full bg-green-100 dark:bg-green-500/20">
                    <Check size={32} class="text-green-600 dark:text-green-400" />
                </div>
                <h3 class="text-xl font-black text-slate-900 dark:text-white">خوش آمدید</h3>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    به آکادمی Tekmil خوش آمدید
                </p>
                <div class="mt-6 flex justify-center">
                    <Loader2 size={20} class="animate-spin text-slate-400" />
                </div>
                <p class="mt-3 text-xs text-slate-400 dark:text-slate-500">در حال انتقال به صفحه اصلی...</p>
            </div>
        {:else}
            <form onsubmit={handleVerifyOtp} class="space-y-5 px-6 pb-8">
                <input type="hidden" name="_token" value={csrfToken} />
                <input type="hidden" name="phone" value={phone || ''} />

                <div>
                    <label for="modal-otp" class="mb-2 block text-center text-sm font-bold text-slate-700 dark:text-slate-200">کد تایید</label>
                    <input
                        id="modal-otp"
                        name="otp"
                        type="text"
                        inputmode="numeric"
                        maxlength={5}
                        bind:value={otpValue}
                        class="w-full rounded-2xl border-2 border-slate-200 bg-white px-4 py-3.5 text-center text-2xl font-bold tracking-[0.5em] transition-all focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20 dark:border-zinc-700 dark:bg-zinc-950"
                        placeholder="-----"
                        required
                    />
                    {#if errors.otp}
                        <p class="mt-1.5 text-center text-xs text-red-600 dark:text-red-400">{errors.otp}</p>
                    {/if}
                </div>

                <button
                    type="submit"
                    disabled={otpValue.length !== 5 || isSending}
                    class="w-full rounded-2xl bg-sky-600 px-5 py-3 text-sm font-bold text-white transition-all hover:-translate-y-0.5 hover:shadow-lg disabled:translate-y-0 disabled:opacity-50 disabled:shadow-none"
                >
                    {#if isSending}
                        <span class="inline-flex items-center gap-2"><Loader2 size={16} class="animate-spin" /> در حال بررسی</span>
                    {:else}
                        تایید و ورود
                    {/if}
                </button>

                <div class="flex items-center justify-center gap-2 text-sm text-slate-400 dark:text-slate-500">
                    {#if countdown > 0}
                        <Clock size={14} />
                        <span>{formatTime(countdown)}</span>
                    {:else}
                        <span>کد منقضی شد.</span>
                        <button type="button" onclick={() => router.visit('/login')} class="inline-flex items-center gap-1 font-medium text-sky-700 hover:underline dark:text-sky-300">
                            <RotateCw size={14} />
                            ارسال مجدد
                        </button>
                    {/if}
                </div>
            </form>
        {/if}
    </Dialog.Content>
</Dialog.Root>
