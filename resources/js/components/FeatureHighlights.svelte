<script lang="ts">
    import { Truck, ShieldCheck, Headset, CreditCard } from 'lucide-svelte';
    import { t, reveal } from '@/lib/utils';

    const features = [
        {
            title: t('ui.fast_delivery'),
            description: t('ui.fast_delivery_desc'),
            icon: Truck,
            color: 'text-blue-600',
            bg: 'bg-blue-50',
        },
        {
            title: t('ui.originality_guarantee'),
            description: t('ui.originality_guarantee_desc'),
            icon: ShieldCheck,
            color: 'text-green-600',
            bg: 'bg-green-50',
        },
        {
            title: t('ui.expert_advice'),
            description: t('ui.expert_advice_desc'),
            icon: Headset,
            color: 'text-orange-600',
            bg: 'bg-orange-50',
        },
        {
            title: t('ui.secure_payment'),
            description: t('ui.secure_payment_desc'),
            icon: CreditCard,
            color: 'text-purple-600',
            bg: 'bg-purple-50',
        },
    ];
</script>

<section class="mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl -mt-10 relative z-10">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-zinc-800 transition-colors duration-300">
        {#each features as feature, i (i)}
            <div
                use:reveal={{ delay: i * 150, y: 30 }}
                class="flex items-center gap-4 p-2 first:border-none md:border-s border-slate-100 dark:border-zinc-800 first:md:border-none group cursor-default"
            >
                <div class="size-12 rounded-full {feature.bg} dark:bg-zinc-800 {feature.color} flex items-center justify-center shrink-0 transition-transform duration-500 group-hover:scale-110 group-hover:rotate-12">
                    <feature.icon class="size-6" />
                </div>
                <div>
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">{feature.title}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{feature.description}</p>
                </div>
            </div>
        {/each}
    </div>
</section>
