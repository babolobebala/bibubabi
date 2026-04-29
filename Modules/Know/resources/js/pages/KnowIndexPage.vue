<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { DataTable } from '@/components/ui/data-table';
import { Plus } from 'lucide-vue-next';
import { ref } from 'vue';
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import {
    getModulePageBreadcrumbs,
    type ModuleNavigationConfig,
} from '../../../../Shared/resources/js/lib/module-navigation';
import CreateKnowDialog from '../components/know/CreateKnowDialog.vue';
import { knowColumns, type KnowItem } from '../components/know/know-columns';
import moduleNavigation from '../config/module-navigation.json';

const breadcrumbs = getModulePageBreadcrumbs(moduleNavigation as ModuleNavigationConfig, 'index');
const isCreateModalOpen = ref(false);

defineProps<{
    knows: KnowItem[];
}>();
</script>

<template>
    <ModuleContentShell :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <div>
                <h2 class="text-lg font-bold tracking-tight text-foreground">Daftar Knowledge</h2>
            </div>

            <DataTable :columns="knowColumns" :data="knows" search-placeholder="Cari knowledge..." search-column="nama">
                <template #actions>
                    <Button size="sm" class="h-8 cursor-pointer gap-1.5" @click="isCreateModalOpen = true">
                        <Plus class="h-4 w-4" />
                        <span class="hidden sm:inline">Tambah Knowledge</span>
                    </Button>
                </template>
            </DataTable>
        </div>

        <CreateKnowDialog v-model:open="isCreateModalOpen" />
    </ModuleContentShell>
</template>
