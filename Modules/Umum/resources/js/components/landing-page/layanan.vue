<script setup lang="ts">
import { media } from '@/lib/media';

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
            <div class="mx-auto max-w-4xl text-center">
                <p class="text-xs font-semibold tracking-[0.24em] text-primary uppercase sm:text-sm">Pelayanan Statistik Terpadu</p>
                <h2 class="mt-3 text-3xl font-black tracking-tight text-foreground sm:text-4xl lg:text-[3rem] lg:leading-[1.02]">
                    Layanan BPS dalam tampilan baru
                </h2>
            </div>

            <div class="mt-7 grid gap-7 md:grid-cols-2 lg:gap-8 xl:grid-cols-3 xl:gap-9">
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
