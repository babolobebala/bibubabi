<script setup lang="ts">
import type { Table } from '@tanstack/vue-table';
import {
    ChevronLeft,
    ChevronRight,
    ChevronsLeft,
    ChevronsRight,
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

const props = defineProps<{
    table: Table<any>;
}>();
</script>

<template>
    <div class="flex items-center justify-between gap-2 text-sm text-muted-foreground">
        <p class="shrink-0">
            {{ table.getFilteredRowModel().rows.length }} data
            <span v-if="table.getPageCount() > 1">
                · halaman {{ table.getState().pagination.pageIndex + 1 }} dari
                {{ table.getPageCount() }}
            </span>
        </p>

        <div class="flex items-center gap-1">
            <Button
                variant="outline"
                size="icon"
                class="h-7 w-7"
                :disabled="!table.getCanPreviousPage()"
                @click="table.setPageIndex(0)"
            >
                <ChevronsLeft class="h-3.5 w-3.5" />
            </Button>
            <Button
                variant="outline"
                size="icon"
                class="h-7 w-7"
                :disabled="!table.getCanPreviousPage()"
                @click="table.previousPage()"
            >
                <ChevronLeft class="h-3.5 w-3.5" />
            </Button>
            <Button
                variant="outline"
                size="icon"
                class="h-7 w-7"
                :disabled="!table.getCanNextPage()"
                @click="table.nextPage()"
            >
                <ChevronRight class="h-3.5 w-3.5" />
            </Button>
            <Button
                variant="outline"
                size="icon"
                class="h-7 w-7"
                :disabled="!table.getCanNextPage()"
                @click="table.setPageIndex(table.getPageCount() - 1)"
            >
                <ChevronsRight class="h-3.5 w-3.5" />
            </Button>
        </div>
    </div>
</template>
