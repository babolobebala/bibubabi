<script setup lang="ts">
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Bell, CircleUserRound, House, LayoutGrid, LogOut } from 'lucide-vue-next';
import type { Component } from 'vue';
import { computed } from 'vue';

interface SharedNavItem {
    key: string;
    label: string;
    href?: string;
    icon?: Component;
    active?: boolean;
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
    { key: 'beranda', label: 'Beranda', icon: House, active: true },
    { key: 'menu-cepat', label: 'Menu Cepat', icon: LayoutGrid },
    { key: 'notifikasi', label: 'Notifkasi', icon: Bell },
    { key: 'landing-page', label: 'Saku Eksternal', icon: CircleUserRound },
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

const desktopNavItems = computed(() => fixedNavItems);
const mobileNavItems = computed(() => fixedNavItems.slice(0, 4));
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
                    <a href="/logout" class="inline-flex items-center gap-2">
                        <LogOut class="h-4 w-4" />
                        <span class="text-sm">Logout</span>
                    </a>
                </Button>
            </div>
        </header>

        <main
            class="mx-auto grid max-w-400 gap-4 px-3 py-4 sm:gap-5 sm:px-4 sm:py-5 lg:grid-cols-[240px_minmax(0,1fr)] xl:grid-cols-[240px_minmax(0,1fr)_184px]"
        >
            <aside class="hidden lg:flex lg:flex-col lg:gap-4">
                <Card class="gap-0 rounded-3xl border-border p-2">
                    <CardContent class="pt-0">
                        <div class="mt-1 flex flex-col items-center text-center">
                            <Avatar class="h-32 w-32 border-4 border-primary/90 bg-muted p-0.5 shadow-inner">
                                <AvatarFallback class="bg-muted text-muted-foreground">
                                    <CircleUserRound class="h-16 w-16" />
                                </AvatarFallback>
                            </Avatar>
                            <h2 class="mt-5 text-xl font-bold tracking-tight text-foreground">{{ fixedProfile.name }}</h2>
                            <p class="mt-2 rounded-full bg-accent px-4 py-1 text-sm font-semibold text-primary">
                                {{ fixedProfile.id }}
                            </p>
                            <p class="mt-4 text-sm font-semibold text-foreground">{{ fixedProfile.unit }}</p>
                            <p class="mt-1 text-sm leading-5 text-muted-foreground">{{ fixedProfile.organization }}</p>
                        </div>
                    </CardContent>
                </Card>

                <Card class="gap-0 rounded-2xl border-border p-2 py-2">
                    <CardContent class="p-0">
                        <a
                            v-for="item in desktopNavItems"
                            :key="item.key"
                            :href="item.href ?? '#'"
                            class="mb-1 flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition last:mb-0"
                            :class="
                                item.active ? 'bg-primary text-primary-foreground shadow-sm' : 'text-foreground hover:bg-muted hover:text-foreground'
                            "
                        >
                            <component :is="item.icon ?? LayoutGrid" class="h-4 w-4 shrink-0" />
                            <span>{{ item.label }}</span>
                        </a>
                    </CardContent>
                </Card>
            </aside>

            <section class="min-w-0 rounded-2xl border border-border bg-card shadow-sm">
                <slot />
            </section>

            <aside class="hidden xl:block">
                <Card class="sticky top-24 gap-0 rounded-2xl border-border py-0">
                    <CardHeader class="space-y-1 pb-3">
                        <CardTitle class="text-base font-bold text-primary">Pengingat</CardTitle>
                        <p class="text-xs leading-5 text-muted-foreground">
                            Secondary panel untuk notifikasi ringan, reminder, atau informasi singkat.
                        </p>
                    </CardHeader>
                    <CardContent class="space-y-3 pt-0">
                        <Card
                            v-for="reminder in fixedReminderItems"
                            :key="reminder.key"
                            class="gap-2 rounded-xl border-border bg-muted p-3 py-3 shadow-none"
                        >
                            <p class="text-sm font-semibold text-foreground">{{ reminder.title }}</p>
                            <p class="text-xs leading-5 text-muted-foreground">{{ reminder.description }}</p>
                        </Card>
                    </CardContent>
                </Card>
            </aside>
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
                    <a
                        :href="item.href ?? '#'"
                        class="flex flex-col items-center justify-center text-[11px] font-medium transition"
                        :class="item.active ? 'text-primary' : 'text-muted-foreground hover:text-foreground'"
                    >
                        <component :is="item.icon ?? LayoutGrid" class="mb-1 h-4 w-4" />
                        <span class="truncate">{{ item.label }}</span>
                    </a>
                </Button>
            </div>
        </nav>

        <div class="h-24 lg:hidden" />
    </div>
</template>
