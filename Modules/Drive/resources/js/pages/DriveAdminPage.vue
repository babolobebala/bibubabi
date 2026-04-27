<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { DataTable } from '@/components/ui/data-table';
import { Plus } from 'lucide-vue-next';
import { ref } from 'vue';
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import { getModulePageBreadcrumbs, type ModuleNavigationConfig } from '../../../../Shared/resources/js/lib/module-navigation';
import CreateDriveDialog from '../components/drive/CreateDriveDialog.vue';
import DeleteDriveDialog from '../components/drive/DeleteDriveDialog.vue';
import { getDriveColumns, type DriveItem } from '../components/drive/drive-columns';
import UpdateDriveDialog from '../components/drive/UpdateDriveDialog.vue';
import moduleNavigation from '../config/module-navigation.json';

defineProps<{
    drives: DriveItem[];
    availableUsers: { value: string; label: string }[];
    availableRoles: { value: string; label: string }[];
}>();

const breadcrumbs = getModulePageBreadcrumbs(moduleNavigation as ModuleNavigationConfig, 'drive-admin');

const isCreateModalOpen = ref(false);
const isUpdateModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedDrive = ref<DriveItem | null>(null);

const handleEditDrive = (drive: DriveItem) => {
    selectedDrive.value = drive;
    isUpdateModalOpen.value = true;
};

const handleDeleteDrive = (drive: DriveItem) => {
    selectedDrive.value = drive;
    isDeleteModalOpen.value = true;
};

const columns = getDriveColumns({
    onEdit: handleEditDrive,
    onDelete: handleDeleteDrive,
});
</script>

<template>
    <ModuleContentShell :module="'drive'" body-variant="page" :breadcrumbs="breadcrumbs">
        <div class="p-4 md:p-6 space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Admin Drive</h1>
                    <p class="text-sm text-muted-foreground mt-1">
                        Kelola seluruh link drive internal, akses, dan kepemilikan tim.
                    </p>
                </div>
                <Button class="w-full md:w-auto cursor-pointer" @click="isCreateModalOpen = true">
                    <Plus class="mr-2 h-4 w-4" />
                    Tambah Drive Baru
                </Button>
            </div>

            <DataTable
                :columns="columns"
                :data="drives"
                search-placeholder="Cari nama drive..."
                search-column="nama"
            />
        </div>

        <!-- Dialogs -->
        <CreateDriveDialog 
            v-model:open="isCreateModalOpen" 
            :available-users="availableUsers"
            :available-roles="availableRoles"
        />
        <UpdateDriveDialog 
            v-model:open="isUpdateModalOpen" 
            :drive="selectedDrive" 
            :available-users="availableUsers"
            :available-roles="availableRoles"
        />
        <DeleteDriveDialog 
            v-model:open="isDeleteModalOpen" 
            :drive="selectedDrive" 
        />
    </ModuleContentShell>
</template>
