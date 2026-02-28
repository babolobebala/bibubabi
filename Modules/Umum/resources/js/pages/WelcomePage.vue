<!-- eslint-disable @typescript-eslint/no-unused-vars -->
<script setup lang="ts">
import CTA from '../components/landing-page/cta.vue';
import FAQ from '../components/landing-page/faq.vue';
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
        <div class="bg-[linear-gradient(180deg,#edf3fb_0%,#f7faff_18%,#f8fafc_100%)]">
            <section id="layanan">
                <Layanan />
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
    </div>
</template>
