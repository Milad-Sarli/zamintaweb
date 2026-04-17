<script lang="ts">
    import { ShoppingCart, Eye, ArrowLeft, ChevronLeft, ChevronRight, LayoutGrid } from 'lucide-svelte';
    import { fly } from 'svelte/transition';
    import { backOut } from 'svelte/easing';
    import { reveal } from '@/lib/utils';
    import { page, Link } from '@inertiajs/svelte';

    interface Category {
        id: number;
        name: string;
        slug: string;
    }

    interface Product {
        id: number;
        name: string;
        slug: string;
        price: number;
        sale_price: number;
        is_on_sale: boolean;
        discount_percentage: number;
        formatted_price: string;
        images: string[] | null;
        brand: string | null;
        unit: string | null;
        categories: Category[];
    }

    let { products, categories }: { products: Product[], categories: Category[] } = $props();

    let selectedCategory = $state('all');
    let currentPage = $state(1);
    const itemsPerPage = 8;

    let scrollContainer: HTMLDivElement | null = $state(null);
    let showLeftArrow = $state(false);
    let showRightArrow = $state(true);

    const settings = $derived($page.props.settings);

    function addToCart(productId: number) {
        // Placeholder: will be implemented with cart functionality
        console.warn('addToCart not yet implemented', productId);
    }

    const filteredProducts = $derived(
        selectedCategory === 'all'
            ? products
            : products.filter(p => p.categories.some(c => c.slug === selectedCategory))
    );

    const totalPages = $derived(Math.ceil(filteredProducts.length / itemsPerPage));

    const paginatedProducts = $derived(
        filteredProducts.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage)
    );

    // Reset to page 1 when category changes
    $effect(() => {
        if (selectedCategory) {
            currentPage = 1;
        }
    });

    // Scroll to products top when page changes
    let sectionElement = $state<HTMLElement | null>(null);
    $effect(() => {
        if (currentPage && sectionElement instanceof HTMLElement) {
            const yOffset = -100;
            const y = sectionElement.getBoundingClientRect().top + window.pageYOffset + yOffset;
            window.scrollTo({ top: y, behavior: 'smooth' });
        }
    });

    // Get categories that actually have products from the 100 products list
    const availableCategories = $derived([
        { id: 0, name: 'همه محصولات', slug: 'all' },
        ...categories.filter(cat => products.some(p => p.categories.some(pc => pc.id === cat.id)))
    ]);

    function scroll(direction: 'left' | 'right') {
        if (!scrollContainer) return;
        const scrollAmount = 300;
        scrollContainer.scrollBy({
            left: direction === 'left' ? -scrollAmount : scrollAmount,
            behavior: 'smooth'
        });
    }

    function handleScroll(e: Event) {
        const target = e.target as HTMLDivElement;
        showLeftArrow = target.scrollLeft < -10; // RTL scrollLeft is negative
        showRightArrow = target.scrollLeft > -(target.scrollWidth - target.clientWidth - 10);
    }

    function toPersianDigits(n: number | string) {
        const persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return n.toString().replace(/\d/g, x => persian[parseInt(x)]);
    }

    function formatPrice(price: number) {
        return toPersianDigits(price.toLocaleString('en-US'));
    }

    function formatNumber(num: number) {
        return toPersianDigits(num);
    }
</script>

<section bind:this={sectionElement} class="relative py-24 overflow-hidden bg-neutral-50 dark:bg-transparent">
    <!-- Pattern Background -->
    <div class="absolute inset-0 z-0 opacity-[0.1] dark:opacity-0 pointer-events-none">
        <div
            class="w-full h-full"
            style="background-image: url('{settings?.background_pattern || '/images/pattern.jpg'}'); background-repeat: repeat; background-size: 400px;"
        ></div>
    </div>

    <div class="container relative z-10 mx-auto px-4">
        <div class="text-center mb-16" use:reveal>
            <h2 class="text-4xl font-black text-neutral-900 dark:text-white mb-4 flex items-center justify-center gap-3">
                <span class="w-12 h-1 bg-primary rounded-full"></span>
                محصولات ما
                <span class="w-12 h-1 bg-primary rounded-full"></span>
            </h2>
            <p class="text-neutral-500 dark:text-neutral-400 max-w-2xl mx-auto text-lg">
                بهترین تجهیزات پزشکی و درمانی را با بالاترین کیفیت از ما بخواهید.
            </p>
        </div>

        <!-- Modern Category Switcher (Carousel) -->
        <div class="relative max-w-5xl mx-auto mb-16 group/carousel" use:reveal={{ delay: 200 }}>
            <!-- Left Gradient & Arrow -->
            <div class="absolute left-0 top-0 bottom-0 z-20 flex items-center pointer-events-none">
                <div class="w-20 h-full bg-gradient-to-r from-neutral-50/90 dark:from-zinc-950/90 to-transparent"></div>
                {#if showLeftArrow}
                    <button
                        onclick={() => scroll('left')}
                        class="absolute left-2 w-10 h-10 rounded-full bg-white dark:bg-zinc-800 shadow-xl border border-neutral-100 dark:border-zinc-700 flex items-center justify-center text-neutral-600 dark:text-neutral-300 hover:bg-primary hover:text-primary-foreground transition-all duration-300 pointer-events-auto"
                        aria-label="Next"
                    >
                        <ChevronLeft class="w-6 h-6" />
                    </button>
                {/if}
            </div>

            <!-- Right Gradient & Arrow -->
            <div class="absolute right-0 top-0 bottom-0 z-20 flex items-center pointer-events-none">
                <div class="w-20 h-full bg-gradient-to-l from-neutral-50/90 dark:from-zinc-950/90 to-transparent"></div>
                {#if showRightArrow}
                    <button
                        onclick={() => scroll('right')}
                        class="absolute right-2 w-10 h-10 rounded-full bg-white dark:bg-zinc-800 shadow-xl border border-neutral-100 dark:border-zinc-700 flex items-center justify-center text-neutral-600 dark:text-neutral-300 hover:bg-primary hover:text-primary-foreground transition-all duration-300 pointer-events-auto"
                        aria-label="Previous"
                    >
                        <ChevronRight class="w-6 h-6" />
                    </button>
                {/if}
            </div>

            <!-- Scroll Container -->
            <div
                bind:this={scrollContainer}
                onscroll={handleScroll}
                class="overflow-x-auto no-scrollbar flex items-center gap-3 px-12 py-4 scroll-smooth"
                dir="rtl"
            >
                {#each availableCategories as category (category.slug)}
                    <button
                        onclick={() => selectedCategory = category.slug}
                        class="flex-none px-6 py-3 rounded-2xl text-sm font-bold transition-all duration-300 whitespace-nowrap border-2
                               {selectedCategory === category.slug
                                   ? 'bg-primary border-primary text-primary-foreground shadow-lg shadow-primary/30 -translate-y-1'
                                   : 'bg-white dark:bg-zinc-900 border-transparent dark:border-zinc-800 text-neutral-600 dark:text-neutral-400 hover:border-primary/20 hover:text-primary shadow-sm'}"
                    >
                        {#if category.slug === 'all'}
                            <LayoutGrid class="w-4 h-4 inline-block me-2 mb-0.5" />
                        {/if}
                        {category.name}
                    </button>
                {/each}
            </div>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            {#each paginatedProducts as product, i (product.id)}
                <div
                    in:fly={{ y: 20, duration: 500, delay: i * 50, easing: backOut }}
                    class="group relative bg-white dark:bg-zinc-900 rounded-[2rem] border border-neutral-100 dark:border-zinc-800 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 flex flex-col h-full overflow-hidden"
                >
                    <!-- Product Image Container -->
                    <div class="relative aspect-[4/3] overflow-hidden m-3 rounded-[1.5rem] bg-neutral-100 dark:bg-zinc-800">
                        <Link href={`/products/${product.slug}`} class="block w-full h-full">
                            <img
                                src={product.images && product.images.length > 0 ? product.images[0] : '/images/default.jpg'}
                                alt={product.name}
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                            />
                        </Link>

                        <!-- Overlay on Hover -->
                        <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-3 pointer-events-none">
                            <button
                                onclick={() => addToCart(product.id)}
                                class="w-12 h-12 rounded-full bg-white text-primary flex items-center justify-center shadow-lg hover:bg-primary hover:text-primary-foreground dark:bg-zinc-800 dark:text-zinc-100 dark:border dark:border-zinc-700 dark:hover:bg-primary transition-all duration-300 scale-90 group-hover:scale-100 pointer-events-auto"
                            >
                                <ShoppingCart class="w-5 h-5" />
                            </button>
                            <Link
                                href={`/products/${product.slug}`}
                                class="w-12 h-12 rounded-full bg-white text-neutral-900 flex items-center justify-center shadow-lg hover:bg-neutral-900 hover:text-white dark:bg-zinc-800 dark:text-zinc-100 dark:border dark:border-zinc-700 dark:hover:bg-primary transition-all duration-300 scale-90 group-hover:scale-100 pointer-events-auto"
                            >
                                <Eye class="w-5 h-5" />
                            </Link>
                        </div>

                        {#if product.is_on_sale}
                            <div class="absolute top-4 right-4 z-20">
                                <span class="px-3 py-1.5 rounded-lg bg-[#ef394e] text-white text-[10px] font-black shadow-lg shadow-red-500/30">
                                    {formatNumber(product.discount_percentage)}٪
                                </span>
                            </div>
                        {/if}

                        <!-- Brand Badge -->
                        {#if product.brand}
                            <div class="absolute top-4 start-4">
                                <span class="px-3 py-1.5 rounded-lg bg-white/90 dark:bg-zinc-950/90 backdrop-blur-md text-neutral-800 dark:text-white text-[10px] font-black shadow-sm border border-white/20 uppercase tracking-wider">
                                    {product.brand}
                                </span>
                            </div>
                        {/if}
                    </div>

                    <!-- Product Content -->
                    <div class="p-6 pt-2 flex flex-col flex-grow">
                        <!-- Categories -->
                        <div class="flex flex-wrap gap-1.5 mb-3">
                            {#each product.categories as category (category.id)}
                                <span class="text-[10px] font-bold text-primary/80 bg-primary/5 dark:bg-primary/10 px-2 py-0.5 rounded-md">
                                    #{category.name}
                                </span>
                            {/each}
                        </div>

                        <!-- Title -->
                        <h3 class="text-lg font-black text-neutral-900 dark:text-white mb-4 line-clamp-2 min-h-[3.5rem] group-hover:text-primary transition-colors duration-300">
                            {product.name}
                        </h3>

                        <!-- Price & Unit -->
                        <div class="mt-auto pt-4 border-t border-neutral-50 dark:border-zinc-800 flex items-end justify-between">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold text-neutral-400 mb-1">قیمت کالا:</span>
                                {#if product.is_on_sale}
                                     <div class="flex flex-col items-start">
                                         <span class="text-xs text-slate-400 line-through decoration-red-500/50">
                                            {formatPrice(product.price)}
                                         </span>
                                         <div class="flex items-baseline gap-1.5">
                                            <span class="text-2xl font-black text-[#ef394e]">
                                                {formatPrice(product.sale_price)}
                                            </span>
                                            <span class="text-[10px] font-bold text-neutral-500">تومان</span>
                                         </div>
                                     </div>
                                {:else}
                                     <div class="flex items-baseline gap-1.5">
                                        <span class="text-2xl font-black text-neutral-900 dark:text-white">
                                            {formatPrice(product.price)}
                                        </span>
                                        <span class="text-[10px] font-bold text-neutral-500">تومان</span>
                                     </div>
                                {/if}
                            </div>
                            {#if product.unit}
                                <div class="px-3 py-1.5 bg-neutral-50 dark:bg-zinc-800 rounded-xl border border-neutral-100 dark:border-zinc-700">
                                    <span class="text-[10px] font-black text-neutral-600 dark:text-neutral-400">{product.unit}</span>
                                </div>
                            {/if}
                        </div>

                        <!-- Action Button -->
                        <Link
                            href={`/products/${product.slug}`}
                            class="mt-6 w-full py-4 bg-neutral-900 dark:bg-zinc-800 text-white rounded-2xl font-bold text-sm flex items-center justify-center gap-2 group/btn overflow-hidden relative"
                        >
                            <span class="relative z-10">مشاهده جزئیات</span>
                            <ArrowLeft class="w-4 h-4 relative z-10 transition-transform duration-300 group-hover/btn:-translate-x-1" />
                            <div class="absolute inset-0 bg-primary translate-y-full group-hover/btn:translate-y-0 transition-transform duration-300"></div>
                        </Link>
                    </div>
                </div>
            {:else}
                <div class="col-span-full py-20 text-center bg-white dark:bg-zinc-900 rounded-[2rem] border border-dashed border-neutral-300 dark:border-zinc-700">
                    <div class="w-20 h-20 bg-neutral-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <ShoppingCart class="w-10 h-10 text-neutral-300" />
                    </div>
                    <p class="text-neutral-500 font-bold">محصولی در این دسته یافت نشد.</p>
                </div>
            {/each}
        </div>

        <!-- Modern Pagination -->
        {#if totalPages > 1}
            <div class="mt-16 flex flex-col items-center gap-6">
                <div class="flex items-center gap-2 p-2 bg-white dark:bg-zinc-900 rounded-3xl border border-neutral-100 dark:border-zinc-800 shadow-xl shadow-neutral-100/50 dark:shadow-none">
                    <!-- Next Button (RTL: Previous page) -->
                    <button
                        onclick={() => currentPage = Math.max(1, currentPage - 1)}
                        disabled={currentPage === 1}
                        class="w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-300
                               {currentPage === 1 ? 'text-neutral-300 dark:text-zinc-700 cursor-not-allowed' : 'text-neutral-600 dark:text-slate-400 hover:bg-primary/10 hover:text-primary'}"
                    >
                        <ChevronRight class="w-6 h-6" />
                    </button>

                    <!-- Page Numbers -->
                    <div class="flex items-center gap-1">
                        {#each Array.from({ length: totalPages }, (_, i) => i + 1) as page (page)}
                            {#if page === 1 || page === totalPages || (page >= currentPage - 1 && page <= currentPage + 1)}
                                <button
                                    onclick={() => currentPage = page}
                                    class="w-12 h-12 rounded-2xl font-bold text-sm transition-all duration-300
                                           {currentPage === page
                                               ? 'bg-primary text-primary-foreground shadow-lg shadow-primary/30'
                                               : 'text-neutral-500 dark:text-slate-400 hover:bg-neutral-50 dark:hover:bg-zinc-800 hover:text-primary'}"
                                >
                                    {toPersianDigits(page)}
                                </button>
                            {:else if page === currentPage - 2 || page === currentPage + 2}
                                <span class="w-8 text-center text-neutral-300 dark:text-zinc-700 font-bold">...</span>
                            {/if}
                        {/each}
                    </div>

                    <!-- Previous Button (RTL: Next page) -->
                    <button
                        onclick={() => currentPage = Math.min(totalPages, currentPage + 1)}
                        disabled={currentPage === totalPages}
                        class="w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-300
                               {currentPage === totalPages ? 'text-neutral-300 dark:text-zinc-700 cursor-not-allowed' : 'text-neutral-600 dark:text-slate-400 hover:bg-primary/10 hover:text-primary'}"
                    >

                        <ChevronLeft class="w-6 h-6" />
                    </button>
                </div>

                <!-- Info -->
                <p class="text-sm font-bold text-neutral-400">
                    نمایش {toPersianDigits(paginatedProducts.length)} محصول از مجموع {toPersianDigits(filteredProducts.length)} مورد
                </p>
            </div>
        {/if}

        <!-- View All Button -->
        <div class="mt-20 text-center">
            <Link
                href="/products"
                class="inline-flex items-center gap-3 px-10 py-5 bg-white dark:bg-zinc-900 text-neutral-900 dark:text-white rounded-2xl font-black shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 border border-neutral-100 dark:border-zinc-800"
            >
                مشاهده همه محصولات
                <ArrowLeft class="w-5 h-5 text-primary" />
            </Link>
        </div>
    </div>
</section>

<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
