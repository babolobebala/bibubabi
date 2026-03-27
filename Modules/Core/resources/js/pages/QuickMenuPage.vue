<script setup lang="ts">
import { Card, CardHeader, CardTitle } from '@/components/ui/card';
import { Link, usePage } from '@inertiajs/vue3';
import { Inbox } from 'lucide-vue-next';
import { computed } from 'vue';
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import UpdateQuickMenuDialog from '../components/UpdateQuickMenuDialog.vue';
import { getCoreModuleEntries } from '../lib/core-menu';

const breadcrumbs = [
    { label: 'Home', href: '/app' },
    { label: 'Menu Cepat' },
];

const userRoles = (usePage().props as any).auth?.roles ?? [];
// Mengambil modul SECARA KETAT SESUAI ROLE (tanpa membocorkan menu admin jika bukan admin)
const allModules = getCoreModuleEntries(userRoles);

// Mengekstrak semua sub-halaman potensial ditambah modul Single-Page (Direct Link)
const allAvailableFeatures = computed(() => {
    return allModules.flatMap((mod) => {
        if (mod.features && mod.features.length > 0) {
            return mod.features;
        } else {
            // Jika modul ini tidak punya sub-halaman (Single-Page Module seperti Know)
            // dan memiliki href langsung (bukan anchor #), kita masukkan sebagai fitur yang bisa dipilih.
            if (mod.menu.href && !mod.menu.href.includes('#')) {
                return [mod.menu];
            }
            return [];
        }
    });
});

// Mengekstrak pilihan Key dari profil database user
const selectedKeys = computed<string[]>(() => {
    return (usePage().props as any).auth?.user?.quick_menu_keys ?? [];
});

// Data final mutlak yang muncul di Home
const activeQuickMenus = computed(() => {
    if (!selectedKeys.value || selectedKeys.value.length === 0) return [];
    
    return allAvailableFeatures.value.filter(feature => 
        selectedKeys.value.includes(feature.key)
    );
});

</script>

<template>
    <ModuleContentShell :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <Card>
                <CardHeader class="flex flex-row items-start justify-between">
                    <div>
                        <CardTitle class="text-lg sm:text-xl">Menu Cepat</CardTitle>
                        <p class="mt-2 text-sm text-muted-foreground">
                             Pintasan ke halaman yang paling sering dipakai. Sesuaikan daftar ini sesuai kebutuhan operasional.
                        </p>
                    </div>
                    <UpdateQuickMenuDialog :modules="allModules" />
                </CardHeader>
            </Card>

            <div v-if="activeQuickMenus.length > 0" class="grid gap-3 md:grid-cols-2">
                <Link
                    v-for="menu in activeQuickMenus"
                    :key="menu.key"
                    :href="menu.href"
                    class="block rounded-xl border border-border bg-card p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <p class="text-sm font-semibold text-foreground">{{ menu.title }}</p>
                    <p class="mt-1 text-xs leading-5 text-muted-foreground">{{ menu.description }}</p>
                </Link>
            </div>
            
            <div v-else class="flex flex-col items-center justify-center rounded-xl border border-dashed py-12 text-center bg-muted/30">
                <div class="grid h-12 w-12 place-items-center rounded-full bg-accent text-muted-foreground mb-4">
                     <Inbox class="w-6 h-6" />
                </div>
                <h3 class="text-lg font-medium">Belum ada menu tersimpan</h3>
                <p class="text-sm text-muted-foreground max-w-sm mt-1 mb-4">
                     Anda belum memilih menu apa pun. Silakan klik tombol kustomisasi di atas untuk menambahkan pintasan.
                </p>
                <UpdateQuickMenuDialog :modules="allModules" />
            </div>
        </div>
    </ModuleContentShell>
</template>
