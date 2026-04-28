<script setup lang="ts">
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { Info } from 'lucide-vue-next';
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import { getModulePageBreadcrumbs, type ModuleNavigationConfig } from '../../../../Shared/resources/js/lib/module-navigation';
import moduleNavigation from '../config/module-navigation.json';

const pageBreadcrumbs = getModulePageBreadcrumbs(moduleNavigation as ModuleNavigationConfig, 'drive-index');

interface DriveLink {
    id: number;
    nama: string;
    link: string;
    jenis: 'personal' | 'tim';
    personal: number | null;
    tim: number | null;
    personal_user?: {
        username: string;
    } | null;
    tim_role?: {
        name: string;
    } | null;
}

defineProps<{
    drives: DriveLink[];
}>();

const getDriveInfoText = (drive: DriveLink): string => {
    if (drive.jenis === 'personal') {
        return `Personal:\n${drive.personal_user?.username ?? '-'}`;
    }

    return `Role:\n${drive.tim_role?.name ?? '-'}`;
};
</script>

<template>
    <ModuleContentShell :breadcrumbs="pageBreadcrumbs">
        <div class="flex min-h-[calc(100vh-250px)] flex-col items-center justify-center px-4 py-6">
            <!-- Profile/Header Area -->
            <div class="mb-6 text-center">
                <div class="mx-auto mb-2 flex h-12 w-12 items-center justify-center rounded-xl border-2 border-primary bg-primary/10 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"
                        />
                    </svg>
                </div>
                <h1 class="text-lg font-black tracking-tighter text-foreground uppercase">Direktori Drive</h1>
                <p class="text-[10px] font-bold text-muted-foreground/60 uppercase tracking-widest">Storage Access</p>
            </div>

            <!-- Compact Bold List -->
            <div class="w-full max-w-[340px] space-y-2.5">
                <template v-if="drives && drives.length > 0">
                    <TooltipProvider :delay-duration="0">
                        <div v-for="drive in drives" :key="drive.id" class="flex items-center gap-2">
                            <a
                                :href="drive.link"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="group relative flex w-full items-center justify-center rounded-lg border-2 border-primary bg-background p-3 text-center transition-all duration-200 hover:bg-primary hover:text-primary-foreground hover:shadow-md hover:shadow-primary/20 active:scale-[0.98]"
                            >
                                <span class="text-xs font-extrabold uppercase tracking-widest">{{ drive.nama }}</span>
                            </a>

                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <button type="button" class="shrink-0 cursor-default text-muted-foreground transition-colors hover:text-primary" aria-label="Informasi drive">
                                        <Info class="h-4 w-4" />
                                    </button>
                                </TooltipTrigger>
                                <TooltipContent side="right" class="text-[10px] font-semibold tracking-wide">
                                    <span class="block leading-tight whitespace-pre-line">{{ getDriveInfoText(drive) }}</span>
                                </TooltipContent>
                            </Tooltip>
                        </div>
                    </TooltipProvider>
                </template>

                <div v-else class="rounded-xl border-2 border-dashed border-muted bg-muted/20 py-8 text-center">
                    <p class="text-[10px] font-bold text-muted-foreground/50 uppercase tracking-widest italic">No links available</p>
                </div>
            </div>
        </div>
    </ModuleContentShell>
</template>

<style scoped>
/* Optional: Add custom animations if needed */
</style>
