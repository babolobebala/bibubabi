<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { DataTable } from '@/components/ui/data-table';
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import {
    getModulePageBreadcrumbs,
    type ModuleNavigationConfig,
} from '../../../../Shared/resources/js/lib/module-navigation';
import { userColumns, type UserItem } from '../components/user-columns';
import UserExpandedRow from '../components/UserExpandedRow.vue';
import moduleNavigation from '../config/module-navigation.json';

defineProps<{
    users: UserItem[];
    roles: string[];
}>();

const breadcrumbs = getModulePageBreadcrumbs(moduleNavigation as ModuleNavigationConfig, 'users');
</script>

<template>
    <ModuleContentShell :breadcrumbs="breadcrumbs">
        <Card class="rounded-xl border-border py-0 shadow-sm">
            <CardHeader class="border-b border-border px-4 py-3">
                <CardTitle class="text-sm font-semibold">Daftar Pengguna</CardTitle>
            </CardHeader>
            <CardContent class="p-4">
                <DataTable
                    :data="users"
                    :columns="userColumns"
                    :search-fields="['nama', 'nip', 'nip_baru', 'jabatan', 'username', 'status_pegawai']"
                    search-placeholder="Cari nama, NIP, jabatan..."
                >
                    <!-- Opt-in: expanded row aktif karena slot ini di-provide -->
                    <template #expanded-row="{ original }">
                        <UserExpandedRow :original="original" />
                    </template>
                </DataTable>
            </CardContent>
        </Card>
    </ModuleContentShell>
</template>
