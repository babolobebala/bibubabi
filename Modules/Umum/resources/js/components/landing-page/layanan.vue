<script setup lang="ts">
import { Card, CardContent, CardDescription, CardTitle } from '@/components/ui/card';
import { media } from '@/lib/media';

interface LayananCard {
    id: string;
    title: string;
    description: string;
    image: string;
    imageAlt: string;
    imageFirst: boolean;
}

const layananCards: LayananCard[] = [
    {
        id: '01',
        title: 'Layanan Perpustakaan',
        description: 'BPS Kabupaten Sumbawa Barat menyediakan layanan perpustakaan sebagai sarana akses terhadap publikasi dan referensi statistik.',
        image: media + 'img/layanan-1.png',
        imageAlt: 'Layanan Perpustakaan',
        imageFirst: true,
    },
    {
        id: '02',
        title: 'Layanan Rekomendasi Statistik Sektoral',
        description:
            'BPS Kabupaten Sumbawa Barat memberikan layanan rekomendasi kegiatan statistik sektoral guna menjamin kesesuaian dengan standar dan metodologi statistik nasional.',
        image: media + 'img/layanan-2.png',
        imageAlt: 'Ilustrasi data strategis',
        imageFirst: false,
    },
    {
        id: '03',
        title: 'Layanan Konsultasi Statistik',
        description:
            'BPS Kabupaten Sumbawa Barat memberikan layanan konsultasi statistik untuk membantu pengguna dalam memahami dan memanfaatkan data statistik.',
        image: media + 'img/layanan-3.png',
        imageAlt: 'Ilustrasi pengaduan responsif',
        imageFirst: true,
    },
];
</script>

<template>
    <section class="py-7 sm:py-10 md:py-9 lg:py-6">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-4 text-center sm:mb-6 md:mb-5 lg:mb-3">
                <h2 class="text-2xl leading-tight font-bold tracking-tight text-slate-900 sm:text-3xl md:text-2xl lg:text-[1.65rem]">
                    Jenis Layanan PST
                </h2>
                <p class="mt-1 text-sm text-slate-600 sm:text-base md:text-sm lg:text-sm">Pelayanan Statistik Terpadu</p>
            </div>

            <div
                class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-3.5 lg:grid-cols-3 lg:gap-2.5 md:[&>*:last-child]:col-span-2 md:[&>*:last-child]:max-w-136 md:[&>*:last-child]:justify-self-center lg:[&>*:last-child]:col-span-1 lg:[&>*:last-child]:max-w-none"
            >
                <Card
                    v-for="card in layananCards"
                    :key="card.id"
                    class="gap-0 overflow-hidden rounded-3xl border border-white/75 bg-white/70 py-0 shadow-[0_10px_24px_rgba(15,23,42,0.08)] backdrop-blur sm:rounded-4xl"
                >
                    <CardContent class="p-3 sm:p-4 md:p-3.5 lg:p-3.5">
                        <div
                            class="grid grid-cols-[7rem_1fr] items-center gap-3 sm:flex sm:h-full sm:flex-col sm:items-stretch sm:gap-4 md:gap-3 lg:gap-2"
                        >
                            <div
                                class="overflow-hidden rounded-2xl border border-slate-200/80 bg-slate-100 sm:rounded-3xl"
                                :class="card.imageFirst ? 'sm:order-1' : 'sm:order-2 sm:mt-auto'"
                            >
                                <div class="aspect-square w-28 sm:w-full md:w-[84%] md:self-center lg:mx-auto lg:w-[66%]">
                                    <img :src="card.image" :alt="card.imageAlt" class="h-full w-full object-cover lg:rounded-2xl" />
                                </div>
                            </div>

                            <div class="px-0.5 sm:px-1" :class="card.imageFirst ? 'sm:order-2' : 'sm:order-1'">
                                <p class="text-sm leading-none font-light text-slate-500 sm:text-base md:text-[0.9rem] lg:text-[0.95rem]">
                                    {{ card.id }}
                                </p>
                                <CardTitle
                                    class="mt-1 text-base leading-tight font-bold tracking-tight text-slate-900 sm:mt-2 sm:text-xl md:text-[1.05rem] lg:text-[1.2rem]"
                                >
                                    {{ card.title }}
                                </CardTitle>
                                <CardDescription
                                    class="mt-1 text-sm leading-5 text-slate-600 sm:mt-2 sm:text-base sm:leading-6 md:text-[0.85rem] md:leading-[1.35] lg:text-[0.88rem] lg:leading-[1.35]"
                                >
                                    {{ card.description }}
                                </CardDescription>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </section>
</template>
