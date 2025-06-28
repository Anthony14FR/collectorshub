/// <reference types="vite/client" />

declare module '@inertiajs/vue3' {
    export function createInertiaApp(options: any): void;
    export const Head: any;
    export const Link: any;
    export const router: any;
}

declare module 'laravel-vite-plugin/inertia-helpers' {
    import type { DefineComponent } from 'vue';
    export function resolvePageComponent(
        path: string,
        pages: Record<string, () => Promise<DefineComponent>>
    ): Promise<DefineComponent>;
}

declare module '../../vendor/tightenco/ziggy' {
    export const ZiggyVue: any;
}

interface ImportMetaEnv {
    readonly VITE_APP_NAME: string;
    readonly VITE_APP_ENV: string;
    readonly VITE_APP_DEBUG: string;
    readonly VITE_APP_URL: string;
}

interface ImportMeta {
    readonly env: ImportMetaEnv;
    readonly glob: <T = any>(pattern: string) => Record<string, () => Promise<T>>;
}

declare module '*.vue' {
    import type { DefineComponent } from 'vue';
    const component: DefineComponent<{}, {}, any>;
    export default component;
} 