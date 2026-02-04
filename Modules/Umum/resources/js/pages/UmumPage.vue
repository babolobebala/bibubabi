<script setup lang="ts">
import { Button } from '@/components/ui/button';
import UmumComponent from '../components/UmumComponent.vue';

</script>

<template>
    <Button class="w-full transform rounded-md" as-child>
        <a href="/"> Ini umum sih, aman sejauh ini ya wkwk </a>
    </Button>

    <UmumComponent />
</template>
