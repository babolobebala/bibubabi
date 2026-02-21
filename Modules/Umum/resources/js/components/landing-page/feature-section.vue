<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { computed } from 'vue';

interface FeatureItem {
    title: string;
    description: string;
    imageUrl: string;
    isImageLeft: boolean;
}

const defaultItems: FeatureItem[] = [
    {
        title: 'Rapid Landing Page Development',
        description: 'Build stunning landing pages in minutes with our intuitive drag-and-drop interface and pre-designed components.',
        imageUrl: 'https://ui.convertfa.st/images/graphic-walker-light-2.png',
        isImageLeft: true,
    },
    {
        title: 'Customizable Templates',
        description: 'Choose from a wide range of professionally designed templates and easily customize them to match your brand.',
        imageUrl: 'https://ui.convertfa.st/images/convertfast-ui-cli.png',
        isImageLeft: false,
    },
    {
        title: 'Code Export and Integration',
        description: 'Export clean, optimized code that seamlessly integrates with your existing projects, saving valuable development time.',
        imageUrl: 'https://ui.convertfa.st/images/convertfast-ui-light-demo.png',
        isImageLeft: true,
    },
];

interface Props {
    items?: FeatureItem[];
    brand?: string;
    title?: string;
    description?: string;
}

const props = withDefaults(defineProps<Props>(), {
    brand: 'SAKU',
    title: 'Satu Aplikasi untuk Kinerja Unggul',
    description:
        'Platform digital BPS Kabupaten Sumbawa Barat untuk layanan statistik, akses data strategis, dan penyampaian pengaduan masyarakat secara efisien dan transparan.',
});

const featureItems = computed<FeatureItem[]>(() => props.items ?? defaultItems);
</script>

<template>
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="mx-auto mb-16 max-w-2xl text-center">
            <div class="text-base leading-7 font-semibold text-primary">{{ props.brand }}</div>
            <h2 class="mt-2 text-3xl font-bold tracking-tight text-primary sm:text-4xl">{{ props.title }}</h2>
            <p class="mt-6 text-lg leading-8 text-muted-foreground">{{ props.description }}</p>
        </div>

        <div class="mt-8 flex flex-col gap-16">
            <div v-for="(feature, index) in featureItems" :key="index" class="grid grid-cols-1 items-center gap-8 md:grid-cols-2 md:gap-24">
                <div v-if="feature.isImageLeft" class="order-1 md:order-1">
                    <img class="w-full max-w-2xl rounded-xl shadow-xl ring-1 ring-gray-400/10" :src="feature.imageUrl" :alt="feature.title" />
                </div>

                <div :class="['order-2', feature.isImageLeft ? 'md:order-2' : 'md:order-1']">
                    <h3 class="text-3xl font-bold tracking-tight text-primary sm:text-4xl">{{ feature.title }}</h3>
                    <p class="mt-6 text-lg leading-8 text-muted-foreground">{{ feature.description }}</p>
                    <div class="mt-4">
                        <Button variant="secondary">Learn more</Button>
                    </div>
                </div>

                <div v-if="!feature.isImageLeft" class="order-1 md:order-2">
                    <img class="w-full max-w-2xl rounded-xl shadow-xl ring-1 ring-gray-400/10" :src="feature.imageUrl" :alt="feature.title" />
                </div>
            </div>
        </div>
    </div>
</template>
