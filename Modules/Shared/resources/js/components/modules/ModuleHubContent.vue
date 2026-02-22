<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { ChevronRight, Grid3X3, List, Search, ShieldCheck } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface ModuleHubItem {
    key: string;
    title: string;
    href: string;
    description?: string;
}

interface BreadcrumbItem {
    label: string;
    href?: string;
}

const props = withDefaults(
    defineProps<{
        sectionTitle?: string;
        breadcrumbs?: BreadcrumbItem[];
        searchPlaceholder?: string;
        items: ModuleHubItem[];
    }>(),
    {
        sectionTitle: 'Menu Module',
        breadcrumbs: () => [{ label: 'Home' }],
        searchPlaceholder: 'Cari menu ...',
    },
);

const viewMode = ref<'grid' | 'list'>('grid');
const search = ref('');

const filteredItems = computed(() => {
    const keyword = search.value.trim().toLowerCase();
    if (!keyword) {
        return props.items;
    }

    return props.items.filter((item) => item.title.toLowerCase().includes(keyword));
});
</script>

<template>
    <div class="border-b border-border px-4 py-4 sm:px-6">
        <div class="flex flex-wrap items-center gap-2 text-sm">
            <span class="font-semibold text-foreground">Navigasi</span>
            <span class="mx-1 text-border">|</span>
            <template v-for="(crumb, index) in breadcrumbs" :key="`${crumb.label}-${index}`">
                <a
                    v-if="crumb.href"
                    :href="crumb.href"
                    class="cursor-pointer text-muted-foreground transition hover:text-primary"
                >
                    {{ crumb.label }}
                </a>
                <span v-else class="font-semibold text-foreground">{{ crumb.label }}</span>
                <span v-if="index < breadcrumbs.length - 1" class="text-border">›</span>
            </template>
        </div>
    </div>

    <div class="space-y-4 px-4 py-4 sm:px-6 sm:py-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex w-full max-w-sm items-center gap-2 rounded-xl border border-input bg-background px-3 py-2 shadow-sm">
                <Search class="h-4 w-4 text-muted-foreground" />
                <input
                    v-model="search"
                    type="text"
                    :placeholder="searchPlaceholder"
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
            <a v-for="item in filteredItems" :key="`grid-${item.key}`" :href="item.href" class="block cursor-pointer">
                <Card class="rounded-lg border-border py-0 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <CardContent class="flex flex-col items-center gap-2.5 p-3.5 text-center">
                        <div class="grid h-12 w-12 place-items-center rounded-full border border-primary/15 bg-accent">
                            <ShieldCheck class="h-6 w-6 text-primary" />
                        </div>
                        <p class="line-clamp-2 min-h-9 text-[13px] leading-4 font-medium text-foreground">{{ item.title }}</p>
                    </CardContent>
                </Card>
            </a>
        </div>

        <div v-else class="hidden space-y-2 md:block">
            <a v-for="item in filteredItems" :key="`desktop-list-${item.key}`" :href="item.href" class="block cursor-pointer">
                <Card class="rounded-xl border-border py-0 shadow-sm transition hover:shadow-md">
                    <CardContent class="flex items-center gap-3 p-3">
                        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-full border border-primary/15 bg-accent">
                            <ShieldCheck class="h-5 w-5 text-primary" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-foreground">{{ item.title }}</p>
                            <p v-if="item.description" class="truncate text-xs text-muted-foreground">{{ item.description }}</p>
                        </div>
                        <ChevronRight class="h-4 w-4 text-muted-foreground" />
                    </CardContent>
                </Card>
            </a>
        </div>

        <div class="space-y-2 md:hidden">
            <a v-for="item in filteredItems" :key="`mobile-list-${item.key}`" :href="item.href" class="block cursor-pointer">
                <Card class="rounded-xl border-border py-0 shadow-sm transition hover:shadow-md">
                    <CardContent class="flex items-center gap-3 p-3">
                        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-full border border-primary/15 bg-accent">
                            <ShieldCheck class="h-5 w-5 text-primary" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-foreground">{{ item.title }}</p>
                        </div>
                        <ChevronRight class="h-4 w-4 text-muted-foreground" />
                    </CardContent>
                </Card>
            </a>
        </div>

        <Card v-if="filteredItems.length === 0" class="rounded-xl border-border py-0">
            <CardContent class="p-5 text-sm text-muted-foreground">Tidak ada menu yang cocok dengan pencarian.</CardContent>
        </Card>
    </div>
</template>
