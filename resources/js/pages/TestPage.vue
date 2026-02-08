<script setup lang="ts">
import { Button } from '@/components/ui/button';
</script>

<template>
    <Button class="w-full transform rounded-md" as-child>
        <a href="test_auth"> Cek Sini untuk Authorisasi </a>
    </Button>

    <Button class="w-full transform rounded-md" as-child>
        <a href="logout"> Logout </a>
    </Button>
</template>
