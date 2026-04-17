<script lang="ts">
    import * as Carousel from "@/components/ui/carousel";
    import { Button } from "@/components/ui/button";
    import Autoplay from "embla-carousel-autoplay";
    import { ChevronLeft, ChevronRight } from "lucide-svelte";
    import type { CarouselAPI } from "@/components/ui/carousel/context";
    import { cn } from "@/lib/utils";

    interface SliderItem {
        id: number;
        image: string;
        title: string | null;
        subtitle: string | null;
        link: string | null;
        button_text: string | null;
    }

    let { sliders = [] }: { sliders: SliderItem[] } = $props();

    let api = $state<CarouselAPI>();
    let current = $state(0);
    let count = $state(0);

    const plugin = Autoplay({ delay: 5000, stopOnInteraction: true });

    $effect(() => {
        if (!api) return;

        count = api.scrollSnapList().length;
        current = api.selectedScrollSnap();

        api.on("select", () => {
            current = api!.selectedScrollSnap();
        });
    });

    function scrollPrev() {
        api?.scrollPrev();
    }

    function scrollNext() {
        api?.scrollNext();
    }

    function scrollTo(index: number) {
        api?.scrollTo(index);
    }
</script>

{#if sliders.length > 0}
    <div class="relative group">
        <Carousel.Root
            setApi={(val) => (api = val)}
            plugins={[plugin]}
            class="w-full"
            opts={{
                direction: "rtl",
                loop: true,
            }}
        >
            <Carousel.Content>
                {#each sliders as slider, i (slider.id)}
                    <Carousel.Item>
                        <div class="relative h-[400px] md:h-[700px] w-full overflow-hidden">
                            <!-- Background Image with Parallax effect -->
                            <img
                                src={slider.image}
                                alt={slider.title || ""}
                                class="absolute inset-0 h-full w-full object-cover transition-transform duration-1000 ease-out"
                                style="transform: scale({current === i ? '1.1' : '1'})"
                            />

                            <!-- Gradient Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-r from-slate-900/90 via-slate-900/40 to-transparent rtl:bg-gradient-to-l"></div>

                            <!-- Content Container (Centered Max Width) -->
                            <div class="absolute inset-0 flex items-center">
                                <div class="max-w-7xl mx-auto w-full px-8 md:px-16">
                                    <div class="max-w-2xl text-start">
                                        {#if slider.subtitle}
                                            <span
                                                class="inline-block px-4 py-1.5 mb-6 text-sm font-medium text-blue-100 bg-blue-600/40 backdrop-blur-md rounded-full border border-blue-400/30 transform transition-all duration-700 delay-100"
                                                class:translate-y-0={current === i}
                                                class:opacity-100={current === i}
                                                class:translate-y-4={current !== i}
                                                class:opacity-0={current !== i}
                                            >
                                                {slider.subtitle}
                                            </span>
                                        {/if}

                                        {#if slider.title}
                                            {@const firstSpace = slider.title.indexOf(' ')}
                                            <h2
                                                class="text-4xl md:text-7xl font-extrabold text-white mb-8 leading-tight transform transition-all duration-700 delay-300"
                                                class:translate-y-0={current === i}
                                                class:opacity-100={current === i}
                                                class:translate-y-8={current !== i}
                                                class:opacity-0={current !== i}
                                            >
                                                <span>{firstSpace >= 0 ? slider.title.substring(0, firstSpace) : slider.title}</span>
                                                {#if firstSpace >= 0}
                                                    <br class="hidden md:block" />
                                                    <span>{slider.title.substring(firstSpace + 1)}</span>
                                                {/if}
                                            </h2>
                                        {/if}

                                        <div
                                            class="flex flex-wrap gap-4 transform transition-all duration-700 delay-500"
                                            class:translate-y-0={current === i}
                                            class:opacity-100={current === i}
                                            class:translate-y-12={current !== i}
                                            class:opacity-0={current !== i}
                                        >
                                            {#if slider.link}
                                                <Button
                                                    href={slider.link}
                                                    size="lg"
                                                    class="rounded-2xl px-8 h-14 text-lg font-bold bg-blue-600 hover:bg-blue-700 text-white border-none shadow-xl shadow-blue-600/40 transition-all hover:scale-105 active:scale-95"
                                                >
                                                    {slider.button_text || 'مشاهده محصولات'}
                                                    <ChevronLeft class="ms-2 size-5" />
                                                </Button>
                                            {/if}

                                            <Button
                                                variant="outline"
                                                size="lg"
                                                class="rounded-2xl px-8 h-14 text-lg font-bold bg-white/10 backdrop-blur-md text-white border-white/20 hover:bg-white/20 transition-all hover:scale-105 active:scale-95"
                                            >
                                                مشاوره رایگان
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Carousel.Item>
                {/each}
            </Carousel.Content>

            <!-- Custom Navigation Controls -->
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-3 z-10">
                {#each Array.from({ length: count }, (value, idx) => idx) as i (i)}
                    <button
                        onclick={() => scrollTo(i)}
                        class={cn(
                            "h-2 rounded-full transition-all duration-500",
                            current === i ? "w-10 bg-blue-500" : "w-2 bg-white/40 hover:bg-white/60"
                        )}
                        aria-label="برو به اسلاید {i + 1}"
                    ></button>
                {/each}
            </div>

            <!-- Arrows -->
            <button
                onclick={scrollPrev}
                class="absolute top-1/2 start-4 -translate-y-1/2 size-12 flex items-center justify-center rounded-2xl bg-white/10 backdrop-blur-md text-white border border-white/20 opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-white/20 z-10"
                aria-label="قبلی"
            >
                <ChevronRight class="size-6" />
            </button>
            <button
                onclick={scrollNext}
                class="absolute top-1/2 end-4 -translate-y-1/2 size-12 flex items-center justify-center rounded-2xl bg-white/10 backdrop-blur-md text-white border border-white/20 opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-white/20 z-10"
                aria-label="بعدی"
            >
                <ChevronLeft class="size-6" />
            </button>
        </Carousel.Root>
    </div>
{/if}

<style>
    :global(.embla__container) {
        display: flex;
        flex-direction: row;
        height: 100%;
    }
</style>
