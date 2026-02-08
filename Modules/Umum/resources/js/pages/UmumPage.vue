<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { media } from '@/lib/media';
import UmumComponent from '../components/UmumComponent.vue';
</script>

<template>
    <Button class="w-full transform rounded-md" as-child>
        <a href="/"> Ini page umum. rubah</a>
    </Button>

    <img :src="media + 'img/logo/logo.png'" />
    <UmumComponent />
</template>
