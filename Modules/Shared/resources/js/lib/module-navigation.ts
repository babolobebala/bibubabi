export interface ModuleNavigationBreadcrumbItem {
    label: string;
    href?: string;
}

export interface ModuleNavigationMenuItem {
    key: string;
    title: string;
    href: string;
    description?: string;
    componentKey?: string;
}

export interface ModuleNavigationHubConfig {
    sectionTitle?: string;
    breadcrumbs?: ModuleNavigationBreadcrumbItem[];
    items?: ModuleNavigationMenuItem[];
}

export interface ModuleNavigationPageConfig {
    breadcrumbs?: ModuleNavigationBreadcrumbItem[];
}

export interface ModuleNavigationConfig {
    module: {
        key: string;
        name: string;
        coreMenu?: ModuleNavigationMenuItem;
    };
    hubs?: Record<string, ModuleNavigationHubConfig>;
    pages?: Record<string, ModuleNavigationPageConfig>;
}

export function getModuleHubConfig(config: ModuleNavigationConfig, hubKey = 'index'): ModuleNavigationHubConfig {
    return config.hubs?.[hubKey] ?? {};
}

export function getModuleHubItems(config: ModuleNavigationConfig, hubKey = 'index'): ModuleNavigationMenuItem[] {
    return getModuleHubConfig(config, hubKey).items ?? [];
}

export function getModuleHubBreadcrumbs(config: ModuleNavigationConfig, hubKey = 'index'): ModuleNavigationBreadcrumbItem[] {
    return getModuleHubConfig(config, hubKey).breadcrumbs ?? [];
}

export function getModulePageBreadcrumbs(config: ModuleNavigationConfig, pageKey: string): ModuleNavigationBreadcrumbItem[] {
    return config.pages?.[pageKey]?.breadcrumbs ?? [];
}

export function getModuleCoreMenu(config: ModuleNavigationConfig): ModuleNavigationMenuItem | null {
    return config.module.coreMenu ?? null;
}
