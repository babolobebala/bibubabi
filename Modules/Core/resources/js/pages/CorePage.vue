<script setup lang="ts">
import SharedModuleLayout from '../../../../Shared/resources/js/components/layouts/SharedModuleLayout.vue';
import ModuleHubContent from '../../../../Shared/resources/js/components/modules/ModuleHubContent.vue';

const moduleMenus = [
    {
        key: 'tools',
        title: 'Tools',
        href: '/tools',
        description: 'Kumpulan utilitas seperti geotagging gambar dan tools operasional lain.',
    },
];
</script>

<template>
    <SharedModuleLayout>
        <ModuleHubContent
            section-title="Beranda"
            search-placeholder="Cari module ..."
            :breadcrumbs="[{ label: 'Home' }]"
            :items="moduleMenus"
        />
    </SharedModuleLayout>
</template>
