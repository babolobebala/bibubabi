import { usePage } from '@inertiajs/vue3';
import { defineAsyncComponent, defineComponent, type Component } from 'vue';

type ModuleStatuses = Record<string, boolean>;

const EmptyComponent = defineComponent({
    name: 'EmptyComponent',
    render: () => null,
});

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

export function loadModuleComponent(
    moduleName: string,
    loader: () => Promise<{ default: Component }>,
): Component {
    const statuses = (usePage().props.modules?.statuses ?? {}) as ModuleStatuses;

    if (!statuses[moduleName]) {
        return EmptyComponent;
    }

    return defineAsyncComponent(loader);
}
