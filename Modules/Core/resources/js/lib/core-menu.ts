import { onBeforeUnmount, onMounted, ref, type Ref } from 'vue';
import {
    getModuleCoreMenu,
    getModuleHubConfig,
    getModuleHubItems,
    type ModuleNavigationConfig,
    type ModuleNavigationMenuItem,
} from '../../../../Shared/resources/js/lib/module-navigation';
import { loadModuleValue } from './module-components';

export interface CoreModuleEntry {
    moduleName: string;
    menu: ModuleNavigationMenuItem;
    hubConfig: ReturnType<typeof getModuleHubConfig>;
    features: ModuleNavigationMenuItem[];
}

export function getCoreModuleEntries(): CoreModuleEntry[] {
    const moduleNavigationFiles = import.meta.glob('../../../../*/resources/js/config/module-navigation.json', {
        eager: true,
        import: 'default',
    }) as Record<string, ModuleNavigationConfig>;

    return Object.entries(moduleNavigationFiles)
        .map(([path, navigation]) => {
            const moduleName = extractModuleNameFromConfigPath(path);

            if (!moduleName) {
                return null;
            }

            return loadModuleValue<CoreModuleEntry | null>(
                moduleName,
                () => buildCoreModuleEntry(moduleName, navigation),
                null,
            );
        })
        .filter((item): item is CoreModuleEntry => item !== null);
}

export function useCoreMenuHashSection(moduleEntries: Ref<CoreModuleEntry[]>) {
    const activeModuleKey = ref<string | null>(null);

    const syncModuleFromHash = (): void => {
        if (typeof window === 'undefined') {
            return;
        }

        const hash = window.location.hash.replace(/^#/, '').trim().toLowerCase();

        if (!hash) {
            activeModuleKey.value = null;
            return;
        }

        const matchedModule = moduleEntries.value.find((item) => item.menu.key.toLowerCase() === hash);
        activeModuleKey.value = matchedModule?.menu.key ?? null;
    };

    const setHash = (value: string): void => {
        if (typeof window === 'undefined') {
            return;
        }

        const nextHash = `#${value}`;
        if (window.location.hash !== nextHash) {
            window.location.hash = value;
        }
    };

    const clearHash = (): void => {
        if (typeof window === 'undefined') {
            return;
        }

        if (!window.location.hash) {
            return;
        }

        window.history.replaceState(window.history.state, '', `${window.location.pathname}${window.location.search}`);
    };

    onMounted(() => {
        syncModuleFromHash();
        window.addEventListener('hashchange', syncModuleFromHash);
    });

    onBeforeUnmount(() => {
        window.removeEventListener('hashchange', syncModuleFromHash);
    });

    return {
        activeModuleKey,
        syncModuleFromHash,
        setHash,
        clearHash,
    };
}

function buildCoreModuleEntry(moduleName: string, navigation: ModuleNavigationConfig): CoreModuleEntry | null {
    const menu = getModuleCoreMenu(navigation);

    if (!menu) {
        return null;
    }

    return {
        moduleName,
        menu,
        hubConfig: getModuleHubConfig(navigation),
        features: getModuleHubItems(navigation),
    };
}

function extractModuleNameFromConfigPath(path: string): string | null {
    const match = path.match(/\.\.\/\.\.\/\.\.\/\.\.\/([^/]+)\/resources\/js\/config\/module-navigation\.json$/);

    return match?.[1] ?? null;
}
