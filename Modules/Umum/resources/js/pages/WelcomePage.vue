<!-- eslint-disable @typescript-eslint/no-unused-vars -->
<script setup lang="ts">
import CTA from '../components/landing-page/cta.vue';
import FAQ from '../components/landing-page/faq.vue';
import FeatureSection from '../components/landing-page/feature-section.vue';
import HeroSection from '../components/landing-page/hero-section.vue';
import LogoCloud from '../components/landing-page/logo-cloud.vue';
import SalsaNavbar from '../components/landing-page/navbar.vue';
import PricingSection from '../components/landing-page/pricing.vue';
import SocialProof from '../components/landing-page/social-proof.vue';
// import { media } from '@/lib/media';
</script>

<template>
    <SalsaNavbar />
    <section id="start">
        <HeroSection />
    </section>
    <LogoCloud />
    <section id="features">
        <FeatureSection />
    </section>
    <section id="social-proof">
        <!-- <SocialProof /> -->
    </section>
    <!-- <CTA /> -->
    <section id="faq">
        <!-- <FAQ /> -->
    </section>
    <!-- <PricingSection /> -->
</template>
