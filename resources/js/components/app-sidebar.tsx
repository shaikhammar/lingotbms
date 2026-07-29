import { Link } from '@inertiajs/react';
import { BookOpen, FolderGit2, LayoutGrid, User2, Wallet2, Folder, Sheet, Printer, Cog } from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
        isAvailable: true,
        hint: 'View your dashboard with all the important metrics and information at a glance.'
    },
    {
        title: 'Clients',
        href: '#',
        icon: User2,
        isAvailable: false,
    },
    {
        title: 'Rates',
        href: '#',
        icon: Wallet2,
        isAvailable: false,
    },
    {
        title: 'Projects',
        href: '#',
        icon: Folder,
        isAvailable: false,
    },
    {
        title: 'Quotes',
        href: '#',
        icon: Sheet,
        isAvailable: false,
    },
    {
        title: 'Invoices',
        href: '#',
        icon: Printer,
        isAvailable: false,
    },
    {
        title: 'Settings',
        href: '#',
        icon: Cog,
        isAvailable: true,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/react-starter-kit',
        icon: FolderGit2,
        isAvailable: false,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#react',
        icon: BookOpen,
        isAvailable: false,
    },
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
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
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
