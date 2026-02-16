<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { loadModuleComponent } from '../lib/module-components';

const KnowComponent = loadModuleComponent('Know', () => import('../../../../Know/resources/js/components/KnowComponent.vue'));
</script>

<template>
    <Button class="w-full transform rounded-md" as-child>
        <a href="/"> Ini HARDCORE </a>
    </Button>

    <KnowComponent />
</template>
