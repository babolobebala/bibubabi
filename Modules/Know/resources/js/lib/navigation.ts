import {
    getModuleCoreMenu,
    getModuleHubBreadcrumbs,
    getModuleHubConfig,
    getModuleHubItems,
    getModulePageBreadcrumbs,
    type ModuleNavigationConfig,
} from '../../../../Shared/resources/js/lib/module-navigation';
import moduleNavigation from '../config/module-navigation.json';

const knowNavigation = moduleNavigation as ModuleNavigationConfig;

export function getKnowNavigation() {
    return knowNavigation;
}

export function getKnowCoreMenu() {
    return getModuleCoreMenu(knowNavigation);
}

export function getKnowHubConfig(hubKey = 'index') {
    return getModuleHubConfig(knowNavigation, hubKey);
}

export function getKnowHubBreadcrumbs(hubKey = 'index') {
    return getModuleHubBreadcrumbs(knowNavigation, hubKey);
}

export function getKnowHubItems(hubKey = 'index') {
    return getModuleHubItems(knowNavigation, hubKey);
}

export function getKnowPageBreadcrumbs(pageKey: string) {
    return getModulePageBreadcrumbs(knowNavigation, pageKey);
}
