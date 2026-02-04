import { usePage } from '@inertiajs/vue3';
import { defineAsyncComponent, defineComponent } from 'vue';
import type { Component } from 'vue';

type ModuleStatuses = Record<string, boolean>;

const EmptyComponent = defineComponent({
    render: () => null,
});

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
