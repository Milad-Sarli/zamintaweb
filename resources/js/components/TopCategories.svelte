<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { t, reveal } from '@/lib/utils';

    interface FeaturedCategory {
        id: number;
        name: string;
        slug: string;
        featured_title: string;
        featured_subtitle: string;
        featured_image: string | null;
        featured_color: string;
    }

    interface Props {
        categories: FeaturedCategory[];
    }

    let { categories = [] }: Props = $props();

    // Bento pattern for 3 columns: 1,1,1 | 2,1 | 1,2 | 1,1,1
    const gridConfig: Record<number, string> = {
        0: 'lg:col-span-1 lg:row-span-1',
        1: 'lg:col-span-1 lg:row-span-1',
        2: 'lg:col-span-1 lg:row-span-1',
        3: 'lg:col-span-2 lg:row-span-1',
        4: 'lg:col-span-1 lg:row-span-1',
        5: 'lg:col-span-1 lg:row-span-1',
        6: 'lg:col-span-2 lg:row-span-1',
        7: 'lg:col-span-1 lg:row-span-1',
        8: 'lg:col-span-1 lg:row-span-1',
        9: 'lg:col-span-1 lg:row-span-1',
    };

    function isLarge(index: number) {
        const span = gridConfig[index] || '';
        return span.includes('col-span-2');
    }

    function getGridClass(index: number) {
        return gridConfig[index] || 'col-span-1';
    }

    function getContrastColor(hexcolor: string) {
        if (!hexcolor) return "text-slate-900";
        hexcolor = hexcolor.replace("#", "");
        if (hexcolor.length === 3) hexcolor = hexcolor.split('').map(s => s + s).join('');
        var r = parseInt(hexcolor.substring(0, 2), 16);
        var g = parseInt(hexcolor.substring(2, 4), 16);
        var b = parseInt(hexcolor.substring(4, 6), 16);
        var yiq = (r * 299 + g * 587 + b * 114) / 1000;
        return yiq >= 128 ? "text-slate-900" : "text-white";
    }
</script>

{#if categories.length > 0}
    <section class="mx-auto px-4 sm:px-6 lg:px-8 w-full max-w-7xl py-12">
        <div class="flex flex-col gap-10">
            <div class="text-center space-y-3" use:reveal>
                <h2 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white tracking-tight">{t('category.resource.featured_label')}</h2>
                <div class="h-1.5 w-20 bg-[#ef394e] mx-auto rounded-full shadow-lg shadow-red-500/20"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {#each categories.slice(0, 10) as category, i (category.id)}
                    <div class="contents" use:reveal={{ delay: i * 100, y: 40 }}>
                        <Link
                            href={`/products?category=${category.slug}`}
                            class="block w-full relative group rounded-2xl overflow-hidden transition-all duration-500 hover:shadow-2xl hover:shadow-red-500/10 hover:-translate-y-1 {getGridClass(i)} h-[240px] md:h-[260px]"
                            style="background-color: {category.featured_color};"
                        >
                        <!-- Content Layer -->
                        <div class="absolute inset-0 p-6 flex flex-col justify-between z-20">
                            <div class="space-y-2">
                                <span class="inline-block px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-[10px] font-black uppercase tracking-wider {getContrastColor(category.featured_color)}">
                                    {category.featured_subtitle || t('ui.new_arrival')}
                                </span>
                                <h3 class="font-black leading-tight {getContrastColor(category.featured_color)} break-words {isLarge(i) ? 'text-3xl max-w-[60%]' : 'text-xl max-w-[80%]' }">
                                    {category.featured_title || category.name}
                                </h3>
                                <div class="text-xs font-bold opacity-70 {getContrastColor(category.featured_color)}">
                                    {category.name}
                                </div>
                            </div>

                            <div class="mt-4">
                                <div
                                    class="inline-flex items-center px-5 py-2 rounded-xl bg-white text-slate-900 text-[11px] font-black hover:bg-red-600 hover:text-white transition-all duration-300 shadow-xl"
                                >
                                    {t('ui.browse')}
                                </div>
                            </div>
                        </div>

                        <!-- Image Layer -->
                        {#if category.featured_image}
                            <div class="absolute inset-y-0 end-0 {isLarge(i) ? 'w-1/2' : 'w-2/3'} flex items-center justify-center p-4 z-10 pointer-events-none">
                                <img
                                    src={category.featured_image}
                                    alt={category.name}
                                    class="max-h-[85%] w-auto object-contain transition-all duration-700 group-hover:scale-110 group-hover:rotate-3 translate-x-4 group-hover:translate-x-0 opacity-100 mix-blend-multiply"
                                />
                            </div>
                        {/if}

                        <!-- Background Pattern -->
                        <div class="absolute inset-0 bg-linear-to-br from-white/10 to-transparent pointer-events-none"></div>
                        <div class="absolute -bottom-10 -start-10 w-24 h-24 bg-black/5 rounded-full blur-2xl pointer-events-none"></div>
                    </Link>
                </div>
                {/each}
            </div>
        </div>
    </section>
{/if}
