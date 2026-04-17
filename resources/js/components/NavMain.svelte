<script lang="ts">
    import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
    import { t } from '@/lib/utils';
    import type { NavItem } from '@/types';
    import { Link, page } from '@inertiajs/svelte';

    interface Props {
        items: NavItem[];
    }

    let { items = [] }: Props = $props();

    function isActive(href: string): boolean {
        const pageUrl = $page.url;
        const pagePath = pageUrl.split('#')[0];
        const pageHash = pageUrl.split('#')[1] || 'overview';
        const itemHash = href.split('#')[1] || 'overview';
        return pagePath === href.split('#')[0] && pageHash === itemHash;
    }
</script>

<SidebarGroup class="px-2 py-0">
    <SidebarGroupLabel>{t('ui.platform')}</SidebarGroupLabel>
    <SidebarMenu>
        {#each items as item (item.title)}
            <SidebarMenuItem>
                <Link href={item.href} class="block w-full">
                    <SidebarMenuButton isActive={isActive(item.href)}>
                        {#snippet tooltipContent()}
                            {item.title}
                        {/snippet}
                        {#if item.icon}
                            {@const Icon = item.icon}
                            <Icon class="h-4 w-4 shrink-0" />
                        {/if}
                        <span>{item.title}</span>
                    </SidebarMenuButton>
                </Link>
            </SidebarMenuItem>
        {/each}
    </SidebarMenu>
</SidebarGroup>
