<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { media } from '@/lib/media';
import { canInstallPwa, getDeferredInstallPrompt } from '@/lib/pwa-install';
import { ArrowRight, ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { onBeforeUnmount, onMounted, ref } from 'vue';

interface HeroSlide {
    src: string;
    alt: string;
}

const slides: HeroSlide[] = [
    {
        src: media + 'img/landing-1.jpg',
        alt: 'Landing 1',
    },
    {
        src: media + 'img/landing-2.jpeg',
        alt: 'Landing 2',
    },
    {
        src: media + 'img/landing-3.jpeg',
        alt: 'Landing 3',
    },
];

const activeSlide = ref(0);
let autoplayTimer: ReturnType<typeof setInterval> | null = null;

const goToSlide = (index: number): void => {
    activeSlide.value = index;
};

const nextSlide = (): void => {
    activeSlide.value = (activeSlide.value + 1) % slides.length;
};

const prevSlide = (): void => {
    activeSlide.value = (activeSlide.value - 1 + slides.length) % slides.length;
};

const startAutoplay = (): void => {
    stopAutoplay();
    autoplayTimer = setInterval(() => {
        nextSlide();
    }, 4500);
};

const stopAutoplay = (): void => {
    if (autoplayTimer) {
        clearInterval(autoplayTimer);
        autoplayTimer = null;
    }
};

// PWA
const showInstallButton = ref(false);
async function installApp(): Promise<void> {
    const deferredPrompt = getDeferredInstallPrompt();

    if (!deferredPrompt) {
        return;
    }

    await deferredPrompt.prompt();
    await deferredPrompt.userChoice;
    syncInstallUi();
}

function syncInstallUi(): void {
    showInstallButton.value = canInstallPwa();
}

onMounted(() => {
    startAutoplay();
    syncInstallUi();
});

onBeforeUnmount(() => {
    stopAutoplay();
});
</script>

<template>
    <section
        class="relative isolate flex min-h-svh items-center justify-center overflow-hidden"
        @mouseenter="stopAutoplay"
        @mouseleave="startAutoplay"
    >
        <div class="absolute inset-0 -z-20">
            <img
                v-for="(slide, index) in slides"
                :key="slide.src"
                :src="slide.src"
                :alt="slide.alt"
                class="absolute inset-0 size-full object-cover transition-opacity duration-700"
                :class="index === activeSlide ? 'opacity-100' : 'opacity-0'"
            />
        </div>

        <div class="absolute inset-0 -z-10 bg-black/55" />

        <div class="mx-auto flex w-full max-w-5xl flex-col items-center px-4 py-16 text-center sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold tracking-tight text-balance text-white sm:text-3xl lg:text-5xl">SAKU - BPS Kabupaten Sumbawa Barat</h1>

            <p class="sm:text-md mt-5 max-w-3xl text-base leading-relaxed text-pretty text-white/90 lg:text-lg">
                Platform digital BPS Kabupaten Sumbawa Barat untuk layanan statistik, akses data strategis, dan penyampaian pengaduan masyarakat
                secara efisien dan transparan.
            </p>

            <div class="mt-8 flex w-full flex-col items-center justify-center gap-3 sm:w-auto sm:flex-row">
                <Button as-child size="lg" v-if="showInstallButton" class="w-full sm:w-auto" @click="installApp"
                    >Install SAKU Mobile
                    <ArrowRight class="size-4" />
                </Button>

                <Button as-child size="lg" variant="outline" class="w-full border-white/40 bg-white/10 text-white hover:bg-white/20 sm:w-auto">
                    <a href="#features">Learn More</a>
                </Button>
            </div>
        </div>

        <Button
            variant="secondary"
            size="icon"
            class="absolute top-1/2 left-3 z-20 -translate-y-1/2 rounded-full bg-black/30 text-white hover:bg-black/50 sm:left-6"
            @click="prevSlide"
        >
            <ChevronLeft class="size-5" />
        </Button>

        <Button
            variant="secondary"
            size="icon"
            class="absolute top-1/2 right-3 z-20 -translate-y-1/2 rounded-full bg-black/30 text-white hover:bg-black/50 sm:right-6"
            @click="nextSlide"
        >
            <ChevronRight class="size-5" />
        </Button>

        <div class="absolute bottom-6 left-1/2 z-20 flex -translate-x-1/2 items-center gap-2">
            <button
                v-for="(slide, index) in slides"
                :key="`dot-${slide.src}`"
                type="button"
                class="h-2.5 w-2.5 rounded-full transition-all duration-300"
                :class="index === activeSlide ? 'w-6 bg-white' : 'bg-white/50 hover:bg-white/80'"
                @click="goToSlide(index)"
            />
        </div>
    </section>
</template>
