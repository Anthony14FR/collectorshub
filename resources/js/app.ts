import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, DefineComponent, h } from 'vue';
import PrimeVue from 'primevue/config';
import VirtualScroller from 'primevue/virtualscroller';
import musicPlayerPlugin from './plugins/musicPlayer';
import { useMatomoTracking } from './composables/useMatomoTracking';

const appName: string = import.meta.env.VITE_APP_NAME || 'Collector\'s Hub';

createInertiaApp({
  title: (title: string) => title ? `${title} | ${appName}` : appName,
  resolve: (name: string) =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
    ),
  setup({ el, App, props, plugin }: any) {
    const app = createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(PrimeVue)
      .use(musicPlayerPlugin)
      .component('VirtualScroller', VirtualScroller);

    app.mount(el);
    
    setTimeout(() => {
      const { checkForFlashEvents } = useMatomoTracking();
      checkForFlashEvents();
    }, 100);

    return app;
  },
  progress: {
    color: '#4B5563',
  },
});