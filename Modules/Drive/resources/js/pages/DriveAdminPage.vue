<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { DataTable } from '@/components/ui/data-table';
import { Plus } from 'lucide-vue-next';
import { ref } from 'vue';
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import { getModulePageBreadcrumbs, type ModuleNavigationConfig } from '../../../../Shared/resources/js/lib/module-navigation';
import CreateDriveDialog from '../components/drive/CreateDriveDialog.vue';
import DeleteDriveDialog from '../components/drive/DeleteDriveDialog.vue';
import DetailDriveDialog from '../components/drive/DetailDriveDialog.vue';
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
const isDetailModalOpen = ref(false);
const selectedDrive = ref<DriveItem | null>(null);

const handleViewDrive = (drive: DriveItem) => {
    selectedDrive.value = drive;
    isDetailModalOpen.value = true;
};

const handleEditDrive = (drive: DriveItem) => {
    selectedDrive.value = drive;
    isUpdateModalOpen.value = true;
};

const handleDeleteDrive = (drive: DriveItem) => {
    selectedDrive.value = drive;
    isDeleteModalOpen.value = true;
};

const columns = getDriveColumns({
    onView: handleViewDrive,
    onEdit: handleEditDrive,
    onDelete: handleDeleteDrive,
});
</script>

<template>
    <ModuleContentShell :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <div>
                <h2 class="text-lg font-bold tracking-tight text-foreground">Admin Drive</h2>
            </div>

            <DataTable
                :columns="columns"
                :data="drives"
                search-placeholder="Cari nama drive..."
                search-column="nama"
            >
                <template #actions>
                    <Button @click="isCreateModalOpen = true" size="sm" class="h-8 cursor-pointer gap-1.5">
                        <Plus class="h-4 w-4" />
                        <span class="">Tambah Drive Baru</span>
                    </Button>
                </template>
            </DataTable>
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
        <DetailDriveDialog
            v-model:open="isDetailModalOpen"
            :drive="selectedDrive"
        />
    </ModuleContentShell>
</template>
