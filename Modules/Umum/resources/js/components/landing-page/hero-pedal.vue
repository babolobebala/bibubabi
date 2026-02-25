<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { media } from '@/lib/media';
import { getDeferredInstallPrompt, isIosDevice } from '@/lib/pwa-install';
import { Link } from '@inertiajs/vue3';
import { ArrowDown, Bike, MapPinned, ParkingCircle, ScanSearch, Sparkles } from 'lucide-vue-next';

const mapPins = [
    { key: 'a', count: 4, className: 'left-8 top-8' },
    { key: 'b', count: 9, className: 'right-10 top-16' },
    { key: 'c', count: 37, className: 'left-14 bottom-10' },
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
                        <Link href="/app">Buka Hub Internal</Link>
                    </Button>
                </div>
            </div>

            <div class="relative mx-auto w-full max-w-xl pb-14 lg:max-w-lg lg:pb-0 xl:max-w-xl">
                <div class="grid grid-cols-[1fr_1.2fr] grid-rows-[auto_auto] gap-4">
                    <div class="rounded-4xlrder relative overflow-hidden border-white/80 bg-white shadow-[0_18px_40px_rgba(15,23,42,0.10)]">
                        <div class="absolute inset-0 bg-[linear-gradient(180deg,#ffffff_0%,#f8fbff_100%)]" />
                        <div class="relative aspect-[4/3.9] p-3">
                            <div
                                class="h-full w-full rounded-2xl border border-slate-200/80 bg-[linear-gradient(180deg,#fafafa_0%,#eef4fb_100%)] p-2.5"
                            >
                                <div class="relative h-full w-full rounded-xl border border-dashed border-slate-300/70 bg-white/80">
                                    <div v-for="pin in mapPins" :key="pin.key" class="absolute" :class="pin.className">
                                        <div
                                            class="inline-flex items-center gap-1 rounded-full bg-primary px-2 py-1 text-[10px] font-semibold text-white shadow"
                                        >
                                            <Bike class="h-3 w-3" />
                                            <span>{{ pin.count }}</span>
                                        </div>
                                    </div>
                                    <div
                                        class="absolute inset-0 bg-[radial-gradient(#94a3b8_0.8px,transparent_0.8px)] bg-size-[14px_14px] opacity-40"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row-span-2 overflow-hidden rounded-4xl border border-white/80 bg-white shadow-[0_18px_40px_rgba(15,23,42,0.10)]">
                        <div class="min-h-56ilg:min-h-52in-h-[14rem] relative h-full">
                            <img :src="media + 'img/landing-1.jpg'" alt="Layanan SAKU" class="h-full w-full object-cover" />
                            <div class="absolute inset-x-0 bottom-0 bg-linear-to-t from-black/55 to-transparent p-4">
                                <p class="text-xs font-semibold tracking-wide text-white/85 uppercase">SAKU BPS KSB</p>
                                <p class="mt-1 text-sm font-semibold text-white">Layanan statistik & akses informasi dalam satu pintu</p>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-4xl border border-white/80 bg-white shadow-[0_18px_40px_rgba(15,23,42,0.10)]">
                        <div class="relative aspect-[4/2.4]">
                            <img :src="media + 'img/landing-2.jpeg'" alt="Hub Internal" class="h-full w-full object-cover" />
                            <div class="absolute inset-0 bg-linear-to-t from-black/35 via-transparent to-transparent" />
                        </div>
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-end gap-2.5 sm:gap-3">
                    <div class="grid h-10 w-10 place-items-center rounded-full border border-primary/20 bg-white text-primary shadow-sm">
                        <ScanSearch class="h-5 w-5" />
                    </div>
                    <div class="grid h-10 w-10 place-items-center rounded-full border border-primary/20 bg-white text-primary shadow-sm">
                        <MapPinned class="h-5 w-5" />
                    </div>
                    <div class="grid h-10 w-10 place-items-center rounded-full border border-primary/20 bg-white text-primary shadow-sm">
                        <ParkingCircle class="h-5 w-5" />
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
