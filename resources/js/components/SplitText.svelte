<script lang="ts">
    import { onMount } from 'svelte';
    import { gsap } from 'gsap';
    import { cn } from '@/lib/utils';

    interface Props {
        text: string;
        tag?: 'h1' | 'h2' | 'h3' | 'h4' | 'h5' | 'h6' | 'p' | 'span';
        class?: string;
        splitType?: 'chars' | 'words';
        delay?: number;
        duration?: number;
        y?: number;
        stagger?: number;
        once?: boolean;
        rootMargin?: string;
    }

    let {
        text: txt,
        tag = 'p',
        class: className = '',
        splitType = 'chars',
        delay = 0,
        duration = 1.2,
        y = 40,
        stagger = 0.03,
        once = true,
        rootMargin = '-80px',
    }: Props = $props();

    let el: HTMLElement;
    let animDone = $state(false);

    onMount(() => {
        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting && !animDone) {
                    animDone = true;
                    const targets = el!.querySelectorAll('.su');
                    if (targets.length) {
                        gsap.fromTo(
                            targets,
                            { opacity: 0, y, willChange: 'transform, opacity' },
                            {
                                opacity: 1,
                                y: 0,
                                duration,
                                delay,
                                ease: 'power3.out',
                                stagger,
                                willChange: 'transform, opacity',
                                force3D: true,
                            } as gsap.TweenVars
                        );
                    }
                    if (once) observer.disconnect();
                }
            },
            { rootMargin, threshold: 0.1 }
        );
        observer.observe(el);
        return () => observer.disconnect();
    });

</script>

<svelte:element
    this={tag}
    bind:this={el}
    class={cn('split-parent inline', className)}
    style="word-wrap: break-word;"
>
    {#if splitType === 'chars'}
        {#each txt.split('') as char, i}
            <span class="su" style="display: inline-block; opacity: 0;">{char === ' ' ? '\u00A0' : char}</span>
        {/each}
    {:else}
        {#each txt.split(' ') as word, i}
            <span class="su" style="display: inline-block; opacity: 0;">{word}{i < txt.split(' ').length - 1 ? '\u00A0' : ''}</span>
        {/each}
    {/if}
</svelte:element>
