<script lang="ts">
    import { Eye, ArrowLeft, Flame } from 'lucide-svelte';
    import { onMount, onDestroy } from 'svelte';
    import { reveal } from '@/lib/utils';
    import { Link } from '@inertiajs/svelte';

    interface AmazingOffer {
        id: number;
        name: string;
        slug: string;
        price: number;
        formatted_price: string;
        sale_price: number;
        formatted_sale_price: string;
        discount_percentage: number;
        sale_end_at: string | null;
        images: string[] | null;
        brand: string | null;
        categories: { id: number; name: string; slug: string; }[];
    }

    let { offers = [] }: { offers: AmazingOffer[] } = $props();

    function toPersianDigits(n: number | string) {
        const persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return n.toString().replace(/\d/g, x => persian[parseInt(x)]);
    }

    // Countdown Logic (simplified for the section, picking the soonest ending offer)
    let timeLeft = $state({ hours: 0, minutes: 0, seconds: 0 });
    let timerInterval: any;

    function updateTimer() {
        // Find the offer that ends soonest but hasn't ended yet
        const now = new Date();
        const activeOffers = offers.filter(o => o.sale_end_at && new Date(o.sale_end_at) > now);

        if (activeOffers.length === 0) return;

        // Sort by end time
        activeOffers.sort((a, b) => new Date(a.sale_end_at!).getTime() - new Date(b.sale_end_at!).getTime());

        const targetDate = new Date(activeOffers[0].sale_end_at!);
        const diff = targetDate.getTime() - now.getTime();

        if (diff > 0) {
            timeLeft = {
                hours: Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
                minutes: Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60)),
                seconds: Math.floor((diff % (1000 * 60)) / 1000)
            };
        }
    }

    onMount(() => {
        updateTimer();
        timerInterval = setInterval(updateTimer, 1000);
    });

    onDestroy(() => {
        if (timerInterval) clearInterval(timerInterval);
    });
</script>

{#if offers.length > 0}
    <section class="relative py-20 overflow-hidden bg-[#ef394e]">
        <!-- Background Pattern -->
        <div class="absolute inset-0 z-0 opacity-10">
            <svg class="w-full h-full" width="100%" height="100%" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white" />
            </svg>
        </div>

        <div class="container relative z-10 mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-12 items-center">
                <!-- Banner / Timer Section -->
                <div class="lg:w-1/4 text-center lg:text-right text-white" use:reveal>
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full mb-6 border border-white/30">
                        <Flame class="w-5 h-5 animate-pulse text-yellow-300" />
                        <span class="font-bold text-sm">پیشنهادهای شگفت‌انگیز</span>
                    </div>

                    <h2 class="text-4xl md:text-5xl font-black mb-6 leading-tight">
                        تخفیف‌های<br/>
                        <span class="text-yellow-300">داغ امروز</span>
                    </h2>

                    <p class="text-white/80 mb-8 text-lg leading-relaxed">
                        فرصت را از دست ندهید! بهترین محصولات با قیمت‌های باورنکردنی برای مدت محدود.
                    </p>

                    <!-- Timer Box -->
                    <div class="flex items-center justify-center lg:justify-start gap-4 mb-8" dir="ltr">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-3xl font-black text-[#ef394e] shadow-lg mb-2">
                                {toPersianDigits(timeLeft.seconds.toString().padStart(2, '0'))}
                            </div>
                            <span class="text-xs font-bold text-white/80">ثانیه</span>
                        </div>
                        <span class="text-3xl font-black text-white/50 mb-6">:</span>
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-3xl font-black text-[#ef394e] shadow-lg mb-2">
                                {toPersianDigits(timeLeft.minutes.toString().padStart(2, '0'))}
                            </div>
                            <span class="text-xs font-bold text-white/80">دقیقه</span>
                        </div>
                        <span class="text-3xl font-black text-white/50 mb-6">:</span>
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-3xl font-black text-[#ef394e] shadow-lg mb-2">
                                {toPersianDigits(timeLeft.hours.toString().padStart(2, '0'))}
                            </div>
                            <span class="text-xs font-bold text-white/80">ساعت</span>
                        </div>
                    </div>

                    <Link href="/offers" class="inline-flex items-center gap-3 px-8 py-4 bg-white text-[#ef394e] rounded-2xl font-black hover:bg-neutral-50 hover:scale-105 transition-all duration-300 shadow-xl">
                        مشاهده همه تخفیف‌ها
                        <ArrowLeft class="w-5 h-5" />
                    </Link>
                </div>

                <!-- Products Carousel/Grid -->
                <div class="lg:w-3/4 w-full overflow-x-auto pb-8 -mx-4 px-4 lg:mx-0 lg:px-0 no-scrollbar" dir="rtl">
                    <div class="flex gap-6 w-max lg:w-full">
                        {#each offers as product (product.id)}
                            <div class="w-72 bg-white dark:bg-zinc-900 rounded-3xl p-4 shadow-xl hover:-translate-y-2 transition-transform duration-300 group relative flex-shrink-0 border border-transparent dark:border-zinc-800">
                                <!-- Discount Badge -->
                                <div class="absolute top-4 right-4 z-20 bg-[#ef394e] text-white px-3 py-1 rounded-full text-sm font-black shadow-lg">
                                    {toPersianDigits(product.discount_percentage)}٪
                                </div>

                                <!-- Image -->
                                <div class="relative aspect-square rounded-2xl overflow-hidden mb-4 bg-gray-100 dark:bg-zinc-800">
                                    <img
                                        src={product.images && product.images.length > 0 ? product.images[0] : '/images/default.jpg'}
                                        alt={product.name}
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                    />
                                    <!-- Overlay Actions -->
                                    <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-2">
                                        <Link href={`/products/${product.slug}`} class="w-10 h-10 bg-white dark:bg-zinc-800 rounded-full flex items-center justify-center text-neutral-900 dark:text-zinc-100 hover:bg-[#ef394e] hover:text-white transition-colors shadow-lg">
                                            <Eye class="w-5 h-5" />
                                        </Link>
                                    </div>
                                </div>

                                <!-- Info -->
                                <div class="space-y-2">
                                    {#if product.brand}
                                        <span class="text-xs text-neutral-400 dark:text-zinc-500 font-bold">{product.brand}</span>
                                    {/if}
                                    <h3 class="font-bold text-neutral-900 dark:text-zinc-100 line-clamp-2 h-12 leading-6 group-hover:text-[#ef394e] transition-colors">
                                        {product.name}
                                    </h3>

                                    <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-zinc-800">
                                        <div class="flex flex-col">
                                            <span class="text-xs text-neutral-400 dark:text-zinc-500 line-through decoration-red-500/50">
                                                {product.formatted_price}
                                            </span>
                                            <div class="flex items-center gap-1 text-[#ef394e] font-black text-lg">
                                                <span>{product.formatted_sale_price}</span>
                                                <span class="text-xs">تومان</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/each}
                    </div>
                </div>
            </div>
        </div>
    </section>
{/if}

<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
