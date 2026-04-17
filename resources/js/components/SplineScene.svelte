<script lang="ts">
    import { onMount } from 'svelte';
    import { Application } from '@splinetool/runtime';
    import { cn } from '@/lib/utils';

    interface Props {
        scene: string;
        class?: string;
        loadingClass?: string;
        renderMode?: 'auto' | 'manual' | 'continuous';
        lazy?: boolean;
    }

    let { scene, class: className = '', loadingClass = '', renderMode = 'auto', lazy = true }: Props = $props();

    let canvas: HTMLCanvasElement;
    let container: HTMLDivElement;
    let app: Application | null = $state(null);
    let loaded = $state(false);
    let error = $state(false);

    onMount(() => {
        if (lazy) {
            const observer = new IntersectionObserver(
                ([entry]) => {
                    if (entry.isIntersecting) {
                        initSpline();
                        observer.disconnect();
                    }
                },
                { rootMargin: '200px' }
            );
            observer.observe(container);
            return () => {
                observer.disconnect();
                app?.dispose();
            };
        } else {
            initSpline();
            return () => app?.dispose();
        }
    });

    async function initSpline() {
        if (!canvas) return;
        try {
            const spline = new Application(canvas, { renderMode });
            await spline.load(scene);
            app = spline;
            loaded = true;
        } catch {
            error = true;
        }
    }
</script>

    <div bind:this={container} class={cn('relative', className)}>
    {#if !loaded && !error}
        <div class={cn('flex h-full w-full items-center justify-center bg-slate-100 dark:bg-zinc-900', loadingClass)}>
            <div class="size-8 animate-pulse rounded-full bg-slate-300 dark:bg-zinc-700"></div>
        </div>
    {/if}

    {#if error}
        <div class="flex h-full w-full items-center justify-center bg-slate-100 dark:bg-zinc-900">
            <p class="text-sm text-slate-400 dark:text-slate-500">3D scene unavailable</p>
        </div>
    {/if}

    <canvas
        bind:this={canvas}
        class={cn('h-full w-full', loaded ? 'block' : 'hidden')}
        style="pointer-events: none;"
    ></canvas>
</div>
