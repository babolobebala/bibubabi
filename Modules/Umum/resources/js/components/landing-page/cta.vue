<script setup lang="ts">
import { Button } from '@/components/ui/button';

interface Props {
    title?: string;
    description?: string;
}

withDefaults(defineProps<Props>(), {
    title: 'Ready to build your high-converting landing page?',
    description:
        'With ConvertFast, you can create stunning landing pages that turn visitors into customers. Start building for free and see the difference.',
});
</script>

<template>
    <section class="relative bg-linear-to-t from-zinc-50 to-white dark:from-zinc-950">
        <div
            class="absolute inset-0 top-0 right-0 bottom-0 left-0 bg-[url('/_convertfast/gradient-bg-0.svg')] bg-auto bg-center bg-no-repeat grayscale"
        ></div>

        <div class="relative z-10 mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-24 lg:px-8 lg:py-32">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl lg:text-5xl">{{ title }}</h2>

                <p class="mt-6 text-xl leading-8 text-muted-foreground opacity-90">
                    {{ description }}
                </p>

                <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                    <Button size="lg" variant="outline" class="w-full sm:w-auto"> Get started for free </Button>

                    <Button size="lg" class="w-full sm:w-auto"> View documentation </Button>
                </div>

                <p class="mt-6 text-sm text-muted-foreground opacity-75">No credit card required. Start building in minutes.</p>
            </div>
        </div>
    </section>
</template>
