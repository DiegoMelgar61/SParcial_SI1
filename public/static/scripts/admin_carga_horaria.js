/**
 * admin_carga_horaria.js
 * Gestión de carga horaria docente
 */

document.addEventListener("DOMContentLoaded", () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

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
    const botonesVerDetalle = document.querySelectorAll(".btn-ver-detalle");

    botonesVerDetalle.forEach(btn => {
        btn.addEventListener("click", async function() {
            const codigo = this.getAttribute("data-codigo");
            const nombre = this.getAttribute("data-nombre");

            await cargarDetalleDocente(codigo, nombre);
        });
    });

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

        materias.forEach(materia => {
            const tr = document.createElement("tr");
            tr.className = "border-b border-gray-200 hover:bg-gray-100 transition";
            
            tr.innerHTML = `
                <td class="px-4 py-3 font-mono text-xs">${materia.sigla}</td>
                <td class="px-4 py-3 font-medium">${materia.nombre}</td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        ${materia.sigla_grupo}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">${materia.semestre}°</td>
                <td class="px-4 py-3 text-center font-semibold text-purple-600">${materia.carga_horaria} hrs</td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
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
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-300 px-3 py-2 text-left font-semibold">Día</th>
                        <th class="border border-gray-300 px-3 py-2 text-left font-semibold">Horario</th>
                        <th class="border border-gray-300 px-3 py-2 text-left font-semibold">Materia</th>
                        <th class="border border-gray-300 px-3 py-2 text-left font-semibold">Grupo</th>
                        <th class="border border-gray-300 px-3 py-2 text-left font-semibold">Aula</th>
                    </tr>
                </thead>
                <tbody>
        `;

        dias.forEach(dia => {
            const horariosDelDia = horariosPorDia[dia] || [];
            
            if (horariosDelDia.length > 0) {
                horariosDelDia.forEach((horario, index) => {
                    html += `
                        <tr class="hover:bg-gray-50 transition">
                            ${index === 0 ? `<td class="border border-gray-300 px-3 py-2 font-semibold bg-gray-50" rowspan="${horariosDelDia.length}">${dia}</td>` : ''}
                            <td class="border border-gray-300 px-3 py-2 font-mono text-xs">
                                ${horario.hora_inicio} - ${horario.hora_fin}
                            </td>
                            <td class="border border-gray-300 px-3 py-2">
                                <div class="font-medium text-gray-900">${horario.materia_nombre}</div>
                                <div class="text-xs text-gray-500">${horario.materia_sigla}</div>
                            </td>
                            <td class="border border-gray-300 px-3 py-2 text-center">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    ${horario.grupo}
                                </span>
                            </td>
                            <td class="border border-gray-300 px-3 py-2 text-center">
                                <span class="font-semibold text-purple-600">${horario.aula}</span>
                                <span class="text-xs text-gray-500">${horario.modulo}</span>
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
