import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, Fragment, h } from 'vue';
import ScrollToTopButton from '@/components/common/ScrollToTopButton.vue';
import { autoSubscribePushForAuthenticatedUser } from '@/lib/push-auto-subscribe';
import { markInstalled, setDeferredInstallPrompt, type BeforeInstallPromptEvent } from '@/lib/pwa-install';
import { initializeTheme } from '@/lib/theme';
import '../css/app.css';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
initializeTheme('light');

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePage(name),
    setup({ el, App, props, plugin }) {
        createApp({
            render: () => h(Fragment, [h(App, props), h(ScrollToTopButton)]),
        })
            .use(plugin)
            .mount(el);

        void autoSubscribePushForAuthenticatedUser(props.initialPage.props);
    },
    progress: {
        color: '#4B5563',
    },
});

router.on('success', (event) => {
    void autoSubscribePushForAuthenticatedUser(event.detail.page.props);
});

window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    setDeferredInstallPrompt(event as BeforeInstallPromptEvent);
});

window.addEventListener('appinstalled', () => {
    markInstalled();
});

function resolvePage(name: string) {
    const appPages = import.meta.glob<DefineComponent>('./pages/**/*.vue');
    const modulePages = import.meta.glob<DefineComponent>('../../Modules/**/resources/js/pages/**/*.vue');

    if (!name.includes('::')) {
        return resolvePageComponent<DefineComponent>(`./pages/${name}.vue`, appPages);
    }

    return resolveModulePage(name, modulePages);
}

function resolveModulePage(name: string, modulePages: Record<string, () => Promise<DefineComponent>>) {
    const [moduleAlias, pageName] = name.split('::');
    const moduleAliasLower = moduleAlias.toLowerCase();
    const pageFile = `${pageName}.vue`;

    const match = Object.keys(modulePages).find((path) => {
        const normalized = path.replace(/\\/g, '/');
        const parts = normalized.split('/');
        const modulesIndex = parts.lastIndexOf('Modules');
        if (modulesIndex === -1 || modulesIndex + 1 >= parts.length) {
            return false;
        }

        const moduleName = parts[modulesIndex + 1];

        return moduleName?.toLowerCase() === moduleAliasLower && normalized.endsWith(`/resources/js/pages/${pageFile}`);
    });

    if (!match) {
        throw new Error(`Module page not found: ${name}`);
    }

    return resolvePageComponent<DefineComponent>(match, modulePages);
}
