<script lang="ts">
    import { ArrowLeft, LayoutGrid } from 'lucide-svelte';
    import { Link } from '@inertiajs/svelte';
    import { t, reveal } from '@/lib/utils';

    interface CategoryItem {
        id: number;
        name: string;
        slug: string;
        image: string;
        description?: string;
    }

    interface Props {
        categories: CategoryItem[];
    }

    let { categories = [] }: Props = $props();
</script>

{#if categories.length > 0}
    <section class="mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
        <div class="flex items-end justify-between mb-10" use:reveal>
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-800">
                    <LayoutGrid size={16} />
                    <span class="text-xs font-bold uppercase tracking-wider">{t('ui.categories')}</span>
                </div>
                <h2 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight">{t('ui.what_you_need')}</h2>
            </div>
            <Link
                href="/categories"
                class="group flex items-center gap-2 text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 font-bold text-sm transition-all duration-300"
            >
                <span class="border-b-2 border-transparent group-hover:border-blue-600 dark:group-hover:border-blue-400 pb-0.5">{t('ui.view_all')}</span>
                <div class="size-8 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                    <ArrowLeft class="size-4" />
                </div>
            </Link>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
            {#each categories as category, i (category.id)}
                <div class="contents" use:reveal={{ delay: i * 80, y: 30 }}>
                    <Link
                        href="/categories/{category.slug}"
                        class="group relative block aspect-4/5 overflow-hidden rounded-[2.5rem] bg-slate-100 dark:bg-zinc-900 shadow-sm hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-700"
                    >
                    <!-- Background Image with Parallax-like effect -->
                    <div
                        class="absolute inset-0 bg-cover bg-center transition-transform duration-1000 group-hover:scale-110"
                        style="background-image: url('{category.image}')"
                    ></div>

                    <!-- Sophisticated Overlays -->
                    <div class="absolute inset-0 bg-linear-to-t from-slate-950/90 via-slate-950/20 to-transparent opacity-60 group-hover:opacity-80 transition-opacity duration-500"></div>
                    <div class="absolute inset-0 bg-blue-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                    <!-- Content -->
                    <div class="absolute inset-0 p-6 flex flex-col justify-end items-center text-center">
                        <div class="translate-y-4 group-hover:translate-y-0 transition-transform duration-500 ease-out">
                            <h3 class="text-white font-black text-xl md:text-2xl mb-2 drop-shadow-md">
                                {category.name}
                            </h3>

                            {#if category.description}
                                <p class="text-slate-200 text-xs line-clamp-2 opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100 max-w-[80%] mx-auto">
                                    {category.description}
                                </p>
                            {/if}

                            <div class="mt-4 inline-flex size-10 rounded-full bg-white/20 backdrop-blur-md border border-white/30 items-center justify-center text-white opacity-0 group-hover:opacity-100 scale-50 group-hover:scale-100 transition-all duration-500 delay-150">
                                <ArrowLeft class="size-5" />
                            </div>
                        </div>
                    </div>

                    <!-- Glassmorphism Badge -->
                    <div class="absolute top-4 right-4 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-[10px] font-bold text-white opacity-0 group-hover:opacity-100 transition-all duration-500">
                        {t('ui.explore')}
                    </div>
                </Link>
            </div>
        {/each}
    </div>
    </section>
{/if}
