<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { DataTable } from '@/components/ui/data-table';
import { Plus } from 'lucide-vue-next';
import { ref } from 'vue';
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import { getModulePageBreadcrumbs, type ModuleNavigationConfig } from '../../../../Shared/resources/js/lib/module-navigation';
import CreateRoleDialog from '../components/role/CreateRoleDialog.vue';
import DeleteRoleDialog from '../components/role/DeleteRoleDialog.vue';
import { getRoleColumns, type RoleItem } from '../components/role/role-columns';
import RoleExpandedRow from '../components/role/RoleExpandedRow.vue';
import UpdateRoleDialog from '../components/role/UpdateRoleDialog.vue';
import moduleNavigation from '../config/module-navigation.json';

defineProps<{
    roles: RoleItem[];
    availableUsers: { value: string; label: string }[];
}>();

const breadcrumbs = getModulePageBreadcrumbs(moduleNavigation as ModuleNavigationConfig, 'roles');

const isCreateModalOpen = ref(false);
const isUpdateModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedRole = ref<RoleItem | null>(null);

const handleEditRole = (role: RoleItem) => {
    selectedRole.value = role;
    isUpdateModalOpen.value = true;
};

const handleDeleteRole = (role: RoleItem) => {
    selectedRole.value = role;
    isDeleteModalOpen.value = true;
};

const tableColumns = getRoleColumns({
    onEdit: handleEditRole,
    onDelete: handleDeleteRole,
});
</script>

<template>
    <ModuleContentShell :breadcrumbs="breadcrumbs">
        <Card class="rounded-xl border-border py-0 shadow-sm">
            <CardHeader class="flex flex-row items-center justify-between border-b border-border px-4 py-3">
                <CardTitle class="text-sm font-semibold">Daftar Role</CardTitle>
                <Button @click="isCreateModalOpen = true" size="sm" class="h-8 gap-1.5 cursor-pointer">
                    <Plus class="h-4 w-4" />
                    <span class="hidden sm:inline">Tambah Role</span>
                </Button>
            </CardHeader>
            <CardContent class="p-4">
                <DataTable :data="roles" :columns="tableColumns" search-placeholder="Cari nama role..." search-column="name">
                    <template #expanded-row="{ original }">
                        <RoleExpandedRow :original="original" />
                    </template>
                </DataTable>
            </CardContent>
        </Card>

        <CreateRoleDialog v-model:open="isCreateModalOpen" />
        <UpdateRoleDialog v-model:open="isUpdateModalOpen" :role="selectedRole" :available-users="availableUsers" />
        <DeleteRoleDialog v-model:open="isDeleteModalOpen" :role="selectedRole" />
    </ModuleContentShell>
</template>
