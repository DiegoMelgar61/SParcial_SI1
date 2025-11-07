// admin.js - Módulo de funcionalidades para el panel de administración

class AdminPanel {
    constructor() {
        this.sidebar = document.getElementById('admin-sidebar');
        this.overlay = document.getElementById('sidebar-overlay');
        this.menuToggle = document.getElementById('menu-toggle');
        this.clockElement = document.getElementById('clock');
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.startClock();
    }

    bindEvents() {
        // Toggle del sidebar en móviles
        this.menuToggle.addEventListener('click', () => this.toggleSidebar());
        
        // Cerrar sidebar al hacer clic en el overlay
        this.overlay.addEventListener('click', () => this.closeSidebar());
        
        // Cerrar sidebar al redimensionar la ventana (responsive)
        window.addEventListener('resize', () => this.handleResize());
    }

    toggleSidebar() {
        this.sidebar.classList.toggle('-translate-x-full');
        this.overlay.classList.toggle('hidden');
        
        // Prevenir scroll del body cuando el sidebar está abierto en móviles
        document.body.classList.toggle('overflow-hidden', !this.sidebar.classList.contains('-translate-x-full'));
    }

    closeSidebar() {
        this.sidebar.classList.add('-translate-x-full');
        this.overlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    handleResize() {
        // Cerrar sidebar automáticamente en desktop
        if (window.innerWidth >= 768) {
            this.sidebar.classList.remove('-translate-x-full');
            this.overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        } else {
            // Asegurar que esté cerrado en móviles al cargar
            this.sidebar.classList.add('-translate-x-full');
        }
    }

    startClock() {
        const updateClock = () => {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            
            if (this.clockElement) {
                this.clockElement.textContent = now.toLocaleDateString('es-ES', options);
            }
        };

        // Actualizar inmediatamente y luego cada segundo
        updateClock();
        setInterval(updateClock, 1000);
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    new AdminPanel();
});

// Funciones utilitarias adicionales
const AdminUtils = {
    // Formatear números grandes
    formatNumber: (number) => {
        return new Intl.NumberFormat('es-ES').format(number);
    },

    // Mostrar notificación toast
    showToast: (message, type = 'info') => {
        // Implementación básica de toast notifications
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-transform duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        // Auto-remover después de 3 segundos
        setTimeout(() => {
            toast.remove();
        }, 3000);
    },

    // Confirmación de acciones
    confirmAction: (message) => {
        return new Promise((resolve) => {
            // En una implementación real, usarías un modal personalizado
            const confirmed = window.confirm(message);
            resolve(confirmed);
        });
    }
};

// Exportar para uso global si es necesario
window.AdminPanel = AdminPanel;
window.AdminUtils = AdminUtils;