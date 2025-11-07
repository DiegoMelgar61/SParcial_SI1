document.addEventListener("DOMContentLoaded", () => {
  // Helper para obtener el token CSRF de Laravel
  const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
  const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute("content") : "";

  // --- SIDEBAR / AVATAR / RELOG (sin cambios) ---
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


// ---  RELOJ EN TIEMPO REAL ---
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
  // --- FILTRADO de usuarios/roles (se mantiene igual si aplica) ---
  const filterNombre = document.getElementById("filter-nombre");
  const filterCiCodigo = document.getElementById("filter-ci-codigo");
  const filterRol = document.getElementById("filter-rol");
  const tableBody = document.getElementById("usuarios-table-body");
  if (tableBody && filterNombre && filterCiCodigo && filterRol) {
    const allRows = tableBody.querySelectorAll("tr.user-row");
    const noRecordsRow = document.getElementById("no-records");
    const totalRecordsDisplay = document.getElementById("total-records");

    function applyUserFilters() {
      const nombreValue = (filterNombre.value || "").toLowerCase();
      const ciCodigoValue = (filterCiCodigo.value || "").toLowerCase();
      const rolValue = filterRol.value;
      let visibleRows = 0;

      allRows.forEach((row) => {
        const nombreCell = (row.querySelector(".nombre-cell")?.textContent || "").toLowerCase();
        const ciCell = (row.querySelector(".ci-cell")?.textContent || "").toLowerCase();
        const codigoCell = (row.querySelector(".codigo-cell")?.textContent || "").toLowerCase();
        const rolCell = (row.querySelector(".rol-cell span")?.textContent || "").trim();

        const nombreMatch = nombreCell.includes(nombreValue);
        const ciCodigoMatch = ciCell.includes(ciCodigoValue) || codigoCell.includes(ciCodigoValue);
        const rolMatch = rolValue === "" ? true : rolCell === rolValue;

        if (nombreMatch && ciCodigoMatch && rolMatch) {
          row.style.display = "";
          visibleRows++;
        } else {
          row.style.display = "none";
        }
      });

      if (noRecordsRow && allRows.length > 0) {
        noRecordsRow.style.display = visibleRows === 0 ? "" : "none";
      }
      if (totalRecordsDisplay) {
        if (visibleRows === allRows.length && !nombreValue && !ciCodigoValue && !rolValue) {
          totalRecordsDisplay.textContent = `Mostrando ${allRows.length} registros.`;
        } else {
          totalRecordsDisplay.textContent = `Mostrando ${visibleRows} de ${allRows.length} registros encontrados.`;
        }
      }
    }

    filterNombre.addEventListener("keyup", applyUserFilters);
    filterCiCodigo.addEventListener("keyup", applyUserFilters);
    filterRol.addEventListener("change", applyUserFilters);
  }

  // -----------------------
  // MÓDULO ROLES (CRUD)
  // -----------------------

  // Elementos del modal/ form (adaptados a roles)
  const roleFormModal = document.getElementById("user-form-modal"); // puedes renombrar a role-form-modal si quieres
  const roleForm = document.getElementById("user-form"); // idem
  const btnCancelForm = document.getElementById("btn-cancel-form");
  const btnCancelFormX = document.getElementById("btn-cancel-form-x");
  const formModalTitle = document.getElementById("form-modal-title");
  const btnSaveForm = document.getElementById("btn-save-form");
  const hiddenRoleId = document.getElementById("form-user-id"); // contains role id when editing
  const inputNombre = document.getElementById("form-nombre");
  const inputDescripcion = document.getElementById("form-descripcion");

// Contenedor donde renderizaremos los checkboxes de permisos (agrega este div en el HTML tal como te sugerí antes)
  const permisosContainer = document.getElementById("form-permisos-lista");

  // Cache en memoria: lista global de permisos (se carga 1 vez)
  let permisosGlobales = null;

  async function cargarPermisosGlobales() {
    if (permisosGlobales) return permisosGlobales; // ya cargados
    try {
      const res = await fetch("/admin/permisos/listar", { headers: { "X-Requested-With": "XMLHttpRequest" } });
      if (!res.ok) throw new Error("No se pudo cargar permisos");
      permisosGlobales = await res.json(); // espera un array [{id, nombre, descripcion},...]
      return permisosGlobales;
    } catch (err) {
      console.error("Error cargando permisos globales:", err);
      permisosGlobales = [];
      return permisosGlobales;
    }
  }

  function renderizarPermisosCheckbox(permisosSeleccionados = []) {
    if (!permisosContainer) return;
    permisosContainer.innerHTML = "";
    const lista = permisosGlobales || [];
    if (lista.length === 0) {
      permisosContainer.innerHTML = '<p class="text-gray-500 text-sm">No hay permisos disponibles.</p>';
      return;
    }
    // Crear checkbox por permiso
    lista.forEach((p) => {
      const checked = permisosSeleccionados.includes(String(p.id)) || permisosSeleccionados.includes(p.id);
      const label = document.createElement("label");
      label.className = "flex items-center gap-2 text-sm";
      label.innerHTML = `
        <input type="checkbox" name="permisos[]" value="${p.id}" ${checked ? "checked" : ""} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <div class="flex flex-col">
          <span class="font-medium">${p.nombre}</span>
          ${p.descripcion ? `<small class="text-gray-500">${p.descripcion}</small>` : ""}
        </div>
      `;
      permisosContainer.appendChild(label);
    });
  }

  // Botón "Agregar Rol" (header)
  const btnAdd = document.getElementById("btn-add");
  if (btnAdd) {
    btnAdd.addEventListener("click", async () => {
      if (roleForm) roleForm.reset();
      if (formModalTitle) formModalTitle.textContent = "Agregar Nuevo Rol";
      if (hiddenRoleId) hiddenRoleId.value = "";
      if (inputNombre) inputNombre.focus();
      if (roleFormModal) roleFormModal.classList.remove("hidden");
      document.documentElement.classList.add("overflow-hidden");

      // cargar permisos globales y renderizar (ninguno seleccionado)
      await cargarPermisosGlobales();
      renderizarPermisosCheckbox([]);
    });
  }

  // Delegado: botones "Editar" en la tabla de roles
  document.addEventListener("click", async (e) => {
    const editBtn = e.target.closest && e.target.closest(".btn-edit");
    if (!editBtn) return;

    // Reset formulario
    if (roleForm) roleForm.reset();
    if (formModalTitle) formModalTitle.textContent = "Editar Rol";

    // Rellenar campos básicos (si vienen en data-* del botón)
    const dataset = editBtn.dataset || {};
    const roleId = dataset.id || "";
    if (hiddenRoleId) hiddenRoleId.value = roleId;
    if (inputNombre) inputNombre.value = dataset.nombre || "";
    if (inputDescripcion) inputDescripcion.value = dataset.descripcion || "";

    // Abrir modal
    if (roleFormModal) roleFormModal.classList.remove("hidden");
    document.documentElement.classList.add("overflow-hidden");

    // Cargar permisos globales (si no cargados) y permisos del rol
    await cargarPermisosGlobales();

    // Opción A: si el botón trae la lista ids de permisos en data-permisos (ej: "1,2,3")
    if (dataset.permisos) {
      const sel = dataset.permisos.split(",").map(s => s.trim()).filter(Boolean);
      renderizarPermisosCheckbox(sel);
      return;
    }

    // Opción B: pedir permisos del rol al backend (más seguro si no tienes data-permisos)
    try {
      const res = await fetch(`/admin/roles/get-permisos?role_id=${encodeURIComponent(roleId)}`, {
        headers: { "X-Requested-With": "XMLHttpRequest" },
      });
      if (!res.ok) throw new Error("No se pudo obtener permisos del rol");
      const permisosRol = await res.json(); // espera array de ids: [1,2,3]
      renderizarPermisosCheckbox(permisosRol);
    } catch (err) {
      console.error("Error al obtener permisos del rol:", err);
      renderizarPermisosCheckbox([]); // no bloquear edición, pero sin marcas
    }
  });

  // Cancelar modal
  function closeRoleFormModal() {
    if (roleFormModal) roleFormModal.classList.add("hidden");
    document.documentElement.classList.remove("overflow-hidden");
  }
  if (btnCancelForm) btnCancelForm.addEventListener("click", closeRoleFormModal);
  if (btnCancelFormX) btnCancelFormX.addEventListener("click", closeRoleFormModal);

  // Envío del formulario (roles)
  if (roleForm) {
    roleForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      if (!btnSaveForm) return;
      btnSaveForm.disabled = true;
      const origText = btnSaveForm.textContent;
      btnSaveForm.textContent = "Guardando...";

      // Recolectar datos
      const formData = new FormData(roleForm);
      const dataObj = Object.fromEntries(formData.entries()); // recoge nombre, descripcion, id si existe
      const roleId = hiddenRoleId ? hiddenRoleId.value : "";
      const isEditing = !!roleId;

      // Recoger permisos seleccionados
      const permisosSeleccionados = Array.from(roleForm.querySelectorAll('input[name="permisos[]"]:checked'))
        .map(i => i.value);

      // Construir payload
      const payload = {
        id: isEditing ? roleId : undefined,
        nombre: dataObj.nombre || "",
        descripcion: dataObj.descripcion || "",
        permisos: permisosSeleccionados
      };
      // Eliminar id en creación
      if (!isEditing) delete payload.id;

      // Endpoint (ajusta rutas si tu backend es diferente)
      const url = isEditing ? "/admin/roles/update" : "/admin/roles/create";

      try {
        const response = await fetch(url, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify(payload),
        });

        const result = await response.json().catch(() => ({}));
        if (response.ok) {
          alert(result.message || (isEditing ? "Rol actualizado." : "Rol creado."));
          window.location.reload(); // simplifica sincronización con la vista
        } else {
          let errorMessage = "Error al guardar el rol. ";
          if (result.errors) errorMessage += Object.values(result.errors).join(" ");
          else errorMessage += result.message || "Inténtalo de nuevo.";
          alert(errorMessage);
        }
      } catch (err) {
        console.error("Error de red:", err);
        alert("Error de conexión. No se pudo guardar el rol.");
      } finally {
        btnSaveForm.disabled = false;
        btnSaveForm.textContent = origText;
      }
    });
  }

  // --- Eliminación de roles (modal) ---
  const deleteModal = document.getElementById("delete-modal");
  const btnCancelDelete = document.getElementById("btn-cancel-delete");
  const btnConfirmDelete = document.getElementById("btn-confirm-delete");
  const deleteItemName = document.getElementById("delete-user-name");
  let roleIdToDelete = null;
  let rowToDelete = null;

  // Delegado: btn-delete
  document.addEventListener("click", (e) => {
    const button = e.target.closest && e.target.closest(".btn-delete");
    if (!button) return;
    roleIdToDelete = button.dataset.id;
    const roleName = button.dataset.nombre || "";
    rowToDelete = button.closest("tr.user-row") || button.closest("div");
    if (deleteItemName) deleteItemName.textContent = roleName;
    if (deleteModal) deleteModal.classList.remove("hidden");
    document.documentElement.classList.add("overflow-hidden");
  });

  if (btnCancelDelete) {
    btnCancelDelete.addEventListener("click", () => {
      if (deleteModal) deleteModal.classList.add("hidden");
      roleIdToDelete = null;
      rowToDelete = null;
      document.documentElement.classList.remove("overflow-hidden");
    });
  }

  if (btnConfirmDelete) {
    btnConfirmDelete.addEventListener("click", async () => {
      if (!roleIdToDelete) return;
      btnConfirmDelete.disabled = true;
      btnConfirmDelete.textContent = "Eliminando...";

      try {
        const response = await fetch("/admin/roles/delete", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify({ id: roleIdToDelete }),
        });

        const result = await response.json().catch(() => ({}));
        if (response.ok) {
          alert(result.message || "Rol eliminado con éxito.");
          window.location.reload();
        } else {
          alert(result.message || "Error al eliminar el rol.");
        }
      } catch (err) {
        console.error("Error de red:", err);
        alert("Error de conexión al eliminar el rol.");
      } finally {
        if (deleteModal) deleteModal.classList.add("hidden");
        btnConfirmDelete.disabled = false;
        btnConfirmDelete.textContent = "Sí, eliminar";
        roleIdToDelete = null;
        rowToDelete = null;
        document.documentElement.classList.remove("overflow-hidden");
      }
    });
  }
});
