<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { ChevronRight, Grid3X3, List, Search, ShieldCheck } from 'lucide-vue-next';
import { ref } from 'vue';
import SharedModuleLayout from '../../../../Shared/resources/js/components/layouts/SharedModuleLayout.vue';

const apps = [
    'Update Data',
    'Kartu ASN Virtual',
    'Karis/Karsu Virtual',
    'Kompetensi ASN',
    'Indeks Profesionalitas ASN',
    'Peduli ASN Jaga ASN',
    'Disabilitas ASN',
    'Sertifikat Elektronik BSrE',
];

const viewMode = ref<'grid' | 'list'>('grid');
</script>

<template>
    <SharedModuleLayout>
        <div class="border-b border-border px-4 py-4 sm:px-6">
            <div class="flex flex-wrap items-center gap-2 text-sm">
                <span class="font-semibold text-foreground">Layanan ASN</span>
                <span class="mx-1 text-border">|</span>
                <span class="text-muted-foreground">Home</span>
                <span class="text-border">›</span>
                <span class="font-semibold text-foreground">Layanan ASN</span>
            </div>
        </div>

        <div class="space-y-4 px-4 py-4 sm:px-6 sm:py-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex w-full max-w-sm items-center gap-2 rounded-xl border border-input bg-background px-3 py-2 shadow-sm">
                    <Search class="h-4 w-4 text-muted-foreground" />
                    <input
                        type="text"
                        placeholder="Cari Layanan ASN ..."
                        class="w-full border-0 bg-transparent p-0 text-sm text-foreground outline-none placeholder:text-muted-foreground"
                    />
                </div>

                <div class="ml-auto flex items-center gap-2 rounded-xl bg-muted p-1">
                    <Button
                        size="icon"
                        :variant="viewMode === 'list' ? 'default' : 'ghost'"
                        class="h-9 w-9"
                        :class="viewMode === 'list' ? '' : 'text-muted-foreground'"
                        @click="viewMode = 'list'"
                    >
                        <List class="h-4 w-4" />
                    </Button>
                    <Button size="icon" :variant="viewMode === 'grid' ? 'default' : 'ghost'" class="h-9 w-9" @click="viewMode = 'grid'">
                        <Grid3X3 class="h-4 w-4" />
                    </Button>
                </div>
            </div>

            <div v-if="viewMode === 'grid'" class="hidden gap-2.5 md:grid md:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-5">
                <Card
                    v-for="app in apps"
                    :key="`grid-${app}`"
                    class="cursor-pointer rounded-lg border-border py-0 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <CardContent class="flex flex-col items-center gap-2.5 p-3.5 text-center">
                        <div class="grid h-12 w-12 place-items-center rounded-full border border-primary/15 bg-accent">
                            <ShieldCheck class="h-6 w-6 text-primary" />
                        </div>
                        <p class="line-clamp-2 min-h-9 text-[13px] leading-4 font-medium text-foreground">{{ app }}</p>
                    </CardContent>
                </Card>
            </div>

            <div v-else class="hidden space-y-3 md:block">
                <Card
                    v-for="app in apps"
                    :key="`desktop-list-${app}`"
                    class="cursor-pointer rounded-xl border-border py-0 shadow-sm transition hover:shadow-md"
                >
                    <CardContent class="flex items-center gap-3 p-3">
                        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-full border border-primary/15 bg-accent">
                            <ShieldCheck class="h-5 w-5 text-primary" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-foreground">{{ app }}</p>
                        </div>
                        <ChevronRight class="h-4 w-4 text-muted-foreground" />
                    </CardContent>
                </Card>
            </div>

            <div class="space-y-3 md:hidden">
                <Card
                    v-for="app in apps"
                    :key="`list-${app}`"
                    class="cursor-pointer rounded-xl border-border py-0 shadow-sm transition hover:shadow-md"
                >
                    <CardContent class="flex items-center gap-3 p-3">
                        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-full border border-primary/15 bg-accent">
                            <ShieldCheck class="h-5 w-5 text-primary" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-foreground">{{ app }}</p>
                        </div>
                        <ChevronRight class="h-4 w-4 text-muted-foreground" />
                    </CardContent>
                </Card>
            </div>
        </div>
    </SharedModuleLayout>
</template>
