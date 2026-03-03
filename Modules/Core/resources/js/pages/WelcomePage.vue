<script lang="ts">
export default {
    layout: null,
};
</script>

<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import SharedLoginContent from '../../../../Shared/resources/js/components/public/LoginContent.vue';
import { loadModuleComponent } from '../lib/module-components';

type ModuleStatuses = Record<string, boolean>;
interface WelcomePageProps {
    modules?: {
        statuses?: ModuleStatuses;
    };
    [key: string]: unknown;
}

const page = usePage<WelcomePageProps>();
const statuses = computed(() => page.props.modules?.statuses ?? {});
const isUmumActive = computed(() => Boolean(statuses.value.Umum));

const WelcomeContent = loadModuleComponent('Umum', () => import('../../../../Umum/resources/js/components/landing-page/WelcomeContent.vue'));
</script>

<template>
    <WelcomeContent v-if="isUmumActive" />
    <div v-else class="min-h-screen bg-background px-6 py-16 text-foreground">
        <div class="mx-auto overflow-hidden rounded-xl border border-border/70 bg-background shadow-2xl sm:max-w-83">
            <SharedLoginContent />
        </div>
    </div>
</template>
