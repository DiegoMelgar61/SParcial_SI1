document.addEventListener('DOMContentLoaded', () => {

    // --- MÓDULO 1: LÓGICA DEL SIDEBAR (Menú lateral) ---
    const sidebar = document.getElementById('admin-sidebar');
    const toggleButton = document.getElementById('menu-toggle');
    const overlay = document.getElementById('sidebar-overlay');

    if (toggleButton) {
        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
    }

    // --- MÓDULO 2: LÓGICA DEL PANEL DE USUARIO (Avatar) ---
    const userAvatar = document.getElementById('user-avatar');
    const userAside = document.getElementById('user-aside');

    if (userAvatar) {
        userAvatar.addEventListener('click', (e) => {
            e.stopPropagation();
            if (userAside.classList.contains('opacity-0')) {
                userAside.classList.remove('hidden');
                setTimeout(() => {
                    userAside.classList.remove('opacity-0', 'scale-95');
                    userAside.classList.add('opacity-100', 'scale-100');
                }, 10);
            } else {
                userAside.classList.add('opacity-0', 'scale-95');
                userAside.classList.remove('opacity-100', 'scale-100');
                setTimeout(() => {
                    userAside.classList.add('hidden');
                }, 300);
            }
        });
    }
    
    // Ocultar panel de usuario si se hace clic fuera
    document.addEventListener('click', (e) => {
        if (userAside && !userAside.contains(e.target) && !userAvatar.contains(e.target) && !userAside.classList.contains('opacity-0')) {
            userAside.classList.add('opacity-0', 'scale-95');
            userAside.classList.remove('opacity-100', 'scale-100');
            setTimeout(() => {
                userAside.classList.add('hidden');
            }, 300);
        }
    });


    // --- MÓDULO 3: RELOJ EN TIEMPO REAL ---
    const clockElement = document.getElementById('clock');
    if (clockElement) {
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
            clockElement.textContent = now.toLocaleDateString('es-ES', options);
        };
        setInterval(updateClock, 1000);
        updateClock(); // Carga inicial
    }

    // --- MÓDULO 4: BOTÓN DE ACTUALIZAR ---
    const refreshButton = document.getElementById('refresh-btn');
    if (refreshButton) {
        refreshButton.addEventListener('click', () => {
            location.reload(); // Simplemente recarga la página
        });
    }

    // --- MÓDULO 5: FILTRADO DE TABLA DE BITÁCORA ---
    const filterStatus = document.getElementById('filter-status');
    const filterAction = document.getElementById('filter-action');
    const filterUser = document.getElementById('filter-user');
    const tableBody = document.getElementById('bitacora-table-body');
    const allRows = tableBody.querySelectorAll('tr.log-row');
    const noRecordsRow = document.getElementById('no-records');
    const totalRecordsDisplay = document.getElementById('total-records');

    function applyFilters() {
        const statusValue = filterStatus.value.toUpperCase();
        const actionValue = filterAction.value.toLowerCase();
        const userValue = filterUser.value.toLowerCase();
        
        let visibleRows = 0;

        allRows.forEach(row => {
            const statusCell = row.querySelector('.status-cell span');
            const actionCell = row.querySelector('.action-cell');
            const userCell = row.querySelector('.user-cell');
            
            // 1. Comprobar estado
            let statusMatch = true;
            if (statusValue) {
                // Comparamos el valor del filtro (ej: "SUCCESS") con el texto del badge (ej: "Éxito")
                // Esto es un poco frágil, sería mejor si el badge tuviera un data-status="SUCCESS"
                // Pero para "Éxito" y "Error" funciona
                if (statusValue === 'SUCCESS') {
                    statusMatch = statusCell.textContent.trim() === 'Éxito';
                } else if (statusValue === 'ERROR') {
                    statusMatch = statusCell.textContent.trim() === 'Error';
                }
            }
            
            // 2. Comprobar acción
            const actionMatch = actionCell.textContent.toLowerCase().includes(actionValue);
            
            // 3. Comprobar usuario
            const userMatch = userCell.textContent.toLowerCase().includes(userValue);

            // Mostrar u ocultar la fila
            if (statusMatch && actionMatch && userMatch) {
                row.style.display = ''; // Mostrar fila
                visibleRows++;
            } else {
                row.style.display = 'none'; // Ocultar fila
            }
        });

        // Mostrar u ocultar el mensaje "No se encontraron registros"
        if (noRecordsRow) {
            noRecordsRow.style.display = (visibleRows === 0) ? '' : 'none';
        }

        // Actualizar el contador de registros
        if (totalRecordsDisplay) {
            if (visibleRows === allRows.length && !statusValue && !actionValue && !userValue) {
                // Si no hay filtros, mostrar el mensaje original
                totalRecordsDisplay.textContent = `Mostrando ${allRows.length} de los últimos 30 registros.`;
            } else {
                totalRecordsDisplay.textContent = `Mostrando ${visibleRows} registros encontrados.`;
            }
        }
    }

    // Añadir listeners a los filtros
    if (filterStatus) filterStatus.addEventListener('change', applyFilters);
    if (filterAction) filterAction.addEventListener('keyup', applyFilters);
    if (filterUser) filterUser.addEventListener('keyup', applyFilters);

});
