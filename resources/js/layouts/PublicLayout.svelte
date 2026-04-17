<script lang="ts">
    import PublicHeader from '@/components/PublicHeader.svelte';
    import PublicFooter from '@/components/PublicFooter.svelte';
    import ThemeHandler from '@/components/ThemeHandler.svelte';
    import { Toaster } from '@/components/ui/sonner';

    interface Props {
        children?: import('svelte').Snippet;
    }

    let { children }: Props = $props();
</script>

<div class="min-h-screen bg-slate-50 font-vazir text-slate-900 transition-colors duration-300 dark:bg-zinc-950 dark:text-slate-100" dir="rtl">
    <ThemeHandler />
    <PublicHeader />

    <main>
        {@render children?.()}
    </main>

    <PublicFooter />
    <Toaster />
</div>
