<!-- eslint-disable @typescript-eslint/no-unused-vars -->
<script setup lang="ts">
import CTA from '../components/landing-page/cta.vue';
import FAQ from '../components/landing-page/faq.vue';
import FeatureSection from '../components/landing-page/feature-section.vue';
import HeroCarousel from '../components/landing-page/hero-carousel.vue';
import WelcomeNavbar from '../components/landing-page/navbar.vue';
import PricingSection from '../components/landing-page/pricing.vue';
import SocialProof from '../components/landing-page/social-proof.vue';
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
    </div>
</template>
