<script setup lang="ts"></script>

<template>
    <footer class="mt-16 border-t border-border bg-background/50 pt-6 pb-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center justify-center gap-3 text-center">
                <p class="text-[11px] font-medium text-muted-foreground/80">
                    © {{ new Date().getFullYear() }} BPS Kabupaten Sumbawa Barat.
                </p>
                <div class="flex items-center gap-1.5 text-[9px] font-bold tracking-[0.2em] text-muted-foreground/40 uppercase">
                    <span>Handcrafted with</span>
                    <span class="animate-pulse text-xs text-red-500">❤️</span>
                    <span>for better statistics</span>
                </div>
            </div>
        </div>
    </footer>
</template>
