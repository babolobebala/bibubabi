<script setup lang="ts">
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Bell,
    CircleUserRound,
    Home,
    LayoutGrid,
    PencilLine,
    Search,
} from 'lucide-vue-next';
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

interface SharedProfileAction {
    key: string;
    label: string;
    href?: string;
    icon?: Component;
}

const props = withDefaults(
    defineProps<{
        title?: string;
        subtitle?: string;
        profileName?: string;
        profileId?: string;
        profileUnit?: string;
        profileOrganization?: string;
        navItems?: SharedNavItem[];
        reminderItems?: SharedReminderItem[];
        profileActions?: SharedProfileAction[];
    }>(),
    {
        title: 'Workspace',
        subtitle: 'Ringkasan modul dan aktivitas terbaru',
        profileName: 'Nama Pengguna',
        profileId: '000000000000000000',
        profileUnit: 'Satuan Kerja',
        profileOrganization: 'Organisasi / Instansi',
        navItems: () => [
            { key: 'dashboard', label: 'Dashboard', icon: Home, active: true },
            { key: 'layanan', label: 'Layanan', icon: LayoutGrid },
            { key: 'profil', label: 'Profil', icon: CircleUserRound },
        ],
        reminderItems: () => [
            {
                key: 'welcome',
                title: 'Reminder',
                description: 'Panel kanan bisa dipakai untuk notifikasi singkat, deadline, atau quick tips.',
            },
        ],
        profileActions: () => [
            { key: 'lihat', label: 'Lihat Profil', icon: CircleUserRound },
            { key: 'edit', label: 'Edit Profil', icon: PencilLine },
        ],
    },
);

const desktopNavItems = computed(() => props.navItems ?? []);
const mobileNavItems = computed(() => (props.navItems ?? []).slice(0, 4));
</script>

<template>
    <div class="min-h-screen bg-secondary text-foreground">
        <div class="h-2 bg-linear-to-r from-primary via-primary/80 to-accent" />

        <header class="sticky top-0 z-30 border-b border-border bg-background/90 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-400 items-center justify-between px-4 sm:px-6">
                <div class="flex items-center gap-3">
                    <div
                        class="inline-flex items-center rounded-xl border border-border bg-card px-3 py-1.5 shadow-sm"
                    >
                        <span class="text-lg font-black tracking-wide text-primary sm:text-xl">MY</span>
                        <span class="text-lg font-black tracking-wide text-foreground sm:text-xl">ASN</span>
                    </div>
                    <div class="hidden md:block">
                        <p class="text-sm font-semibold text-foreground">{{ title }}</p>
                        <p class="text-xs text-muted-foreground">{{ subtitle }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-transparent text-muted-foreground transition hover:border-border hover:bg-muted hover:text-foreground"
                    >
                        <Search class="h-5 w-5" />
                    </button>
                    <button
                        type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-full border-2 border-primary bg-background text-primary shadow-sm transition hover:bg-accent"
                    >
                        <Bell class="h-5 w-5" />
                    </button>
                </div>
            </div>
        </header>

        <main class="mx-auto grid max-w-400 gap-4 px-3 py-4 sm:gap-5 sm:px-4 sm:py-5 lg:grid-cols-[240px_minmax(0,1fr)] xl:grid-cols-[240px_minmax(0,1fr)_184px]">
            <aside class="hidden lg:flex lg:flex-col lg:gap-4">
                <Card class="gap-0 rounded-3xl border-border py-0">
                    <CardHeader class="pb-2">
                        <div class="flex justify-end">
                            <Button
                                type="button"
                                variant="outline"
                                size="icon"
                                class="h-9 w-9 rounded-xl border-border bg-accent text-primary hover:bg-accent/80"
                            >
                                <PencilLine class="h-4 w-4" />
                            </Button>
                        </div>
                    </CardHeader>

                    <CardContent class="pt-0">
                        <div class="mt-1 flex flex-col items-center text-center">
                            <Avatar class="h-32 w-32 border-4 border-primary/90 bg-muted p-0.5 shadow-inner">
                                <AvatarFallback class="bg-muted text-muted-foreground">
                                    <CircleUserRound class="h-16 w-16" />
                                </AvatarFallback>
                            </Avatar>
                            <h2 class="mt-5 text-xl font-bold tracking-tight text-foreground">{{ profileName }}</h2>
                            <p class="mt-2 rounded-full bg-accent px-4 py-1 text-sm font-semibold text-primary">
                                {{ profileId }}
                            </p>
                            <p class="mt-4 text-sm font-semibold text-foreground">{{ profileUnit }}</p>
                            <p class="mt-1 text-sm leading-5 text-muted-foreground">{{ profileOrganization }}</p>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-2">
                            <Button
                                v-for="action in profileActions"
                                :key="action.key"
                                as-child
                                variant="outline"
                                class="h-auto rounded-xl border-border px-3 py-2.5 text-sm font-medium text-foreground hover:bg-accent hover:text-primary"
                            >
                                <a :href="action.href ?? '#'" class="inline-flex items-center justify-center gap-2">
                                    <component :is="action.icon ?? CircleUserRound" class="h-4 w-4" />
                                    <span>{{ action.label }}</span>
                                </a>
                            </Button>
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
                                item.active
                                    ? 'bg-primary text-primary-foreground shadow-sm'
                                    : 'text-foreground hover:bg-muted hover:text-foreground'
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
                            v-for="reminder in reminderItems"
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

        <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-border bg-background/95 px-2 pb-[max(env(safe-area-inset-bottom),0.5rem)] pt-2 shadow-[0_-8px_24px_rgba(15,23,42,0.08)] backdrop-blur lg:hidden">
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
