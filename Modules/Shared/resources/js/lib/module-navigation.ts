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
    iconImage?: string;
}

export interface ModuleNavigationPageItem extends ModuleNavigationMenuItem {
    level: number;
    parentKey?: string;
    roles?: string[];
}

export interface ModuleNavigationHubConfig {
    sectionTitle?: string;
    breadcrumbs?: ModuleNavigationBreadcrumbItem[];
    items?: ModuleNavigationMenuItem[];
}

export interface ModuleNavigationConfig {
    module: {
        key: string;
        name: string;
        title?: string;
        anchor?: string;
        description?: string;
        iconImage?: string;
        roles?: string[];
    };
    pages?: ModuleNavigationPageItem[];
}

export function getModuleTitle(config: ModuleNavigationConfig): string {
    return config.module.title ?? config.module.name;
}

export function getModuleAnchor(config: ModuleNavigationConfig): string | null {
    return config.module.anchor?.trim() ? config.module.anchor.trim() : null;
}

export function getModulePages(config: ModuleNavigationConfig): ModuleNavigationPageItem[] {
    return config.pages ?? [];
}

export function getModulePagesByLevel(config: ModuleNavigationConfig, level: number): ModuleNavigationPageItem[] {
    return getModulePages(config).filter((item) => item.level === level);
}

export function getModulePageByKey(config: ModuleNavigationConfig, pageKey: string): ModuleNavigationPageItem | null {
    return getModulePages(config).find((item) => item.key === pageKey) ?? null;
}

export function getModuleCoreMenu(config: ModuleNavigationConfig): ModuleNavigationMenuItem | null {
    const anchor = getModuleAnchor(config);

    if (!anchor) {
        return null;
    }

    return {
        key: anchor,
        title: getModuleTitle(config),
        href: `/app#${anchor}`,
        description: config.module.description,
        iconImage: config.module.iconImage,
    };
}

// eslint-disable-next-line @typescript-eslint/no-unused-vars
export function getModuleHubConfig(config: ModuleNavigationConfig, _hubKey = 'index'): ModuleNavigationHubConfig {
    return {
        sectionTitle: getModuleTitle(config),
        breadcrumbs: getModuleHubBreadcrumbs(config),
        items: getModuleHubItems(config),
    };
}

// eslint-disable-next-line @typescript-eslint/no-unused-vars
export function getModuleHubItems(config: ModuleNavigationConfig, _hubKey = 'index'): ModuleNavigationPageItem[] {
    return getModulePagesByLevel(config, 2);
}

// eslint-disable-next-line @typescript-eslint/no-unused-vars
export function getModuleHubBreadcrumbs(config: ModuleNavigationConfig, _hubKey = 'index'): ModuleNavigationBreadcrumbItem[] {
    if (!getModuleAnchor(config)) {
        return [{ label: 'Home' }];
    }

    return [
        { label: 'Home', href: '/app' },
        { label: getModuleTitle(config) },
    ];
}

export function getModulePageBreadcrumbs(config: ModuleNavigationConfig, pageKey: string): ModuleNavigationBreadcrumbItem[] {
    const page = getModulePageByKey(config, pageKey);
    const anchor = getModuleAnchor(config);

    if (!page) {
        return [{ label: 'Home', href: '/app' }, { label: getModuleTitle(config) }];
    }

    const breadcrumbs: ModuleNavigationBreadcrumbItem[] = [{ label: 'Home', href: '/app' }];

    if (anchor) {
        breadcrumbs.push({ label: getModuleTitle(config), href: `/app#${anchor}` });
    } else {
        breadcrumbs.push({ label: getModuleTitle(config) });
    }

    breadcrumbs.push({ label: page.title });

    return breadcrumbs;
}

export function hasRoleAccess(roles: string[] | undefined, userRoles: string[]): boolean {
    if (!roles || roles.length === 0) {
        return true;
    }

    return roles.some((role) => userRoles.includes(role));
}

export function filterPagesByRoles(
    pages: ModuleNavigationPageItem[],
    userRoles: string[],
): ModuleNavigationPageItem[] {
    return pages.filter((page) => hasRoleAccess(page.roles, userRoles));
}
