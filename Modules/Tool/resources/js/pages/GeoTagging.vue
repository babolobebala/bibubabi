<script setup lang="ts">
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import GeoTaggingContent from '../components/GeoTaggingContent.vue';
import { getToolPageBreadcrumbs } from '../lib/navigation';

const pageBreadcrumbs = getToolPageBreadcrumbs('geotagging-gambar');
</script>

<template>
    <ModuleContentShell :breadcrumbs="pageBreadcrumbs">
        <GeoTaggingContent />
    </ModuleContentShell>
</template>
