<!-- eslint-disable @typescript-eslint/no-unused-vars -->
<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { media } from '@/lib/media';
import { useTheme } from '@/lib/theme';
import { usePage } from '@inertiajs/vue3';
import { MoonIcon, SunIcon } from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const { isDark, toggleTheme } = useTheme();

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
    const offsetTop = 96;
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
    <header
        class="fixed inset-x-0 top-0 z-50 w-full border-b border-border/70 bg-background/90 backdrop-blur supports-backdrop-filter:bg-background/75"
    >
        <div class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="/welcome" class="flex items-center gap-2 text-lg font-semibold tracking-tight text-primary">
                <img :src="media + 'img/logo/logo.png'" alt="Logo SAKU" class="h-8 w-8 object-contain" />
                <span>SAKU - BPS KSB</span>
            </a>

            <nav class="hidden items-center gap-8 md:flex">
                <a
                    v-for="item in navItems"
                    :key="item.id"
                    :href="`#${item.id}`"
                    :class="[
                        'text-sm font-medium transition-colors',
                        activeSection === item.id
                            ? 'text-primary underline decoration-2 underline-offset-8'
                            : 'text-muted-foreground hover:text-foreground',
                    ]"
                >
                    {{ item.label }}
                </a>
            </nav>

            <div class="flex items-center gap-2 sm:gap-3">
                <!-- <Button variant="ghost" size="icon" :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'" @click="toggleTheme">
                    <SunIcon v-if="isDark" class="size-4" />
                    <MoonIcon v-else class="size-4" />
                </Button> -->

                <Button v-if="!isAuthenticated" as-child class="w-full transform cursor-pointer rounded-md" variant="default">
                    <a href="/bypass/?nama=fatihwisesa"> Login SSO BPS</a>
                </Button>
                <Button v-else as-child class="w-full transform cursor-pointer rounded-md" variant="default">
                    <a href="/home"> Masuk ke SAKU </a>
                </Button>
            </div>
        </div>
    </header>
</template>
