<script setup lang="ts">
import ComplaintChannels from './complaint-channels.vue';
import DataStrategis from './data-strategis.vue';
import HeroPedal from './hero-pedal.vue';
import Layanan from './layanan.vue';
import WelcomeNavbar from './navbar.vue';
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
                <DataStrategis />
            </section>
            <section id="pengaduan">
                <ComplaintChannels />
            </section>
        </div>
    </div>
</template>
