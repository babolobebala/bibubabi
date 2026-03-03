<script setup lang="ts">
import ComplaintChannels from './complaint-channels.vue';
import CTA from './cta.vue';
import HeroPedal from './hero-pedal.vue';
import Layanan from './layanan.vue';
import WelcomeNavbar from './navbar.vue';
import PricingSection from './pricing.vue';
import SocialProof from './social-proof.vue';
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
