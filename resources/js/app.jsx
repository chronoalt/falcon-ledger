import './bootstrap';

import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import AppLayout from './Layouts/AppLayout.jsx';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

console.log("VITE DEV MODE ACTIVE");

createInertiaApp({
    resolve: async (name) => {
        const page = await resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob('./Pages/**/*.jsx')
        );

        page.default.layout = page.default.layout || ((page) => <AppLayout>{page}</AppLayout>);

        return page;
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
    progress: {
        color: '#2563eb',
        showSpinner: true,
    },
});
