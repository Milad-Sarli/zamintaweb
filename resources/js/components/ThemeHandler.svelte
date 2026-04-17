<script lang="ts">
    import { useAppearance } from '@/hooks/useAppearance.svelte';
    import { page } from '@inertiajs/svelte';
    import { onMount } from 'svelte';

    const appearanceManager = useAppearance();

    let settings = $derived($page.props.settings as any);
    let primaryColor = $derived(settings?.primary_color);
    let backgroundPattern = $derived(settings?.background_pattern);

    $effect(() => {
        if (typeof document === 'undefined') return;

        const root = document.documentElement;

        if (primaryColor) {
            // Apply primary color
            // If it's a hex, we can apply it directly. Tailwind 4 handles hex in CSS variables well.
            root.style.setProperty('--primary', primaryColor);
            
            // Optionally set a ring color based on primary
            root.style.setProperty('--ring', primaryColor);

            // If we have a very light primary color, we might need a dark foreground
            // For now, we'll stick to the default primary-foreground which is usually white/light
        } else {
            root.style.removeProperty('--primary');
            root.style.removeProperty('--ring');
        }

        if (backgroundPattern) {
            const url = `/storage/${backgroundPattern}`;
            root.style.setProperty('--background-pattern', `url("${url}")`);
        } else {
            root.style.removeProperty('--background-pattern');
        }
    });

    onMount(() => {
        appearanceManager.updateAppearance(appearanceManager.appearance);
    });
</script>

<style>
    :global(body) {
        background-image: var(--background-pattern);
        background-repeat: repeat;
        background-position: center top;
        background-attachment: fixed;
    }
</style>
