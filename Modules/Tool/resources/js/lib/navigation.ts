import {
    getModuleCoreMenu,
    getModuleHubBreadcrumbs,
    getModuleHubConfig,
    getModuleHubItems,
    getModulePageBreadcrumbs,
    type ModuleNavigationConfig,
} from '../../../../Shared/resources/js/lib/module-navigation';
import moduleNavigation from '../config/module-navigation.json';

const toolNavigation = moduleNavigation as ModuleNavigationConfig;

export function getToolNavigation() {
    return toolNavigation;
}

export function getToolCoreMenu() {
    return getModuleCoreMenu(toolNavigation);
}

export function getToolHubConfig(hubKey = 'index') {
    return getModuleHubConfig(toolNavigation, hubKey);
}

export function getToolHubBreadcrumbs(hubKey = 'index') {
    return getModuleHubBreadcrumbs(toolNavigation, hubKey);
}

export function getToolHubItems(hubKey = 'index') {
    return getModuleHubItems(toolNavigation, hubKey);
}

export function getToolPageBreadcrumbs(pageKey: string) {
    return getModulePageBreadcrumbs(toolNavigation, pageKey);
}
