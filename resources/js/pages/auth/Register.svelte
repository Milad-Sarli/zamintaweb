<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import PublicLayout from '@/layouts/PublicLayout.svelte';
    import { t } from '@/lib/utils';

    interface Props {
        errors?: Record<string, string>;
    }

    let { errors = {} }: Props = $props();
    let csrfToken = $derived($page.props.csrf_token);
</script>

<svelte:head>
    <title>{t('ui.register_title')}</title>
</svelte:head>

<PublicLayout>
    <section class="mx-auto max-w-xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
            <div class="text-center">
                <h1 class="text-3xl font-black text-slate-900 dark:text-white">{t('ui.register_title')}</h1>
                <p class="mt-4 text-sm leading-7 text-slate-600 dark:text-slate-300">{t('ui.register_desc')}</p>
            </div>

            <form method="POST" action="/register" class="mt-8 space-y-4 rounded-[1.5rem] bg-slate-50 p-5 dark:bg-zinc-900">
                <input type="hidden" name="_token" value={csrfToken} />

                <div>
                    <label for="name" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">{t('ui.full_name')}</label>
                    <input id="name" name="name" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-950" required />
                    {#if errors.name}
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{errors.name}</p>
                    {/if}
                </div>

                <div>
                    <label for="username" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">{t('ui.username')}</label>
                    <input id="username" name="username" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-950" />
                    <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">{t('ui.username_helper')}</p>
                    {#if errors.username}
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{errors.username}</p>
                    {/if}
                </div>

                <div>
                    <label for="email" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">{t('ui.email')}</label>
                    <input id="email" name="email" type="email" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-950" required />
                    {#if errors.email}
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{errors.email}</p>
                    {/if}
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">{t('ui.password')}</label>
                    <input id="password" name="password" type="password" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-950" required />
                    {#if errors.password}
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{errors.password}</p>
                    {/if}
                </div>

                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">{t('ui.password_confirmation')}</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-950" required />
                </div>

                <button type="submit" class="w-full rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white dark:bg-sky-500">{t('ui.register')}</button>

                <div class="text-center text-sm text-slate-600 dark:text-slate-300">
                    {t('ui.already_registered')}
                    <Link href="/login" class="font-medium text-sky-700 hover:underline dark:text-sky-300">{t('ui.login')}</Link>
                </div>
            </form>
        </div>
    </section>
</PublicLayout>