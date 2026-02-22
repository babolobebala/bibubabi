<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import coreNavigation from '../config/module-navigation.json';
import { loadModuleValue } from '../lib/module-components';
import ModuleHubContent from '../../../../Shared/resources/js/components/modules/ModuleHubContent.vue';
import toolNavigation from '../../../../Tool/resources/js/config/module-navigation.json';
import {
    getModuleCoreMenu,
    getModuleHubBreadcrumbs,
    getModuleHubConfig,
    getModuleHubItems,
    type ModuleNavigationBreadcrumbItem,
    type ModuleNavigationConfig,
    type ModuleNavigationMenuItem,
} from '../../../../Shared/resources/js/lib/module-navigation';

interface CoreModuleEntry {
    moduleName: string;
    menu: ModuleNavigationMenuItem;
    hubConfig: ReturnType<typeof getModuleHubConfig>;
    features: ModuleNavigationMenuItem[];
}

const coreHubConfig = getModuleHubConfig(coreNavigation);

const moduleEntries = [
    loadModuleValue<CoreModuleEntry | null>(
        'Tool',
        () => {
            const menu = getModuleCoreMenu(toolNavigation as ModuleNavigationConfig);

            if (!menu) {
                return null;
            }

            return {
                moduleName: 'Tool',
                menu,
                hubConfig: getModuleHubConfig(toolNavigation as ModuleNavigationConfig),
                features: getModuleHubItems(toolNavigation as ModuleNavigationConfig),
            };
        },
        null,
    ),
].filter((item): item is CoreModuleEntry => item !== null);

const activeModuleKey = ref<string | null>(null);

const activeModule = computed(() => moduleEntries.find((item) => item.menu.key === activeModuleKey.value) ?? null);

const homeMenuItems = computed(() =>
    moduleEntries.map((entry) => ({
        ...entry.menu,
        href: undefined,
        onClick: () => openModule(entry.menu.key),
    })),
);

const moduleMenuItems = computed(() => {
    if (!activeModule.value) {
        return [];
    }

    return activeModule.value.features;
});

const menuBreadcrumbs = computed<Array<ModuleNavigationBreadcrumbItem & { onClick?: () => void }>>(() => {
    const homeCrumb = {
        label: 'Home',
        onClick: () => goHome(),
    };

    if (!activeModule.value) {
        return [{ label: 'Home' }];
    }

    return [homeCrumb, { label: activeModule.value.menu.title }];
});

const menuSectionTitle = computed(() => {
    if (!activeModule.value) {
        return coreHubConfig.sectionTitle ?? 'Beranda';
    }

    return activeModule.value.hubConfig.sectionTitle ?? activeModule.value.menu.title;
});

function goHome(): void {
    activeModuleKey.value = null;
    clearHash();
}

function openModule(moduleKey: string): void {
    if (!moduleKey) {
        return;
    }

    activeModuleKey.value = moduleKey;
    setHash(moduleKey);
}

function syncModuleFromHash(): void {
    if (typeof window === 'undefined') {
        return;
    }

    const hash = window.location.hash.replace(/^#/, '').trim().toLowerCase();

    if (!hash) {
        activeModuleKey.value = null;
        return;
    }

    const matchedModule = moduleEntries.find((item) => item.menu.key.toLowerCase() === hash);
    activeModuleKey.value = matchedModule?.menu.key ?? null;
}

function setHash(value: string): void {
    if (typeof window === 'undefined') {
        return;
    }

    const nextHash = `#${value}`;
    if (window.location.hash !== nextHash) {
        window.location.hash = value;
    }
}

function clearHash(): void {
    if (typeof window === 'undefined') {
        return;
    }

    if (!window.location.hash) {
        return;
    }

    window.history.replaceState(window.history.state, '', `${window.location.pathname}${window.location.search}`);
}

onMounted(() => {
    syncModuleFromHash();
    window.addEventListener('hashchange', syncModuleFromHash);
});

onBeforeUnmount(() => {
    window.removeEventListener('hashchange', syncModuleFromHash);
});
</script>

<template>
    <div>
        <ModuleHubContent
            :section-title="menuSectionTitle"
            :breadcrumbs="activeModule ? menuBreadcrumbs : getModuleHubBreadcrumbs(coreNavigation)"
            :items="activeModule ? moduleMenuItems : homeMenuItems"
        />
    </div>
</template>
