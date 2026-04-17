<script lang="ts">
    import * as DropdownMenu from '@/components/ui/dropdown-menu';
    import { Button } from '@/components/ui/button';
    import { useAppearance } from '@/hooks/useAppearance.svelte';
    import { Sun, Moon, Monitor, Check } from 'lucide-svelte';
    import { t } from '@/lib/utils';

    const appearanceManager = useAppearance();

    const themes = [
        { value: 'light', icon: Sun, label: 'ui.light' },
        { value: 'dark', icon: Moon, label: 'ui.dark' },
        { value: 'system', icon: Monitor, label: 'ui.system' },
    ] as const;
</script>

<DropdownMenu.Root>
    <DropdownMenu.Trigger>
        <Button variant="ghost" size="icon" class="size-9 rounded-lg">
            {#if appearanceManager.appearance === 'light'}
                <Sun class="size-5" />
            {:else}
                <Moon class="size-5" />
            {/if}
            <span class="sr-only">Toggle theme</span>
        </Button>
    </DropdownMenu.Trigger>
    <DropdownMenu.Content align="end" class="w-40">
        {#each themes as { value, icon: Icon, label } (value)}
            <DropdownMenu.Item
                onclick={() => appearanceManager.updateAppearance(value)}
                class="flex items-center justify-between"
            >
                <div class="flex items-center gap-2">
                    <Icon class="size-4" />
                    <span>{t(label)}</span>
                </div>
                {#if appearanceManager.appearance === value}
                    <Check class="size-4" />
                {/if}
            </DropdownMenu.Item>
        {/each}
    </DropdownMenu.Content>
</DropdownMenu.Root>
