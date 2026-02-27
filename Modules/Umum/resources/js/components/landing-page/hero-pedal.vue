<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { media } from '@/lib/media';
import { getDeferredInstallPrompt, isIosDevice } from '@/lib/pwa-install';
import { ArrowDown, MapPinned, ParkingCircle, ScanSearch, Sparkles } from 'lucide-vue-next';
import { type Component } from 'vue';
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
    href: string;
    label: string;
}

const quickLinks: HeroQuickLink[] = [
    {
        key: 'scan',
        icon: ScanSearch,
        href: 'https://www.bps.go.id',
        label: 'Website BPS',
    },
    {
        key: 'map',
        icon: MapPinned,
        href: 'https://pst.bps.go.id',
        label: 'PST BPS',
    },
    {
        key: 'parking',
        icon: ParkingCircle,
        href: 'https://webapi.bps.go.id',
        label: 'Web API BPS',
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
        window.alert('Untuk iPhone/iPad: tekan Share lalu pilih Add to Home Screen.');
        return;
    }

    window.alert('Install app bisa lewat menu browser: Install app / Add to Home Screen.');
}
</script>

<template>
    <section class="relative overflow-hidden bg-[#edf3fb] pt-24 pb-14 sm:pt-28 lg:min-h-screen lg:pt-16 lg:pb-8">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute top-0 left-0 h-72 w-72 rounded-full bg-sky-200/30 blur-3xl" />
            <div class="absolute right-0 bottom-0 h-80 w-80 rounded-full bg-blue-200/25 blur-3xl" />
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_35%,rgba(59,130,246,0.07),transparent_40%)]" />
        </div>

        <div
            class="relative mx-auto grid max-w-7xl items-center gap-10 px-4 sm:px-6 lg:min-h-[calc(100svh-4.5rem)] lg:grid-cols-[1.02fr_1fr] lg:gap-10 lg:px-8"
        >
            <div class="max-w-2xl">
                <div class="mb-4 flex flex-wrap items-center gap-2">
                    <p
                        class="inline-flex items-center gap-2 rounded-full border border-primary/15 bg-white/70 px-3 py-1 text-xs font-semibold text-primary shadow-sm"
                    >
                        <Sparkles class="h-3.5 w-3.5" />
                        Platform layanan dan mobilitas informasi BPS KSB
                    </p>

                    <div class="inline-flex items-center gap-2 rounded-full border border-white/90 bg-white/85 px-2.5 py-1 shadow-sm">
                        <img :src="media + 'img/logo/saku.png'" alt="Logo SAKU" class="h-5 w-auto object-contain" />
                        <span class="text-[11px] font-semibold tracking-tight text-slate-700">SAKU - Satu Aplikasi untuk Kinerja Unggul</span>
                    </div>
                </div>

                <h1 class="text-4xl leading-tight font-black tracking-tight text-balance text-slate-900 sm:text-5xl lg:text-5xl xl:text-6xl">
                    SAKU BPS
                    <br />
                    Bergerak Bersinergi
                </h1>

                <p class="mt-4 max-w-xl text-base leading-8 text-slate-600 sm:text-lg lg:text-base lg:leading-7">
                    Platform digital BPS Kabupaten Sumbawa Barat untuk layanan statistik, akses data strategis, dan penyampaian pengaduan masyarakat
                    secara efisien dan transparan.
                </p>

                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <Button size="lg" class="cursor-pointer rounded-full px-6" @click="installApp">
                        Install SAKU Mobile
                        <ArrowDown class="h-4 w-4" />
                    </Button>

                    <Button as-child size="lg" variant="outline" class="rounded-full border-slate-300 bg-white/80 px-6 text-slate-800">
                        <a href="http://wa.me/+6282172886060">Hubungi Kami (WhatsApp)</a>
                    </Button>
                </div>
            </div>

            <div class="relative mx-auto w-full max-w-884 sm:max-w-[24rem] lg:mx-0 lg:ml-auto lg:max-w-104 lg:pb-0 xl:max-w-md">
                <div class="grid grid-cols-2 gap-3 sm:gap-4">
                    <div class="flex flex-col gap-3 sm:gap-4">
                        <article class="overflow-hidden rounded-4xl border border-white/80 bg-white shadow-[0_18px_40px_rgba(15,23,42,0.10)]">
                            <div class="relative grid aspect-square place-items-center bg-[linear-gradient(180deg,#ffffff_0%,#f4f8ff_100%)] p-3">
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

                        <article class="overflow-hidden rounded-4xl border border-white/80 bg-white shadow-[0_18px_40px_rgba(15,23,42,0.10)]">
                            <div class="relative aspect-5/3 bg-[linear-gradient(135deg,#fff8f1_0%,#ffedd5_45%,#fed7aa_100%)]">
                                <img :src="heroVisualAssets.orange" alt="Ilustrasi dominan oranye" class="h-full w-full object-contain p-3" />
                            </div>
                        </article>
                    </div>

                    <div class="flex flex-col gap-3 sm:gap-4">
                        <article class="overflow-hidden rounded-4xl border border-white/80 bg-white shadow-[0_18px_40px_rgba(15,23,42,0.10)]">
                            <div class="relative aspect-5/3 bg-[linear-gradient(135deg,#f0fdf4_0%,#dcfce7_45%,#bbf7d0_100%)]">
                                <img :src="heroVisualAssets.green" alt="Ilustrasi dominan hijau" class="h-full w-full object-contain p-3" />
                            </div>
                        </article>

                        <article class="overflow-hidden rounded-4xl border border-white/80 bg-white shadow-[0_18px_40px_rgba(15,23,42,0.10)]">
                            <div class="relative aspect-square bg-[linear-gradient(135deg,#eff6ff_0%,#dbeafe_45%,#bfdbfe_100%)] p-3">
                                <img :src="heroVisualAssets.blue" alt="Ilustrasi dominan biru" class="h-full w-full object-contain" />
                            </div>
                        </article>
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-end gap-2.5 sm:gap-3">
                    <a
                        v-for="item in quickLinks"
                        :key="item.key"
                        :href="item.href"
                        :title="item.label"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="grid h-10 w-10 place-items-center rounded-full border border-primary/20 bg-white text-primary shadow-sm transition hover:-translate-y-0.5 hover:bg-primary/5"
                    >
                        <component :is="item.icon" class="h-5 w-5" />
                    </a>
                </div>
            </div>
        </div>
    </section>
</template>
