import './bootstrap';
import '../css/app.css';

import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import axios from 'axios';

const appName = import.meta.env.VITE_APP_NAME || 'Sistema Académico FICCT';

// Función para actualizar el token CSRF
function updateCsrfToken() {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    }
}

// Configurar el token CSRF inicialmente
updateCsrfToken();

// Actualizar el token cuando cambie el meta tag
const observer = new MutationObserver(() => {
    updateCsrfToken();
});

const metaTag = document.head.querySelector('meta[name="csrf-token"]');
if (metaTag) {
    observer.observe(metaTag, { attributes: true, attributeFilter: ['content'] });
}

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./pages/${name}.jsx`, import.meta.glob('./pages/**/*.jsx')),
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(<App {...props} />);
        
        // Actualizar token CSRF cuando se reciben nuevas props
        if (props.page?.props?.csrf_token) {
            const metaTag = document.head.querySelector('meta[name="csrf-token"]');
            if (metaTag) {
                metaTag.setAttribute('content', props.page.props.csrf_token);
                updateCsrfToken();
            }
        }
    },
    progress: {
        color: '#3B82F6',
    },
});