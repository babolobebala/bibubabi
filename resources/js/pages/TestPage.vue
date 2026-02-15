<script setup lang="ts">
import { Button } from '@/components/ui/button';
</script>

<template>
    <Button class="w-full transform rounded-md" as-child>
        <a href="test_auth"> Cek Sini untuk Authorisasi </a>
    </Button>

    <Button id="enable-push">Enable push notifications</Button>
    <Button id="disable-push">Disable push notifications</Button>
    <br />
    <a href="/notifications/send">Send a notification</a>

    <Button class="w-full transform rounded-md" as-child>
        <a href="logout"> Logout </a>
    </Button>
</template>
