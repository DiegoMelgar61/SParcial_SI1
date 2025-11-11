/**
 * admin_gestiones.js
 * CRUD de Gestiones Académicas
 */

document.addEventListener("DOMContentLoaded", () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

    // ========================================
    // MÓDULO 1: Panel de Usuario (Avatar)
    // ========================================
    const userAvatar = document.getElementById("user-avatar");
    const userAside = document.getElementById("user-aside");
    const menuToggle = document.getElementById("menu-toggle");
    const sidebar = document.getElementById("admin-sidebar");
    const sidebarOverlay = document.getElementById("sidebar-overlay");

    if (userAvatar && userAside) {
        userAvatar.addEventListener("click", (e) => {
            e.stopPropagation();
            toggleUserPanel();
        });

        document.addEventListener("click", (e) => {
            if (!userAside.contains(e.target) && !userAvatar.contains(e.target)) {
                closeUserPanel();
            }
        });
    }

    if (menuToggle && sidebar && sidebarOverlay) {
        menuToggle.addEventListener("click", () => {
            sidebar.classList.toggle("-translate-x-full");
            sidebarOverlay.classList.toggle("hidden");
        });

        sidebarOverlay.addEventListener("click", () => {
            sidebar.classList.add("-translate-x-full");
            sidebarOverlay.classList.add("hidden");
        });
    }

    function toggleUserPanel() {
        if (userAside.classList.contains("opacity-0")) {
            userAside.classList.remove("hidden");
            setTimeout(() => {
                userAside.classList.remove("opacity-0", "scale-95");
                userAside.classList.add("opacity-100", "scale-100");
            }, 10);
        } else {
            closeUserPanel();
        }
    }

    function closeUserPanel() {
        userAside.classList.add("opacity-0", "scale-95");
        userAside.classList.remove("opacity-100", "scale-100");
        setTimeout(() => {
            userAside.classList.add("hidden");
        }, 300);
    }

    // ========================================
    // MÓDULO 2: Gestión de Gestiones
    // ========================================
    const modalGestion = document.getElementById("modal-gestion");
    const modalTitle = document.getElementById("modal-title");
    const formGestion = document.getElementById("form-gestion");
    const btnCrearGestion = document.getElementById("btn-crear-gestion");
    const btnCancelarModal = document.getElementById("btn-cancelar-modal");
    const btnGuardarGestion = document.getElementById("btn-guardar-gestion");

    const inputGestionId = document.getElementById("gestion-id");
    const inputSemestre = document.getElementById("input-semestre");
    const inputAño = document.getElementById("input-año");
    const inputFechaInicio = document.getElementById("input-fecha-inicio");
    const inputFechaFin = document.getElementById("input-fecha-fin");
    const previewNombre = document.getElementById("preview-nombre");

    let modoEdicion = false;

    // Llenar selector de años (año anterior, actual y siguiente)
    const añoActual = new Date().getFullYear();
    const años = [añoActual - 1, añoActual, añoActual + 1];
    años.forEach(año => {
        const option = document.createElement("option");
        option.value = año;
        option.textContent = año;
        inputAño.appendChild(option);
    });

    // Actualizar preview del nombre cuando cambian semestre o año
    function actualizarPreview() {
        const semestre = inputSemestre.value;
        const año = inputAño.value;

        if (semestre && año) {
            previewNombre.textContent = `${semestre}-${año}`;
        } else {
            previewNombre.textContent = "-";
        }
    }

    inputSemestre.addEventListener("change", actualizarPreview);
    inputAño.addEventListener("change", actualizarPreview);

    // Cargar gestiones al iniciar
    cargarGestiones();

    // Abrir modal para crear
    btnCrearGestion.addEventListener("click", () => {
        modoEdicion = false;
        modalTitle.textContent = "Nueva Gestión";
        formGestion.reset();
        inputGestionId.value = "";
        previewNombre.textContent = "-";
        abrirModal();
    });

    // Cerrar modal
    btnCancelarModal.addEventListener("click", cerrarModal);
    modalGestion.addEventListener("click", (e) => {
        if (e.target === modalGestion) {
            cerrarModal();
        }
    });

    // Guardar gestión (crear o editar)
    formGestion.addEventListener("submit", async (e) => {
        e.preventDefault();

        const semestre = inputSemestre.value;
        const año = inputAño.value;
        const nombre = `${semestre}-${año}`;
        const fecha_inicio = inputFechaInicio.value;
        const fecha_fin = inputFechaFin.value;

        // Validar fechas
        if (new Date(fecha_inicio) >= new Date(fecha_fin)) {
            mostrarNotificacion("La fecha de inicio debe ser anterior a la fecha de fin", "error");
            return;
        }

        const datos = {
            nombre,
            fecha_inicio,
            fecha_fin
        };

        if (modoEdicion) {
            await actualizarGestion(inputGestionId.value, datos);
        } else {
            await crearGestion(datos);
        }
    });

    // ========================================
    // FUNCIONES CRUD
    // ========================================

    async function cargarGestiones() {
        try {
            const response = await fetch("/admin/gestiones/list", {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                }
            });

            if (!response.ok) throw new Error("Error al cargar gestiones");

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || "Error al cargar gestiones");
            }

            renderizarGestiones(data.gestiones || []);

        } catch (error) {
            console.error("Error:", error);
            mostrarNotificacion("Error al cargar las gestiones", "error");
        }
    }

    function renderizarGestiones(gestiones) {
        const tbody = document.getElementById("tbody-gestiones");
        tbody.innerHTML = "";

        if (gestiones.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p>No hay gestiones registradas</p>
                    </td>
                </tr>
            `;
            return;
        }

        gestiones.forEach(gestion => {
            const tr = document.createElement("tr");
            tr.className = "hover:bg-gray-50 transition-colors";

            const fechaInicio = new Date(gestion.fecha_inicio + 'T00:00:00').toLocaleDateString('es-ES');
            const fechaFin = new Date(gestion.fecha_fin + 'T00:00:00').toLocaleDateString('es-ES');

            tr.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${gestion.id}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm font-semibold text-indigo-700 bg-indigo-50 px-3 py-1 rounded-full">
                        ${gestion.nombre}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${fechaInicio}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${fechaFin}</td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                    <button class="btn-editar text-blue-600 hover:text-blue-900 mr-3 transition-colors"
                            data-id="${gestion.id}"
                            data-nombre="${gestion.nombre}"
                            data-fecha-inicio="${gestion.fecha_inicio}"
                            data-fecha-fin="${gestion.fecha_fin}">
                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <button class="btn-eliminar text-red-600 hover:text-red-900 transition-colors"
                            data-id="${gestion.id}"
                            data-nombre="${gestion.nombre}">
                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </td>
            `;

            tbody.appendChild(tr);
        });

        // Asignar event listeners
        asignarEventListeners();
    }

    function asignarEventListeners() {
        // Botones editar
        document.querySelectorAll(".btn-editar").forEach(btn => {
            btn.addEventListener("click", function() {
                const id = this.dataset.id;
                const nombre = this.dataset.nombre;
                const fechaInicio = this.dataset.fechaInicio;
                const fechaFin = this.dataset.fechaFin;

                abrirModalEdicion(id, nombre, fechaInicio, fechaFin);
            });
        });

        // Botones eliminar
        document.querySelectorAll(".btn-eliminar").forEach(btn => {
            btn.addEventListener("click", function() {
                const id = this.dataset.id;
                const nombre = this.dataset.nombre;

                confirmarEliminar(id, nombre);
            });
        });
    }

    async function crearGestion(datos) {
        try {
            btnGuardarGestion.disabled = true;
            btnGuardarGestion.textContent = "Guardando...";

            const response = await fetch("/admin/gestiones", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify(datos)
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || "Error al crear la gestión");
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || "Error al crear la gestión");
            }

            mostrarNotificacion("Gestión creada exitosamente", "success");
            cerrarModal();
            cargarGestiones();

        } catch (error) {
            console.error("Error:", error);
            mostrarNotificacion(error.message, "error");
        } finally {
            btnGuardarGestion.disabled = false;
            btnGuardarGestion.textContent = "Guardar Gestión";
        }
    }

    function abrirModalEdicion(id, nombre, fechaInicio, fechaFin) {
        modoEdicion = true;
        modalTitle.textContent = "Editar Gestión";

        const [semestre, año] = nombre.split("-");

        inputGestionId.value = id;
        inputSemestre.value = semestre;
        inputAño.value = año;
        inputFechaInicio.value = fechaInicio;
        inputFechaFin.value = fechaFin;

        actualizarPreview();
        abrirModal();
    }

    async function actualizarGestion(id, datos) {
        try {
            btnGuardarGestion.disabled = true;
            btnGuardarGestion.textContent = "Actualizando...";

            const response = await fetch(`/admin/gestiones/${id}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify(datos)
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || "Error al actualizar la gestión");
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || "Error al actualizar la gestión");
            }

            mostrarNotificacion("Gestión actualizada exitosamente", "success");
            cerrarModal();
            cargarGestiones();

        } catch (error) {
            console.error("Error:", error);
            mostrarNotificacion(error.message, "error");
        } finally {
            btnGuardarGestion.disabled = false;
            btnGuardarGestion.textContent = "Guardar Gestión";
        }
    }

    function confirmarEliminar(id, nombre) {
        if (confirm(`¿Está seguro de eliminar la gestión "${nombre}"?\n\nEsta acción no se puede deshacer.`)) {
            eliminarGestion(id);
        }
    }

    async function eliminarGestion(id) {
        try {
            const response = await fetch(`/admin/gestiones/${id}`, {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                }
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || "Error al eliminar la gestión");
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || "Error al eliminar la gestión");
            }

            mostrarNotificacion("Gestión eliminada exitosamente", "success");
            cargarGestiones();

        } catch (error) {
            console.error("Error:", error);
            mostrarNotificacion(error.message, "error");
        }
    }

    // ========================================
    // FUNCIONES AUXILIARES
    // ========================================

    function abrirModal() {
        modalGestion.classList.remove("hidden");
    }

    function cerrarModal() {
        modalGestion.classList.add("hidden");
        formGestion.reset();
        previewNombre.textContent = "-";
    }

    function mostrarNotificacion(mensaje, tipo = "info") {
        // Crear elemento de notificación
        const notif = document.createElement("div");
        notif.className = `fixed top-4 right-4 z-[60] px-6 py-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-0 ${
            tipo === "success" ? "bg-green-500 text-white" :
            tipo === "error" ? "bg-red-500 text-white" :
            "bg-blue-500 text-white"
        }`;
        notif.innerHTML = `
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${tipo === "success" ? 
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>' :
                        tipo === "error" ?
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>' :
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                    }
                </svg>
                <span class="font-medium">${mensaje}</span>
            </div>
        `;

        document.body.appendChild(notif);

        // Remover después de 3 segundos
        setTimeout(() => {
            notif.style.opacity = "0";
            notif.style.transform = "translateX(100%)";
            setTimeout(() => {
                notif.remove();
            }, 300);
        }, 3000);
    }

});
