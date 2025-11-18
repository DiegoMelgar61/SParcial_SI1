/**
 * admin_carga_horaria.js
 * Gestión de carga horaria docente
 */

document.addEventListener("DOMContentLoaded", () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

    // ========================================
    // MÓDULO 0: Selector de Gestión
    // ========================================
    const selectGestion = document.getElementById("select-gestion-carga");
    const mensajeInicial = document.getElementById("mensaje-seleccionar-gestion");
    const contentWrapper = document.getElementById("carga-content-wrapper");

    if (selectGestion) {
        selectGestion.addEventListener("change", async (e) => {
            const gestionId = e.target.value;
            if (gestionId) {
                await cargarDocentesPorGestion(gestionId);
            } else {
                if (mensajeInicial) mensajeInicial.classList.remove("hidden");
                if (contentWrapper) contentWrapper.classList.add("hidden");
            }
        });
    }

    async function cargarDocentesPorGestion(gestionId) {
        try {
            // Mostrar loading
            if (mensajeInicial) {
                mensajeInicial.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin text-4xl text-blue-500"></i><p class="mt-4 text-gray-600">Cargando docentes...</p></div>';
                mensajeInicial.classList.remove("hidden");
            }
            if (contentWrapper) contentWrapper.classList.add("hidden");

            const response = await fetch(`/admin/carga-horaria/docentes/${gestionId}`, {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                }
            });

            if (!response.ok) throw new Error("Error al cargar docentes");

            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || "Error al cargar docentes");
            }

            const docentes = data.docentes || [];

            // Ocultar mensaje y mostrar contenido
            if (mensajeInicial) mensajeInicial.classList.add("hidden");
            if (contentWrapper) contentWrapper.classList.remove("hidden");

            // Actualizar estadísticas
            actualizarEstadisticas(docentes);

            // Actualizar tabla
            actualizarTablaDocentes(docentes);

        } catch (error) {
            console.error("Error:", error);
            if (mensajeInicial) {
                mensajeInicial.innerHTML = '<div class="text-center text-red-600"><i class="fas fa-exclamation-circle text-4xl"></i><p class="mt-4">Error al cargar los datos</p></div>';
                mensajeInicial.classList.remove("hidden");
            }
        }
    }

    function actualizarEstadisticas(docentes) {
        const totalDocentes = docentes.length;
        const totalHoras = docentes.reduce((sum, d) => sum + parseInt(d.carga_horaria_total || 0), 0);
        const promedioHoras = totalDocentes > 0 ? Math.round(totalHoras / totalDocentes) : 0;

        // Actualizar los valores en el DOM
        const statTotal = document.querySelector('[data-stat="total"]');
        const statHoras = document.querySelector('[data-stat="horas"]');
        const statPromedio = document.querySelector('[data-stat="promedio"]');

        if (statTotal) statTotal.textContent = totalDocentes;
        if (statHoras) statHoras.textContent = totalHoras;
        if (statPromedio) statPromedio.textContent = promedioHoras;
    }

    function actualizarTablaDocentes(docentes) {
        const grid = document.getElementById("grid-docentes");
        if (!grid) return;

        grid.innerHTML = "";

        if (docentes.length === 0) {
            grid.innerHTML = `
                <div class="col-span-full text-center py-12 text-gray-500">
                    <svg class="w-20 h-20 mx-auto text-gold-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-lg font-bold text-navy-900">No hay docentes registrados para esta gestión</p>
                </div>
            `;
            return;
        }

        docentes.forEach((docente, index) => {
            const card = document.createElement("div");

            // Alternar diseños entre navy y gold
            const isNavy = index % 2 === 0;

            card.className = `group bg-white border-4 ${isNavy ? 'border-navy-900' : 'border-gold-500'} shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 cursor-pointer overflow-hidden`;

            card.innerHTML = `
                <!-- Header del Card -->
                <div class="${isNavy ? 'bg-navy-900 border-b-4 border-gold-500' : 'bg-gold-500 border-b-4 border-navy-900'} p-6 text-center">
                    <div class="w-20 h-20 mx-auto ${isNavy ? 'bg-gold-500' : 'bg-navy-900'} flex items-center justify-center text-4xl mb-3 border-4 border-white font-black ${isNavy ? 'text-navy-900' : 'text-gold-500'}">
                        ${docente.nombre ? docente.nombre.charAt(0).toUpperCase() : '?'}
                    </div>
                    <span class="inline-block ${isNavy ? 'bg-gold-500 text-navy-900' : 'bg-navy-900 text-gold-500'} px-3 py-1 text-xs font-black uppercase tracking-wider">
                        Docente #${index + 1}
                    </span>
                </div>

                <!-- Contenido del Card -->
                <div class="p-6">
                    <!-- Nombre del docente -->
                    <h3 class="text-lg font-black text-navy-900 uppercase tracking-wide mb-2 text-center min-h-[3rem] flex items-center justify-center">
                        ${docente.nombre || 'Sin nombre'}
                    </h3>
                    <p class="text-sm text-center text-gray-600 mb-4 font-mono">
                        Código: <span class="font-bold text-navy-900">${docente.codigo}</span>
                    </p>

                    <!-- Estadísticas en mini-cards -->
                    <div class="grid grid-cols-2 gap-3 mb-5">
                        <div class="bg-slate-50 p-3 border-l-4 border-navy-900 text-center">
                            <p class="text-xs uppercase font-bold text-slate-600 mb-1">Materias</p>
                            <p class="text-2xl font-black text-navy-900">${docente.materias_count || 0}</p>
                        </div>
                        <div class="bg-slate-50 p-3 border-l-4 border-gold-500 text-center">
                            <p class="text-xs uppercase font-bold text-slate-600 mb-1">Horas</p>
                            <p class="text-2xl font-black text-gold-600">${docente.carga_horaria_total || 0}</p>
                        </div>
                    </div>

                    <!-- Botón Ver Detalle Mejorado -->
                    <button class="btn-ver-detalle w-full py-3 ${isNavy ? 'bg-navy-900 hover:bg-navy-800 border-navy-800' : 'bg-gold-500 hover:bg-gold-600 border-gold-600'} text-${isNavy ? 'white' : 'navy-900'} font-bold uppercase tracking-wide transition-all border-b-4 group-hover:border-${isNavy ? 'gold-500' : 'navy-900'} flex items-center justify-center gap-2"
                            data-codigo="${docente.codigo}"
                            data-nombre="${docente.nombre || ''}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span>Ver Detalle Completo</span>
                    </button>
                </div>
            `;

            grid.appendChild(card);
        });

        // Re-asignar event listeners a los nuevos botones
        asignarEventListenersDetalle();
    }

    // ========================================
    // MÓDULO 1: Panel de Usuario (Avatar)
    // ========================================
    const userAvatar = document.getElementById("user-avatar");
    const userAside = document.getElementById("user-aside");

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
    // MÓDULO 2: Modal Detalle Carga Horaria
    // ========================================
    const modalDetalle = document.getElementById("modal-detalle");
    const btnCerrarModal = document.getElementById("btn-cerrar-modal");

    // Cerrar modal
    if (btnCerrarModal) {
        btnCerrarModal.addEventListener("click", cerrarModal);
    }

    // Cerrar modal al hacer clic fuera
    if (modalDetalle) {
        modalDetalle.addEventListener("click", (e) => {
            if (e.target === modalDetalle) {
                cerrarModal();
            }
        });
    }

    function cerrarModal() {
        modalDetalle.classList.add("hidden");
    }

    function abrirModal() {
        modalDetalle.classList.remove("hidden");
    }

    // ========================================
    // MÓDULO 3: Ver Detalle de Docente
    // ========================================
    function asignarEventListenersDetalle() {
        const botonesVerDetalle = document.querySelectorAll(".btn-ver-detalle");

        botonesVerDetalle.forEach(btn => {
            btn.addEventListener("click", async function() {
                const codigo = this.getAttribute("data-codigo");
                const nombre = this.getAttribute("data-nombre");

                await cargarDetalleDocente(codigo, nombre);
            });
        });
    }

    // Asignar listeners iniciales (si hay docentes cargados desde el servidor)
    asignarEventListenersDetalle();

    async function cargarDetalleDocente(codigo, nombre) {
        try {
            // Mostrar loading
            document.getElementById("modal-docente-nombre").textContent = "Cargando...";
            document.getElementById("modal-docente-info").textContent = "";
            abrirModal();

            // Cargar detalle
            const response = await fetch(`/admin/carga-horaria/detalle/${codigo}`, {
                method: "GET",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest"
                }
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || "Error al cargar detalle");
            }

            // Actualizar header del modal
            document.getElementById("modal-docente-nombre").textContent = nombre;
            document.getElementById("modal-docente-info").textContent = 
                `CI: ${data.docente.ci} | ${data.docente.correo || 'Sin correo'}`;

            // Actualizar estadísticas
            document.getElementById("detalle-carga-total").textContent = `${data.carga_total} hrs`;
            document.getElementById("detalle-horas-semanales").textContent = `${data.horas_semanales} hrs`;
            document.getElementById("detalle-total-materias").textContent = data.total_materias;

            // Llenar tabla de materias
            renderizarTablaMaterias(data.materias);

            // Cargar horario semanal
            await cargarHorarioSemanal(codigo);

        } catch (error) {
            console.error("Error:", error);
            alert("Error al cargar detalle: " + error.message);
            cerrarModal();
        }
    }

    function renderizarTablaMaterias(materias) {
        const tbody = document.getElementById("tbody-materias-detalle");
        tbody.innerHTML = "";

        if (materias.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                        No tiene materias asignadas
                    </td>
                </tr>
            `;
            return;
        }

        materias.forEach((materia, idx) => {
            const tr = document.createElement("tr");
            tr.className = "border-b border-gray-200 hover:bg-slate-50 transition";

            tr.innerHTML = `
                <td class="px-4 py-3 font-mono text-xs font-bold text-navy-900">${materia.sigla}</td>
                <td class="px-4 py-3 font-medium text-gray-900">${materia.nombre}</td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-flex items-center px-3 py-1 text-xs font-bold uppercase bg-navy-900 text-gold-500 border-2 border-gold-500">
                        ${materia.sigla_grupo}
                    </span>
                </td>
                <td class="px-4 py-3 text-center font-bold text-navy-900">${materia.semestre}°</td>
                <td class="px-4 py-3 text-center font-bold text-gold-600">${materia.carga_horaria} hrs</td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-flex items-center px-3 py-1 text-xs font-bold bg-gold-500 text-navy-900 border-2 border-navy-900">
                        ${materia.total_clases}
                    </span>
                </td>
            `;

            tbody.appendChild(tr);
        });
    }

    // ========================================
    // MÓDULO 4: Horario Semanal
    // ========================================
    async function cargarHorarioSemanal(codigo) {
        try {
            const response = await fetch(`/admin/carga-horaria/horario/${codigo}`, {
                method: "GET",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest"
                }
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || "Error al cargar horario");
            }

            renderizarHorarioSemanal(data.horarios);

        } catch (error) {
            console.error("Error al cargar horario:", error);
            document.getElementById("contenedor-horario").innerHTML = `
                <p class="text-red-600 text-sm">Error al cargar horario: ${error.message}</p>
            `;
        }
    }

    function renderizarHorarioSemanal(horarios) {
        const contenedor = document.getElementById("contenedor-horario");

        if (horarios.length === 0) {
            contenedor.innerHTML = `
                <p class="text-gray-500 text-sm text-center py-4">No tiene horarios asignados</p>
            `;
            return;
        }

        // Agrupar horarios por día
        const dias = ["Lunes", "Martes", "Miércoles", "Miercoles", "Jueves", "Viernes", "Sábado", "Sabado"];
        const horariosPorDia = {};

        dias.forEach(dia => {
            horariosPorDia[dia] = horarios.filter(h => 
                h.dia.toLowerCase() === dia.toLowerCase()
            );
        });

        // Crear tabla de horario
        let html = `
            <table class="w-full text-sm border-collapse border-4 border-navy-900">
                <thead>
                    <tr class="bg-navy-900 text-white">
                        <th class="border-2 border-gold-500 px-3 py-3 text-left font-bold uppercase">Día</th>
                        <th class="border-2 border-gold-500 px-3 py-3 text-left font-bold uppercase">Horario</th>
                        <th class="border-2 border-gold-500 px-3 py-3 text-left font-bold uppercase">Materia</th>
                        <th class="border-2 border-gold-500 px-3 py-3 text-left font-bold uppercase">Grupo</th>
                        <th class="border-2 border-gold-500 px-3 py-3 text-left font-bold uppercase">Aula</th>
                    </tr>
                </thead>
                <tbody>
        `;

        dias.forEach(dia => {
            const horariosDelDia = horariosPorDia[dia] || [];

            if (horariosDelDia.length > 0) {
                horariosDelDia.forEach((horario, index) => {
                    html += `
                        <tr class="hover:bg-slate-50 transition">
                            ${index === 0 ? `<td class="border-2 border-gray-300 px-3 py-2 font-bold bg-gold-500 text-navy-900" rowspan="${horariosDelDia.length}">${dia}</td>` : ''}
                            <td class="border-2 border-gray-300 px-3 py-2 font-mono text-xs font-bold text-navy-900">
                                ${horario.hora_inicio} - ${horario.hora_fin}
                            </td>
                            <td class="border-2 border-gray-300 px-3 py-2">
                                <div class="font-bold text-navy-900">${horario.materia_nombre}</div>
                                <div class="text-xs text-gray-600 font-semibold">${horario.materia_sigla}</div>
                            </td>
                            <td class="border-2 border-gray-300 px-3 py-2 text-center">
                                <span class="inline-flex items-center px-3 py-1 text-xs font-bold uppercase bg-navy-900 text-gold-500 border-2 border-gold-500">
                                    ${horario.grupo}
                                </span>
                            </td>
                            <td class="border-2 border-gray-300 px-3 py-2 text-center">
                                <div class="font-bold text-gold-600 text-lg">${horario.aula}</div>
                                <div class="text-xs text-gray-600">${horario.modulo}</div>
                            </td>
                        </tr>
                    `;
                });
            }
        });

        html += `
                </tbody>
            </table>
        `;

        contenedor.innerHTML = html;
    }

    // ========================================
    // MÓDULO 5: Utilidades
    // ========================================
    
    /**
     * Normalizar nombre del día (manejar acentos)
     */
    function normalizarDia(dia) {
        const normalizacion = {
            'miércoles': 'Miércoles',
            'miercoles': 'Miércoles',
            'sábado': 'Sábado',
            'sabado': 'Sábado'
        };
        return normalizacion[dia.toLowerCase()] || dia;
    }

    console.log("✅ Admin Carga Horaria JS cargado correctamente");
});

// ========================================
// MÓDULO 6: Sidebar Toggle y Reloj
// ========================================

// Toggle sidebar en móviles
const menuToggle = document.getElementById('menu-toggle');
const sidebar = document.getElementById('admin-sidebar');
const overlay = document.getElementById('sidebar-overlay');

menuToggle?.addEventListener('click', () => {
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
});

overlay?.addEventListener('click', () => {
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
});

// Reloj en tiempo real
function updateClock() {
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
    const clockElement = document.getElementById('clock');
    if (clockElement) {
        clockElement.textContent = now.toLocaleDateString('es-ES', options);
    }
}
setInterval(updateClock, 1000);
updateClock();
