import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Función para actualizar el token CSRF
function updateCsrfToken() {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    }
}

// Configurar CSRF token inicialmente
updateCsrfToken();

// Observar cambios en el meta tag CSRF
const metaTag = document.head.querySelector('meta[name="csrf-token"]');
if (metaTag) {
    const observer = new MutationObserver(() => {
        updateCsrfToken();
    });
    observer.observe(metaTag, { attributes: true, attributeFilter: ['content'] });
}
