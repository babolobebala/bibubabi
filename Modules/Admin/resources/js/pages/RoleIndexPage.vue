<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { DataTable } from '@/components/ui/data-table';
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import {
    getModulePageBreadcrumbs,
    type ModuleNavigationConfig,
} from '../../../../Shared/resources/js/lib/module-navigation';
import { roleColumns, type RoleItem } from '../components/role-columns';
import moduleNavigation from '../config/module-navigation.json';

defineProps<{
    roles: RoleItem[];
}>();

const breadcrumbs = getModulePageBreadcrumbs(moduleNavigation as ModuleNavigationConfig, 'roles');
</script>

<template>
    <ModuleContentShell :breadcrumbs="breadcrumbs">
        <Card class="rounded-xl border-border py-0 shadow-sm">
            <CardHeader class="border-b border-border px-4 py-3">
                <CardTitle class="text-sm font-semibold">Daftar Role</CardTitle>
            </CardHeader>
            <CardContent class="p-4">
                <DataTable
                    :data="roles"
                    :columns="roleColumns"
                    search-placeholder="Cari nama role..."
                    search-column="name"
                />
            </CardContent>
        </Card>
    </ModuleContentShell>
</template>
