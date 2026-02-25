<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Link } from '@inertiajs/vue3';
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';

const breadcrumbs = [
    { label: 'Home', href: '/app' },
    { label: 'Menu Cepat' },
];

const quickMenus = [
    {
        key: 'app-home',
        title: 'Beranda Internal',
        description: 'Akses hub aplikasi internal dan pilih module yang dibutuhkan.',
        href: '/app',
    },
    {
        key: 'know-list',
        title: 'Lihat Knowledge',
        description: 'Buka daftar knowledge untuk membaca panduan dan dokumentasi.',
        href: '/knows',
    },
    {
        key: 'know-create',
        title: 'Buat Knowledge',
        description: 'Tambah knowledge baru untuk dibagikan ke tim internal.',
        href: '/knows/create',
    },
    {
        key: 'tool-geotagging',
        title: 'Geotagging Gambar',
        description: 'Tambahkan geotag dan informasi overlay pada gambar operasional.',
        href: '/app/tools/geotagging-gambar',
    },
];
</script>

<template>
    <ModuleContentShell :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <Card>
                <CardHeader>
                    <CardTitle class="text-lg sm:text-xl">Menu Cepat</CardTitle>
                </CardHeader>
                <CardContent class="text-sm text-muted-foreground">
                    Pintasan ke halaman yang paling sering dipakai. Sesuaikan daftar ini sesuai kebutuhan operasional.
                </CardContent>
            </Card>

            <div class="grid gap-3 md:grid-cols-2">
                <Link
                    v-for="menu in quickMenus"
                    :key="menu.key"
                    :href="menu.href"
                    class="block rounded-xl border border-border bg-card p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <p class="text-sm font-semibold text-foreground">{{ menu.title }}</p>
                    <p class="mt-1 text-xs leading-5 text-muted-foreground">{{ menu.description }}</p>
                </Link>
            </div>
        </div>
    </ModuleContentShell>
</template>
