<script setup lang="ts">
import { Button } from '@/components/ui/button';
</script>

<template>
    <Button class="w-full transform rounded-md" as-child>
        <a href="test_auth"> Cek Sini untuk Authorisasi </a>
    </Button>

    <Button id="enable-push">Enable push notifications</Button>
    <Button id="disable-push">Disable push notifications</Button>
    <Button id="clear-push-debug" variant="outline">Clear push debug log</Button>
    <br />
    <a href="/notifications/send?target=all">Send to all users</a>
    <br />
    <a href="/notifications/send?target=role&role=super_admin">Send to super_admin users</a>

    <div class="mt-4 rounded-md border p-3">
        <p class="mb-2 text-sm font-semibold">Push Debug Log</p>
        <pre id="push-debug-log" class="max-h-64 overflow-auto whitespace-pre-wrap text-xs"></pre>
    </div>

    <Button class="w-full transform rounded-md" as-child>
        <a href="logout"> Logout </a>
    </Button>
</template>
