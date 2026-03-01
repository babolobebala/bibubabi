<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { media } from '@/lib/media';
import { getDeferredInstallPrompt, isIosDevice } from '@/lib/pwa-install';
import { ArrowDown, Download, Globe, MapPinned, Sparkles } from 'lucide-vue-next';
import { type Component } from 'vue';
import { toast } from 'vue-sonner';
import { Vue3Lottie } from 'vue3-lottie';
import logoLottieData from '../../assets/lottie/logo-lottie.json';

const heroVisualAssets = {
    orange: media + 'img/illustrations/storyset-orange-dominant.svg',
    green: media + 'img/illustrations/storyset-green-dominant.svg',
    blue: media + 'img/illustrations/storyset-blue-dominant.svg',
};

interface HeroQuickLink {
    key: string;
    icon: Component;
    label: string;
    href?: string;
}

const quickLinks: HeroQuickLink[] = [
    {
        key: 'web',
        icon: Globe,
        href: 'https://sumbawabaratkab.bps.go.id/',
        label: 'Website BPS',
    },
    {
        key: 'map',
        icon: MapPinned,
        href: 'https://pst.bps.go.id',
        label: 'Lokasi',
    },
    {
        key: 'download',
        icon: Download,
        label: 'Install SAKU Mobile',
    },
];

async function installApp(): Promise<void> {
    const deferredPrompt = getDeferredInstallPrompt();

    if (deferredPrompt) {
        await deferredPrompt.prompt();
        await deferredPrompt.userChoice;
        return;
    }

    if (isIosDevice()) {
        toast.error('Install manual di iPhone/iPad', {
            description: 'Tekan Share lalu pilih Add to Home Screen.',
        });
        return;
    }

    toast.error('Install SAKU Mobile', {
        description: 'Install app bisa lewat menu browser: Install app / Add to Home Screen.',
    });
}
</script>

<template>
    <section class="relative overflow-hidden bg-accent/20 pt-24 pb-14 sm:pt-28 lg:min-h-screen lg:pt-16 lg:pb-8">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute top-0 left-0 h-72 w-72 rounded-full bg-primary/10 blur-3xl" />
            <div class="absolute right-0 bottom-0 h-80 w-80 rounded-full bg-accent/60 blur-3xl" />
            <div class="absolute inset-0 bg-radial from-primary/10 via-transparent to-transparent" />
        </div>

        <div
            class="relative mx-auto grid max-w-7xl items-center gap-10 px-4 sm:px-6 lg:min-h-[calc(100svh-4.5rem)] lg:grid-cols-[1.02fr_1fr] lg:gap-10 lg:px-8"
        >
            <div class="max-w-2xl">
                <div class="mb-4 flex flex-wrap items-center gap-2">
                    <p
                        class="inline-flex items-center gap-2 rounded-full border border-primary/15 bg-background/80 px-3 py-1 text-xs font-semibold text-primary shadow-sm"
                    >
                        <Sparkles class="h-3.5 w-3.5" />
                        Platform layanan dan mobilitas informasi BPS KSB
                    </p>

                    <div class="inline-flex items-center gap-2 rounded-full border border-border/80 bg-background/90 px-2.5 py-1 shadow-sm">
                        <img :src="media + 'img/logo/saku.png'" alt="Logo SAKU" class="h-5 w-auto object-contain" />
                        <span class="text-[11px] font-semibold tracking-tight text-foreground/80">SAKU - Satu Aplikasi untuk Kinerja Unggul</span>
                    </div>
                </div>

                <h1 class="text-4xl leading-tight font-black tracking-tight text-balance text-foreground sm:text-5xl lg:text-5xl xl:text-6xl">
                    SAKU BPS
                    <br />
                    Bergerak Bersinergi
                </h1>

                <p class="mt-4 max-w-xl text-base leading-8 text-muted-foreground sm:text-lg lg:text-base lg:leading-7">
                    Platform digital BPS Kabupaten Sumbawa Barat untuk layanan statistik, akses data strategis, dan penyampaian pengaduan masyarakat
                    secara efisien dan transparan.
                </p>

                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <Button as-child size="lg" class="rounded-full px-6">
                        <a href="#data">
                            Lihat Data Strategis
                            <ArrowDown class="h-4 w-4" />
                        </a>
                    </Button>

                    <Button as-child size="lg" class="rounded-full bg-success text-success-foreground hover:bg-success/90">
                        <a target="_blank" href="http://wa.me/+6282144406055">
                            <img :src="media + 'img/logo/wa.svg'" alt="WhatsApp" class="h-4 w-4 object-contain brightness-0 invert" />
                            Hubungi Kami (WhatsApp)
                        </a>
                    </Button>
                </div>
            </div>

            <div class="relative mx-auto w-full max-w-884 sm:max-w-[24rem] lg:mx-0 lg:ml-auto lg:max-w-104 lg:pb-0 xl:max-w-md">
                <div class="grid grid-cols-2 gap-3 sm:gap-4">
                    <div class="flex flex-col gap-3 sm:gap-4">
                        <article class="overflow-hidden rounded-4xl border border-border/80 bg-card shadow-lg">
                            <div class="relative grid aspect-square place-items-center bg-linear-to-b from-background to-accent/25 p-3">
                                <div class="h-[86%] w-[86%]">
                                    <Vue3Lottie
                                        :animation-data="logoLottieData"
                                        height="100%"
                                        width="100%"
                                        :loop="false"
                                        :speed="1"
                                        class="h-full w-full"
                                    />
                                </div>
                            </div>
                        </article>

                        <article class="overflow-hidden rounded-4xl border border-border/80 bg-card shadow-lg">
                            <div class="relative aspect-5/3 bg-linear-to-br from-secondary via-secondary to-accent/30">
                                <img :src="heroVisualAssets.orange" alt="Ilustrasi dominan oranye" class="h-full w-full object-contain p-3" />
                            </div>
                        </article>
                    </div>

                    <div class="flex flex-col gap-3 sm:gap-4">
                        <article class="overflow-hidden rounded-4xl border border-border/80 bg-card shadow-lg">
                            <div class="relative aspect-5/3 bg-linear-to-br from-background via-success/10 to-success/25">
                                <img :src="heroVisualAssets.green" alt="Ilustrasi dominan hijau" class="h-full w-full object-contain p-3" />
                            </div>
                        </article>

                        <article class="overflow-hidden rounded-4xl border border-border/80 bg-card shadow-lg">
                            <div class="relative aspect-square bg-linear-to-br from-background via-accent/40 to-primary/15 p-3">
                                <img :src="heroVisualAssets.blue" alt="Ilustrasi dominan biru" class="h-full w-full object-contain" />
                            </div>
                        </article>
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-end gap-2.5 sm:gap-3">
                    <a
                        v-for="item in quickLinks"
                        :key="item.key"
                        :href="item.href ?? '#'"
                        :target="item.href ? '_blank' : undefined"
                        :rel="item.href ? 'noopener noreferrer' : undefined"
                        class="group relative grid h-10 w-10 place-items-center rounded-full border border-primary/20 bg-background text-primary shadow-sm hover:-translate-y-0.5 hover:bg-primary/5"
                        @click="item.key === 'download' ? ($event.preventDefault(), installApp()) : undefined"
                    >
                        <component :is="item.icon" class="h-5 w-5" />
                        <span
                            class="pointer-events-none absolute bottom-full left-1/2 mb-2 hidden -translate-x-1/2 rounded-md bg-foreground px-2 py-1 text-[11px] font-medium whitespace-nowrap text-background group-hover:block group-focus-visible:block"
                        >
                            {{ item.label }}
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </section>
</template>
