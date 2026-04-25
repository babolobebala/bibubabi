<script setup lang="ts">
import { media } from '@/lib/media';
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { ShieldCheck } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import ModuleContentShell from '../../../../Shared/resources/js/components/modules/ModuleContentShell.vue';
import { type ModuleNavigationBreadcrumbItem, type ModuleNavigationMenuItem } from '../../../../Shared/resources/js/lib/module-navigation';
import { getCoreModuleEntries, useCoreMenuHashSection } from '../lib/core-menu';

const HOME_BREADCRUMBS: ModuleNavigationBreadcrumbItem[] = [{ label: 'Home' }];

const userRoles = (usePage().props.auth as { roles?: string[] })?.roles ?? [];
const moduleEntries = ref(getCoreModuleEntries(userRoles));
const { activeModuleKey, clearHash, setHash } = useCoreMenuHashSection(moduleEntries);

type CoreMenuUiItem = Omit<ModuleNavigationMenuItem, 'href'> & {
    href?: string;
    onClick?: () => void;
    searchText?: string;
};

const activeModule = computed(() => moduleEntries.value.find((item) => item.menu.key === activeModuleKey.value) ?? null);
const brokenIconKeys = ref<Record<string, true>>({});

const homeMenuItems = computed<CoreMenuUiItem[]>(() =>
    moduleEntries.value.map((entry) => {
        const hasChildren = entry.features && entry.features.length > 0;

        return {
            ...entry.menu,
            href: hasChildren ? undefined : entry.menu.href,
            onClick: hasChildren ? () => openModule(entry.menu.key) : undefined,
            searchText: [entry.menu.title, entry.menu.description, ...entry.features.flatMap((feature) => [feature.title, feature.description])]
                .filter((value): value is string => Boolean(value?.trim()))
                .join(' ')
                .toLowerCase(),
        };
    }),
);

const moduleMenuItems = computed<CoreMenuUiItem[]>(() => {
    if (!activeModule.value) {
        return [];
    }

    return activeModule.value.features;
});

const currentMenuItems = computed<CoreMenuUiItem[]>(() => (activeModule.value ? moduleMenuItems.value : homeMenuItems.value));

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

function handleSelect(item: CoreMenuUiItem): void {
    item.onClick?.();
}

function markBrokenIcon(itemKey: string): void {
    brokenIconKeys.value[itemKey] = true;
}
</script>

<template>
    <div>
        <ModuleContentShell :breadcrumbs="activeModule ? menuBreadcrumbs : HOME_BREADCRUMBS" body-variant="hub">
            <!-- Grid View -->
            <div class="grid grid-cols-3 gap-x-4 gap-y-8 md:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6">
                <component
                    v-for="item in currentMenuItems"
                    :key="`grid-${item.key}`"
                    :is="item.href ? Link : 'button'"
                    v-bind="item.href ? { href: item.href } : { type: 'button' }"
                    class="block cursor-pointer text-left"
                    @click="!item.href ? handleSelect(item) : undefined"
                >
                    <div class="group flex flex-col items-center gap-3 transition hover:-translate-y-1">
                        <div
                            class="relative flex h-16 w-16 items-center justify-center overflow-hidden rounded-2xl border border-border bg-white shadow-sm transition group-hover:shadow-md md:h-24 md:w-24"
                        >
                            <img
                                v-if="item.iconImage && !brokenIconKeys[item.key]"
                                :src="media + item.iconImage"
                                :alt="`${item.title} icon`"
                                class="h-10 w-10 object-contain md:h-14 md:w-14"
                                @error="markBrokenIcon(item.key)"
                            />
                            <ShieldCheck v-else class="h-10 w-10 text-primary md:h-12 md:w-12" />
                        </div>
                        <p
                            class="line-clamp-2 min-h-[2.5em] w-full max-w-[110px] text-center text-[12px] leading-tight text-foreground group-hover:text-primary md:min-h-[2.8em] md:max-w-[140px] md:text-[13px]"
                        >
                            {{ item.title }}
                        </p>
                    </div>
                </component>
            </div>
        </ModuleContentShell>
    </div>
</template>
