<script lang="ts">
    import NavFooter from '@/components/NavFooter.svelte';
    import NavMain from '@/components/NavMain.svelte';
    import NavUser from '@/components/NavUser.svelte';
    import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
    import { type NavItem } from '@/types';
    import { Link } from '@inertiajs/svelte';
    import { t } from '@/lib/utils';
    import { BookOpen, LayoutGrid, ShieldCheck, Video } from 'lucide-svelte';
    import AppLogo from './AppLogo.svelte';

    const mainNavItems: NavItem[] = [
        {
            title: t('ui.overview'),
            href: '/dashboard#overview',
            icon: LayoutGrid,
        },
        {
            title: t('ui.active_courses'),
            href: '/dashboard#courses',
            icon: BookOpen,
        },
        {
            title: t('ui.educational_videos'),
            href: '/dashboard#videos',
            icon: Video,
        },
        {
            title: t('ui.payment_status'),
            href: '/dashboard#payments',
            icon: ShieldCheck,
        },
    ];

    const footerNavItems: NavItem[] = [
        {
            title: 'مشاهده سایت',
            href: '/',
            icon: BookOpen,
        },
    ];
</script>

<Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
        <SidebarMenu>
            <SidebarMenuItem>
                <SidebarMenuButton size="lg">
                    <Link href="/dashboard">
                        <AppLogo />
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
        <NavMain items={mainNavItems} />
    </SidebarContent>

    <SidebarFooter>
        <NavFooter items={footerNavItems} class="mt-auto" />
        <NavUser />
    </SidebarFooter>
</Sidebar>
