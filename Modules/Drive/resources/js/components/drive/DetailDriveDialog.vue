<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { ExternalLink, User, Users, Shield, CheckCircle2, XCircle, FileText, Info, Link2 } from 'lucide-vue-next';
import type { DriveItem } from './drive-columns';

defineProps<{
    open: boolean;
    drive: DriveItem | null;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();
</script>

<template>
    <Dialog :open="open" @update:open="(val) => emit('update:open', val)">
        <DialogContent class="max-h-[90vh] overflow-y-auto border-t-4 border-t-primary sm:max-w-[450px]">
            <DialogHeader>
                <div class="mb-1 flex items-center gap-2 text-primary">
                    <Info class="h-5 w-5" />
                    <span class="text-xs font-bold tracking-widest uppercase">Detail Informasi</span>
                </div>
                <DialogTitle class="text-2xl font-extrabold tracking-tight">Lihat Drive</DialogTitle>
            </DialogHeader>

            <div v-if="drive" class="relative space-y-5 py-4">
                <!-- Art Background (Watermark) -->
                <div class="pointer-events-none absolute top-0 right-0 translate-x-1/4 -translate-y-1/4 transform opacity-[0.03]">
                    <Link2 class="h-64 w-64 rotate-12" />
                </div>

                <!-- Nama Drive -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-muted-foreground uppercase">Nama Drive</label>
                    <div class="rounded-lg border border-muted-foreground/10 bg-muted/30 p-3 font-bold text-slate-800">
                        {{ drive.nama }}
                    </div>
                </div>

                <!-- Link Drive -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-muted-foreground uppercase">URL / Link Drive</label>
                    <a
                        :href="drive.link || '#'"
                        target="_blank"
                        class="group flex items-center justify-between rounded-lg border border-blue-200/50 bg-blue-50/50 p-3 transition-all hover:bg-blue-50"
                    >
                        <span class="mr-2 truncate text-sm font-semibold text-blue-600">{{ drive.link }}</span>
                        <ExternalLink class="h-4 w-4 shrink-0 text-blue-500 transition-transform group-hover:scale-110" />
                    </a>
                </div>

                <!-- Jenis & Akses (Grid only on desktop) -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-muted-foreground uppercase">Jenis</label>
                        <div class="flex items-center gap-2 rounded-lg border border-muted-foreground/10 bg-muted/30 p-2.5">
                            <component :is="drive.jenis === 'personal' ? User : Users" class="h-4 w-4 text-primary" />
                            <span class="text-sm font-bold capitalize">{{ drive.jenis }}</span>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-muted-foreground uppercase">Akses</label>
                        <div class="flex items-center gap-2 rounded-lg border border-muted-foreground/10 bg-muted/30 p-2.5">
                            <Shield class="h-4 w-4 text-primary" />
                            <span class="text-sm font-bold capitalize">{{ drive.akses }}</span>
                        </div>
                    </div>
                </div>

                <!-- Pilih Pemilik -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-muted-foreground uppercase">Pemilik / Tim</label>
                    <div class="flex items-center gap-3 rounded-lg border border-muted-foreground/10 bg-muted/30 p-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10">
                            <User v-if="drive.jenis === 'personal'" class="h-4 w-4 text-primary" />
                            <Users v-else class="h-4 w-4 text-primary" />
                        </div>
                        <span class="text-sm font-bold text-slate-800">
                            {{ drive.jenis === 'personal' ? drive.personal_user?.nama || 'N/A' : drive.tim_role?.name || 'N/A' }}
                        </span>
                    </div>
                </div>

                <!-- Status -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-muted-foreground uppercase">Status</label>
                    <div
                        class="flex items-center gap-2 rounded-lg border p-3"
                        :class="
                            drive.status === 'success' ? 'border-green-200 bg-green-50/50 text-green-700' : 'border-red-200 bg-red-50/50 text-red-700'
                        "
                    >
                        <component :is="drive.status === 'success' ? CheckCircle2 : XCircle" class="h-5 w-5" />
                        <span class="text-sm font-black capitalize">{{ drive.status }}</span>
                    </div>
                </div>

                <!-- Catatan -->
                <div v-if="drive.catatan" class="space-y-1.5">
                    <label class="text-xs font-bold text-muted-foreground uppercase">Catatan</label>
                    <div class="flex gap-2 rounded-lg border border-amber-100 bg-amber-50/30 p-3 italic">
                        <FileText class="mt-0.5 h-4 w-4 shrink-0 text-amber-500" />
                        <p class="text-sm leading-relaxed text-slate-600">{{ drive.catatan }}</p>
                    </div>
                </div>
            </div>

            <DialogFooter class="gap-2 sm:justify-end">
                <Button variant="outline" class="w-full cursor-pointer sm:w-auto" @click="emit('update:open', false)"> Tutup </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
