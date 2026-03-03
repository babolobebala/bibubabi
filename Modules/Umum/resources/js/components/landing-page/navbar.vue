<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { media } from '@/lib/media';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import LoginDialog from './LoginDialog.vue';

interface NavbarPageProps {
    [key: string]: unknown;
    auth?: {
        user?: unknown | null;
    };
}

const page = usePage<NavbarPageProps>();
const isAuthenticated = computed(() => Boolean(page.props.auth?.user));

const navItems = [
    { id: 'layanan', label: 'Layanan BPS' },
    { id: 'data', label: 'Data Strategis' },
    { id: 'pengaduan', label: 'Pengaduan' },
];

const activeSection = ref<string>('');
let scrollTicking = false;

function updateActiveSectionFromScroll(): void {
    const offsetTop = 64;
    const sectionMetrics = navItems
        .map((item) => {
            const element = document.getElementById(item.id);
            if (!element) {
                return null;
            }

            return {
                id: item.id,
                top: element.getBoundingClientRect().top,
            };
        })
        .filter((value): value is { id: string; top: number } => value !== null);

    if (sectionMetrics.length === 0) {
        return;
    }

    const reachedSections = sectionMetrics.filter((section) => section.top <= offsetTop);
    if (reachedSections.length > 0) {
        activeSection.value = reachedSections[reachedSections.length - 1].id;
        return;
    }

    activeSection.value = sectionMetrics[0].id;
}

function handleScroll(): void {
    if (scrollTicking) {
        return;
    }

    scrollTicking = true;
    window.requestAnimationFrame(() => {
        updateActiveSectionFromScroll();
        scrollTicking = false;
    });
}

onMounted(() => {
    updateActiveSectionFromScroll();
    window.addEventListener('scroll', handleScroll, { passive: true });
    window.addEventListener('resize', handleScroll);
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', handleScroll);
    window.removeEventListener('resize', handleScroll);
});
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

            <nav class="hidden items-center gap-1 rounded-full border border-border/80 bg-background/80 p-0.5 md:flex">
                <a
                    v-for="item in navItems"
                    :key="item.id"
                    :href="`#${item.id}`"
                    :class="[
                        'rounded-full px-2 py-0.5 text-[10px] font-medium transition-colors',
                        activeSection === item.id ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-secondary hover:text-foreground',
                    ]"
                >
                    {{ item.label }}
                </a>
            </nav>

            <div class="flex items-center gap-2.5 sm:gap-2">
                <LoginDialog v-if="!isAuthenticated" />
                <Button v-else as-child class="h-10 cursor-pointer rounded-full px-3.5 text-sm sm:h-8 sm:px-2.5 sm:text-xs" variant="default">
                    <Link href="/app"> Masuk ke SAKU </Link>
                </Button>
            </div>
        </div>
    </header>
</template>
