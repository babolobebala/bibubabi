<script setup lang="ts">
import { DataTable } from '@/components/ui/data-table';
import { computed, ref } from 'vue';
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import { getModulePageBreadcrumbs, type ModuleNavigationConfig } from '../../../../Shared/resources/js/lib/module-navigation';
import UpdatePasswordDialog from '../components/UpdatePasswordDialog.vue';
import UpdateRoleDialog from '../components/UpdateRoleDialog.vue';
import { getUserColumns, type UserItem } from '../components/user-columns';
import UserExpandedRow from '../components/UserExpandedRow.vue';
import moduleNavigation from '../config/module-navigation.json';

const props = defineProps<{
    users: UserItem[];
    roles: string[];
}>();

const breadcrumbs = getModulePageBreadcrumbs(moduleNavigation as ModuleNavigationConfig, 'users');

const passwordModalOpen = ref(false);
const roleModalOpen = ref(false);
const selectedUserId = ref<number | null>(null);

const selectedUser = computed(() => {
    if (!selectedUserId.value) return null;
    return props.users.find((u) => u.id === selectedUserId.value) || null;
});

const openRoleModal = (id: number) => {
    selectedUserId.value = id;
    roleModalOpen.value = true;
};

const openPasswordModal = (id: number) => {
    selectedUserId.value = id;
    passwordModalOpen.value = true;
};

const columns = computed(() =>
    getUserColumns({
        onEditPassword: openPasswordModal,
        onEditRole: openRoleModal,
    }),
);
</script>

<template>
    <ModuleContentShell :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <div>
                <h2 class="text-sm font-semibold">Daftar Pengguna</h2>
            </div>
            <DataTable
                :data="users"
                :columns="columns"
                :search-fields="['nama', 'nip', 'nip_baru', 'jabatan', 'username', 'status_pegawai']"
                search-placeholder="Cari nama, NIP, jabatan..."
            >
                <template #expanded-row="{ original }">
                    <UserExpandedRow :original="original" />
                </template>
            </DataTable>
        </div>

        <!-- Dialog Ubah Role -->
        <UpdateRoleDialog
            v-model:open="roleModalOpen"
            :user-id="selectedUserId"
            :user-name="selectedUser?.nama || 'Pengguna'"
            :current-user-roles="selectedUser?.roles || []"
            :available-roles="roles"
        />

        <!-- Dialog Ubah Password -->
        <UpdatePasswordDialog
            v-model:open="passwordModalOpen"
            :user-id="selectedUserId"
            :user-name="selectedUser?.nama || 'Pengguna'"
        />
    </ModuleContentShell>
</template>
