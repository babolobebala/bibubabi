<script setup lang="ts">
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import {
    getModulePageBreadcrumbs,
    type ModuleNavigationConfig,
} from '../../../../Shared/resources/js/lib/module-navigation';
import GeoTaggingContent from '../components/GeoTaggingContent.vue';
import moduleNavigation from '../config/module-navigation.json';

const pageBreadcrumbs = getModulePageBreadcrumbs(moduleNavigation as ModuleNavigationConfig, 'geotagging-gambar');
</script>

<template>
    <ModuleContentShell :breadcrumbs="pageBreadcrumbs">
        <GeoTaggingContent />
    </ModuleContentShell>
</template>
