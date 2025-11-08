document.addEventListener('DOMContentLoaded', () => {

    // Helper para obtener el token CSRF de Laravel
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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

    // --- MÓDULO 4: FILTRADO DE TABLA DE USUARIOS ---
    const filterNombre = document.getElementById('filter-nombre');
    const filterCiCodigo = document.getElementById('filter-ci-codigo');
    const filterRol = document.getElementById('filter-rol');
    const tableBody = document.getElementById('usuarios-table-body');
    const allRows = tableBody.querySelectorAll('tr.user-row');
    const noRecordsRow = document.getElementById('no-records');
    const totalRecordsDisplay = document.getElementById('total-records');

    function applyUserFilters() {
        const nombreValue = filterNombre.value.toLowerCase();
        const ciCodigoValue = filterCiCodigo.value.toLowerCase();
        const rolValue = filterRol.value;
        
        let visibleRows = 0;

        allRows.forEach(row => {
            const nombreCell = row.querySelector('.nombre-cell').textContent.toLowerCase();
            const ciCell = row.querySelector('.ci-cell').textContent.toLowerCase();
            const codigoCell = row.querySelector('.codigo-cell').textContent.toLowerCase();
            const rolCell = row.querySelector('.rol-cell span').textContent.trim();
            
            const nombreMatch = nombreCell.includes(nombreValue);
            const ciCodigoMatch = ciCell.includes(ciCodigoValue) || codigoCell.includes(ciCodigoValue);
            const rolMatch = (rolValue === "") ? true : rolCell === rolValue;

            if (nombreMatch && ciCodigoMatch && rolMatch) {
                row.style.display = ''; // Mostrar fila
                visibleRows++;
            } else {
                row.style.display = 'none'; // Ocultar fila
            }
        });

        if (noRecordsRow) {
            // Maneja el caso de que no haya registros desde el inicio
            if (allRows.length > 0) {
                noRecordsRow.style.display = (visibleRows === 0) ? '' : 'none';
            }
        }

        if (totalRecordsDisplay) {
            if (visibleRows === allRows.length && !nombreValue && !ciCodigoValue && !rolValue) {
                totalRecordsDisplay.textContent = `Mostrando ${allRows.length} registros.`;
            } else {
                totalRecordsDisplay.textContent = `Mostrando ${visibleRows} de ${allRows.length} registros encontrados.`;
            }
        }
    }

    if (filterNombre) filterNombre.addEventListener('keyup', applyUserFilters);
    if (filterCiCodigo) filterCiCodigo.addEventListener('keyup', applyUserFilters);
    if (filterRol) filterRol.addEventListener('change', applyUserFilters);


    // --- MÓDULO 5: LÓGICA DE MODALES CRUD (AGREGAR, EDITAR, ELIMINAR) ---

    // --- A. Lógica de Agregar/Editar ---
    const userFormModal = document.getElementById('user-form-modal');
    const userForm = document.getElementById('user-form');
    const btnCancelForm = document.getElementById('btn-cancel-form');
    const btnCancelFormX = document.getElementById('btn-cancel-form-x'); // Botón 'X'
    const formModalTitle = document.getElementById('form-modal-title');
    const btnSaveForm = document.getElementById('btn-save-form');
    const hiddenUserId = document.getElementById('form-user-id');
    const passwordHelpText = document.getElementById('password-help-text');
    const passwordInput = document.getElementById('form-password');

    // Botón "Agregar Usuario" (Header)
    const btnAddUser = document.getElementById('btn-add-user');
    if (btnAddUser) {
        btnAddUser.addEventListener('click', () => {
            userForm.reset(); // Limpia el formulario
            formModalTitle.textContent = 'Agregar Nuevo Usuario';
            hiddenUserId.value = ''; // Asegura que no haya ID (modo "crear")
            passwordHelpText.classList.add('hidden'); // Oculta el texto de ayuda
            passwordInput.required = true; // La contraseña es obligatoria al crear
            userFormModal.classList.remove('hidden');
        });
    }

    // Botones "Editar" (en cada fila de la tabla)
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', (e) => {
            userForm.reset();
            formModalTitle.textContent = 'Editar Usuario';
            passwordHelpText.classList.remove('hidden'); // Muestra "Dejar en blanco..."
            passwordInput.required = false; // La contraseña es opcional al editar
            
            // Obtiene los datos de los atributos data-* del botón
            const data = e.currentTarget.dataset;
            
            // Rellena el formulario
            hiddenUserId.value = data.id; // ID oculto
            document.getElementById('form-ci').value = data.ci;
            document.getElementById('form-nomb_comp').value = data.nombre;
            document.getElementById('form-correo').value = data.correo;
            document.getElementById('form-tel').value = data.tel;
            document.getElementById('form-rol').value = data.rol;
            // NOTA: fecha_nacimiento y profesion no están en tus data-*, así que quedarán en blanco
            // Deberás añadirlos a la consulta SQL y a los atributos data-* si quieres pre-cargarlos.
            
            userFormModal.classList.remove('hidden');
        });
    });

    // Botones "Cancelar" del formulario (ambos)
    function closeFormModal() {
        userFormModal.classList.add('hidden');
    }
    if (btnCancelForm) btnCancelForm.addEventListener('click', closeFormModal);
    if (btnCancelFormX) btnCancelFormX.addEventListener('click', closeFormModal);


    // Envío del formulario (SUBMIT)
    if (userForm) {
        userForm.addEventListener('submit', async (e) => {
            e.preventDefault(); // Previene el envío normal

            btnSaveForm.disabled = true;
            btnSaveForm.textContent = 'Guardando...';

            const formData = new FormData(userForm);
            const data = Object.fromEntries(formData.entries());
            console.log(data);
            const userId = hiddenUserId.value;
            const isEditing = userId !== '';

            // Define el endpoint al que se enviarán los datos
            const url = isEditing ? `/admin/users/update` : `/admin/users/store`;
            
            if (isEditing) {
                data.id = userId; // Asegúrate de que el ID vaya en el body para la actualización
            }

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken // Token CSRF de Laravel
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json(); // Intenta leer la respuesta siempre

                if (response.ok) {
                    alert(result.message || (isEditing ? 'Usuario actualizado con éxito.' : 'Usuario creado con éxito.'));
                    window.location.reload(); // Recarga la página para mostrar los cambios
                } else {
                    // Manejo de errores (ej. validación)
                    let errorMessage = 'Error al guardar. ';
                    if (result.errors) {
                        errorMessage += Object.values(result.errors).join(' ');
                    } else {
                        errorMessage += result.message || 'Inténtalo de nuevo.';
                    }
                    alert(errorMessage);
                }

            } catch (error) {
                console.error('Error de red:', error);
                alert('Error de conexión. No se pudo guardar el usuario.');
            } finally {
                btnSaveForm.disabled = false;
                btnSaveForm.textContent = 'Guardar';
            }
        });
    }


    // --- B. Lógica de Eliminación (con Modal y Fetch) ---
    const deleteModal = document.getElementById('delete-modal');
    const btnCancelDelete = document.getElementById('btn-cancel-delete');
    const btnConfirmDelete = document.getElementById('btn-confirm-delete');
    const deleteUserName = document.getElementById('delete-user-name');
    let userIdToDelete = null;
    let rowToDelete = null;

    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', (e) => {
            userIdToDelete = e.currentTarget.dataset.id;
            const userName = e.currentTarget.dataset.nombre;
            rowToDelete = e.currentTarget.closest('tr.user-row');
            
            deleteUserName.textContent = userName;
            deleteModal.classList.remove('hidden');
        });
    });

    if (btnCancelDelete) {
        btnCancelDelete.addEventListener('click', () => {
            deleteModal.classList.add('hidden');
            userIdToDelete = null;
            rowToDelete = null;
        });
    }

    if (btnConfirmDelete) {
        btnConfirmDelete.addEventListener('click', async () => {
            if (!userIdToDelete) return;
            
            btnConfirmDelete.disabled = true;
            btnConfirmDelete.textContent = 'Eliminando...';

            try {
                // Usamos la ruta que me mostraste
                const response = await fetch('/admin/users/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken 
                    },
                    body: JSON.stringify({
                        id: userIdToDelete,
                    })
                });


                const result = await response.json(); 

                if (response.ok) {
                       
                    alert(result.message || 'Usuario eliminado con éxito.');
                    window.location.reload(); 
                } else {
                    alert(result.error || 'Error al eliminar el usuario. Inténtalo de nuevo.');
                }
        } 
        catch (error) {
                console.error('Error de red:', error);
                alert('Error de red al intentar eliminar el usuario.');
        } finally {
                deleteModal.classList.add('hidden');
                btnConfirmDelete.disabled = false;
                btnConfirmDelete.textContent = 'Sí, eliminar';
                userIdToDelete = null;
                rowToDelete = null;
            }
        });
    }

});
