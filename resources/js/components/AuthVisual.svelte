<script lang="ts">
    interface Props {
        class?: string;
    }

    let { class: className = '' }: Props = $props();

    const codeSnippets = [
        { text: '&lt;html&gt;', color: 'text-orange-400', delay: '0s', x: '10%', y: '15%', dur: '20s' },
        { text: '&lt;/&gt;', color: 'text-sky-400', delay: '2s', x: '75%', y: '20%', dur: '25s' },
        { text: '{ . . . }', color: 'text-yellow-400', delay: '4s', x: '20%', y: '60%', dur: '22s' },
        { text: 'function()', color: 'text-emerald-400', delay: '1s', x: '80%', y: '55%', dur: '28s' },
        { text: 'const', color: 'text-violet-400', delay: '3s', x: '15%', y: '80%', dur: '18s' },
        { text: 'npm run dev', color: 'text-pink-400', delay: '5s', x: '70%', y: '10%', dur: '24s' },
        { text: 'import', color: 'text-cyan-400', delay: '6s', x: '85%', y: '75%', dur: '26s' },
        { text: '=&gt;', color: 'text-amber-400', delay: '7s', x: '5%', y: '40%', dur: '21s' },
        { text: 'return', color: 'text-lime-400', delay: '8s', x: '90%', y: '35%', dur: '23s' },
        { text: 'console.log()', color: 'text-teal-400', delay: '2.5s', x: '30%', y: '10%', dur: '27s' },
        { text: 'useEffect', color: 'text-rose-400', delay: '4.5s', x: '60%', y: '85%', dur: '19s' },
        { text: '&lt;div&gt;', color: 'text-blue-400', delay: '6.5s', x: '40%', y: '95%', dur: '30s' },
        { text: 'npm install', color: 'text-fuchsia-400', delay: '9s', x: '50%', y: '5%', dur: '22s' },
        { text: 'git commit', color: 'text-green-400', delay: '1.5s', x: '65%', y: '45%', dur: '29s' },
        { text: '&lt;template&gt;', color: 'text-indigo-400', delay: '3.5s', x: '45%', y: '70%', dur: '17s' },
    ];

    const lines = [
        { w: '70%', x: '12%', delay: '8s' },
        { w: '50%', x: '25%', delay: '9.5s' },
        { w: '85%', x: '10%', delay: '11s' },
        { w: '40%', x: '35%', delay: '12.5s' },
        { w: '60%', x: '20%', delay: '14s' },
    ];
</script>

<div class={className} style="position: relative; overflow: hidden;">
    <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-950"></div>

    <!-- Grid overlay -->
    <div class="absolute inset-0 bg-[linear-gradient(rgba(99,102,241,0.04)_1px,transparent_1px),linear-gradient(90deg,rgba(99,102,241,0.04)_1px,transparent_1px)] [background-size:48px_48px]"></div>

    <!-- Radial glow -->
    <div class="absolute left-1/2 top-1/2 h-80 w-80 -translate-x-1/2 -translate-y-1/2 rounded-full bg-indigo-500/10 blur-[100px]"></div>
    <div class="absolute right-1/4 top-1/4 h-40 w-40 rounded-full bg-sky-500/8 blur-[80px]"></div>
    <div class="absolute bottom-1/4 left-1/4 h-40 w-40 rounded-full bg-purple-500/8 blur-[80px]"></div>

    <!-- Floating code snippets -->
    {#each codeSnippets as snippet}
        <div
            class="floating-code absolute select-none font-mono text-xs font-bold opacity-0 sm:text-sm {snippet.color}"
            style="left: {snippet.x}; top: {snippet.y}; animation: floatCode {snippet.dur} ease-in-out {snippet.delay} infinite;"
        >
            {snippet.text}
        </div>
    {/each}

    <!-- Terminal window -->
    <div class="terminal-window absolute left-1/2 top-1/2 w-56 -translate-x-1/2 -translate-y-1/2 overflow-hidden rounded-xl border border-white/10 bg-black/60 shadow-2xl shadow-indigo-500/10 backdrop-blur-xl sm:w-72">
        <div class="flex items-center gap-1.5 border-b border-white/10 px-3 py-2.5">
            <span class="h-2.5 w-2.5 rounded-full bg-rose-500/80"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-amber-500/80"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500/80"></span>
            <span class="mr-2 text-[10px] font-medium text-slate-500">terminal</span>
        </div>
        <div class="space-y-1.5 px-3 py-3 font-mono text-[11px] leading-relaxed sm:text-xs">
            {#each lines as line, i}
                <div class="flex" style="animation: typeLine 0.1s ease-out {line.delay} both;">
                    <span class="text-emerald-400">$</span>
                    <span class="mr-2 text-slate-400">&nbsp;</span>
                    <span class="text-slate-200">{'```'}</span>
                </div>
            {/each}
            <div class="flex" style="animation: blink 1s step-end infinite;">
                <span class="text-emerald-400">$</span>
                <span class="mr-2 text-slate-400">&nbsp;</span>
                <span class="inline-block h-4 w-2 animate-pulse bg-slate-300"></span>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes floatCode {
        0% { opacity: 0; transform: translateY(30px) scale(0.9); }
        5% { opacity: 0.6; }
        10% { opacity: 1; transform: translateY(0) scale(1); }
        80% { opacity: 1; }
        90% { opacity: 0.3; }
        100% { opacity: 0; transform: translateY(-30px) scale(0.9); }
    }

    @keyframes typeLine {
        0% { opacity: 0; transform: translateX(-8px); }
        100% { opacity: 1; transform: translateX(0); }
    }

    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0; }
    }
</style>
