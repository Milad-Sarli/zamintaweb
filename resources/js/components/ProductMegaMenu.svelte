<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { ChevronDown, ChevronLeft } from 'lucide-svelte';
    import { cn } from '@/lib/utils';

    interface Category {
        id: number;
        name: string;
        slug: string;
        children?: Category[];
        image?: string;
        featured_image?: string;
    }

    interface Props {
        categories: Category[];
    }

    let { categories = [] }: Props = $props();

    let activeCategory = $state<Category | null>(categories.length > 0 ? categories[0] : null);

    function setActiveCategory(category: Category) {
        activeCategory = category;
    }
</script>

<div class="group">
    <Link href="/products" class="flex items-center gap-1 text-sm font-medium transition-colors hover:text-primary outline-none group-hover:text-primary py-4">
        محصولات
        <ChevronDown size={14} class="opacity-50 transition-transform duration-200 group-hover:rotate-180" />
    </Link>

    <div class="invisible opacity-0 group-hover:visible group-hover:opacity-100 absolute top-full start-0 w-full bg-white dark:bg-zinc-950 rounded-b-3xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.1)] dark:shadow-[0_10px_40px_-10px_rgba(0,0,0,0.5)] border-t border-slate-100 dark:border-zinc-800 transition-all duration-200 z-50 flex overflow-hidden translate-y-2 group-hover:translate-y-0 h-[500px]">
        <!-- Sidebar -->
        <div class="w-[260px] shrink-0 bg-slate-50/80 dark:bg-zinc-900/50 backdrop-blur-sm py-4 border-e border-slate-100 dark:border-zinc-800 flex flex-col h-full">
            <div class="flex-1 overflow-y-auto custom-scrollbar px-3">
                {#each categories as category (category.id)}
                    <Link
                        href={`/products?category=${category.slug}`}
                        class={cn(
                            "w-full px-4 py-3 flex items-center justify-between cursor-pointer transition-all duration-200 text-sm font-medium rounded-xl mb-1 outline-none focus-visible:ring-2 focus-visible:ring-primary/20",
                            activeCategory?.id === category.id
                                ? "bg-white dark:bg-zinc-800 text-primary shadow-sm ring-1 ring-slate-100 dark:ring-zinc-700"
                                : "text-slate-600 dark:text-zinc-400 hover:bg-white/50 dark:hover:bg-zinc-800/50 hover:text-slate-900 dark:hover:text-zinc-100"
                        )}
                        onmouseenter={() => setActiveCategory(category)}
                        onfocus={() => setActiveCategory(category)}
                    >
                        <div class="flex items-center gap-3">
                            <span class={cn("size-1.5 rounded-full transition-colors", activeCategory?.id === category.id ? "bg-primary" : "bg-slate-300 dark:bg-zinc-700")}></span>
                            <span class="truncate">{category.name}</span>
                        </div>
                        {#if category.children && category.children.length > 0}
                            <ChevronLeft size={14} class={cn("transition-opacity shrink-0", activeCategory?.id === category.id ? "opacity-100 text-primary" : "opacity-30")} />
                        {/if}
                    </Link>
                {/each}
            </div>
            <div class="px-5 py-4 mt-2 border-t border-slate-100 dark:border-zinc-800">
                <Link href="/products" class="text-sm text-primary font-bold hover:underline flex items-center gap-2 group/link">
                    مشاهده همه محصولات
                    <ChevronLeft size={14} class="group-hover/link:-translate-x-1 transition-transform" />
                </Link>
            </div>
        </div>

        <!-- Content Area -->
        <div class="flex-1 p-8 bg-white dark:bg-zinc-950 h-full overflow-y-auto custom-scrollbar">
            {#if activeCategory}
                <div class="flex gap-8 h-full">
                    <div class="flex-1 flex flex-col">
                        <div class="mb-6 flex items-end justify-between border-b border-slate-50 dark:border-zinc-900 pb-4 shrink-0">
                            <Link href={`/products?category=${activeCategory.slug}`} class="text-xl font-black text-slate-900 dark:text-zinc-100 hover:text-primary transition-colors tracking-tight">
                                {activeCategory.name}
                            </Link>
                            <Link href={`/products?category=${activeCategory.slug}`} class="text-xs font-medium text-slate-500 dark:text-zinc-400 hover:text-primary flex items-center gap-1 mb-1">
                                مشاهده همه
                                <ChevronLeft size={12} />
                            </Link>
                        </div>

                        {#if activeCategory.children && activeCategory.children.length > 0}
                            <div class="grid grid-cols-3 gap-x-6 gap-y-8 content-start">
                                {#each activeCategory.children as child (child.id)}
                                    <div class="flex flex-col gap-3 group/item break-inside-avoid">
                                        <Link href={`/products?category=${child.slug}`} class="font-bold text-sm text-slate-800 dark:text-zinc-200 hover:text-primary transition-colors flex items-center gap-2">
                                            <span class="size-1 rounded-full bg-slate-300 dark:bg-zinc-700 group-hover/item:bg-primary transition-colors"></span>
                                            {child.name}
                                        </Link>
                                        {#if child.children && child.children.length > 0}
                                            <div class="flex flex-col gap-2 pe-3 border-e border-slate-100 dark:border-zinc-800">
                                                {#each child.children.slice(0, 5) as subChild (subChild.id)}
                                                    <Link href={`/products?category=${subChild.slug}`} class="text-xs text-slate-500 dark:text-zinc-400 hover:text-primary transition-colors hover:translate-x-1 duration-200 block truncate">
                                                        {subChild.name}
                                                    </Link>
                                                {/each}
                                                {#if child.children.length > 5}
                                                    <Link href={`/products?category=${child.slug}`} class="text-[11px] text-primary font-medium mt-1 hover:underline">
                                                        + {child.children.length - 5} مورد دیگر
                                                    </Link>
                                                {/if}
                                            </div>
                                        {/if}
                                    </div>
                                {/each}
                            </div>
                        {:else}
                            <div class="flex flex-col items-center justify-center h-full text-slate-400 dark:text-zinc-500 bg-slate-50 dark:bg-zinc-900/50 rounded-2xl border border-dashed border-slate-200 dark:border-zinc-800">
                                <p class="font-medium">زیردسته‌ای برای این گروه وجود ندارد.</p>
                                <Link href={`/products?category=${activeCategory.slug}`} class="mt-4 px-6 py-2.5 bg-primary text-white rounded-xl text-sm font-bold hover:bg-primary/90 transition-all shadow-lg shadow-primary/20 hover:shadow-primary/30 hover:-translate-y-0.5">
                                    مشاهده محصولات {activeCategory.name}
                                </Link>
                            </div>
                        {/if}
                    </div>

                    <!-- Featured Image / Banner -->
                    {#if activeCategory.featured_image || activeCategory.image}
                        <div class="w-64 shrink-0 hidden 2xl:block h-full">
                            <Link href={`/products?category=${activeCategory.slug}`} class="block w-full h-full relative rounded-2xl overflow-hidden group/banner shadow-md">
                                <img
                                    src={activeCategory.featured_image || activeCategory.image}
                                    alt={activeCategory.name}
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover/banner:scale-110"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent flex flex-col justify-end p-6 text-white">
                                    <span class="text-xs font-medium opacity-80 mb-1">پیشنهاد ویژه</span>
                                    <span class="text-lg font-bold line-clamp-2">{activeCategory.name}</span>
                                    <span class="mt-3 text-xs bg-white/20 backdrop-blur-md px-3 py-1.5 rounded-lg w-fit group-hover/banner:bg-white group-hover/banner:text-black transition-colors">مشاهده محصولات</span>
                                </div>
                            </Link>
                        </div>
                    {/if}
                </div>
            {:else}
                <div class="flex items-center justify-center h-full text-slate-400 w-full">
                    <p>لطفاً یک دسته‌بندی را انتخاب کنید.</p>
                </div>
            {/if}
        </div>
    </div>
</div>
