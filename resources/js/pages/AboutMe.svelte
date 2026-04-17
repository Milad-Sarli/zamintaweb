<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import PublicLayout from '@/layouts/PublicLayout.svelte';
    import ScrollReveal from '@/components/ScrollReveal.svelte';

    interface Social {
        name: string;
        icon: string;
        url: string;
    }

    interface Profile {
        name: string;
        title: string;
        avatar: string;
        bio: string;
        shortBio: string;
        skills: string[];
        socials: Social[];
    }

    interface Project {
        title: string;
        description: string;
        tags: string[];
        image: string;
        link: string;
    }

    interface Props {
        profile: Profile;
        projects: Project[];
    }

    let { profile, projects }: Props = $props();

    function socialIcon(name: string) {
        const icons: Record<string, string> = {
            github: 'M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z',
            linkedin: 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z',
            telegram: 'M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z',
        };
        return icons[name] || '';
    }

    function tagColor(index: number) {
        const colors = [
            'bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300',
            'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300',
            'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
            'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
            'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300',
            'bg-violet-100 text-violet-800 dark:bg-violet-900/40 dark:text-violet-300',
        ];
        return colors[index % colors.length];
    }

    function skillColor(index: number) {
        const colors = [
            'bg-slate-100 text-slate-700 dark:bg-zinc-800 dark:text-slate-300',
            'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
            'bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
            'bg-teal-50 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300',
            'bg-orange-50 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
            'bg-pink-50 text-pink-700 dark:bg-pink-900/30 dark:text-pink-300',
        ];
        return colors[index % colors.length];
    }
</script>

<svelte:head>
    <title>درباره من - میلاد سرلی</title>
</svelte:head>

<PublicLayout>
    <!-- Hero -->
    <section class="relative min-h-[520px] overflow-hidden bg-[radial-gradient(ellipse_at_center,rgba(99,102,241,0.06),transparent_75%)] bg-white transition-colors duration-300 dark:bg-[radial-gradient(ellipse_at_center,rgba(56,189,248,0.05),transparent_65%)] dark:bg-zinc-950">
        <div class="absolute inset-0 bg-[radial-gradient(#e2e8f0_1.2px,transparent_1.2px)] [background-size:32px_32px] opacity-65 dark:bg-[radial-gradient(#1e293b_1.2px,transparent_1.2px)] dark:opacity-30"></div>
        <div class="absolute left-1/2 top-1/3 -z-10 h-96 w-[800px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-blue-500/5 blur-[120px] dark:bg-sky-500/10"></div>

        <div class="relative z-10 mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
            <div class="flex flex-col items-center text-center">
                <div class="opacity-0 animate-in fade-in duration-700" style="animation: fadeIn 0.7s ease-out forwards;">
                    <div class="group relative mx-auto h-36 w-36 overflow-hidden rounded-full border-4 border-white shadow-xl shadow-slate-900/10 ring-2 ring-slate-200 dark:border-zinc-800 dark:shadow-black/20 dark:ring-white/10 sm:h-44 sm:w-44">
                        <img
                            src={profile.avatar}
                            alt={profile.name}
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-110"
                            onerror={(e) => { (e.currentTarget as HTMLImageElement).src = 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect fill="#3b82f6" width="100" height="100"/><text fill="white" font-size="50" x="50" y="65" text-anchor="middle">م</text></svg>'; }}
                        />
                        <div class="absolute inset-0 rounded-full ring-1 ring-inset ring-black/5 dark:ring-white/10"></div>
                    </div>
                </div>

                <div class="mt-8 space-y-2 opacity-0 animate-in fade-in duration-700" style="animation: fadeIn 0.7s ease-out 0.2s forwards;">
                    <h1 class="text-4xl font-black text-slate-950 dark:text-white sm:text-5xl">{profile.name}</h1>
                    <p class="text-lg font-semibold text-sky-700 dark:text-sky-300">{profile.title}</p>
                </div>

                <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-600 dark:text-slate-300 opacity-0 animate-in fade-in duration-700" style="animation: fadeIn 0.7s ease-out 0.35s forwards;">
                    {profile.shortBio}
                </p>

                <div class="mt-6 flex items-center gap-3 opacity-0 animate-in fade-in duration-700" style="animation: fadeIn 0.7s ease-out 0.5s forwards;">
                    {#each profile.socials as social}
                        <a
                            href={social.url}
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex size-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 hover:text-slate-900 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:text-slate-400 dark:hover:border-zinc-700 dark:hover:text-white"
                            title={social.name}
                        >
                            <svg class="size-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d={socialIcon(social.icon)} />
                            </svg>
                        </a>
                    {/each}
                </div>
            </div>
        </div>
    </section>

    <!-- Bio -->
    <ScrollReveal y={60} duration={0.8}>
        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-5">
                <div class="lg:col-span-3">
                    <h2 class="text-3xl font-black text-slate-900 dark:text-white">درباره من</h2>
                    <div class="mt-2 h-1.5 w-16 rounded-full bg-sky-600 dark:bg-sky-400"></div>
                    <p class="mt-6 text-base leading-8 text-slate-600 dark:text-slate-300">
                        {profile.bio}
                    </p>
                </div>

                <div class="lg:col-span-2">
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">مهارت‌ها</h3>
                        <div class="mt-2 h-1 w-12 rounded-full bg-sky-600 dark:bg-sky-400"></div>
                        <div class="mt-6 flex flex-wrap gap-2">
                            {#each profile.skills as skill, i}
                                <span class="rounded-xl px-3.5 py-1.5 text-xs font-bold {skillColor(i)}">{skill}</span>
                            {/each}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </ScrollReveal>

    <!-- Projects -->
    <ScrollReveal y={60} duration={0.8} delay={0.1}>
        <section class="bg-slate-100 py-20 dark:bg-zinc-900/60">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl font-black text-slate-900 dark:text-white">نمونه پروژه‌ها</h2>
                    <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">پروژه‌هایی که روی آن‌ها کار کرده‌ام</p>
                </div>

                <div class="mt-12 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    {#each projects as project}
                        <article class="group relative flex flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-950">
                            <div class="relative aspect-[16/10] overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200 dark:from-zinc-800 dark:to-zinc-900">
                                <img
                                    src={project.image}
                                    alt={project.title}
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                    onerror={(e) => { (e.currentTarget as HTMLImageElement).style.display = 'none'; }}
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                            </div>

                            <div class="flex flex-1 flex-col p-6">
                                <div class="flex flex-wrap gap-1.5">
                                    {#each project.tags as tag, i}
                                        <span class="rounded-lg px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider {tagColor(i)}">{tag}</span>
                                    {/each}
                                </div>

                                <h3 class="mt-4 text-xl font-bold text-slate-900 dark:text-white">{project.title}</h3>
                                <p class="mt-3 flex-1 text-sm leading-7 text-slate-600 dark:text-slate-300">{project.description}</p>

                                <div class="mt-6">
                                    <a
                                        href={project.link}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center gap-2 text-sm font-bold text-sky-700 transition hover:text-sky-600 dark:text-sky-300 dark:hover:text-sky-200"
                                    >
                                        مشاهده پروژه
                                        <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                    </a>
                                </div>
                            </div>
                        </article>
                    {/each}
                </div>
            </div>
        </section>
    </ScrollReveal>

    <!-- CTA -->
    <ScrollReveal y={40} duration={0.8} delay={0.2}>
        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-slate-950 to-slate-800 px-8 py-16 text-center shadow-xl dark:from-zinc-950 dark:to-zinc-900 sm:px-16">
                <div class="absolute inset-0 bg-[radial-gradient(#ffffff12_1px,transparent_1px)] [background-size:24px_24px] opacity-30"></div>
                <div class="relative z-10">
                    <h2 class="text-3xl font-black text-white">بیا با هم کار کنیم</h2>
                    <p class="mt-4 text-sm leading-7 text-slate-300">اگر به دنبال یک توسعه‌دهنده برای پروژه خود هستید، خوشحال می‌شوم با شما همکاری کنم.</p>
                    <div class="mt-8 flex flex-wrap justify-center gap-4">
                        <a href="mailto:milad@example.com" class="inline-flex items-center gap-2 rounded-full bg-white px-8 py-3 text-sm font-bold text-slate-950 shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            ارسال ایمیل
                        </a>
                        <Link href="/" class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/5 px-8 py-3 text-sm font-bold text-white backdrop-blur-md transition hover:-translate-y-0.5 hover:bg-white/10">
                            بازگشت به خانه
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </ScrollReveal>
</PublicLayout>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
