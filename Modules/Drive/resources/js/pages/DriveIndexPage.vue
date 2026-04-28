<script setup lang="ts">
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import { getModulePageBreadcrumbs, type ModuleNavigationConfig } from '../../../../Shared/resources/js/lib/module-navigation';
import moduleNavigation from '../config/module-navigation.json';

const pageBreadcrumbs = getModulePageBreadcrumbs(moduleNavigation as ModuleNavigationConfig, 'drive-index');

interface DriveLink {
    id: number;
    nama: string;
    link: string;
}

defineProps<{
    drives: DriveLink[];
}>();
</script>

<template>
    <ModuleContentShell :breadcrumbs="pageBreadcrumbs">
        <div class="flex min-h-[calc(100vh-200px)] flex-col items-center justify-center px-4 py-4">
            <!-- Profile/Header Area -->
            <div class="mb-4 text-center">
                <div class="mx-auto mb-2 flex h-14 w-14 items-center justify-center rounded-full border-2 border-primary/20 bg-primary/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"
                        />
                    </svg>
                </div>
                <h1 class="text-lg font-bold text-foreground">Direktori Drive</h1>
                <p class="text-xs text-muted-foreground italic">Kumpulan akses penyimpanan dokumen digital</p>
            </div>

            <!-- Linktree Style List -->
            <div class="w-full max-w-[450px] space-y-3">
                <template v-if="drives && drives.length > 0">
                    <a
                        v-for="drive in drives"
                        :key="drive.id"
                        :href="drive.link"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="group relative flex w-full items-center justify-center rounded-lg border border-muted-foreground/20 bg-background p-2.5 transition-all duration-200 hover:-translate-y-0.5 hover:border-primary hover:bg-primary hover:text-primary-foreground active:translate-y-0"
                    >
                        <span class="text-sm font-semibold tracking-wide">{{ drive.nama }}</span>

                        <!-- Subtle external link icon on hover -->
                        <div class="absolute right-3 opacity-0 transition-opacity group-hover:opacity-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                                />
                            </svg>
                        </div>
                    </a>
                </template>

                <div v-else class="rounded-2xl border-2 border-dashed border-muted bg-muted/50 py-12 text-center">
                    <p class="text-muted-foreground italic">Belum ada link drive yang tersedia.</p>
                </div>
            </div>
        </div>
    </ModuleContentShell>
</template>

<style scoped>
/* Optional: Add custom animations if needed */
</style>
