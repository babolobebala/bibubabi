<script setup lang="ts">
import ModuleHubContent from '../../../../Shared/resources/js/components/modules/ModuleHubContent.vue';

const toolMenus = [
    {
        key: 'geotagging-gambar',
        title: 'Geotagging Gambar',
        href: '/app/tools/geotagging-gambar',
        description: 'Tambah lokasi, waktu, dan overlay informasi pada foto.',
    },
];
</script>

<template>
    <div>
        <ModuleHubContent
            section-title="Tools"
            :breadcrumbs="[
                { label: 'Home', href: '/app' },
                { label: 'Tools' },
            ]"
            :items="toolMenus"
        />
    </div>
</template>
