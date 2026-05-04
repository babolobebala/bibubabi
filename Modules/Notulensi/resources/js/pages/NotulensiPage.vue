<script setup lang="ts">
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import {
    getModulePageBreadcrumbs,
    type ModuleNavigationConfig,
} from '../../../../Shared/resources/js/lib/module-navigation';
import moduleNavigation from '../config/module-navigation.json';

const pageBreadcrumbs = getModulePageBreadcrumbs(moduleNavigation as ModuleNavigationConfig, '');
</script>

<template>
    <ModuleContentShell body-variant="hub" :breadcrumbs="pageBreadcrumbs">
        <div class="rounded-lg border border-border bg-card p-4 text-sm text-muted-foreground">
            Halaman utama modul Notulensi.
        </div>
    </ModuleContentShell>
</template>
