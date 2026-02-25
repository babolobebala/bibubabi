import { usePage } from '@inertiajs/vue3';

type ModuleStatuses = Record<string, boolean>;

export function loadModuleValue<T>(
    moduleName: string,
    resolver: () => T,
    fallback: T,
): T {
    const statuses = (usePage().props.modules?.statuses ?? {}) as ModuleStatuses;

    if (!statuses[moduleName]) {
        return fallback;
    }

    return resolver();
}
