<script lang="ts">
    import { type Snippet } from 'svelte';
    import { onMount } from 'svelte';
    import { gsap } from 'gsap';
    import { cn } from '@/lib/utils';

    interface Props {
        children?: Snippet;
        class?: string;
        y?: number;
        duration?: number;
        delay?: number;
        once?: boolean;
        rootMargin?: string;
    }

    let {
        children,
        class: className = '',
        y = 50,
        duration = 0.9,
        delay = 0,
        once = true,
        rootMargin = '-60px',
    }: Props = $props();

    let el: HTMLDivElement;

    onMount(() => {
        gsap.set(el, { opacity: 0, y, willChange: 'transform, opacity' });

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    gsap.to(el, {
                        opacity: 1,
                        y: 0,
                        duration,
                        delay,
                        ease: 'power3.out',
                        willChange: 'transform, opacity',
                        force3D: true,
                    } as gsap.TweenVars);
                    if (once) observer.disconnect();
                }
            },
            { rootMargin, threshold: 0.05 }
        );
        observer.observe(el);
        return () => observer.disconnect();
    });
</script>

<div bind:this={el} class={cn('', className)}>
    {@render children?.()}
</div>
