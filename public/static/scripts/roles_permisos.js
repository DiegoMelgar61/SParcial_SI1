/**
 * ============================================================================
 * GESTIÓN DE ROLES Y PERMISOS - Frontend JavaScript
 * ============================================================================
 * 
 * Este archivo contiene toda la lógica del frontend para la gestión de roles
 * y permisos del sistema. Está organizado en módulos para facilitar su
 * mantenimiento y lectura.
 * 
 * MÓDULOS PRINCIPALES:
 * 1. Configuración inicial y variables globales
 * 2. Lógica del Sidebar (menú lateral)
 * 3. Lógica del Panel de Usuario (avatar)
 * 4. Gestión de ROLES (CRUD completo)
 * 5. Gestión de PERMISOS (CRUD completo)
 * 6. Asignación de Roles a Permisos (modal con checkboxes)
 * 
 * @author Grupo 32 - INF342 SA
 * @version 1.0
 */

document.addEventListener("DOMContentLoaded", () => {
  // ============================================================================
  // MÓDULO 1: CONFIGURACIÓN INICIAL Y VARIABLES GLOBALES
  // ============================================================================
  
  /**
   * Token CSRF de Laravel - requerido para todas las peticiones POST
   * Se obtiene del meta tag en el HTML
   */
  const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

  /**
   * Variable global para almacenar todos los permisos disponibles
   * Se carga una vez al inicio y se reutiliza en diferentes módulos
   */
  let todosLosPermisos = [];

  /**
   * Variable global para almacenar todos los roles disponibles
   * Se utiliza en el modal de asignación de roles a permisos
   */
  let todosLosRoles = [];

  // ============================================================================
  // MÓDULO 2: LÓGICA DEL SIDEBAR (Menú lateral)
  // ============================================================================
  
  const sidebar = document.getElementById("admin-sidebar");
  const toggleButton = document.getElementById("menu-toggle");
  const overlay = document.getElementById("sidebar-overlay");

  /**
   * Event listener para el botón de toggle del sidebar en móviles
   * Muestra/oculta el menú lateral y el overlay de fondo
   */
  if (toggleButton) {
    toggleButton.addEventListener("click", () => {
      sidebar.classList.toggle("-translate-x-full");
      overlay.classList.toggle("hidden");
    });
  }

  /**
   * Event listener para cerrar el sidebar al hacer clic en el overlay
   */
  if (overlay) {
    overlay.addEventListener("click", () => {
      sidebar.classList.add("-translate-x-full");
      overlay.classList.add("hidden");
    });
  }

  // ============================================================================
  // MÓDULO 3: LÓGICA DEL PANEL DE USUARIO (Avatar)
  // ============================================================================
  
  const userAvatar = document.getElementById("user-avatar");
  const userAside = document.getElementById("user-aside");

  /**
   * Event listener para mostrar/ocultar el panel de información del usuario
   * al hacer clic en el avatar
   */
  if (userAvatar) {
    userAvatar.addEventListener("click", (e) => {
      e.stopPropagation();
      if (userAside.classList.contains("opacity-0")) {
        // Mostrar panel
        userAside.classList.remove("hidden");
        setTimeout(() => {
          userAside.classList.remove("opacity-0", "scale-95");
          userAside.classList.add("opacity-100", "scale-100");
        }, 10);
      } else {
        // Ocultar panel
        userAside.classList.add("opacity-0", "scale-95");
        userAside.classList.remove("opacity-100", "scale-100");
        setTimeout(() => {
          userAside.classList.add("hidden");
        }, 300);
      }
    });
  }

  /**
   * Event listener global para cerrar el panel de usuario 
   * cuando se hace clic fuera de él
   */
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

  // ============================================================================
  // MÓDULO 4: GESTIÓN DE ROLES (CRUD)
  // ============================================================================

  // ---------- 4.1: ELEMENTOS DEL DOM PARA ROLES ----------
  const rolFormModal = document.getElementById("rol-form-modal");
  const rolForm = document.getElementById("rol-form");
  const btnCancelRolForm = document.getElementById("btn-cancel-rol-form");
  const btnCancelRolFormX = document.getElementById("btn-cancel-rol-form-x");
  const rolFormModalTitle = document.getElementById("rol-form-modal-title");
  const btnSaveRol = document.getElementById("btn-save-rol");
  const hiddenRolId = document.getElementById("form-rol-id");
  const inputRolNombre = document.getElementById("form-rol-nombre");
  const inputRolDescripcion = document.getElementById("form-rol-descripcion");
  const permisosCheckboxesContainer = document.getElementById("permisos-checkboxes");

  // Elementos para eliminar rol
  const deleteRolModal = document.getElementById("delete-rol-modal");
  const btnCancelDeleteRol = document.getElementById("btn-cancel-delete-rol");
  const btnConfirmDeleteRol = document.getElementById("btn-confirm-delete-rol");
  const deleteRolNombre = document.getElementById("delete-rol-nombre");
  let rolIdToDelete = null;

  // ---------- 4.2: CARGAR PERMISOS DISPONIBLES ----------

  /**
   * Función para cargar todos los permisos disponibles desde el backend
   * Se ejecuta al inicio y se almacena en la variable global todosLosPermisos
   */
  async function cargarPermisosDisponibles() {
    try {
      const response = await fetch("/admin/permisos/listar", {
        method: "GET",
        headers: {
          "X-CSRF-TOKEN": csrfToken,
          "X-Requested-With": "XMLHttpRequest",
        },
      });

      if (response.ok) {
        todosLosPermisos = await response.json();
      } else {
        console.error("Error al cargar permisos");
        todosLosPermisos = [];
      }
    } catch (error) {
      console.error("Error de red al cargar permisos:", error);
      todosLosPermisos = [];
    }
  }

  /**
   * Función para renderizar checkboxes de permisos en el modal de rol
   * @param {Array} permisosSeleccionados - IDs de los permisos ya asignados al rol
   */
  function renderPermisosCheckboxes(permisosSeleccionados = []) {
    if (todosLosPermisos.length === 0) {
      permisosCheckboxesContainer.innerHTML =
        '<p class="text-gray-500 text-sm">No hay permisos disponibles</p>';
      return;
    }

    let html = "";
    todosLosPermisos.forEach((permiso) => {
      const isChecked = permisosSeleccionados.includes(permiso.id);
      html += `
        <label class="flex items-center gap-2 p-2 hover:bg-white rounded cursor-pointer transition">
          <input type="checkbox" name="permisos[]" value="${permiso.id}" 
                 ${isChecked ? "checked" : ""}
                 class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
          <span class="text-sm text-gray-700">
            <strong>${permiso.nombre}</strong>
            ${permiso.descripcion ? `<span class="text-gray-500"> - ${permiso.descripcion}</span>` : ""}
          </span>
        </label>
      `;
    });

    permisosCheckboxesContainer.innerHTML = html;
  }

  // ---------- 4.3: ABRIR MODAL PARA AGREGAR ROL ----------

  const btnAddRol = document.getElementById("btn-add-rol");
  if (btnAddRol) {
    btnAddRol.addEventListener("click", async () => {
      // Resetear formulario
      if (rolForm) rolForm.reset();
      if (rolFormModalTitle) rolFormModalTitle.textContent = "Agregar Nuevo Rol";
      if (hiddenRolId) hiddenRolId.value = "";

      // Renderizar permisos sin selección
      renderPermisosCheckboxes([]);

      // Mostrar modal
      if (rolFormModal) rolFormModal.classList.remove("hidden");
      if (inputRolNombre) inputRolNombre.focus();
      document.documentElement.classList.add("overflow-hidden");
    });
  }

  // ---------- 4.4: ABRIR MODAL PARA EDITAR ROL ----------

  /**
   * Event listener delegado para los botones de editar rol
   * Carga los datos del rol y los permisos asignados
   */
  document.addEventListener("click", async (e) => {
    const editBtn = e.target.closest && e.target.closest(".btn-edit-rol");
    if (!editBtn) return;

    // Resetear formulario
    if (rolForm) rolForm.reset();
    if (rolFormModalTitle) rolFormModalTitle.textContent = "Editar Rol";

    const dataset = editBtn.dataset || {};
    const rolId = dataset.id || "";

    if (hiddenRolId) hiddenRolId.value = rolId;
    if (inputRolNombre) inputRolNombre.value = dataset.nombre || "";
    if (inputRolDescripcion) inputRolDescripcion.value = dataset.descripcion || "";

    // Obtener permisos asignados a este rol
    try {
      const response = await fetch(`/admin/roles/get-permisos?role_id=${rolId}`, {
        method: "GET",
        headers: {
          "X-CSRF-TOKEN": csrfToken,
          "X-Requested-With": "XMLHttpRequest",
        },
      });

      let permisosAsignados = [];
      if (response.ok) {
        permisosAsignados = await response.json();
      }

      renderPermisosCheckboxes(permisosAsignados);
    } catch (error) {
      console.error("Error al cargar permisos del rol:", error);
      renderPermisosCheckboxes([]);
    }

    // Mostrar modal
    if (rolFormModal) rolFormModal.classList.remove("hidden");
    document.documentElement.classList.add("overflow-hidden");
  });

  // ---------- 4.5: CERRAR MODAL DE ROL ----------

  /**
   * Función auxiliar para cerrar el modal de rol
   */
  function closeRolFormModal() {
    if (rolFormModal) rolFormModal.classList.add("hidden");
    document.documentElement.classList.remove("overflow-hidden");
  }

  if (btnCancelRolForm) btnCancelRolForm.addEventListener("click", closeRolFormModal);
  if (btnCancelRolFormX) btnCancelRolFormX.addEventListener("click", closeRolFormModal);

  // ---------- 4.6: GUARDAR ROL (CREAR O ACTUALIZAR) ----------

  /**
   * Event listener para el submit del formulario de rol
   * Maneja tanto la creación como la actualización de roles
   */
  if (rolForm) {
    rolForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      if (!btnSaveRol) return;
      btnSaveRol.disabled = true;
      const origText = btnSaveRol.textContent;
      btnSaveRol.textContent = "Guardando...";

      // Recoger datos del formulario
      const formData = new FormData(rolForm);
      const data = {
        nombre: formData.get("nombre"),
        descripcion: formData.get("descripcion") || "",
        permisos: formData.getAll("permisos[]").map((p) => parseInt(p)),
      };

      const rolId = hiddenRolId ? hiddenRolId.value : "";
      const isEditing = rolId !== "";

      if (isEditing) {
        data.id = parseInt(rolId);
      }

      // Validación
      if (!data.nombre || data.nombre.trim() === "") {
        alert("El nombre del rol es obligatorio");
        btnSaveRol.disabled = false;
        btnSaveRol.textContent = origText;
        return;
      }

      // Definir endpoint
      const url = isEditing ? `/admin/roles/update` : `/admin/roles/create`;

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
              (isEditing ? "Rol actualizado con éxito." : "Rol creado con éxito.")
          );
          // Recargar página para mostrar cambios
          window.location.reload();
        } else {
          let errorMessage = "Error al guardar el rol. ";
          if (result.errors) errorMessage += Object.values(result.errors).join(" ");
          else errorMessage += result.message || "Inténtalo de nuevo.";
          alert(errorMessage);
        }
      } catch (error) {
        console.error("Error de red:", error);
        alert("Error de conexión. No se pudo guardar el rol.");
      } finally {
        btnSaveRol.disabled = false;
        btnSaveRol.textContent = origText;
      }
    });
  }

  // ---------- 4.7: ELIMINAR ROL ----------

  /**
   * Event listener delegado para abrir el modal de confirmación de eliminación
   */
  document.addEventListener("click", (e) => {
    const deleteBtn = e.target.closest && e.target.closest(".btn-delete-rol");
    if (!deleteBtn) return;

    rolIdToDelete = deleteBtn.dataset.id;
    const rolNombre = deleteBtn.dataset.nombre;

    if (deleteRolNombre) deleteRolNombre.textContent = rolNombre;
    if (deleteRolModal) deleteRolModal.classList.remove("hidden");
    document.documentElement.classList.add("overflow-hidden");
  });

  /**
   * Event listener para cancelar la eliminación
   */
  if (btnCancelDeleteRol) {
    btnCancelDeleteRol.addEventListener("click", () => {
      if (deleteRolModal) deleteRolModal.classList.add("hidden");
      rolIdToDelete = null;
      document.documentElement.classList.remove("overflow-hidden");
    });
  }

  /**
   * Event listener para confirmar la eliminación del rol
   */
  if (btnConfirmDeleteRol) {
    btnConfirmDeleteRol.addEventListener("click", async () => {
      if (!rolIdToDelete) return;

      btnConfirmDeleteRol.disabled = true;
      const origText = btnConfirmDeleteRol.textContent;
      btnConfirmDeleteRol.textContent = "Eliminando...";

      try {
        const response = await fetch("/admin/roles/delete", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify({ id: rolIdToDelete }),
        });

        const result = await response.json().catch(() => ({}));

        if (response.ok) {
          alert(result.message || "Rol eliminado con éxito.");
          window.location.reload();
        } else {
          alert(result.message || "Error al eliminar el rol.");
        }
      } catch (error) {
        console.error("Error de red:", error);
        alert("Error de conexión. No se pudo eliminar el rol.");
      } finally {
        btnConfirmDeleteRol.disabled = false;
        btnConfirmDeleteRol.textContent = origText;
      }
    });
  }

  // ============================================================================
  // MÓDULO 5: GESTIÓN DE PERMISOS (CRUD)
  // ============================================================================

  // ---------- 5.1: ELEMENTOS DEL DOM PARA PERMISOS ----------
  const permisoFormModal = document.getElementById("permiso-form-modal");
  const permisoForm = document.getElementById("permiso-form");
  const btnCancelPermisoForm = document.getElementById("btn-cancel-permiso-form");
  const btnCancelPermisoFormX = document.getElementById("btn-cancel-permiso-form-x");
  const permisoFormModalTitle = document.getElementById("permiso-form-modal-title");
  const btnSavePermiso = document.getElementById("btn-save-permiso");
  const hiddenPermisoId = document.getElementById("form-permiso-id");
  const inputPermisoNombre = document.getElementById("form-permiso-nombre");
  const inputPermisoDescripcion = document.getElementById("form-permiso-descripcion");

  // Elementos para eliminar permiso
  const deletePermisoModal = document.getElementById("delete-permiso-modal");
  const btnCancelDeletePermiso = document.getElementById("btn-cancel-delete-permiso");
  const btnConfirmDeletePermiso = document.getElementById("btn-confirm-delete-permiso");
  const deletePermisoNombre = document.getElementById("delete-permiso-nombre");
  let permisoIdToDelete = null;

  // ---------- 5.2: CARGAR PERMISOS EN LA TABLA ----------

  /**
   * Función para cargar y renderizar los permisos en la tabla
   * Se ejecuta al inicio de la página
   */
  async function cargarPermisosEnTabla() {
    const tablaPermisos = document.getElementById("tabla-permisos");
    if (!tablaPermisos) return;

    try {
      const response = await fetch("/admin/permisos/listar", {
        method: "GET",
        headers: {
          "X-CSRF-TOKEN": csrfToken,
          "X-Requested-With": "XMLHttpRequest",
        },
      });

      if (!response.ok) {
        throw new Error("Error al cargar permisos");
      }

      const permisos = await response.json();

      if (permisos.length === 0) {
        tablaPermisos.innerHTML = `
          <tr>
            <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">
              No hay permisos registrados en el sistema.
            </td>
          </tr>
        `;
        return;
      }

      let html = "";
      permisos.forEach((permiso, index) => {
        html += `
          <tr class="hover:bg-gray-50 transition permiso-row">
            <td class="px-6 py-4 text-gray-700">${index + 1}</td>
            <td class="px-6 py-4 font-medium text-gray-800 nombre-cell">${permiso.nombre}</td>
            <td class="px-6 py-4 text-gray-600 descripcion-cell">${permiso.descripcion || "—"}</td>
            <td class="px-6 py-4 text-center">
              <div class="flex justify-center gap-3">
                <!-- Botón Asignar Roles -->
                <button data-id="${permiso.id}" 
                        data-nombre="${permiso.nombre}"
                        class="btn-assign-roles text-blue-600 hover:text-blue-800 p-1 rounded-md hover:bg-blue-50 transition" 
                        title="Asignar Roles">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13.121 9.121a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243z"/>
                  </svg>
                </button>
                <!-- Botón Editar -->
                <button data-id="${permiso.id}" 
                        data-nombre="${permiso.nombre}" 
                        data-descripcion="${permiso.descripcion || ""}"
                        class="btn-edit-permiso text-green-600 hover:text-green-800 p-1 rounded-md hover:bg-green-50 transition" 
                        title="Editar Permiso">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </button>
                <!-- Botón Eliminar -->
                <button data-id="${permiso.id}" 
                        data-nombre="${permiso.nombre}"
                        class="btn-delete-permiso text-red-600 hover:text-red-800 p-1 rounded-md hover:bg-red-50 transition" 
                        title="Eliminar Permiso">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        `;
      });

      tablaPermisos.innerHTML = html;
    } catch (error) {
      console.error("Error al cargar permisos:", error);
      tablaPermisos.innerHTML = `
        <tr>
          <td colspan="4" class="px-6 py-8 text-center text-red-500">
            Error al cargar permisos. Por favor, recargue la página.
          </td>
        </tr>
      `;
    }
  }

  // ---------- 5.3: ABRIR MODAL PARA AGREGAR PERMISO ----------

  const btnAddPermiso = document.getElementById("btn-add-permiso");
  if (btnAddPermiso) {
    btnAddPermiso.addEventListener("click", () => {
      // Resetear formulario
      if (permisoForm) permisoForm.reset();
      if (permisoFormModalTitle) permisoFormModalTitle.textContent = "Agregar Nuevo Permiso";
      if (hiddenPermisoId) hiddenPermisoId.value = "";

      // Mostrar modal
      if (permisoFormModal) permisoFormModal.classList.remove("hidden");
      if (inputPermisoNombre) inputPermisoNombre.focus();
      document.documentElement.classList.add("overflow-hidden");
    });
  }

  // ---------- 5.4: ABRIR MODAL PARA EDITAR PERMISO ----------

  /**
   * Event listener delegado para los botones de editar permiso
   */
  document.addEventListener("click", (e) => {
    const editBtn = e.target.closest && e.target.closest(".btn-edit-permiso");
    if (!editBtn) return;

    // Resetear formulario
    if (permisoForm) permisoForm.reset();
    if (permisoFormModalTitle) permisoFormModalTitle.textContent = "Editar Permiso";

    const dataset = editBtn.dataset || {};
    if (hiddenPermisoId) hiddenPermisoId.value = dataset.id || "";
    if (inputPermisoNombre) inputPermisoNombre.value = dataset.nombre || "";
    if (inputPermisoDescripcion) inputPermisoDescripcion.value = dataset.descripcion || "";

    // Mostrar modal
    if (permisoFormModal) permisoFormModal.classList.remove("hidden");
    document.documentElement.classList.add("overflow-hidden");
  });

  // ---------- 5.5: CERRAR MODAL DE PERMISO ----------

  /**
   * Función auxiliar para cerrar el modal de permiso
   */
  function closePermisoFormModal() {
    if (permisoFormModal) permisoFormModal.classList.add("hidden");
    document.documentElement.classList.remove("overflow-hidden");
  }

  if (btnCancelPermisoForm) btnCancelPermisoForm.addEventListener("click", closePermisoFormModal);
  if (btnCancelPermisoFormX) btnCancelPermisoFormX.addEventListener("click", closePermisoFormModal);

  // ---------- 5.6: GUARDAR PERMISO (CREAR O ACTUALIZAR) ----------

  /**
   * Event listener para el submit del formulario de permiso
   */
  if (permisoForm) {
    permisoForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      if (!btnSavePermiso) return;
      btnSavePermiso.disabled = true;
      const origText = btnSavePermiso.textContent;
      btnSavePermiso.textContent = "Guardando...";

      // Recoger datos del formulario
      const formData = new FormData(permisoForm);
      const data = {
        nombre: formData.get("nombre"),
        descripcion: formData.get("descripcion") || "",
      };

      const permisoId = hiddenPermisoId ? hiddenPermisoId.value : "";
      const isEditing = permisoId !== "";

      if (isEditing) {
        data.id = parseInt(permisoId);
      }

      // Validación
      if (!data.nombre || data.nombre.trim() === "") {
        alert("El nombre del permiso es obligatorio");
        btnSavePermiso.disabled = false;
        btnSavePermiso.textContent = origText;
        return;
      }

      // Definir endpoint
      const url = isEditing ? `/admin/permisos/update` : `/admin/permisos/create`;

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
              (isEditing ? "Permiso actualizado con éxito." : "Permiso creado con éxito.")
          );
          // Recargar página para mostrar cambios
          window.location.reload();
        } else {
          let errorMessage = "Error al guardar el permiso. ";
          if (result.errors) errorMessage += Object.values(result.errors).join(" ");
          else errorMessage += result.message || "Inténtalo de nuevo.";
          alert(errorMessage);
        }
      } catch (error) {
        console.error("Error de red:", error);
        alert("Error de conexión. No se pudo guardar el permiso.");
      } finally {
        btnSavePermiso.disabled = false;
        btnSavePermiso.textContent = origText;
      }
    });
  }

  // ---------- 5.7: ELIMINAR PERMISO ----------

  /**
   * Event listener delegado para abrir el modal de confirmación de eliminación
   */
  document.addEventListener("click", (e) => {
    const deleteBtn = e.target.closest && e.target.closest(".btn-delete-permiso");
    if (!deleteBtn) return;

    permisoIdToDelete = deleteBtn.dataset.id;
    const permisoNombre = deleteBtn.dataset.nombre;

    if (deletePermisoNombre) deletePermisoNombre.textContent = permisoNombre;
    if (deletePermisoModal) deletePermisoModal.classList.remove("hidden");
    document.documentElement.classList.add("overflow-hidden");
  });

  /**
   * Event listener para cancelar la eliminación
   */
  if (btnCancelDeletePermiso) {
    btnCancelDeletePermiso.addEventListener("click", () => {
      if (deletePermisoModal) deletePermisoModal.classList.add("hidden");
      permisoIdToDelete = null;
      document.documentElement.classList.remove("overflow-hidden");
    });
  }

  /**
   * Event listener para confirmar la eliminación del permiso
   */
  if (btnConfirmDeletePermiso) {
    btnConfirmDeletePermiso.addEventListener("click", async () => {
      if (!permisoIdToDelete) return;

      btnConfirmDeletePermiso.disabled = true;
      const origText = btnConfirmDeletePermiso.textContent;
      btnConfirmDeletePermiso.textContent = "Eliminando...";

      try {
        const response = await fetch("/admin/permisos/delete", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify({ id: permisoIdToDelete }),
        });

        const result = await response.json().catch(() => ({}));

        if (response.ok) {
          alert(result.message || "Permiso eliminado con éxito.");
          window.location.reload();
        } else {
          alert(result.message || "Error al eliminar el permiso.");
        }
      } catch (error) {
        console.error("Error de red:", error);
        alert("Error de conexión. No se pudo eliminar el permiso.");
      } finally {
        btnConfirmDeletePermiso.disabled = false;
        btnConfirmDeletePermiso.textContent = origText;
      }
    });
  }

  // ============================================================================
  // MÓDULO 6: ASIGNACIÓN DE ROLES A PERMISOS
  // ============================================================================

  // ---------- 6.1: ELEMENTOS DEL DOM ----------
  const assignRolesModal = document.getElementById("assign-roles-modal");
  const btnCancelAssignRoles = document.getElementById("btn-cancel-assign-roles");
  const btnCancelAssignRolesX = document.getElementById("btn-cancel-assign-roles-x");
  const btnSaveAssignRoles = document.getElementById("btn-save-assign-roles");
  const assignRolesPermisoNombre = document.getElementById("assign-roles-permiso-nombre");
  const assignRolesPermisoId = document.getElementById("assign-roles-permiso-id");
  const rolesCheckboxesContainer = document.getElementById("roles-checkboxes");

  // ---------- 6.2: CARGAR TODOS LOS ROLES ----------

  /**
   * Función para cargar todos los roles disponibles desde el backend
   */
  async function cargarRolesDisponibles() {
    try {
      // Utilizamos el endpoint que ya existe para obtener los roles
      // Parseamos la página HTML para extraer los roles (alternativa: crear endpoint JSON)
      const tablaRoles = document.getElementById("tabla-roles");
      if (!tablaRoles) return;

      const rows = tablaRoles.querySelectorAll("tr.rol-row");
      todosLosRoles = [];

      rows.forEach((row) => {
        const editBtn = row.querySelector(".btn-edit-rol");
        if (editBtn) {
          todosLosRoles.push({
            id: editBtn.dataset.id,
            nombre: editBtn.dataset.nombre,
          });
        }
      });
    } catch (error) {
      console.error("Error al cargar roles:", error);
      todosLosRoles = [];
    }
  }

  /**
   * Función para renderizar checkboxes de roles en el modal de asignación
   * @param {String} permisoId - ID del permiso para el cual se asignan roles
   */
  async function renderRolesCheckboxes(permisoId) {
    rolesCheckboxesContainer.innerHTML = '<p class="text-gray-500 text-sm">Cargando roles...</p>';

    try {
      // Obtener roles que ya tienen este permiso
      let rolesConPermiso = [];
      
      // Recorremos todos los roles y verificamos cuáles tienen este permiso
      for (const rol of todosLosRoles) {
        const response = await fetch(`/admin/roles/get-permisos?role_id=${rol.id}`, {
          method: "GET",
          headers: {
            "X-CSRF-TOKEN": csrfToken,
            "X-Requested-With": "XMLHttpRequest",
          },
        });

        if (response.ok) {
          const permisos = await response.json();
          if (permisos.includes(parseInt(permisoId))) {
            rolesConPermiso.push(rol.id);
          }
        }
      }

      if (todosLosRoles.length === 0) {
        rolesCheckboxesContainer.innerHTML =
          '<p class="text-gray-500 text-sm">No hay roles disponibles</p>';
        return;
      }

      let html = "";
      todosLosRoles.forEach((rol) => {
        const isChecked = rolesConPermiso.includes(rol.id);
        html += `
          <label class="flex items-center gap-2 p-2 hover:bg-white rounded cursor-pointer transition">
            <input type="checkbox" name="roles[]" value="${rol.id}" 
                   ${isChecked ? "checked" : ""}
                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
            <span class="text-sm text-gray-700 font-medium">${rol.nombre}</span>
          </label>
        `;
      });

      rolesCheckboxesContainer.innerHTML = html;
    } catch (error) {
      console.error("Error al renderizar roles:", error);
      rolesCheckboxesContainer.innerHTML =
        '<p class="text-red-500 text-sm">Error al cargar roles</p>';
    }
  }

  // ---------- 6.3: ABRIR MODAL DE ASIGNACIÓN ----------

  /**
   * Event listener delegado para abrir el modal de asignación de roles
   */
  document.addEventListener("click", async (e) => {
    const assignBtn = e.target.closest && e.target.closest(".btn-assign-roles");
    if (!assignBtn) return;

    const permisoId = assignBtn.dataset.id;
    const permisoNombre = assignBtn.dataset.nombre;

    if (assignRolesPermisoNombre) assignRolesPermisoNombre.textContent = permisoNombre;
    if (assignRolesPermisoId) assignRolesPermisoId.value = permisoId;

    // Renderizar checkboxes de roles
    await renderRolesCheckboxes(permisoId);

    // Mostrar modal
    if (assignRolesModal) assignRolesModal.classList.remove("hidden");
    document.documentElement.classList.add("overflow-hidden");
  });

  // ---------- 6.4: CERRAR MODAL DE ASIGNACIÓN ----------

  /**
   * Función auxiliar para cerrar el modal de asignación
   */
  function closeAssignRolesModal() {
    if (assignRolesModal) assignRolesModal.classList.add("hidden");
    document.documentElement.classList.remove("overflow-hidden");
  }

  if (btnCancelAssignRoles) btnCancelAssignRoles.addEventListener("click", closeAssignRolesModal);
  if (btnCancelAssignRolesX) btnCancelAssignRolesX.addEventListener("click", closeAssignRolesModal);

  // ---------- 6.5: GUARDAR ASIGNACIÓN DE ROLES ----------

  /**
   * Event listener para guardar la asignación de roles al permiso
   * Actualiza cada rol añadiendo o quitando el permiso según los checkboxes
   */
  if (btnSaveAssignRoles) {
    btnSaveAssignRoles.addEventListener("click", async () => {
      btnSaveAssignRoles.disabled = true;
      const origText = btnSaveAssignRoles.textContent;
      btnSaveAssignRoles.textContent = "Guardando...";

      const permisoId = assignRolesPermisoId.value;
      const checkboxes = rolesCheckboxesContainer.querySelectorAll('input[name="roles[]"]');
      
      try {
        // Para cada rol, actualizamos sus permisos
        for (const checkbox of checkboxes) {
          const rolId = checkbox.value;
          const shouldHavePermiso = checkbox.checked;

          // Obtener permisos actuales del rol
          const response = await fetch(`/admin/roles/get-permisos?role_id=${rolId}`, {
            method: "GET",
            headers: {
              "X-CSRF-TOKEN": csrfToken,
              "X-Requested-With": "XMLHttpRequest",
            },
          });

          let permisosActuales = [];
          if (response.ok) {
            permisosActuales = await response.json();
          }

          // Determinar nuevos permisos
          let nuevosPermisos = [...permisosActuales];
          const tienePermiso = permisosActuales.includes(parseInt(permisoId));

          if (shouldHavePermiso && !tienePermiso) {
            // Agregar permiso
            nuevosPermisos.push(parseInt(permisoId));
          } else if (!shouldHavePermiso && tienePermiso) {
            // Quitar permiso
            nuevosPermisos = nuevosPermisos.filter((p) => p !== parseInt(permisoId));
          } else {
            // Sin cambios para este rol
            continue;
          }

          // Obtener datos del rol
          const rolRow = document.querySelector(`.btn-edit-rol[data-id="${rolId}"]`);
          const rolNombre = rolRow ? rolRow.dataset.nombre : "";
          const rolDescripcion = rolRow ? rolRow.dataset.descripcion : "";

          // Actualizar rol con nuevos permisos
          await fetch("/admin/roles/update", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-CSRF-TOKEN": csrfToken,
              "X-Requested-With": "XMLHttpRequest",
            },
            body: JSON.stringify({
              id: parseInt(rolId),
              nombre: rolNombre,
              descripcion: rolDescripcion,
              permisos: nuevosPermisos,
            }),
          });
        }

        alert("Asignación de roles actualizada con éxito.");
        window.location.reload();
      } catch (error) {
        console.error("Error al asignar roles:", error);
        alert("Error de conexión. No se pudo guardar la asignación.");
      } finally {
        btnSaveAssignRoles.disabled = false;
        btnSaveAssignRoles.textContent = origText;
      }
    });
  }

  // ============================================================================
  // INICIALIZACIÓN: Ejecutar funciones al cargar la página
  // ============================================================================

  /**
   * Secuencia de inicialización:
   * 1. Cargar permisos disponibles (para el modal de roles)
   * 2. Cargar permisos en la tabla
   * 3. Cargar roles disponibles (para el modal de asignación)
   */
  (async function init() {
    await cargarPermisosDisponibles();
    await cargarPermisosEnTabla();
    await cargarRolesDisponibles();
  })();
});
