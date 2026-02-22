<script setup lang="ts">
import ModuleHubContent from '../../../../Shared/resources/js/components/modules/ModuleHubContent.vue';

const moduleMenus = [
    {
        key: 'tools',
        title: 'Tools',
        href: '/app/tools',
        description: 'Kumpulan utilitas seperti geotagging gambar dan tools operasional lain.',
    },
];
</script>

<template>
    <div>
        <ModuleHubContent
            section-title="Beranda"
            search-placeholder="Cari module ..."
            :breadcrumbs="[{ label: 'Home', href: '/app' }]"
            :items="moduleMenus"
        />
    </div>
</template>
