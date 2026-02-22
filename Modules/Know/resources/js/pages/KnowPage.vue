<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { BookOpen } from 'lucide-vue-next';
import SharedModuleLayout from '../../../../Shared/resources/js/components/layouts/SharedModuleLayout.vue';
</script>

<template>
    <SharedModuleLayout>
        <div class="border-b border-border px-4 py-4 sm:px-6">
            <div class="flex flex-wrap items-center gap-2 text-sm">
                <span class="font-semibold text-foreground">Layanan Lainnya</span>
                <span class="mx-1 text-border">|</span>
                <span class="text-muted-foreground">Home</span>
                <span class="text-border">›</span>
                <span class="font-semibold text-foreground">Layanan Lainnya</span>
            </div>
        </div>

        <div class="space-y-4 px-4 py-4 sm:px-6 sm:py-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex w-full max-w-md items-center gap-2 rounded-xl border border-input bg-background px-3 py-2 shadow-sm">
                    <BookOpen class="h-4 w-4 text-muted-foreground" />
                    <input
                        type="text"
                        placeholder="Cari layanan ..."
                        class="w-full border-0 bg-transparent p-0 text-sm text-foreground outline-none placeholder:text-muted-foreground"
                    />
                </div>

                <div class="flex items-center gap-2">
                    <Button variant="outline" class="border-border text-muted-foreground">List</Button>
                    <Button>Grid</Button>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                <Card v-for="item in 8" :key="item" class="rounded-2xl border-border shadow-sm">
                    <CardHeader class="pb-2">
                        <div class="flex items-start justify-between gap-2">
                            <Badge variant="secondary" class="text-primary">Layanan</Badge>
                            <span class="text-xs text-muted-foreground">#{{ item }}</span>
                        </div>
                        <CardTitle class="text-base">Kartu Konten {{ item }}</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div class="h-20 rounded-xl border border-dashed border-border bg-muted/60" />
                        <p class="text-sm leading-5 text-muted-foreground">
                            Placeholder area untuk isi module. Slot tengah ini nanti bisa dipakai lintas module.
                        </p>
                        <Button variant="outline" class="w-full border-border">Buka</Button>
                    </CardContent>
                </Card>
            </div>
        </div>
    </SharedModuleLayout>
</template>
