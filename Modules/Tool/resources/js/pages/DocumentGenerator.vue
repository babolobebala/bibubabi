<script setup lang="ts">
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import {
    getModulePageBreadcrumbs,
    type ModuleNavigationConfig,
} from '../../../../Shared/resources/js/lib/module-navigation';
import DocumentGeneratorContent from '../components/DocumentGeneratorContent.vue';
import moduleNavigation from '../config/module-navigation.json';

const pageBreadcrumbs = getModulePageBreadcrumbs(moduleNavigation as ModuleNavigationConfig, 'generator-dokumen');
</script>

<template>
    <ModuleContentShell :breadcrumbs="pageBreadcrumbs">
        <DocumentGeneratorContent />
    </ModuleContentShell>
</template>
