<!-- eslint-disable @typescript-eslint/no-unused-vars -->
<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { ChevronUp, CircleChevronUp } from 'lucide-vue-next';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import CTA from '../components/landing-page/cta.vue';
import FAQ from '../components/landing-page/faq.vue';
import FeatureSection from '../components/landing-page/feature-section.vue';
import HeroCarousel from '../components/landing-page/hero-carousel.vue';
import WelcomeNavbar from '../components/landing-page/navbar.vue';
import PricingSection from '../components/landing-page/pricing.vue';
import SocialProof from '../components/landing-page/social-proof.vue';

const showScrollTopButton = ref(false);

function updateScrollTopButtonVisibility(): void {
    showScrollTopButton.value = window.scrollY > 220;
}

function scrollToTop(): void {
    window.scrollTo({
        top: 0,
        behavior: 'smooth',
    });
}

onMounted(() => {
    updateScrollTopButtonVisibility();
    window.addEventListener('scroll', updateScrollTopButtonVisibility, { passive: true });
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', updateScrollTopButtonVisibility);
});
</script>

<template>
    <div>
        <WelcomeNavbar />
        <section id="start">
            <HeroCarousel />
        </section>
        <section id="layanan">
            <FeatureSection />
        </section>
        <section id="data">
            <SocialProof />
        </section>
        <CTA />
        <section id="pengaduan">
            <FAQ />
        </section>
        <PricingSection />

        <Button
            v-show="showScrollTopButton"
            type="button"
            size="icon"
            class="fixed right-5 bottom-5 z-50 cursor-pointer rounded-full shadow-lg transition-all duration-300 hover:-translate-y-1 sm:right-7 sm:bottom-7"
            aria-label="Kembali ke atas"
            @click="scrollToTop"
        >
            <CircleChevronUp class="size-5" />
        </Button>
    </div>
</template>
