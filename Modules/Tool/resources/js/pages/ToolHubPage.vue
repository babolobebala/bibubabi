<script setup lang="ts">
import SharedModuleLayout from '../../../../Shared/resources/js/components/layouts/SharedModuleLayout.vue';
import ModuleHubContent from '../../../../Shared/resources/js/components/modules/ModuleHubContent.vue';

const toolMenus = [
    {
        key: 'geotagging-gambar',
        title: 'Geotagging Gambar',
        href: '/tools/geotagging-gambar',
        description: 'Tambah lokasi, waktu, dan overlay informasi pada foto.',
    },
];
</script>

<template>
    <SharedModuleLayout>
        <ModuleHubContent
            section-title="Tools"
            search-placeholder="Cari menu Tools ..."
            :breadcrumbs="[
                { label: 'Home', href: '/home' },
                { label: 'Tools' },
            ]"
            :items="toolMenus"
        />
    </SharedModuleLayout>
</template>
