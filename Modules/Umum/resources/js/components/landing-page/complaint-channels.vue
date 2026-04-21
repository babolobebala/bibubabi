<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import { Activity } from 'lucide-vue-next';
import { defineComponent, h } from 'vue';

interface ComplaintChannel {
    title: string;
    description: any;
    image: string;
    badge: string;
    responseTime: string;
    availability: string;
    actionLabel: string;
    href?: string;
    variant: any;
}

const channels: ComplaintChannel[] = [
    {
        title: 'Website SAKU',
        description: 'Buat pengaduan langsung di website dengan memasukkan email',
        image: '/img/logo/saku.png',
        badge: 'Pengaduan Website',
        responseTime: 'Terstruktur',
        availability: '24/7',
        actionLabel: 'Laporkan',
        href: '/pengaduan',
        variant: 'secondary',
    },
    {
        title: 'WhatsApp',
        description: 'Pelayanan dan pengaduan cepat tanggap ',
        image: '/img/logo/wa.svg',
        badge: 'Cepat',
        responseTime: 'Cepat',
        availability: 'Jam kerja',
        actionLabel: 'Buka WA',
        href: 'http://wa.me/+6282144406055',
        variant: 'secondary',
    },
    {
        title: 'Datang Langsung',
        description: 'Cocok jika perlu klarifikasi detail atau membawa dokumen.',
        image: '/img/logo/logo.png',
        badge: 'Tatap muka',
        responseTime: 'Antrean',
        availability: 'Jam kantor',
        actionLabel: 'Info Kantor',
        variant: 'secondary',
    },
    {
        title: 'Media Sosial',
        description: defineComponent({
            setup() {
                return () =>
                    h('div', { class: 'flex items-center justify-center gap-3 mt-1.5' }, [
                        h('img', {
                            src: '/img/logo/ig.svg',
                            class: 'h-4 w-4 grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all',
                        }),
                        h('img', {
                            src: '/img/logo/fb.svg',
                            class: 'h-4 w-4 grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all',
                        }),
                        h('img', {
                            src: '/img/logo/wa.svg',
                            class: 'h-4 w-4 grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all',
                        }),
                    ]);
            },
        }),
        image: '/img/logo/ig.svg',
        badge: 'Publik',
        responseTime: 'Bervariasi',
        availability: 'Tergantung kanal',
        actionLabel: 'Lihat Kanal',
        variant: 'default',
    },
    {
        title: 'SP4N-LAPOR!',
        description: 'Jalur pengaduan nasional yang formal dan terdokumentasi.',
        image: '/img/logo/landing-span.png',
        badge: 'Resmi',
        responseTime: 'Mekanisme',
        availability: '24/7',
        actionLabel: 'Buka SP4N',
        href: 'https://www.lapor.go.id/',
        variant: 'secondary',
    },
];
</script>

<template>
    <section class="py-10 sm:py-12 lg:py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-7 text-center">
                <div
                    class="mx-auto mb-4 flex w-fit items-center gap-2 rounded-full border border-blue-500/30 bg-blue-500/10 px-4 py-1.5 text-[10px] font-bold tracking-[0.2em] text-blue-400 uppercase"
                >
                    <Activity class="h-3.5 w-3.5" />
                    Kanal Pelayanan & Pengaduan
                </div>
                <h2 class="text-2xl font-black tracking-tight text-foreground sm:text-3xl lg:text-4xl">Daftar Kanal Pelayanan dan Pengaduan</h2>
            </div>

            <div class="grid gap-2.5 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5">
                <component
                    :is="channel.href ? 'a' : 'div'"
                    v-for="channel in channels"
                    :key="channel.title"
                    :href="channel.href"
                    :target="channel.href ? '_blank' : undefined"
                    :rel="channel.href ? 'noopener noreferrer' : undefined"
                    class="block"
                >
                    <Card class="h-full rounded-lg border-border py-0 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <CardContent class="flex h-full flex-col items-center gap-2 p-3.5 text-center">
                            <div class="grid h-12 w-12 place-items-center rounded-full border border-primary/15 bg-accent p-2">
                                <img :src="channel.image" :alt="channel.title" class="h-full w-full object-contain" />
                            </div>
                            <p class="line-clamp-2 min-h-9 text-[13px] leading-4 font-medium text-foreground">
                                {{ channel.title }}
                            </p>
                            <div class="line-clamp-2 min-h-8 text-[11px] leading-4 text-muted-foreground">
                                <template v-if="typeof channel.description === 'string'">
                                    {{ channel.description }}
                                </template>
                                <component :is="channel.description" v-else />
                            </div>
                        </CardContent>
                    </Card>
                </component>
            </div>
        </div>
    </section>
</template>
