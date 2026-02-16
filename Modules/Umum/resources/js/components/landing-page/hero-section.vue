<script setup lang="ts">
import { Button } from '@/components/ui/button';
</script>

<template>
    <div class="relative bg-linear-to-t from-zinc-50 to-white dark:from-zinc-950">
        <div
            class="absolute inset-0 top-0 right-0 bottom-0 left-0 z-0 bg-[url('/_convertfast/gradient-bg-0.svg')] bg-auto bg-no-repeat grayscale"
        ></div>
        <div class="relative z-10 mx-auto max-w-7xl px-4 py-20 sm:px-6 sm:py-24 lg:px-8 lg:py-32">
            <div class="max-w-3xl">
                <h1 class="mb-6 text-4xl font-bold tracking-tight text-primary drop-shadow-md sm:text-5xl lg:text-6xl">
                    Bootstrap your landing page with ConvertFast UI
                </h1>
                <p class="mb-8 text-xl text-muted-foreground sm:text-2xl">
                    Build stunning landing pages with ease and convert your customers faster than ever.
                </p>
                <div class="flex flex-col gap-4 sm:flex-row">
                    <Button size="lg" class="w-full sm:w-auto" as-child>
                        <a href="#start"> Start now </a>
                    </Button>
                    <Button variant="outline" size="lg" class="w-full sm:w-auto">
                        <code class="rounded p-1">npm install convertfast-ui</code>
                    </Button>
                </div>
                <p class="mt-4 text-sm text-gray-500">Free and open-source. No credit card required.</p>
            </div>

            <img
                alt="app screenshot"
                src="https://ui.convertfa.st/images/graphic-walker-light-2.png"
                width="2432"
                height="1442"
                class="mt-8 block rounded-md border shadow-2xl sm:mt-12 dark:hidden"
            />
            <img
                alt="app screenshot"
                src="https://ui.convertfa.st/images/graphic-walker-dark-2.png"
                width="2432"
                height="1442"
                class="mt-8 hidden rounded-md border shadow-2xl sm:mt-12 dark:block"
            />
        </div>
    </div>
</template>
