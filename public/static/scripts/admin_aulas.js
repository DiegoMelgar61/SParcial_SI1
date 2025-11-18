// ==================== ADMIN AULAS - JAVASCRIPT ====================

document.addEventListener("DOMContentLoaded", () => {
    // Helper para obtener el token CSRF de Laravel
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

    // --- MÓDULO 1: LÓGICA DEL SIDEBAR (Menú lateral) ---
    const sidebar = document.getElementById("admin-sidebar");
    const toggleButton = document.getElementById("menu-toggle");
    const overlay = document.getElementById("sidebar-overlay");

    if (toggleButton) {
        toggleButton.addEventListener("click", () => {
            sidebar.classList.toggle("-translate-x-full");
            overlay.classList.toggle("hidden");
        });
    }

    if (overlay) {
        overlay.addEventListener("click", () => {
            sidebar.classList.add("-translate-x-full");
            overlay.classList.add("hidden");
        });
    }

    // --- MÓDULO 2: LÓGICA DEL PANEL DE USUARIO (Avatar) ---
    const userAvatar = document.getElementById("user-avatar");
    const userAside = document.getElementById("user-aside");

    if (userAvatar) {
        userAvatar.addEventListener("click", (e) => {
            e.stopPropagation();
            if (userAside.classList.contains("opacity-0")) {
                userAside.classList.remove("hidden");
                setTimeout(() => {
                    userAside.classList.remove("opacity-0", "scale-95");
                    userAside.classList.add("opacity-100", "scale-100");
                }, 10);
            } else {
                userAside.classList.add("opacity-0", "scale-95");
                userAside.classList.remove("opacity-100", "scale-100");
                setTimeout(() => {
                    userAside.classList.add("hidden");
                }, 300);
            }
        });
    }

    // Ocultar panel de usuario si se hace clic fuera
    document.addEventListener("click", (e) => {
        if (
            userAside &&
            !userAside.contains(e.target) &&
            !userAvatar.contains(e.target) &&
            !userAside.classList.contains("opacity-0")
        ) {
            userAside.classList.add("opacity-0", "scale-95");
            userAside.classList.remove("opacity-100", "scale-100");
            setTimeout(() => {
                userAside.classList.add("hidden");
            }, 300);
        }
    });

    // --- MÓDULO 3: LÓGICA DE MODALES CRUD (AGREGAR, EDITAR, ELIMINAR) ---

    // --- A. Lógica de Agregar/Editar ---
    const aulaFormModal = document.getElementById("aula-form-modal");
    const aulaForm = document.getElementById("aula-form");
    const btnCancelForm = document.getElementById("btn-cancel-form");
    const btnCancelFormX = document.getElementById("btn-cancel-form-x");
    const formModalTitle = document.getElementById("form-modal-title");
    const btnSaveForm = document.getElementById("btn-save-form");
    const hiddenAulaNro = document.getElementById("form-aula-nro");

    // Campos del formulario de aulas
    const inputNro = document.getElementById("form-nro");
    const inputCapacidad = document.getElementById("form-capacidad");
    const inputModulo = document.getElementById("form-modulo");
    const inputTipo = document.getElementById("form-tipo");

    // Botón "Agregar Aula" (Header)
    const btnAdd = document.getElementById("btn-add");
    if (btnAdd) {
        btnAdd.addEventListener("click", () => {
            if (aulaForm) aulaForm.reset();
            if (formModalTitle) formModalTitle.textContent = "Agregar Nueva Aula";
            if (hiddenAulaNro) hiddenAulaNro.value = "";
            if (inputNro) {
                inputNro.readOnly = false;
                inputNro.focus();
            }
            if (aulaFormModal) aulaFormModal.classList.remove("hidden");
            document.documentElement.classList.add("overflow-hidden");
        });
    }

    // Botones "Editar" (delegado)
    document.addEventListener("click", (e) => {
        const editBtn = e.target.closest(".btn-edit");
        if (!editBtn) return;
        
        if (aulaForm) aulaForm.reset();
        if (formModalTitle) formModalTitle.textContent = "Editar Aula";
        
        const dataset = editBtn.dataset || {};
        if (hiddenAulaNro) hiddenAulaNro.value = dataset.nro || "";
        if (inputNro) {
            inputNro.value = dataset.nro || "";
            inputNro.readOnly = true;
        }
        if (inputCapacidad) inputCapacidad.value = dataset.capacidad || "";
        if (inputModulo) inputModulo.value = dataset.modulo || "";
        if (inputTipo) inputTipo.value = dataset.tipo || "";
        
        if (aulaFormModal) aulaFormModal.classList.remove("hidden");
        document.documentElement.classList.add("overflow-hidden");
    });

    // Botones "Cancelar" del formulario
    function closeFormModal() {
        if (aulaFormModal) aulaFormModal.classList.add("hidden");
        if (inputNro) inputNro.readOnly = false;
        document.documentElement.classList.remove("overflow-hidden");
    }
    
    if (btnCancelForm) btnCancelForm.addEventListener("click", closeFormModal);
    if (btnCancelFormX) btnCancelFormX.addEventListener("click", closeFormModal);

    // Envío del formulario (SUBMIT)
    if (aulaForm) {
        aulaForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            if (!btnSaveForm) return;
            btnSaveForm.disabled = true;
            const origText = btnSaveForm.textContent;
            btnSaveForm.textContent = "Guardando...";

            const formData = new FormData(aulaForm);
            const data = Object.fromEntries(formData.entries());
            const aulaNro = hiddenAulaNro ? hiddenAulaNro.value : "";
            const isEditing = aulaNro !== "";

            // Validación de campos
            if (!data.nro || data.nro.trim() === "") {
                alert("El número de aula es obligatorio");
                btnSaveForm.disabled = false;
                btnSaveForm.textContent = origText;
                return;
            }
            
            if (!data.capacidad || data.capacidad < 1) {
                alert("La capacidad debe ser un número positivo");
                btnSaveForm.disabled = false;
                btnSaveForm.textContent = origText;
                return;
            }
            
            if (!data.modulo || data.modulo.trim() === "") {
                alert("El módulo es obligatorio");
                btnSaveForm.disabled = false;
                btnSaveForm.textContent = origText;
                return;
            }
            
            if (!data.tipo || data.tipo.trim() === "") {
                alert("El tipo es obligatorio");
                btnSaveForm.disabled = false;
                btnSaveForm.textContent = origText;
                return;
            }

            const url = isEditing ? "/admin/aulas/update" : "/admin/aulas/create";

            try {
                const response = await fetch(url, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: JSON.stringify(data),
                });

                const result = await response.json().catch(() => ({}));
                
                if (response.ok) {
                    alert(
                        result.message ||
                        (isEditing ? "Aula actualizada con éxito." : "Aula creada con éxito.")
                    );
                    window.location.reload();
                } else {
                    let errorMessage = "Error al guardar. ";
                    if (result.errors) {
                        errorMessage += Object.values(result.errors).join(" ");
                    } else {
                        errorMessage += result.message || "Inténtalo de nuevo.";
                    }
                    alert(errorMessage);
                }
            } catch (error) {
                console.error("Error de red:", error);
                alert("Error de conexión. No se pudo guardar el aula.");
            } finally {
                btnSaveForm.disabled = false;
                btnSaveForm.textContent = origText;
            }
        });
    }

    // --- B. Lógica de Eliminación ---
    const deleteModal = document.getElementById("delete-modal");
    const btnCancelDelete = document.getElementById("btn-cancel-delete");
    const btnConfirmDelete = document.getElementById("btn-confirm-delete");
    const deleteAulaNro = document.getElementById("delete-aula-nro");
    const deleteAulaCapacidad = document.getElementById("delete-aula-capacidad");
    let aulaNroToDelete = null;
    let rowToDelete = null;

    document.addEventListener("click", (e) => {
        const deleteBtn = e.target.closest(".btn-delete");
        if (!deleteBtn) return;
        
        aulaNroToDelete = deleteBtn.dataset.nro;
        const aulaCapacidad = deleteBtn.dataset.capacidad;
        rowToDelete = deleteBtn.closest("tr.aula-row") || deleteBtn.closest("div");
        
        if (deleteAulaNro) deleteAulaNro.textContent = aulaNroToDelete;
        if (deleteAulaCapacidad) deleteAulaCapacidad.textContent = aulaCapacidad;
        if (deleteModal) deleteModal.classList.remove("hidden");
        document.documentElement.classList.add("overflow-hidden");
    });

    if (btnCancelDelete) {
        btnCancelDelete.addEventListener("click", () => {
            if (deleteModal) deleteModal.classList.add("hidden");
            aulaNroToDelete = null;
            rowToDelete = null;
            document.documentElement.classList.remove("overflow-hidden");
        });
    }

    if (btnConfirmDelete) {
        btnConfirmDelete.addEventListener("click", async () => {
            if (!aulaNroToDelete) return;

            btnConfirmDelete.disabled = true;
            btnConfirmDelete.textContent = "Eliminando...";

            try {
                const response = await fetch("/admin/aulas/delete", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: JSON.stringify({ nro: aulaNroToDelete }),
                });

                const result = await response.json().catch(() => ({}));

                if (response.ok) {
                    alert(result.message || "Aula eliminada con éxito.");
                    window.location.reload();
                } else {
                    alert(
                        result.error ||
                        result.message ||
                        "Error al eliminar el aula. Inténtalo de nuevo."
                    );
                }
            } catch (error) {
                console.error("Error de red:", error);
                alert("Error de red al intentar eliminar el aula.");
            } finally {
                if (deleteModal) deleteModal.classList.add("hidden");
                btnConfirmDelete.disabled = false;
                btnConfirmDelete.textContent = "Sí, eliminar";
                aulaNroToDelete = null;
                rowToDelete = null;
                document.documentElement.classList.remove("overflow-hidden");
            }
        });
    }

    // --- MÓDULO 4: LÓGICA DE CONSULTA DE HORARIOS ---
    
    const seleccionAulasModal = document.getElementById("seleccion-aulas-modal");
    const horarioAulaModal = document.getElementById("horario-aula-modal");
    const btnConsultarHorarios = document.getElementById("btn-consultar-horarios");
    const btnCloseSeleccion = document.getElementById("btn-close-seleccion");
    const btnCancelarSeleccion = document.getElementById("btn-cancelar-seleccion");
    const btnCloseHorario = document.getElementById("btn-close-horario");
    const btnCerrarHorario = document.getElementById("btn-cerrar-horario");

    // Abrir modal de selección de aulas
    if (btnConsultarHorarios) {
        btnConsultarHorarios.addEventListener("click", () => {
            if (seleccionAulasModal) seleccionAulasModal.classList.remove("hidden");
            document.documentElement.classList.add("overflow-hidden");
        });
    }

    // Cerrar modal de selección
    function cerrarSeleccionModal() {
        if (seleccionAulasModal) seleccionAulasModal.classList.add("hidden");
        document.documentElement.classList.remove("overflow-hidden");
    }

    if (btnCloseSeleccion) btnCloseSeleccion.addEventListener("click", cerrarSeleccionModal);
    if (btnCancelarSeleccion) btnCancelarSeleccion.addEventListener("click", cerrarSeleccionModal);

    // Manejar click en aula para ver su horario (desde modal de selección)
    document.addEventListener("click", async (e) => {
        const aulaCard = e.target.closest(".aula-card-selectable");
        if (!aulaCard) return;

        const aulaNro = aulaCard.dataset.aulaNro;
        const aulaCapacidad = aulaCard.dataset.aulaCapacidad;
        const aulaModulo = aulaCard.dataset.aulaModulo;
        const aulaTipo = aulaCard.dataset.aulaTipo;

        // Guardar aula actual
        aulaNroActual = aulaNro;

        // Cerrar modal de selección y abrir modal de horario
        cerrarSeleccionModal();
        if (horarioAulaModal) horarioAulaModal.classList.remove("hidden");
        document.documentElement.classList.add("overflow-hidden");

        // Actualizar encabezado
        const horarioAulaNumero = document.getElementById("horario-aula-numero");
        const horarioAulaInfo = document.getElementById("horario-aula-info");
        
        if (horarioAulaNumero) horarioAulaNumero.textContent = aulaNro;
        if (horarioAulaInfo) {
            horarioAulaInfo.textContent = `Capacidad: ${aulaCapacidad} | Módulo: ${aulaModulo} | Tipo: ${aulaTipo}`;
        }

        // Limpiar selector de gestión y mostrar mensaje inicial
        const selectGestion = document.getElementById("select-gestion-horario");
        if (selectGestion) selectGestion.value = "";
        
        const horarioSinGestion = document.getElementById("horario-sin-gestion");
        const horarioContentWrapper = document.getElementById("horario-content-wrapper");
        if (horarioSinGestion) horarioSinGestion.classList.remove("hidden");
        if (horarioContentWrapper) horarioContentWrapper.classList.add("hidden");
    });

    // Manejar click en botón "Ver Horario" de la tabla
    document.addEventListener("click", async (e) => {
        const btnVerHorario = e.target.closest(".btn-ver-horario");
        if (!btnVerHorario) return;

        const aulaNro = btnVerHorario.dataset.nro;

        // Guardar aula actual
        aulaNroActual = aulaNro;

        // Buscar información completa del aula
        const aulaRow = btnVerHorario.closest("tr");
        const aulaCapacidad = aulaRow.querySelector("td:nth-child(3)")?.textContent.trim();
        const aulaModulo = aulaRow.querySelector("td:nth-child(4)")?.textContent.trim();
        const aulaTipo = aulaRow.querySelector("td:nth-child(5) span")?.textContent.trim();

        // Abrir modal de horario
        if (horarioAulaModal) horarioAulaModal.classList.remove("hidden");
        document.documentElement.classList.add("overflow-hidden");

        // Actualizar encabezado
        const horarioAulaNumero = document.getElementById("horario-aula-numero");
        const horarioAulaInfo = document.getElementById("horario-aula-info");
        
        if (horarioAulaNumero) horarioAulaNumero.textContent = aulaNro;
        if (horarioAulaInfo) {
            horarioAulaInfo.textContent = `Capacidad: ${aulaCapacidad} | Módulo: ${aulaModulo} | Tipo: ${aulaTipo}`;
        }

        // Limpiar selector de gestión y mostrar mensaje inicial
        const selectGestion = document.getElementById("select-gestion-horario");
        if (selectGestion) selectGestion.value = "";
        
        const horarioSinGestion = document.getElementById("horario-sin-gestion");
        const horarioContentWrapper = document.getElementById("horario-content-wrapper");
        if (horarioSinGestion) horarioSinGestion.classList.remove("hidden");
        if (horarioContentWrapper) horarioContentWrapper.classList.add("hidden");
    });

    // Cerrar modal de horario
    function cerrarHorarioModal() {
        if (horarioAulaModal) horarioAulaModal.classList.add("hidden");
        document.documentElement.classList.remove("overflow-hidden");
        
        // Limpiar selector de gestión
        const selectGestion = document.getElementById("select-gestion-horario");
        if (selectGestion) selectGestion.value = "";
        
        // Mostrar mensaje inicial
        const horarioSinGestion = document.getElementById("horario-sin-gestion");
        const horarioContentWrapper = document.getElementById("horario-content-wrapper");
        if (horarioSinGestion) horarioSinGestion.classList.remove("hidden");
        if (horarioContentWrapper) horarioContentWrapper.classList.add("hidden");
    }

    if (btnCloseHorario) btnCloseHorario.addEventListener("click", cerrarHorarioModal);
    if (btnCerrarHorario) btnCerrarHorario.addEventListener("click", cerrarHorarioModal);

    // Manejador del selector de gestión
    const selectGestionHorario = document.getElementById("select-gestion-horario");
    let aulaNroActual = null;
    
    if (selectGestionHorario) {
        selectGestionHorario.addEventListener("change", async function() {
            const gestionId = this.value;
            
            if (!gestionId || !aulaNroActual) {
                // Ocultar horario si no hay gestión seleccionada
                const horarioSinGestion = document.getElementById("horario-sin-gestion");
                const horarioContentWrapper = document.getElementById("horario-content-wrapper");
                if (horarioSinGestion) horarioSinGestion.classList.remove("hidden");
                if (horarioContentWrapper) horarioContentWrapper.classList.add("hidden");
                return;
            }
            
            // Cargar horario con la gestión seleccionada
            await cargarHorarioAula(aulaNroActual, gestionId);
        });
    }

    // Función para cargar el horario de un aula
    async function cargarHorarioAula(aulaNro, gestionId) {
        const horarioSinGestion = document.getElementById("horario-sin-gestion");
        const horarioContentWrapper = document.getElementById("horario-content-wrapper");
        const horarioLoading = document.getElementById("horario-loading");
        const horarioVacio = document.getElementById("horario-vacio");
        const horarioTablaContainer = document.getElementById("horario-tabla-container");

        // Si no hay gestión seleccionada, mostrar mensaje
        if (!gestionId) {
            if (horarioSinGestion) horarioSinGestion.classList.remove("hidden");
            if (horarioContentWrapper) horarioContentWrapper.classList.add("hidden");
            return;
        }

        // Ocultar mensaje y mostrar contenedor
        if (horarioSinGestion) horarioSinGestion.classList.add("hidden");
        if (horarioContentWrapper) horarioContentWrapper.classList.remove("hidden");

        // Mostrar loading
        if (horarioLoading) horarioLoading.classList.remove("hidden");
        if (horarioVacio) horarioVacio.classList.add("hidden");
        if (horarioTablaContainer) horarioTablaContainer.classList.add("hidden");

        try {
            console.log("Cargando horario para aula:", aulaNro, "Gestión:", gestionId);
            const response = await fetch(`/auto/aulas/horario?aula_nro=${aulaNro}&gestion_id=${gestionId}`, {
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest"
                }
            });

            const result = await response.json();
            console.log("Respuesta del servidor:", result);

            if (response.ok && result.success) {
                const horarios = result.horarios || [];
                console.log("Horarios recibidos:", horarios);
                
                if (horarioLoading) horarioLoading.classList.add("hidden");

                if (horarios.length === 0) {
                    console.log("No hay clases asignadas");
                    if (horarioVacio) horarioVacio.classList.remove("hidden");
                } else {
                    console.log("Renderizando horarios...");
                    renderizarHorario(horarios);
                    if (horarioTablaContainer) horarioTablaContainer.classList.remove("hidden");
                }
            } else {
                throw new Error(result.message || "Error al cargar horario");
            }
        } catch (error) {
            console.error("Error completo:", error);
            if (horarioLoading) horarioLoading.classList.add("hidden");
            alert("Error al cargar el horario del aula: " + error.message);
            cerrarHorarioModal();
        }
    }

    // Función para renderizar el horario en formato Timeline por Día
    function renderizarHorario(horarios) {
        const container = document.getElementById("horario-timeline-container");
        if (!container) return;

        container.innerHTML = "";

        console.log("Renderizando horarios en formato timeline:", horarios);

        // Definir bloques horarios comunes
        const bloquesHorarios = [
            { inicio: "07:00", fin: "07:45" },
            { inicio: "07:45", fin: "08:30" },
            { inicio: "08:30", fin: "09:15" },
            { inicio: "09:15", fin: "10:00" },
            { inicio: "10:00", fin: "10:45" },
            { inicio: "10:45", fin: "11:30" },
            { inicio: "11:30", fin: "12:15" },
            { inicio: "12:15", fin: "13:00" },
            { inicio: "13:00", fin: "13:45" },
            { inicio: "13:45", fin: "14:30" },
            { inicio: "19:00", fin: "19:45" },
            { inicio: "19:45", fin: "20:30" },
            { inicio: "20:30", fin: "21:15" },
            { inicio: "21:15", fin: "22:00" },
            { inicio: "22:00", fin: "22:45" }
        ];

        const diasCompletos = [
            { abrev: "Lun", nombre: "Lunes", color: "navy" },
            { abrev: "Mar", nombre: "Martes", color: "gold" },
            { abrev: "Mie", nombre: "Miércoles", color: "navy" },
            { abrev: "Jue", nombre: "Jueves", color: "gold" },
            { abrev: "Vie", nombre: "Viernes", color: "navy" },
            { abrev: "Sab", nombre: "Sábado", color: "gold" }
        ];

        // Mapeo de nombres completos a abreviados
        const diaMap = {
            "Lunes": "Lun",
            "Martes": "Mar",
            "Miércoles": "Mie",
            "Miercoles": "Mie",
            "Jueves": "Jue",
            "Viernes": "Vie",
            "Sábado": "Sab",
            "Sabado": "Sab"
        };

        // Función auxiliar para convertir hora a minutos
        function horaAMinutos(hora) {
            const [h, m] = hora.split(':').map(Number);
            return h * 60 + m;
        }

        // Función para verificar si un bloque está dentro de un rango
        function bloqueEnRango(bloqueInicio, bloqueFin, rangoInicio, rangoFin) {
            const bIni = horaAMinutos(bloqueInicio);
            const bFin = horaAMinutos(bloqueFin);
            const rIni = horaAMinutos(rangoInicio);
            const rFin = horaAMinutos(rangoFin);

            return bIni < rFin && bFin > rIni;
        }

        // Crear estructura de datos por día
        const ocupacionPorDia = {};

        diasCompletos.forEach(dia => {
            ocupacionPorDia[dia.abrev] = {};
            bloquesHorarios.forEach(bloque => {
                ocupacionPorDia[dia.abrev][`${bloque.inicio}-${bloque.fin}`] = [];
            });
        });

        // Llenar la estructura con las clases
        horarios.forEach(h => {
            const diaAbreviado = diaMap[h.dia] || h.dia;

            // Para cada bloque, verificar si está ocupado por esta clase
            bloquesHorarios.forEach(bloque => {
                if (bloqueEnRango(bloque.inicio, bloque.fin, h.hora_i, h.hora_f)) {
                    const bloqueKey = `${bloque.inicio}-${bloque.fin}`;
                    ocupacionPorDia[diaAbreviado][bloqueKey].push({
                        materia: h.sigla_materia || 'N/A',
                        grupo: h.sigla_grupo || 'N/A',
                        nombreMateria: h.nombre_materia || 'Sin asignar',
                        horarioId: h.horario_id,
                        claseId: h.clase_id,
                        rangoCompleto: `${h.hora_i}-${h.hora_f}`
                    });
                }
            });
        });

        // Renderizar cards por día
        diasCompletos.forEach(dia => {
            const dayCard = document.createElement("div");
            dayCard.className = `bg-white border-4 ${dia.color === 'navy' ? 'border-navy-900' : 'border-gold-500'} shadow-lg`;

            // Header del día
            const header = document.createElement("div");
            header.className = `p-4 text-center ${dia.color === 'navy' ? 'bg-navy-900 border-b-4 border-gold-500' : 'bg-gold-500 border-b-4 border-navy-900'}`;
            header.innerHTML = `
                <h3 class="text-xl font-black ${dia.color === 'navy' ? 'text-white' : 'text-navy-900'} uppercase tracking-wide">${dia.nombre}</h3>
            `;
            dayCard.appendChild(header);

            // Contenedor de bloques con scroll
            const blocksContainer = document.createElement("div");
            blocksContainer.className = "p-4 space-y-2 max-h-[500px] overflow-y-auto";

            bloquesHorarios.forEach(bloque => {
                const bloqueKey = `${bloque.inicio}-${bloque.fin}`;
                const clasesEnBloque = ocupacionPorDia[dia.abrev][bloqueKey];

                const bloqueDiv = document.createElement("div");

                if (clasesEnBloque && clasesEnBloque.length > 0) {
                    // Bloque ocupado
                    const clase = clasesEnBloque[0];
                    bloqueDiv.className = "bg-navy-900 border-l-4 border-gold-500 p-3 hover:shadow-md transition-all";
                    bloqueDiv.innerHTML = `
                        <div class="flex items-start justify-between mb-2">
                            <span class="text-xs font-bold text-gold-500 uppercase tracking-wide">${bloque.inicio} - ${bloque.fin}</span>
                            <span class="px-2 py-0.5 bg-gold-500 text-navy-900 text-xs font-black uppercase">${clase.grupo}</span>
                        </div>
                        <div class="text-white font-bold text-sm mb-1">${clase.materia}</div>
                        <div class="text-gold-500 text-xs font-semibold">${clase.nombreMateria}</div>
                    `;
                } else {
                    // Bloque libre
                    bloqueDiv.className = "bg-slate-50 border-l-4 border-slate-300 p-3 hover:bg-slate-100 transition-all";
                    bloqueDiv.innerHTML = `
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wide">${bloque.inicio} - ${bloque.fin}</span>
                            <span class="text-slate-400 text-xs font-bold uppercase">Libre</span>
                        </div>
                    `;
                }

                blocksContainer.appendChild(bloqueDiv);
            });

            dayCard.appendChild(blocksContainer);
            container.appendChild(dayCard);
        });
    }
});
