<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import {
    getModuleHubBreadcrumbs,
    type ModuleNavigationConfig,
} from '../../../../Shared/resources/js/lib/module-navigation';
import moduleNavigation from '../config/module-navigation.json';

const breadcrumbs = getModuleHubBreadcrumbs(moduleNavigation as ModuleNavigationConfig);
</script>

<template>
    <ModuleContentShell :breadcrumbs="breadcrumbs">
        <Card>
            <CardHeader>
                <CardTitle class="text-lg sm:text-xl">Debugging</CardTitle>
            </CardHeader>
            <CardContent class="text-sm text-muted-foreground">
                Halaman utama modul Debugging. Alat bantu debugging dan pemantauan sistem akan tersedia di sini.
            </CardContent>
        </Card>
    </ModuleContentShell>
</template>
