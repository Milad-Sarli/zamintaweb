<script lang="ts">
    import { Mail, Loader2, CheckCircle2 } from 'lucide-svelte';
    import { t, reveal } from '@/lib/utils';
    import { toast } from 'svelte-sonner';
    import axios from 'axios';

    // No props needed

    let email = $state('');
    let isLoading = $state(false);
    let isSubscribed = $state(false);

    async function handleSubscribe(e: Event) {
        e.preventDefault();
        if (!email) return;

        isLoading = true;
        try {
            const response = await axios.post('/newsletter/subscribe', { email });
            if (response.data.success) {
                toast.success(response.data.message);
                isSubscribed = true;
                email = '';
            }
        } catch (error: any) {
            const message = error.response?.data?.message || t('ui.error_occurred');
            toast.error(message);
        } finally {
            isLoading = false;
        }
    }
</script>

<section class="mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl py-12" use:reveal={{ y: 40 }}>
    <div class="relative overflow-hidden bg-primary rounded-[3rem] px-8 py-16 md:px-16 md:py-20 shadow-2xl shadow-primary/20">
        <!-- Decorative Pattern Background -->
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: url('/images/pattern.jpg'); background-repeat: repeat; background-size: 100px;"></div>

        <!-- Floating Glow Effect -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-black/10 rounded-full blur-3xl"></div>

        <div class="relative z-10 flex flex-col items-center text-center max-w-4xl mx-auto">
            <div class="mb-8 p-4 bg-white/10 rounded-3xl backdrop-blur-md border border-white/20">
                <Mail class="size-12 text-white" />
            </div>

            <h2 class="text-3xl md:text-5xl font-black text-white mb-6 tracking-tight">
                {t('ui.newsletter_title')}
            </h2>

            <p class="text-primary-foreground/90 text-lg md:text-xl mb-12 leading-relaxed opacity-90">
                {t('ui.newsletter_desc')}
            </p>

            {#if !isSubscribed}
                <form
                    onsubmit={handleSubscribe}
                    class="w-full max-w-2xl flex flex-col md:flex-row gap-4 p-2 bg-white/10 backdrop-blur-xl rounded-[2.5rem] border border-white/20 shadow-2xl"
                >
                    <div class="flex-1 relative group">
                        <Mail class="absolute start-6 top-1/2 -translate-y-1/2 size-5 text-primary-foreground/70 group-focus-within:text-white transition-colors" />
                        <input
                            type="email"
                            bind:value={email}
                            placeholder={t('ui.newsletter_placeholder')}
                            required
                            class="w-full bg-transparent border-none focus:ring-0 text-white placeholder:text-primary-foreground/60 py-4 ps-14 pe-6 text-lg"
                        />
                    </div>

                    <button
                        type="submit"
                        disabled={isLoading}
                        class="bg-slate-900 dark:bg-zinc-950 text-white px-10 py-4 rounded-[2rem] font-bold text-lg hover:bg-slate-800 dark:hover:bg-black transition-all duration-300 disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-3 shadow-xl shadow-black/20"
                    >
                        {#if isLoading}
                            <Loader2 class="size-5 animate-spin" />
                        {/if}
                        {t('ui.subscribe')}
                    </button>
                </form>
            {:else}
                <div class="bg-white/20 backdrop-blur-md border border-white/30 rounded-[2.5rem] px-10 py-6 flex items-center gap-4 text-white animate-in zoom-in duration-500">
                    <CheckCircle2 class="size-8 text-green-300" />
                    <span class="text-xl font-bold">{t('ui.newsletter_subscribed_successfully')}</span>
                </div>
            {/if}

            <p class="mt-8 text-primary-foreground/60 text-sm font-medium">
                {t('ui.newsletter_privacy')}
            </p>
        </div>
    </div>
</section>
