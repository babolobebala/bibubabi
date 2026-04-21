<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { media } from '@/lib/media';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import LoginDialog from './LoginDialog.vue';

interface NavbarPageProps {
    [key: string]: unknown;
    auth?: {
        user?: unknown | null;
    };
}

const page = usePage<NavbarPageProps>();
const isAuthenticated = computed(() => Boolean(page.props.auth?.user));
</script>

<template>
    <header class="fixed inset-x-0 top-0 z-50 w-full border-b border-border/60 bg-background/80 backdrop-blur">
        <div class="mx-auto flex h-14 w-full max-w-7xl items-center justify-between px-3.5 sm:h-11 sm:px-4 lg:px-6">
            <Link href="/welcome" class="flex items-center gap-3 sm:gap-2.5">
                <img :src="media + 'img/logo/saku.png'" alt="Logo SAKU" class="h-11 w-auto object-contain sm:h-8" />
                <div class="leading-tight">
                    <p class="text-sm font-bold tracking-tight text-foreground sm:text-xs">SAKU BPS KSB</p>
                    <p class="hidden text-[9px] text-muted-foreground lg:block">Satu Aplikasi untuk Kinerja Unggul</p>
                </div>
            </Link>


            <div class="flex items-center gap-2.5 sm:gap-2">
                <LoginDialog v-if="!isAuthenticated" />
                <Button v-else as-child class="h-10 cursor-pointer rounded-full px-3.5 text-sm sm:h-8 sm:px-2.5 sm:text-xs" variant="default">
                    <Link href="/app"> Masuk ke SAKU </Link>
                </Button>
            </div>
        </div>
    </header>
</template>
