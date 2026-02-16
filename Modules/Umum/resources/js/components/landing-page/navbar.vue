<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { useTheme } from '@/lib/theme';
import { MoonIcon, SunIcon } from 'lucide-vue-next';
import { onMounted } from 'vue';

const { isDark, initializeTheme, toggleTheme } = useTheme();

onMounted(() => {
    initializeTheme('light');
});
</script>

<template>
    <header class="sticky top-0 z-50 w-full border-b border-border/70 bg-background/90 backdrop-blur supports-backdrop-filter:bg-background/75">
        <div class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="/salsa" class="text-lg font-semibold tracking-tight text-primary"> ConvertFast UI </a>

            <nav class="hidden items-center gap-8 md:flex">
                <a href="#features" class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"> Features </a>
                <a href="#social-proof" class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"> Examples </a>
                <a href="#faq" class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"> Documentation </a>
            </nav>

            <div class="flex items-center gap-2 sm:gap-3">
                <Button variant="ghost" size="icon" :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'" @click="toggleTheme">
                    <SunIcon v-if="isDark" class="size-4" />
                    <MoonIcon v-else class="size-4" />
                </Button>
                <a
                    href="https://github.com"
                    target="_blank"
                    rel="noreferrer"
                    class="rounded-md px-3 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                >
                    GitHub
                </a>
                <a
                    href="#start"
                    class="rounded-md bg-primary px-3 py-2 text-sm font-semibold text-primary-foreground transition-opacity hover:opacity-90"
                >
                    Build now
                </a>
            </div>
        </div>
    </header>
</template>
