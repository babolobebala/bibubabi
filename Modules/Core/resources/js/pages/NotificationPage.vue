<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';

const breadcrumbs = [
    { label: 'Home', href: '/app' },
    { label: 'Notifikasi' },
];

const notifications = [
    {
        key: 'n1',
        title: 'Review Knowledge Mingguan',
        message: 'Ada 3 draft knowledge yang belum dipublish minggu ini.',
        time: 'Hari ini',
    },
    {
        key: 'n2',
        title: 'Sinkronisasi Dokumen',
        message: 'Jadwalkan sinkronisasi dokumen sebelum Jumat pukul 16:00.',
        time: 'Kemarin',
    },
    {
        key: 'n3',
        title: 'Update Tools',
        message: 'Fitur geotagging gambar memiliki pembaruan konfigurasi template.',
        time: '2 hari lalu',
    },
];
</script>

<template>
    <ModuleContentShell :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <Card>
                <CardHeader>
                    <CardTitle class="text-lg sm:text-xl">Notifikasi</CardTitle>
                </CardHeader>
                <CardContent class="text-sm text-muted-foreground">
                    Ringkasan notifikasi internal. Integrasi data notifikasi real dapat ditambahkan di tahap berikutnya.
                </CardContent>
            </Card>

            <div class="space-y-3">
                <Card v-for="item in notifications" :key="item.key" class="py-0">
                    <CardContent class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-foreground">{{ item.title }}</p>
                                <p class="mt-1 text-xs leading-5 text-muted-foreground">{{ item.message }}</p>
                            </div>
                            <span class="shrink-0 text-[11px] text-muted-foreground">{{ item.time }}</span>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </ModuleContentShell>
</template>
