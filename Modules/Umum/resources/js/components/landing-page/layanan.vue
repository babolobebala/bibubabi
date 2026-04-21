<script setup lang="ts">
import { media } from '@/lib/media';
import { Toolbox } from 'lucide-vue-next';

interface LayananCard {
    image: string;
    imageAlt: string;
}

const layananCards: LayananCard[] = [
    {
        image: media + 'img/layanan-1.png',
        imageAlt: 'Layanan Perpustakaan',
    },
    {
        image: media + 'img/layanan-2.png',
        imageAlt: 'Layanan rekomendasi statistik sektoral',
    },
    {
        image: media + 'img/layanan-3.png',
        imageAlt: 'Layanan konsultasi statistik',
    },
];
</script>

<template>
    <section class="relative overflow-hidden py-8 sm:py-10 lg:py-12">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute top-12 left-1/2 h-36 w-36 -translate-x-72 rounded-full bg-primary/10 blur-3xl" />
            <div class="absolute right-0 bottom-10 h-48 w-48 rounded-full bg-secondary/80 blur-3xl" />
        </div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto mb-7 max-w-4xl text-center">
                <!-- Header Section -->
                <div
                    class="mx-auto mb-4 flex w-fit items-center gap-2 rounded-full border border-blue-500/30 bg-blue-500/10 px-4 py-1.5 text-[10px] font-bold tracking-[0.2em] text-blue-400 uppercase"
                >
                    <Toolbox class="h-3.5 w-3.5" />
                    Pelayanan Statistik Terpadu
                </div>
                <h2 class="text-2xl font-black tracking-tight text-foreground sm:text-3xl lg:text-4xl">Layanan BPS Dalam Tampilan Baru</h2>
            </div>

            <div class="grid gap-7 md:grid-cols-2 lg:gap-8 xl:grid-cols-3 xl:gap-9">
                <article
                    v-for="card in layananCards"
                    :key="card.image"
                    class="group overflow-hidden rounded-4xl border border-border bg-card shadow-lg transition-transform duration-300 hover:-translate-y-1"
                >
                    <div class="relative aspect-square overflow-hidden rounded-4xl bg-background">
                        <img
                            :src="card.image"
                            :alt="card.imageAlt"
                            class="h-full w-full object-cover object-center transition duration-500 group-hover:scale-[1.02]"
                        />
                    </div>
                </article>
            </div>
        </div>
    </section>
</template>
