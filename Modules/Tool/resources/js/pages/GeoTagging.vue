<script setup lang="ts">
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import { getModulePageBreadcrumbs } from '../../../../Shared/resources/js/lib/module-navigation';
import GeoTaggingContent from '../components/GeoTaggingContent.vue';
import toolNavigation from '../config/module-navigation.json';

const pageBreadcrumbs = getModulePageBreadcrumbs(toolNavigation, 'geotagging-gambar');
</script>

<template>
    <ModuleContentShell :breadcrumbs="pageBreadcrumbs" body-class="mx-auto max-w-7xl space-y-4 px-3 py-4 sm:space-y-5 sm:px-4 sm:py-6">
        <GeoTaggingContent />
    </ModuleContentShell>
</template>
