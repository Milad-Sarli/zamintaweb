<script lang="ts">
    import * as Sheet from '@/components/ui/sheet';
    import { Button } from '@/components/ui/button';
    import { Separator } from '@/components/ui/separator';
    import { ShoppingCart, Plus, Minus, Trash2 } from 'lucide-svelte';
    import { page, router, Link } from '@inertiajs/svelte';
    import { cn } from '@/lib/utils';
    import { toast } from 'svelte-sonner';

    let { open = $bindable(false) } = $props();

    let cart = $derived($page.props.cart);
    let items = $derived(cart?.items || []);
    let total = $derived(cart?.total_price || 0);
    let isLoading = $state(false);

    function updateQuantity(itemId: number, newQuantity: number) {
        if (isLoading) return;
        isLoading = true;

        router.put(route('cart.update', itemId), {
            quantity: newQuantity
        }, {
            preserveScroll: true,
            onFinish: () => isLoading = false,
            onError: () => {
                toast.error('خطایی رخ داد.');
            }
        });
    }

    function removeItem(itemId: number) {
        if (isLoading) return;
        isLoading = true;

        router.delete(route('cart.remove', itemId), {
            preserveScroll: true,
            onFinish: () => isLoading = false,
            onError: () => {
                toast.error('خطایی رخ داد.');
            }
        });
    }

    function formatPrice(price: number) {
        return new Intl.NumberFormat('fa-IR').format(price) + ' تومان';
    }
</script>

<Sheet.Root bind:open>
    <Sheet.Trigger
        class={cn(
            "inline-flex items-center justify-center whitespace-nowrap rounded-xl text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50",
            "hover:bg-accent hover:text-accent-foreground",
            "h-10 w-10 relative text-slate-600 hover:text-primary hover:bg-blue-50 transition-all"
        )}
    >
        <ShoppingCart size={22} />
        {#if cart?.count > 0}
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-sm min-w-[18px] h-[18px] flex items-center justify-center ring-2 ring-white">
                {cart.count}
            </span>
        {/if}
    </Sheet.Trigger>
    <Sheet.Content side="left" class="w-full sm:max-w-md p-0 flex flex-col h-full bg-white dark:bg-zinc-950 border-e dark:border-zinc-800 shadow-2xl z-[100]">
        <Sheet.Header class="px-6 py-4 border-b dark:border-zinc-800 flex flex-row items-center justify-between shrink-0 bg-slate-50/50 dark:bg-zinc-900/50">
            <div class="flex items-center gap-2">
                <div class="bg-primary/10 p-2 rounded-xl text-primary">
                    <ShoppingCart size={20} />
                </div>
                <Sheet.Title class="text-lg font-bold text-slate-900 dark:text-zinc-100">سبد خرید</Sheet.Title>
                <span class="text-sm font-medium text-slate-500 dark:text-zinc-400 bg-slate-100 dark:bg-zinc-800 px-2.5 py-0.5 rounded-full">
                    {cart?.count || 0} مورد
                </span>
            </div>
            <!-- <Sheet.Close class="rounded-full hover:bg-slate-100 p-2 transition-colors">
                <X size={20} class="text-slate-500" />
            </Sheet.Close> -->
        </Sheet.Header>

        <div class="flex-1 overflow-y-auto custom-scrollbar p-6">
            {#if items.length === 0}
                <div class="h-full flex flex-col items-center justify-center text-center space-y-4">
                    <div class="bg-slate-50 dark:bg-zinc-900 p-6 rounded-full">
                        <ShoppingCart size={48} class="text-slate-300 dark:text-zinc-600" />
                    </div>
                    <div class="space-y-1">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-zinc-100">سبد خرید خالی است</h3>
                        <p class="text-slate-500 dark:text-zinc-400 text-sm max-w-[200px]">
                            شما هنوز هیچ محصولی را به سبد خرید خود اضافه نکرده‌اید.
                        </p>
                    </div>
                    <Button variant="outline" class="mt-4" onclick={() => open = false}>
                        مشاهده محصولات
                    </Button>
                </div>
            {:else}
                <div class="space-y-4">
                    {#each items as item (item.id)}
                        <div class="flex gap-4 p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-slate-100 dark:border-zinc-800 hover:border-primary/20 hover:shadow-sm transition-all group">
                            <!-- Image -->
                            <div class="size-20 shrink-0 rounded-xl bg-slate-50 dark:bg-zinc-800 border border-slate-100 dark:border-zinc-700 overflow-hidden relative">
                                {#if item.image}
                                    <img src={item.image} alt={item.name} class="w-full h-full object-cover" />
                                {:else}
                                    <div class="w-full h-full flex items-center justify-center text-slate-300 dark:text-zinc-600">
                                        <ShoppingCart size={24} />
                                    </div>
                                {/if}
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0 flex flex-col justify-between py-0.5">
                                <div>
                                    <h4 class="font-bold text-sm text-slate-900 dark:text-zinc-100 line-clamp-1 group-hover:text-primary transition-colors">
                                        <Link href={`/products/${item.slug}`}>{item.name}</Link>
                                    </h4>
                                    {#if item.price}
                                        <p class="text-sm font-medium text-slate-500 dark:text-zinc-400 mt-1">
                                            {formatPrice(item.price)}
                                        </p>
                                    {/if}
                                </div>

                                <div class="flex items-center justify-between mt-3">
                                    <!-- Quantity Controls -->
                                    <div class="flex items-center gap-1 bg-slate-50 dark:bg-zinc-800 rounded-lg p-1 border border-slate-200 dark:border-zinc-700">
                                        <button
                                            class="size-7 flex items-center justify-center rounded-md hover:bg-white dark:hover:bg-zinc-700 hover:shadow-sm hover:text-primary transition-all disabled:opacity-50"
                                            onclick={() => updateQuantity(item.id, item.quantity + 1)}
                                            disabled={isLoading}
                                        >
                                            <Plus size={14} />
                                        </button>
                                        <span class="w-8 text-center text-sm font-bold text-slate-900 dark:text-zinc-100">{item.quantity}</span>
                                        <button
                                            class="size-7 flex items-center justify-center rounded-md hover:bg-white dark:hover:bg-zinc-700 hover:shadow-sm hover:text-red-500 transition-all disabled:opacity-50"
                                            onclick={() => item.quantity > 1 ? updateQuantity(item.id, item.quantity - 1) : removeItem(item.id)}
                                            disabled={isLoading}
                                        >
                                            {#if item.quantity === 1}
                                                <Trash2 size={14} />
                                            {:else}
                                                <Minus size={14} />
                                            {/if}
                                        </button>
                                    </div>

                                    <!-- Total for item -->
                                    <p class="text-sm font-bold text-primary">
                                        {formatPrice(item.total)}
                                    </p>
                                </div>
                            </div>
                        </div>
                    {/each}
                </div>
            {/if}
        </div>

        {#if items.length > 0}
            <div class="p-6 bg-white dark:bg-zinc-950 border-t dark:border-zinc-800 space-y-4 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-20">
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500 dark:text-zinc-400">مجموع اقلام</span>
                        <span class="font-medium dark:text-zinc-200">{formatPrice(total)}</span>
                    </div>
                    <!-- Shipping can be calculated later -->
                    <!-- <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">هزینه ارسال</span>
                        <span class="font-medium text-slate-900">وابسته به آدرس</span>
                    </div> -->
                    <Separator class="my-2" />
                    <div class="flex items-center justify-between text-lg font-bold">
                        <span class="text-slate-900 dark:text-zinc-100">مبلغ قابل پرداخت</span>
                        <span class="text-primary">{formatPrice(total)}</span>
                    </div>
                </div>

                <Button class="w-full h-12 text-base font-bold shadow-lg shadow-primary/25 rounded-xl" href="/cart">
                    تکمیل خرید
                </Button>
            </div>
        {/if}
    </Sheet.Content>
</Sheet.Root>
