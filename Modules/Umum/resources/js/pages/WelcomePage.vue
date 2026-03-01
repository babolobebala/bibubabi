<!-- eslint-disable @typescript-eslint/no-unused-vars -->
<script setup lang="ts">
import ComplaintChannels from '../components/landing-page/complaint-channels.vue';
import CTA from '../components/landing-page/cta.vue';
import HeroCarousel from '../components/landing-page/hero-carousel.vue';
import HeroPedal from '../components/landing-page/hero-pedal.vue';
import Layanan from '../components/landing-page/layanan.vue';
import WelcomeNavbar from '../components/landing-page/navbar.vue';
import PricingSection from '../components/landing-page/pricing.vue';
import SocialProof from '../components/landing-page/social-proof.vue';
</script>

<template>
    <div>
        <WelcomeNavbar />
        <section id="start">
            <HeroPedal />
        </section>
        <div class="bg-linear-to-b from-accent/20 via-background to-background">
            <section id="layanan">
                <Layanan />
            </section>
            <section id="data">
                <SocialProof />
            </section>
            <CTA />
            <section id="pengaduan">
                <ComplaintChannels />
            </section>
            <PricingSection />
        </div>
    </div>
</template>
