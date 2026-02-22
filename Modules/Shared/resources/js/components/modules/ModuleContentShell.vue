<script setup lang="ts">
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/components/ui/breadcrumb';
import { Link } from '@inertiajs/vue3';

interface ModuleShellBreadcrumbItem {
    label: string;
    href?: string;
}

withDefaults(
    defineProps<{
        rootLabel?: string;
        breadcrumbs?: ModuleShellBreadcrumbItem[];
        bodyClass?: string;
    }>(),
    {
        rootLabel: 'App',
        breadcrumbs: () => [{ label: 'Home', href: '/app' }],
        bodyClass: 'space-y-4 px-4 py-4 sm:px-6 sm:py-5',
    },
);
</script>

<template>
    <div>
        <div class="border-b border-border px-4 py-4 sm:px-6">
            <Breadcrumb>
                <BreadcrumbList class="flex-wrap gap-1.5 sm:gap-2">
                    <BreadcrumbItem>
                        <span class="font-semibold text-foreground">{{ rootLabel }}</span>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator />
                    <template v-for="(crumb, index) in breadcrumbs" :key="`${crumb.label}-${index}`">
                        <BreadcrumbItem>
                            <BreadcrumbLink v-if="crumb.href" as-child>
                                <Link :href="crumb.href">{{ crumb.label }}</Link>
                            </BreadcrumbLink>
                            <BreadcrumbPage v-else>{{ crumb.label }}</BreadcrumbPage>
                        </BreadcrumbItem>
                        <BreadcrumbSeparator v-if="index < breadcrumbs.length - 1" />
                    </template>
                </BreadcrumbList>
            </Breadcrumb>
        </div>

        <div :class="bodyClass">
            <slot />
        </div>
    </div>
</template>
