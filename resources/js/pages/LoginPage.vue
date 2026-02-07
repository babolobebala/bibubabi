<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Toaster } from '@/components/ui/sonner';
import { Head, usePage } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

const page = usePage<any>();
const pesan = page.props.flash as string;

if (pesan) {
    toast('Gabisa Login Woy', {
        description: pesan,
        action: {
            label: 'Undo',
            onClick: () => console.log('Undo'),
        },
    });
}
</script>

<template>
    <Head title="Login" />
    <Toaster />

    <Button class="w-full transform rounded-md" as-child>
        <a href="login_sso"> SSO Pegawai BPS </a>
    </Button>
</template>
