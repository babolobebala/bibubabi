<script setup lang="ts">
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import {
    getModulePageBreadcrumbs,
    type ModuleNavigationConfig,
} from '../../../../Shared/resources/js/lib/module-navigation';
import DocumentGeneratorDocxContent from '../components/DocumentGeneratorDocxContent.vue';
import moduleNavigation from '../config/module-navigation.json';

const pageBreadcrumbs = getModulePageBreadcrumbs(moduleNavigation as ModuleNavigationConfig, 'generator-dokumen-docx');
</script>

<template>
    <ModuleContentShell :breadcrumbs="pageBreadcrumbs">
        <DocumentGeneratorDocxContent />
    </ModuleContentShell>
</template>
