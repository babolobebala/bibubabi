<script setup lang="ts">
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Link, usePage } from '@inertiajs/vue3';
import { Bell, CircleUserRound, House, LayoutGrid, LogOut } from 'lucide-vue-next';
import type { Component } from 'vue';
import { computed } from 'vue';

interface SharedNavItem {
    key: string;
    label: string;
    href?: string;
    icon?: Component;
    match?: string[];
    exact?: boolean;
}

interface SharedReminderItem {
    key: string;
    title: string;
    description: string;
}

const fixedHeader = {
    title: 'Knowledge Center',
    subtitle: 'Contoh shell layout reusable dari module Shared',
};

const fixedProfile = {
    name: 'Fatih Mahawisesa',
    id: '200206252024121002',
    unit: 'BPS Kabupaten Sumbawa Barat',
    organization: 'Badan Pusat Statistik Wilayah Kanreg Denpasar',
};

const fixedNavItems: SharedNavItem[] = [
    { key: 'beranda', label: 'Beranda', icon: House, href: '/app', match: ['/app'], exact: true },
    { key: 'menu-cepat', label: 'Menu Cepat', icon: LayoutGrid, href: '/app/tools', match: ['/app/tools'] },
    { key: 'notifikasi', label: 'Notifkasi', icon: Bell, href: '/app', match: ['/app/notifications'] },
    { key: 'landing-page', label: 'Saku Eksternal', icon: CircleUserRound, href: '/', match: ['/'] },
];

const fixedReminderItems: SharedReminderItem[] = [
    {
        key: 'review',
        title: 'Review Konten',
        description: 'Ada 3 draft knowledge yang belum dipublish minggu ini.',
    },
    {
        key: 'sync',
        title: 'Sinkronisasi Data',
        description: 'Jadwalkan sinkronisasi dokumen sebelum Jumat pukul 16:00.',
    },
];

const page = usePage();

const currentPath = computed(() => {
    const [path] = page.url.split('?');
    return path || '/';
});

const navItemsWithActive = computed(() =>
    fixedNavItems.map((item) => ({
        ...item,
        active: isNavItemActive(currentPath.value, item),
    })),
);

const desktopNavItems = computed(() => navItemsWithActive.value);
const mobileNavItems = computed(() => navItemsWithActive.value.slice(0, 4));

function isNavItemActive(path: string, item: SharedNavItem): boolean {
    const patterns = item.match ?? (item.href ? [item.href] : []);

    if (item.exact) {
        return patterns.some((pattern) => path === pattern);
    }

    return patterns.some((pattern) => path === pattern || path.startsWith(`${pattern}/`));
}
</script>

<template>
    <div class="min-h-screen">
        <div class="h-2 bg-linear-to-r from-primary via-primary/80 to-accent" />

        <header class="sticky top-0 z-30 border-b border-border bg-background/90 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-400 items-center justify-between px-4 sm:px-6">
                <div class="flex items-center gap-3">
                    <div class="inline-flex items-center rounded-xl border border-border bg-card px-3 py-1.5 shadow-sm">
                        <span class="text-lg font-black tracking-wide text-primary sm:text-xl">MY</span>
                        <span class="text-lg font-black tracking-wide text-foreground sm:text-xl">ASN</span>
                    </div>
                    <div class="hidden md:block">
                        <p class="text-sm font-semibold text-foreground">{{ fixedHeader.title }}</p>
                        <p class="text-xs text-muted-foreground">{{ fixedHeader.subtitle }}</p>
                    </div>
                </div>

                <Button as-child variant="outline" class="cursor-pointer rounded-xl border-border bg-card px-3 hover:bg-accent">
                    <Link href="/logout" class="inline-flex items-center gap-2">
                        <LogOut class="h-4 w-4" />
                        <span class="text-sm">Logout</span>
                    </Link>
                </Button>
            </div>
        </header>

        <main
            class="mx-auto grid max-w-400 gap-4 px-3 py-4 sm:gap-5 sm:px-4 sm:py-5 lg:grid-cols-[176px_minmax(0,1fr)]"
        >
            <aside class="hidden lg:flex lg:flex-col lg:gap-4">
                <Card class="gap-0 rounded-2xl border-border p-1.5">
                    <CardContent class="pt-0">
                        <div class="mt-1 flex flex-col items-center text-center">
                            <Avatar class="h-20 w-20 border-3 border-primary/90 bg-muted p-0.5 shadow-inner">
                                <AvatarFallback class="bg-muted text-muted-foreground">
                                    <CircleUserRound class="h-10 w-10" />
                                </AvatarFallback>
                            </Avatar>
                            <h2 class="mt-3 text-sm font-bold leading-4 tracking-tight text-foreground">{{ fixedProfile.name }}</h2>
                            <p class="mt-2 rounded-full bg-accent px-2.5 py-0.5 text-[11px] font-semibold text-primary">
                                {{ fixedProfile.id }}
                            </p>
                            <p class="mt-3 text-xs font-semibold leading-4 text-foreground">{{ fixedProfile.unit }}</p>
                            <p class="mt-1 text-[11px] leading-4 text-muted-foreground">{{ fixedProfile.organization }}</p>
                        </div>
                    </CardContent>
                </Card>

                <Card class="gap-0 rounded-xl border-border p-1.5 py-1.5">
                    <CardContent class="p-0">
                        <Link
                            v-for="item in desktopNavItems"
                            :key="item.key"
                            :href="item.href ?? '#'"
                            class="mb-1 flex items-center gap-2 rounded-lg px-2 py-2 text-xs font-medium transition last:mb-0"
                            :class="
                                item.active ? 'bg-primary text-primary-foreground shadow-sm' : 'text-foreground hover:bg-muted hover:text-foreground'
                            "
                        >
                            <component :is="item.icon ?? LayoutGrid" class="h-3.5 w-3.5 shrink-0" />
                            <span>{{ item.label }}</span>
                        </Link>
                    </CardContent>
                </Card>

                <Card class="gap-0 rounded-xl border-border py-0">
                    <CardHeader class="space-y-1 px-3 py-3 pb-2">
                        <CardTitle class="text-xs font-bold text-primary">Pengingat</CardTitle>
                        <p class="text-[11px] leading-4 text-muted-foreground">
                            Ringkasan notifikasi dan reminder singkat.
                        </p>
                    </CardHeader>
                    <CardContent class="space-y-2 px-3 pt-0 pb-3">
                        <Card
                            v-for="reminder in fixedReminderItems"
                            :key="reminder.key"
                            class="gap-1 rounded-lg border-border bg-muted p-2 py-2 shadow-none"
                        >
                            <p class="text-[11px] font-semibold leading-4 text-foreground">{{ reminder.title }}</p>
                            <p class="text-[10px] leading-4 text-muted-foreground">{{ reminder.description }}</p>
                        </Card>
                    </CardContent>
                </Card>
            </aside>

            <section class="min-w-0 rounded-2xl border border-border bg-card shadow-sm">
                <slot />
            </section>

        </main>

        <nav
            class="fixed inset-x-0 bottom-0 z-40 border-t border-border bg-background/95 px-2 pt-2 pb-[max(env(safe-area-inset-bottom),0.5rem)] shadow-[0_-8px_24px_rgba(15,23,42,0.08)] backdrop-blur lg:hidden"
        >
            <div class="grid grid-cols-4 gap-1">
                <Button
                    v-for="item in mobileNavItems"
                    :key="`mobile-${item.key}`"
                    as-child
                    variant="ghost"
                    class="h-auto min-h-14 rounded-xl px-1 py-2"
                >
                    <Link
                        :href="item.href ?? '#'"
                        class="flex flex-col items-center justify-center text-[11px] font-medium transition"
                        :class="item.active ? 'text-primary' : 'text-muted-foreground hover:text-foreground'"
                    >
                        <component :is="item.icon ?? LayoutGrid" class="mb-1 h-4 w-4" />
                        <span class="truncate">{{ item.label }}</span>
                    </Link>
                </Button>
            </div>
        </nav>

        <div class="h-24 lg:hidden" />
    </div>
</template>
