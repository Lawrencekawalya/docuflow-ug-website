<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { MessageCircle } from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { home } from '@/routes';
import { index as supportConversations } from '@/routes/support/conversations';
import type { NavItem } from '@/types';

const page = usePage();
const isSupportAgent = computed(
    () => page.props.auth.user?.is_support_agent === true,
);
const mainNavItems = computed<NavItem[]>(() =>
    isSupportAgent.value
        ? [
              {
                  title: 'Conversations',
                  href: supportConversations(),
                  icon: MessageCircle,
                  badge: page.props.supportUnreadCount,
              },
          ]
        : [],
);
const workspaceHome = computed(() =>
    isSupportAgent.value ? supportConversations() : home(),
);
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="workspaceHome">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
