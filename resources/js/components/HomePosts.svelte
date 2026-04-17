<script lang="ts">
    import { ArrowLeft, FileText, Calendar, User, ChevronLeft } from 'lucide-svelte';
    import { Link } from '@inertiajs/svelte';
    import { t, reveal } from '@/lib/utils';

    interface PostItem {
        id: number;
        title: string;
        slug: string;
        summary: string | null;
        image: string | null;
        published_at: string;
        category: { name: string; slug: string } | null;
        author: string;
    }

    interface Props {
        posts: PostItem[];
    }

    let { posts = [] }: Props = $props();
</script>

{#if posts.length > 0}
    <section class="mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl py-12">
        <div class="flex items-end justify-between mb-12" use:reveal>
            <div class="space-y-3">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800">
                    <FileText size={16} />
                    <span class="text-xs font-bold uppercase tracking-wider">{t('post.resource.plural_label')}</span>
                </div>
                <h2 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight">آخرین مطالب آموزشی و مقالات</h2>
                <p class="text-slate-500 dark:text-slate-400 max-w-2xl text-lg font-medium">دانستنی‌های پزشکی، راهنمای خرید تجهیزات و تازه‌های دنیای سلامت در مجله پارس طب</p>
            </div>
            <Link
                href="/blog"
                class="group flex items-center gap-2 text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 font-bold text-sm transition-all duration-300"
            >
                <span class="border-b-2 border-transparent group-hover:border-indigo-600 dark:group-hover:border-indigo-400 pb-0.5">{t('ui.view_all')}</span>
                <div class="size-10 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                    <ArrowLeft class="size-5" />
                </div>
            </Link>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {#each posts as post, i (post.id)}
                <article
                    class="group flex flex-col bg-white dark:bg-zinc-900 rounded-[2.5rem] overflow-hidden border border-slate-100 dark:border-zinc-800 shadow-sm hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-500"
                    use:reveal={{ delay: i * 100, y: 40 }}
                >
                    <!-- Image Wrapper -->
                    <Link href="/blog/{post.slug}" class="relative block aspect-video overflow-hidden">
                        {#if post.image}
                            <img
                                src={post.image}
                                alt={post.title}
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                            />
                        {:else}
                            <div class="w-full h-full bg-slate-100 flex items-center justify-center">
                                <FileText class="size-12 text-slate-300" />
                            </div>
                        {/if}

                        {#if post.category}
                            <div class="absolute top-4 right-4">
                                <span class="px-4 py-1.5 rounded-full bg-white/90 backdrop-blur-md text-xs font-black text-indigo-600 shadow-sm">
                                    {post.category.name}
                                </span>
                            </div>
                        {/if}
                    </Link>

                    <!-- Content -->
                    <div class="p-8 flex flex-col flex-1">
                        <div class="flex items-center gap-4 mb-4 text-xs font-bold text-slate-400 dark:text-slate-500">
                            <div class="flex items-center gap-1.5">
                                <Calendar size={14} class="text-indigo-500" />
                                <span>{post.published_at}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <User size={14} class="text-indigo-500" />
                                <span>{post.author}</span>
                            </div>
                        </div>

                        <Link href="/blog/{post.slug}" class="block group/title">
                            <h3 class="text-xl font-black text-slate-900 dark:text-white mb-3 line-clamp-2 group-hover/title:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300 leading-snug">
                                {post.title}
                            </h3>
                        </Link>

                        {#if post.summary}
                            <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed line-clamp-3 mb-6 font-medium">
                                {post.summary}
                            </p>
                        {/if}

                        <div class="mt-auto pt-6 border-t border-slate-50 dark:border-zinc-800">
                            <Link
                                href="/blog/{post.slug}"
                                class="inline-flex items-center gap-2 text-indigo-600 font-black text-sm group/btn"
                            >
                                <span>مشاهده مقاله</span>
                                <ChevronLeft class="size-4 transition-transform duration-300 group-hover/btn:-translate-x-1" />
                            </Link>
                        </div>
                    </div>
                </article>
            {/each}
        </div>
    </section>
{/if}
