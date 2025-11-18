/**
 * =====================================================================
 * SISTEMA DE GESTIÓN DE LICENCIAS PARA DOCENTES - FRONTEND
 * =====================================================================
 *
 * Descripción:
 * Sistema completo para que los docentes puedan solicitar, editar y eliminar
 * licencias con validaciones de negocio específicas.
 *
 * Reglas de Negocio:
 * 1. Máximo 7 días de licencia por mes
 * 2. Licencias de 1 a 7 días por solicitud
 * 3. Edición/eliminación solo dentro de la primera hora
 * 4. Fecha fin se calcula automáticamente
 *
 * Módulos:
 * 1. Inicialización y configuración
 * 2. Gestión de días disponibles
 * 3. Listado de licencias
 * 4. Creación de licencias
 * 5. Edición de licencias
 * 6. Eliminación de licencias
 * 7. Utilidades y helpers
 *
 * Autor: Sistema de Horarios - Grupo 31
 * Fecha: Noviembre 2025
 * =====================================================================
 */

// =====================================================================
// MÓDULO 1: CONFIGURACIÓN Y VARIABLES GLOBALES
// =====================================================================

/**
 * Obtiene el token CSRF del meta tag
 * @returns {string} Token CSRF para las peticiones
 */
const getCsrfToken = () => {
  return document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");
};

/**
 * Variable global para almacenar el número de licencia a eliminar
 */
let licenciaAEliminar = null;

/**
 * Variable global para almacenar los días disponibles actuales
 */
let diasDisponiblesGlobal = 0;

// =====================================================================
// MÓDULO 2: INICIALIZACIÓN DEL SISTEMA
// =====================================================================

/**
 * Inicializa el sistema cuando el DOM está completamente cargado
 * Configura event listeners y carga datos iniciales
 */
document.addEventListener("DOMContentLoaded", function () {
  console.log("🚀 Iniciando Sistema de Gestión de Licencias");

  // Toggle del menú lateral en móviles
  const menuToggle = document.getElementById("menu-toggle");
  const sidebar = document.getElementById("docencia-sidebar");

  if (menuToggle && sidebar) {
    menuToggle.addEventListener("click", () => {
      sidebar.classList.toggle("-translate-x-full");
    });

    // Cerrar sidebar al hacer clic fuera en móviles
    document.addEventListener("click", (e) => {
      if (window.innerWidth < 768) {
        if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
          sidebar.classList.add("-translate-x-full");
        }
      }
    });
  }

  // Establecer fecha mínima en el input de fecha (hoy)
  const inputFechaInicio = document.getElementById("inputFechaInicio");
  if (inputFechaInicio) {
    const hoy = new Date().toISOString().split("T")[0];
    inputFechaInicio.min = hoy;
  }

  // Configurar panel de usuario (avatar clickeable)
  configurarPanelUsuario();

  // Cargar días disponibles
  cargarDiasDisponibles();

  // Cargar tabla de licencias
  cargarLicencias();

  // Event Listeners para botones
  configurarEventListeners();

  // Event Listeners para el formulario
  configurarFormulario();
});

/**
 * Configura el panel de usuario que se muestra al hacer clic en el avatar
 */
function configurarPanelUsuario() {
  const userAvatar = document.getElementById("user-avatar");
  const userAside = document.getElementById("user-aside");

  if (userAvatar && userAside) {
    // Click en el avatar para mostrar/ocultar el panel
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

    // Ocultar panel si se hace clic fuera
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
  }
}

/**
 * Configura todos los event listeners de los botones
 */
function configurarEventListeners() {
  // Botón: Nueva Licencia
  const btnNuevaLicencia = document.getElementById("btnNuevaLicencia");
  if (btnNuevaLicencia) {
    btnNuevaLicencia.addEventListener("click", abrirModalNuevo);
  }

  // Botones: Cerrar modales
  const btnCerrarModal = document.getElementById("btnCerrarModal");
  const btnCancelarModal = document.getElementById("btnCancelarModal");
  const btnCancelarEliminar = document.getElementById("btnCancelarEliminar");
  const btnCerrarMensaje = document.getElementById("btnCerrarMensaje");

  if (btnCerrarModal)
    btnCerrarModal.addEventListener("click", cerrarModalLicencia);
  if (btnCancelarModal)
    btnCancelarModal.addEventListener("click", cerrarModalLicencia);
  if (btnCancelarEliminar)
    btnCancelarEliminar.addEventListener("click", cerrarModalConfirmacion);
  if (btnCerrarMensaje)
    btnCerrarMensaje.addEventListener("click", cerrarModalMensaje);

  // Botón: Confirmar eliminación
  const btnConfirmarEliminar = document.getElementById("btnConfirmarEliminar");
  if (btnConfirmarEliminar) {
    btnConfirmarEliminar.addEventListener("click", confirmarEliminacion);
  }

  // Cerrar modales al hacer clic fuera
  const modalLicencia = document.getElementById("modalLicencia");
  const modalConfirmacion = document.getElementById("modalConfirmacion");
  const modalMensaje = document.getElementById("modalMensaje");

  if (modalLicencia) {
    modalLicencia.addEventListener("click", (e) => {
      if (e.target.id === "modalLicencia") cerrarModalLicencia();
    });
  }

  if (modalConfirmacion) {
    modalConfirmacion.addEventListener("click", (e) => {
      if (e.target.id === "modalConfirmacion") cerrarModalConfirmacion();
    });
  }

  if (modalMensaje) {
    modalMensaje.addEventListener("click", (e) => {
      if (e.target.id === "modalMensaje") cerrarModalMensaje();
    });
  }
}

/**
 * Configura event listeners del formulario y sus campos
 */
function configurarFormulario() {
  const form = document.getElementById("formLicencia");
  const inputFechaInicio = document.getElementById("inputFechaInicio");
  const inputDias = document.getElementById("inputDias");

  // Submit del formulario
  if (form) {
    form.addEventListener("submit", manejarSubmitFormulario);
  }

  // Calcular fecha fin cuando cambien fecha inicio o días
  if (inputFechaInicio) {
    inputFechaInicio.addEventListener("change", calcularFechaFin);
  }
  if (inputDias) {
    inputDias.addEventListener("change", calcularFechaFin);
  }
}

// =====================================================================
// MÓDULO 3: GESTIÓN DE DÍAS DISPONIBLES
// =====================================================================

/**
 * Consulta al backend los días disponibles del mes actual
 * Actualiza la UI con la información obtenida
 */
async function cargarDiasDisponibles() {
  try {
    const response = await fetch("/docente/licencias/dias-disponibles", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": getCsrfToken(),
      },
    });

    const data = await response.json();

    if (data.success) {
      diasDisponiblesGlobal = data.dias_disponibles;

      // Actualizar UI principal - Nuevos widgets estadísticos
      const diasDisponiblesEl = document.getElementById("diasDisponibles");
      const diasUsadosEl = document.getElementById("diasUsados");

      if (diasDisponiblesEl)
        diasDisponiblesEl.textContent = data.dias_disponibles;
      if (diasUsadosEl) diasUsadosEl.textContent = data.dias_usados;

      // Deshabilitar botón si no hay días disponibles
      const btnNuevaLicencia = document.getElementById("btnNuevaLicencia");
      if (btnNuevaLicencia) {
        if (data.dias_disponibles === 0) {
          btnNuevaLicencia.disabled = true;
          btnNuevaLicencia.classList.add("opacity-50", "cursor-not-allowed");
          btnNuevaLicencia.title = "Has alcanzado el límite de días este mes";
        } else {
          btnNuevaLicencia.disabled = false;
          btnNuevaLicencia.classList.remove("opacity-50", "cursor-not-allowed");
          btnNuevaLicencia.title = "";
        }
      }

      console.log(`✅ Días disponibles: ${data.dias_disponibles} de 7`);
    } else {
      console.error("❌ Error al obtener días disponibles:", data.message);
      // No mostrar mensaje al usuario, solo logging
    }
  } catch (error) {
    console.error("❌ Error en cargarDiasDisponibles:", error);
    // No mostrar mensaje al usuario, solo logging
  }
}

/**
 * Actualiza el selector de días en el modal según días disponibles
 * @param {number} diasDisponibles - Cantidad de días que puede seleccionar
 */
function actualizarSelectorDias(diasDisponibles) {
  const selectDias = document.getElementById("inputDias");
  const diasDisponiblesModal = document.getElementById("diasDisponiblesModal");

  if (!selectDias) {
    console.warn("⚠️ Elemento inputDias no encontrado");
    return;
  }

  console.log(
    `🔄 Actualizando selector con ${diasDisponibles} días disponibles`
  );

  // Limpiar opciones existentes
  selectDias.innerHTML = '<option value="">Selecciona los días...</option>';

  // Actualizar contador en el modal
  if (diasDisponiblesModal) {
    diasDisponiblesModal.textContent = diasDisponibles;
  }

  // Si no hay días disponibles
  if (diasDisponibles === 0 || diasDisponibles < 0) {
    selectDias.innerHTML =
      '<option value="">No hay días disponibles este mes</option>';
    selectDias.disabled = true;
    console.log("⚠️ Selector deshabilitado - Sin días disponibles");
    return;
  }

  // Agregar opciones del 1 hasta los días disponibles (máximo 7)
  const maxDias = Math.min(diasDisponibles, 7);
  for (let i = 1; i <= maxDias; i++) {
    const option = document.createElement("option");
    option.value = i;
    option.textContent = `${i} ${i === 1 ? "día" : "días"}`;
    selectDias.appendChild(option);
  }

  selectDias.disabled = false;
  console.log(`✅ Selector actualizado con ${maxDias} opciones`);
}

// =====================================================================
// MÓDULO 4: LISTADO DE LICENCIAS
// =====================================================================

/**
 * Carga y muestra las últimas 5 licencias del docente
 * Actualiza la tabla con la información y botones de acción
 */
async function cargarLicencias() {
  const loadingSpinner = document.getElementById("loadingSpinner");
  const gridLicencias = document.getElementById("gridLicencias");
  const noLicencias = document.getElementById("noLicencias");
  const totalLicenciasEl = document.getElementById("totalLicencias");

  console.log("🔍 DEBUG: Iniciando cargarLicencias()");
  console.log("🔍 gridLicencias:", gridLicencias);

  // Verificar que los elementos existan
  if (!gridLicencias) {
    console.warn(
      "⚠️ Elemento gridLicencias no encontrado, abortando cargarLicencias()"
    );
    return;
  }

  try {
    // Mostrar spinner
    if (loadingSpinner) loadingSpinner.classList.remove("hidden");
    gridLicencias.classList.add("hidden");
    if (noLicencias) noLicencias.classList.add("hidden");

    console.log("🔍 Haciendo fetch a /docente/licencias/listar");

    const response = await fetch("/docente/licencias/listar", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": getCsrfToken(),
      },
    });

    const data = await response.json();

    console.log("🔍 Respuesta recibida:", data);
    console.log("🔍 Cantidad de licencias:", data.licencias?.length);

    if (data.success) {
      // Ocultar spinner
      if (loadingSpinner) loadingSpinner.classList.add("hidden");

      // Actualizar contador de Total Licencias
      if (totalLicenciasEl) {
        totalLicenciasEl.textContent = data.licencias.length;
      }

      if (data.licencias.length === 0) {
        console.log("📋 No hay licencias, mostrando mensaje");
        // Mostrar mensaje de no hay licencias
        if (noLicencias) noLicencias.classList.remove("hidden");
      } else {
        console.log(
          "✅ Mostrando grid con",
          data.licencias.length,
          "licencias"
        );
        // Mostrar grid
        gridLicencias.classList.remove("hidden");
        console.log(
          "🔍 gridLicencias clases después de remove hidden:",
          gridLicencias.className
        );

        // Limpiar grid
        gridLicencias.innerHTML = "";

        // Generar cards en formato paquete corporativo
        data.licencias.forEach((licencia, index) => {
          const isNavy = index % 2 === 0;

          // ===== GENERAR CARD CORPORATIVA =====
          const card = document.createElement("div");
          card.className = `group bg-white border-4 ${
            isNavy ? "border-navy-900" : "border-gold-500"
          } shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 overflow-hidden`;

          // Determinar estado y color
          const puedeModificar = licencia.puede_modificar;
          const estadoBadge = puedeModificar
            ? '<span class="inline-block bg-green-500 text-white px-2 py-1 text-xs font-black uppercase">Editable</span>'
            : '<span class="inline-block bg-red-500 text-white px-2 py-1 text-xs font-black uppercase"><i class="fas fa-lock mr-1"></i>Bloqueada</span>';

          // Botones de acción
          let botonesHTML = "";
          if (puedeModificar) {
            botonesHTML = `
                            <div class="grid grid-cols-2 gap-3">
                                <button onclick="editarLicencia(${
                                  licencia.nro
                                })" class="py-3 ${
              isNavy
                ? "bg-gold-500 hover:bg-gold-600 border-b-4 border-gold-600"
                : "bg-navy-900 hover:bg-navy-800 border-b-4 border-navy-800"
            } text-white font-bold uppercase tracking-wide transition-all flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <span>Editar</span>
                                </button>
                                <button onclick="eliminarLicencia(${
                                  licencia.nro
                                })" class="py-3 bg-red-600 hover:bg-red-700 border-b-4 border-red-700 text-white font-bold uppercase tracking-wide transition-all flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    <span>Eliminar</span>
                                </button>
                            </div>
                        `;
          } else {
            botonesHTML = `
                            <div class="py-4 text-center bg-slate-100 border-t-4 border-slate-300">
                                <span class="text-slate-500 text-sm font-bold uppercase tracking-wide flex items-center justify-center gap-2">
                                    <i class="fas fa-lock"></i>
                                    <span>No modificable (pasó 1 hora)</span>
                                </span>
                            </div>
                        `;
          }

          card.innerHTML = `
                        <!-- Header del Card -->
                        <div class="${
                          isNavy
                            ? "bg-navy-900 border-b-4 border-gold-500"
                            : "bg-gold-500 border-b-4 border-navy-900"
                        } p-5 text-center">
                            <div class="w-16 h-16 mx-auto ${
                              isNavy ? "bg-gold-500" : "bg-navy-900"
                            } flex items-center justify-center text-3xl mb-3 border-4 border-white font-black ${
            isNavy ? "text-navy-900" : "text-gold-500"
          }">
                                #${licencia.nro}
                            </div>
                            ${estadoBadge}
                        </div>

                        <!-- Cuerpo del Card -->
                        <div class="p-6">
                            <!-- Descripción -->
                            <div class="mb-5">
                                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Descripción</h4>
                                <p class="text-sm text-navy-900 font-semibold leading-relaxed">${
                                  licencia.descripcion
                                }</p>
                            </div>

                            <!-- Mini-estadísticas -->
                            <div class="grid grid-cols-2 gap-3 mb-5">
                                <!-- Fecha Solicitada -->
                                <div class="bg-slate-50 border-l-4 ${
                                  isNavy ? "border-navy-900" : "border-gold-500"
                                } p-3">
                                    <p class="text-xs font-bold text-slate-500 uppercase mb-1">
                                        <i class="fas fa-calendar-plus ${
                                          isNavy
                                            ? "text-navy-900"
                                            : "text-gold-500"
                                        } mr-1"></i>
                                        Solicitada
                                    </p>
                                    <p class="text-xs text-navy-900 font-bold">${
                                      licencia.fecha_hora_formato
                                    }</p>
                                </div>

                                <!-- Duración -->
                                <div class="bg-slate-50 border-l-4 ${
                                  isNavy ? "border-navy-900" : "border-gold-500"
                                } p-3">
                                    <p class="text-xs font-bold text-slate-500 uppercase mb-1">
                                        <i class="fas fa-clock ${
                                          isNavy
                                            ? "text-navy-900"
                                            : "text-gold-500"
                                        } mr-1"></i>
                                        Duración
                                    </p>
                                    <p class="text-xl font-black ${
                                      isNavy ? "text-navy-900" : "text-gold-500"
                                    }">${licencia.dias_licencia} ${
            licencia.dias_licencia === 1 ? "día" : "días"
          }</p>
                                </div>
                            </div>

                            <!-- Fechas de Inicio y Fin -->
                            <div class="grid grid-cols-2 gap-3 mb-5 pb-5 border-b-2 border-slate-100">
                                <!-- Fecha Inicio -->
                                <div>
                                    <p class="text-xs font-bold text-slate-500 uppercase mb-1">
                                        <i class="fas fa-play text-green-600 mr-1"></i>
                                        Inicio
                                    </p>
                                    <p class="text-sm font-bold text-navy-900">${
                                      licencia.fecha_i_formato
                                    }</p>
                                </div>

                                <!-- Fecha Fin -->
                                <div>
                                    <p class="text-xs font-bold text-slate-500 uppercase mb-1">
                                        <i class="fas fa-stop text-red-600 mr-1"></i>
                                        Fin
                                    </p>
                                    <p class="text-sm font-bold text-navy-900">${
                                      licencia.fecha_f_formato
                                    }</p>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        ${botonesHTML}
                    `;

          gridLicencias.appendChild(card);
        });

        console.log(
          `✅ Cargadas ${data.licencias.length} licencias en grid corporativo`
        );
        console.log("🔍 Cards en grid:", gridLicencias.children.length);
      }
    } else {
      console.error("❌ Error al cargar licencias:", data.message);
      if (loadingSpinner) loadingSpinner.classList.add("hidden");
      mostrarMensaje("error", "Error", data.message);
    }
  } catch (error) {
    console.error("❌ Error en cargarLicencias:", error);
    if (loadingSpinner) loadingSpinner.classList.add("hidden");
    mostrarMensaje("error", "Error", "No se pudieron cargar las licencias");
  }
}

// =====================================================================
// MÓDULO 5: GESTIÓN DE MODALES
// =====================================================================

/**
 * Abre el modal para crear una nueva licencia
 * Resetea el formulario y actualiza el selector de días
 * Recarga los días disponibles en tiempo real
 */
async function abrirModalNuevo() {
  // Cambiar título
  document.getElementById("modalTitulo").innerHTML =
    '<i class="fas fa-file-medical mr-2"></i>Nueva Solicitud de Licencia';

  // Resetear formulario
  document.getElementById("formLicencia").reset();
  document.getElementById("licenciaNro").value = "";
  document.getElementById("modoEdicion").value = "crear";

  // Limpiar fecha fin calculada
  document.getElementById("fechaFinCalculada").innerHTML =
    '<i class="fas fa-calculator mr-2 text-gray-500"></i>Selecciona la fecha de inicio y los días';

  // Cambiar texto del botón
  document.getElementById("btnGuardarLicencia").innerHTML =
    '<i class="fas fa-save mr-2"></i>Guardar Licencia';

  // Mostrar modal
  document.getElementById("modalLicencia").classList.remove("hidden");

  // IMPORTANTE: Recargar días disponibles en tiempo real
  try {
    const response = await fetch("/docente/licencias/dias-disponibles", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": getCsrfToken(),
      },
    });

    const data = await response.json();

    if (data.success) {
      diasDisponiblesGlobal = data.dias_disponibles;
      actualizarSelectorDias(data.dias_disponibles);
      console.log(
        `📝 Modal abierto - Días disponibles: ${data.dias_disponibles}`
      );
    } else {
      console.error("❌ Error al obtener días:", data.message);
      // En caso de error, usar 7 días por defecto (máximo)
      actualizarSelectorDias(7);
    }
  } catch (error) {
    console.error("❌ Error al cargar días:", error);
    // En caso de error, usar 7 días por defecto (máximo)
    actualizarSelectorDias(7);
  }
}

/**
 * Cierra el modal de licencia
 */
function cerrarModalLicencia() {
  document.getElementById("modalLicencia").classList.add("hidden");
  document.getElementById("formLicencia").reset();
  console.log("❌ Modal de licencia cerrado");
}

/**
 * Cierra el modal de confirmación de eliminación
 */
function cerrarModalConfirmacion() {
  document.getElementById("modalConfirmacion").classList.add("hidden");
  licenciaAEliminar = null;
  console.log("❌ Modal de confirmación cerrado");
}

/**
 * Cierra el modal de mensaje
 */
function cerrarModalMensaje() {
  document.getElementById("modalMensaje").classList.add("hidden");
}

// =====================================================================
// MÓDULO 6: CÁLCULO DE FECHA FIN
// =====================================================================

/**
 * Calcula y muestra la fecha fin basada en fecha inicio + días seleccionados
 * La fecha fin = fecha inicio + (días - 1)
 */
function calcularFechaFin() {
  const inputFechaInicio = document.getElementById("inputFechaInicio");
  const inputDias = document.getElementById("inputDias");
  const fechaFinDisplay = document.getElementById("fechaFinCalculada");

  // Verificar que los elementos existan
  if (!inputFechaInicio || !inputDias || !fechaFinDisplay) {
    console.warn("⚠️ Elementos de fecha no encontrados");
    return;
  }

  const fechaInicio = inputFechaInicio.value;
  const dias = parseInt(inputDias.value);

  // Validar que ambos campos tengan valor
  if (!fechaInicio || !dias) {
    fechaFinDisplay.innerHTML =
      '<i class="fas fa-calculator mr-2 text-gray-500"></i>Selecciona la fecha de inicio y los días';
    return;
  }

  // Calcular fecha fin
  const fecha = new Date(fechaInicio + "T00:00:00");
  fecha.setDate(fecha.getDate() + dias - 1);

  // Formatear fecha
  const opciones = {
    year: "numeric",
    month: "long",
    day: "numeric",
    weekday: "long",
  };
  const fechaFormateada = fecha.toLocaleDateString("es-ES", opciones);

  // Mostrar resultado
  fechaFinDisplay.innerHTML = `
        <i class="fas fa-calendar-check mr-2 text-green-600"></i>
        <strong>${fechaFormateada}</strong>
    `;

  console.log(`📅 Fecha fin calculada: ${fecha.toISOString().split("T")[0]}`);
}

// =====================================================================
// MÓDULO 7: CREAR LICENCIA
// =====================================================================

/**
 * Maneja el submit del formulario
 * Determina si es creación o edición según el modo
 * @param {Event} e - Evento del submit
 */
async function manejarSubmitFormulario(e) {
  e.preventDefault();

  const modo = document.getElementById("modoEdicion").value;

  if (modo === "crear") {
    await crearLicencia();
  } else {
    await actualizarLicencia();
  }
}

/**
 * Crea una nueva licencia en el sistema
 * Valida los datos y envía la petición al backend
 */
async function crearLicencia() {
  const btnGuardar = document.getElementById("btnGuardarLicencia");

  try {
    // Deshabilitar botón
    btnGuardar.disabled = true;
    btnGuardar.innerHTML =
      '<i class="fas fa-spinner fa-spin mr-2"></i>Guardando...';

    // Obtener datos del formulario
    const descripcion = document
      .getElementById("inputDescripcion")
      .value.trim();
    const fechaInicio = document.getElementById("inputFechaInicio").value;
    const dias = parseInt(document.getElementById("inputDias").value);

    // Validación frontend
    if (!descripcion || !fechaInicio || !dias) {
      mostrarMensaje(
        "warning",
        "Campos Incompletos",
        "Por favor completa todos los campos"
      );
      btnGuardar.disabled = false;
      btnGuardar.innerHTML = '<i class="fas fa-save mr-2"></i>Guardar Licencia';
      return;
    }

    // Enviar petición
    const response = await fetch("/docente/licencias/crear", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": getCsrfToken(),
      },
      body: JSON.stringify({
        descripcion: descripcion,
        fecha_inicio: fechaInicio,
        dias: dias,
      }),
    });

    const data = await response.json();

    if (data.success) {
      console.log("✅ Licencia creada exitosamente:", data.licencia);

      // Cerrar modal
      cerrarModalLicencia();

      // Mostrar mensaje de éxito
      mostrarMensaje("success", "Licencia Creada", data.message);

      // Recargar datos
      await cargarDiasDisponibles();
      await cargarLicencias();
    } else {
      console.error("❌ Error al crear licencia:", data.message);
      mostrarMensaje("error", "Error", data.message);
    }
  } catch (error) {
    console.error("❌ Error en crearLicencia:", error);
    mostrarMensaje(
      "error",
      "Error",
      "No se pudo crear la licencia. Intenta nuevamente."
    );
  } finally {
    // Rehabilitar botón
    btnGuardar.disabled = false;
    btnGuardar.innerHTML = '<i class="fas fa-save mr-2"></i>Guardar Licencia';
  }
}

// =====================================================================
// MÓDULO 8: EDITAR LICENCIA
// =====================================================================

/**
 * Abre el modal para editar una licencia existente
 * Carga los datos actuales y permite modificarlos
 * @param {number} nro - Número de la licencia a editar
 */
async function editarLicencia(nro) {
  try {
    // Obtener datos actuales de la licencia
    const response = await fetch("/docente/licencias/listar", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": getCsrfToken(),
      },
    });

    const data = await response.json();

    if (data.success) {
      const licencia = data.licencias.find((l) => l.nro == nro);

      if (!licencia) {
        mostrarMensaje("error", "Error", "Licencia no encontrada");
        return;
      }

      if (!licencia.puede_modificar) {
        mostrarMensaje(
          "warning",
          "No Permitido",
          "Esta licencia ya no puede ser editada (han pasado más de 1 hora)"
        );
        return;
      }

      // Calcular días disponibles incluyendo los días de esta licencia
      const diasDeLicencia = parseInt(licencia.dias_licencia);
      const diasDisponiblesParaEditar = diasDisponiblesGlobal + diasDeLicencia;

      // Cambiar título del modal
      document.getElementById(
        "modalTitulo"
      ).innerHTML = `<i class="fas fa-edit mr-2"></i>Editar Licencia #${nro}`;

      // Llenar formulario
      document.getElementById("licenciaNro").value = nro;
      document.getElementById("modoEdicion").value = "editar";
      document.getElementById("inputDescripcion").value = licencia.descripcion;
      document.getElementById("inputFechaInicio").value = licencia.fecha_i;

      // Actualizar selector de días
      actualizarSelectorDias(diasDisponiblesParaEditar);

      // Seleccionar días actuales
      document.getElementById("inputDias").value = diasDeLicencia;

      // Calcular y mostrar fecha fin
      calcularFechaFin();

      // Cambiar texto del botón
      document.getElementById("btnGuardarLicencia").innerHTML =
        '<i class="fas fa-save mr-2"></i>Actualizar Licencia';

      // Mostrar modal
      document.getElementById("modalLicencia").classList.remove("hidden");

      console.log(`✏️ Editando licencia #${nro}`);
    }
  } catch (error) {
    console.error("❌ Error al cargar datos para editar:", error);
    mostrarMensaje(
      "error",
      "Error",
      "No se pudieron cargar los datos de la licencia"
    );
  }
}

/**
 * Actualiza una licencia existente
 * Envía los nuevos datos al backend
 */
async function actualizarLicencia() {
  const btnGuardar = document.getElementById("btnGuardarLicencia");

  try {
    // Deshabilitar botón
    btnGuardar.disabled = true;
    btnGuardar.innerHTML =
      '<i class="fas fa-spinner fa-spin mr-2"></i>Actualizando...';

    // Obtener datos
    const nro = document.getElementById("licenciaNro").value;
    const descripcion = document
      .getElementById("inputDescripcion")
      .value.trim();
    const fechaInicio = document.getElementById("inputFechaInicio").value;
    const dias = parseInt(document.getElementById("inputDias").value);

    // Validación
    if (!descripcion || !fechaInicio || !dias) {
      mostrarMensaje(
        "warning",
        "Campos Incompletos",
        "Por favor completa todos los campos"
      );
      btnGuardar.disabled = false;
      btnGuardar.innerHTML =
        '<i class="fas fa-save mr-2"></i>Actualizar Licencia';
      return;
    }

    // Enviar petición
    const response = await fetch(`/docente/licencias/editar/${nro}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": getCsrfToken(),
      },
      body: JSON.stringify({
        descripcion: descripcion,
        fecha_inicio: fechaInicio,
        dias: dias,
      }),
    });

    const data = await response.json();

    if (data.success) {
      console.log(`✅ Licencia #${nro} actualizada exitosamente`);

      // Cerrar modal
      cerrarModalLicencia();

      // Mostrar mensaje
      mostrarMensaje("success", "Licencia Actualizada", data.message);

      // IMPORTANTE: Recargar días disponibles y licencias
      await cargarDiasDisponibles();
      await cargarLicencias();
    } else {
      console.error("❌ Error al actualizar:", data.message);
      mostrarMensaje("error", "Error", data.message);
    }
  } catch (error) {
    console.error("❌ Error en actualizarLicencia:", error);
    mostrarMensaje("error", "Error", "No se pudo actualizar la licencia");
  } finally {
    btnGuardar.disabled = false;
    btnGuardar.innerHTML =
      '<i class="fas fa-save mr-2"></i>Actualizar Licencia';
  }
}

// =====================================================================
// MÓDULO 9: ELIMINAR LICENCIA
// =====================================================================

/**
 * Muestra el modal de confirmación para eliminar una licencia
 * @param {number} nro - Número de la licencia a eliminar
 */
function eliminarLicencia(nro) {
  licenciaAEliminar = nro;
  document.getElementById("modalConfirmacion").classList.remove("hidden");
  console.log(`🗑️ Solicitando confirmación para eliminar licencia #${nro}`);
}

/**
 * Confirma y ejecuta la eliminación de la licencia
 */
async function confirmarEliminacion() {
  if (!licenciaAEliminar) return;

  const btnConfirmar = document.getElementById("btnConfirmarEliminar");

  try {
    // Deshabilitar botón
    btnConfirmar.disabled = true;
    btnConfirmar.innerHTML =
      '<i class="fas fa-spinner fa-spin mr-2"></i>Eliminando...';

    const response = await fetch(
      `/docente/licencias/eliminar/${licenciaAEliminar}`,
      {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": getCsrfToken(),
        },
      }
    );

    const data = await response.json();

    if (data.success) {
      console.log(`✅ Licencia #${licenciaAEliminar} eliminada exitosamente`);

      // Cerrar modal de confirmación
      cerrarModalConfirmacion();

      // Mostrar mensaje
      mostrarMensaje("success", "Licencia Eliminada", data.message);

      // IMPORTANTE: Recargar días disponibles y licencias
      await cargarDiasDisponibles();
      await cargarLicencias();
    } else {
      console.error("❌ Error al eliminar:", data.message);
      mostrarMensaje("error", "Error", data.message);
    }
  } catch (error) {
    console.error("❌ Error en confirmarEliminacion:", error);
    mostrarMensaje("error", "Error", "No se pudo eliminar la licencia");
  } finally {
    btnConfirmar.disabled = false;
    btnConfirmar.innerHTML = '<i class="fas fa-trash mr-2"></i>Eliminar';
    licenciaAEliminar = null;
  }
}

// =====================================================================
// MÓDULO 10: UTILIDADES Y HELPERS
// =====================================================================

/**
 * Muestra un modal con un mensaje al usuario
 * @param {string} tipo - Tipo de mensaje: 'success', 'error', 'warning'
 * @param {string} titulo - Título del mensaje
 * @param {string} texto - Texto del mensaje
 */
function mostrarMensaje(tipo, titulo, texto) {
  const modal = document.getElementById("modalMensaje");
  const header = document.getElementById("mensajeHeader");
  const icono = document.getElementById("mensajeIcono");
  const tituloEl = document.getElementById("mensajeTitulo");
  const textoEl = document.getElementById("mensajeTexto");

  // Configurar según el tipo
  if (tipo === "success") {
    header.className =
      "px-6 py-4 rounded-t-lg flex items-center space-x-3 bg-gradient-to-r from-green-600 to-green-700 text-white";
    icono.className = "fas fa-check-circle text-3xl";
  } else if (tipo === "error") {
    header.className =
      "px-6 py-4 rounded-t-lg flex items-center space-x-3 bg-gradient-to-r from-red-600 to-red-700 text-white";
    icono.className = "fas fa-exclamation-circle text-3xl";
  } else if (tipo === "warning") {
    header.className =
      "px-6 py-4 rounded-t-lg flex items-center space-x-3 bg-gradient-to-r from-yellow-600 to-yellow-700 text-white";
    icono.className = "fas fa-exclamation-triangle text-3xl";
  }

  tituloEl.textContent = titulo;
  textoEl.textContent = texto;

  // Mostrar modal
  modal.classList.remove("hidden");

  console.log(`💬 Mensaje mostrado [${tipo}]: ${titulo} - ${texto}`);
}

// Hacer las funciones globales para que puedan ser llamadas desde los botones inline
window.editarLicencia = editarLicencia;
window.eliminarLicencia = eliminarLicencia;
