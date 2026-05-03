<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { DataTable } from '@/components/ui/data-table';
import { Plus } from 'lucide-vue-next';
import { ref } from 'vue';
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import { getModulePageBreadcrumbs, type ModuleNavigationConfig } from '../../../../Shared/resources/js/lib/module-navigation';
import { getKnowCategoryColumns, type KnowCategoryItem } from '../components/know-category/category-columns';
import CreateKnowCategoryDialog from '../components/know-category/CreateKnowCategoryDialog.vue';
import DeleteKnowCategoryDialog from '../components/know-category/DeleteKnowCategoryDialog.vue';
import UpdateKnowCategoryDialog from '../components/know-category/UpdateKnowCategoryDialog.vue';
import moduleNavigation from '../config/module-navigation.json';

defineProps<{
    categories: KnowCategoryItem[];
}>();

const breadcrumbs = getModulePageBreadcrumbs(moduleNavigation as ModuleNavigationConfig, 'know-admin');

const isCreateModalOpen = ref(false);
const isUpdateModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedCategory = ref<KnowCategoryItem | null>(null);

const handleEditCategory = (category: KnowCategoryItem) => {
    selectedCategory.value = category;
    isUpdateModalOpen.value = true;
};

const handleDeleteCategory = (category: KnowCategoryItem) => {
    selectedCategory.value = category;
    isDeleteModalOpen.value = true;
};

const tableColumns = getKnowCategoryColumns({
    onEdit: handleEditCategory,
    onDelete: handleDeleteCategory,
});
</script>

<template>
    <ModuleContentShell :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <div>
                <h2 class="text-lg font-bold tracking-tight text-foreground">Admin Kategori Knowledge</h2>
            </div>

            <DataTable :data="categories" :columns="tableColumns" search-placeholder="Cari kategori..." search-column="nama">
                <template #actions>
                    <Button size="sm" class="h-8 cursor-pointer gap-1.5" @click="isCreateModalOpen = true">
                        <Plus class="h-4 w-4" />
                        <span class="">Tambah Kategori</span>
                    </Button>
                </template>
            </DataTable>
        </div>

        <CreateKnowCategoryDialog v-model:open="isCreateModalOpen" />
        <UpdateKnowCategoryDialog v-model:open="isUpdateModalOpen" :category="selectedCategory" />
        <DeleteKnowCategoryDialog v-model:open="isDeleteModalOpen" :category="selectedCategory" />
    </ModuleContentShell>
</template>
