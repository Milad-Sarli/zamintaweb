<script lang="ts">
    import persianDate from 'persian-date';
    import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
    import { buttonVariants } from '@/components/ui/button';
    import { cn } from '@/lib/utils';
    import { ChevronLeft, ChevronRight } from 'lucide-svelte';

    interface Props {
        value?: string | null;
        onValueChange?: (val: string) => void;
        placeholder?: string;
        class?: string;
    }

    let { value = $bindable(null), onValueChange, placeholder = 'انتخاب تاریخ', class: className = '' }: Props = $props();

    let open = $state(false);
    let view: 'calendar' | 'months' | 'years' = $state('calendar');

    const todayP = new persianDate();
    let viewYear = $state(todayP.year());
    let viewMonth = $state(todayP.month());

    const months = ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'];
    const weekDays = ['ش', 'ی', 'د', 'س', 'چ', 'پ', 'ج'];

    let grid = $derived.by(() => {
        const first = new persianDate([viewYear, viewMonth, 1]);
        const end = first.endOf('month');
        const days = end.date();
        const startDay = first.day();
        const weeks = Math.ceil((days + startDay - 1) / 7);
        const rows: (number | null)[][] = [];
        let d = 1;
        for (let w = 0; w < weeks; w++) {
            const row: (number | null)[] = [];
            for (let c = 0; c < 7; c++) {
                if (w === 0 && c < startDay - 1) { row.push(null); }
                else if (d <= days) { row.push(d); d++; }
                else { row.push(null); }
            }
            rows.push(row);
        }
        return rows;
    });

    function pickDay(day: number) {
        const pd = new persianDate([viewYear, viewMonth, day]);
        const g = pd.toCalendar('gregorian');
        const val = `${g.year()}-${String(g.month()).padStart(2, '0')}-${String(g.date()).padStart(2, '0')}T00:00:00`;
        value = val;
        onValueChange?.(val);
        open = false;
    }

    let displayValue = $derived.by(() => {
        if (!value) return '';
        const d = new persianDate(new Date(value));
        return `${d.year()}/${String(d.month()).padStart(2, '0')}/${String(d.date()).padStart(2, '0')}`;
    });

    function isSelected(d: number) {
        if (!value) return false;
        const p = new persianDate(new Date(value));
        return p.year() === viewYear && p.month() === viewMonth && p.date() === d;
    }

    function isToday(d: number) {
        return todayP.year() === viewYear && todayP.month() === viewMonth && todayP.date() === d;
    }

    let decadeStart = $derived(Math.floor(viewYear / 10) * 10);
</script>

<Popover bind:open>
    <PopoverTrigger class={cn(
        buttonVariants({ variant: 'outline' }),
        'w-full justify-start gap-2 text-right font-normal',
        !value && 'text-muted-foreground',
        className
    )}>
        {#if value}
            <span>{displayValue}</span>
        {:else}
            <span>{placeholder}</span>
        {/if}
    </PopoverTrigger>

    <PopoverContent class="w-auto p-0" align="start" side="bottom">
        <div class="rounded-xl border bg-white p-4 shadow-lg dark:border-zinc-700 dark:bg-zinc-950" dir="rtl">
            {#if view === 'calendar'}
                <div class="mb-4 flex items-center justify-between gap-2">
                    <button type="button" onclick={() => { if (viewMonth === 1) { viewYear--; viewMonth = 12; } else { viewMonth--; } }} class="rounded-lg p-1.5 text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-zinc-800">
                        <ChevronRight class="size-4" />
                    </button>
                    <div class="flex items-center gap-1">
                        <button type="button" onclick={() => view = 'months'} class="rounded-lg border border-slate-200 bg-white px-3 py-1 text-sm font-bold hover:bg-slate-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white dark:hover:bg-zinc-800">
                            {months[viewMonth - 1]}
                        </button>
                        <button type="button" onclick={() => view = 'years'} class="rounded-lg border border-slate-200 bg-white px-3 py-1 text-sm font-bold hover:bg-slate-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white dark:hover:bg-zinc-800">
                            {viewYear}
                        </button>
                    </div>
                    <button type="button" onclick={() => { if (viewMonth === 12) { viewYear++; viewMonth = 1; } else { viewMonth++; } }} class="rounded-lg p-1.5 text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-zinc-800">
                        <ChevronLeft class="size-4" />
                    </button>
                </div>
                <div class="grid grid-cols-7 gap-1 text-center">
                    {#each weekDays as d}
                        <div class="py-1 text-xs font-bold text-slate-500 dark:text-slate-400">{d}</div>
                    {/each}
                </div>
                <div class="mt-1 grid grid-cols-7 gap-1 text-center">
                    {#each grid as week}
                        {#each week as day}
                            {#if day !== null}
                                <button type="button" onclick={() => pickDay(day)}
                                    class={cn(
                                        'flex size-9 items-center justify-center rounded-lg text-sm transition-colors',
                                        isSelected(day) ? 'bg-slate-900 text-white dark:bg-sky-500' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-zinc-800',
                                        isToday(day) && !isSelected(day) ? 'border border-sky-300 dark:border-sky-600' : ''
                                    )}
                                >
                                    {day}
                                </button>
                            {:else}
                                <div class="size-9"></div>
                            {/if}
                        {/each}
                    {/each}
                </div>

            {:else if view === 'months'}
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" onclick={() => view = 'calendar'} class="col-span-3 mb-2 rounded-lg px-3 py-1 text-xs text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-zinc-800">
                        ← بازگشت
                    </button>
                    {#each months as name, i}
                        <button type="button" onclick={() => { viewMonth = i + 1; view = 'calendar'; }}
                            class={cn(
                                'rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                                viewMonth === i + 1 ? 'bg-slate-900 text-white dark:bg-sky-500' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-zinc-800'
                            )}
                        >
                            {name}
                        </button>
                    {/each}
                </div>

            {:else if view === 'years'}
                <div class="space-y-1">
                    <button type="button" onclick={() => view = 'calendar'} class="mb-2 rounded-lg px-3 py-1 text-xs text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-zinc-800">
                        ← بازگشت
                    </button>
                    {#each Array(21) as _, i}
                        {@const y = viewYear - 10 + i}
                        <button type="button" onclick={() => { viewYear = y; view = 'calendar'; }}
                            class={cn(
                                'block w-full rounded-lg px-3 py-1 text-sm text-center transition-colors',
                                viewYear === y ? 'bg-slate-900 text-white dark:bg-sky-500' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-zinc-800'
                            )}
                        >
                            {y}
                        </button>
                    {/each}
                </div>
            {/if}
        </div>
    </PopoverContent>
</Popover>
