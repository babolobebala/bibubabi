<script setup lang="ts">
import CTA from './cta.vue'
import FAQ from './faq.vue'
import FeatureSection from './feature-section.vue'
import HeroSection from './hero-section.vue'
import LogoCloud from './logo-cloud.vue'
import SalsaNavbar from './navbar.vue'
import PricingSection from './pricing.vue'
import SocialProof from './social-proof.vue'
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
    <SocialProof />
  </section>
  <CTA />
  <section id="faq">
    <FAQ />
  </section>
  <PricingSection />
</template>
