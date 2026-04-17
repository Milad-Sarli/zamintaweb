<script lang="ts">
    import { t, reveal } from '@/lib/utils';
    import { Link } from '@inertiajs/svelte';

    interface BrandItem {
        id: number;
        name: string;
        logo: string;
        slug: string;
    }

    interface Props {
        brands: BrandItem[];
    }

    let { brands = [] }: Props = $props();
</script>

{#if brands.length > 0}
    <section class="mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
        <div class="flex flex-col gap-10">
            <div class="text-center space-y-2" use:reveal>
                <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">{t('ui.partner_brands')}</h2>
                <div class="h-1.5 w-20 bg-blue-600 mx-auto rounded-full shadow-lg shadow-blue-500/20"></div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-8">
                {#each brands as brand, i (brand.id)}
                    <div class="contents" use:reveal={{ delay: i * 50, scale: 0.9, y: 20 }}>
                        <Link
                            href="/brands/{brand.slug}"
                            class="group relative flex flex-col items-center justify-center p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-slate-100 dark:border-zinc-800 shadow-sm hover:shadow-2xl hover:shadow-blue-500/10 hover:border-blue-100 dark:hover:border-blue-900 transition-all duration-700 overflow-hidden"
                        >
                        <div class="absolute inset-0 bg-linear-to-br from-blue-50/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>

                        <div class="relative z-10 w-full aspect-square flex items-center justify-center mb-6 transition-transform duration-700 group-hover:scale-110 group-hover:-rotate-3">
                            <img
                                src={brand.logo}
                                alt={brand.name}
                                class="max-h-full max-w-full object-contain filter grayscale dark:invert-[0.1] group-hover:grayscale-0 dark:group-hover:invert-0 transition-all duration-700"
                            />
                        </div>

                        <span class="relative z-10 text-base font-black text-slate-600 dark:text-slate-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors text-center line-clamp-1">
                            {brand.name}
                        </span>
                    </Link>
                </div>
            {/each}
        </div>
        </div>
    </section>
{/if}
