<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import {
    getModulePageBreadcrumbs,
    type ModuleNavigationConfig,
} from '../../../../Shared/resources/js/lib/module-navigation';
import moduleNavigation from '../config/module-navigation.json';

const breadcrumbs = getModulePageBreadcrumbs(moduleNavigation as ModuleNavigationConfig, 'create');
</script>

<template>
    <ModuleContentShell :breadcrumbs="breadcrumbs">
        <Card>
            <CardHeader>
                <CardTitle class="text-lg sm:text-xl">Buat Knowledge</CardTitle>
            </CardHeader>
            <CardContent class="text-sm text-muted-foreground">
                Halaman form pembuatan knowledge. Form create bisa diimplementasikan di sini.
            </CardContent>
        </Card>
    </ModuleContentShell>
</template>
