document.addEventListener("DOMContentLoaded", () => {
  // Helper para obtener el token CSRF de Laravel
  const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

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

  // --- MÓDULO 3: RELOJ EN TIEMPO REAL ---
  const clockElement = document.getElementById("clock");
  if (clockElement) {
    const updateClock = () => {
      const now = new Date();
      const options = {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
      };
      clockElement.textContent = now.toLocaleDateString("es-ES", options);
    };
    setInterval(updateClock, 1000);
    updateClock(); // Carga inicial
  }

  // --- MÓDULO 4: FILTRADO (OPCIONAL) ---
  // En la plantilla actual no existen inputs de filtro; sólo activamos el módulo si están presentes.
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
        const nombreCell = (
          row.querySelector(".nombre-cell")?.textContent || ""
        ).toLowerCase();
        const ciCell = (
          row.querySelector(".ci-cell")?.textContent || ""
        ).toLowerCase();
        const codigoCell = (
          row.querySelector(".codigo-cell")?.textContent || ""
        ).toLowerCase();
        const rolCell = (
          row.querySelector(".rol-cell span")?.textContent || ""
        ).trim();

        const nombreMatch = nombreCell.includes(nombreValue);
        const ciCodigoMatch =
          ciCell.includes(ciCodigoValue) || codigoCell.includes(ciCodigoValue);
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
        if (
          visibleRows === allRows.length &&
          !nombreValue &&
          !ciCodigoValue &&
          !rolValue
        ) {
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

  // --- MÓDULO 5: LÓGICA DE MODALES CRUD (AGREGAR, EDITAR, ELIMINAR) ---

  // --- A. Lógica de Agregar/Editar (adaptada a permisos) ---
  const userFormModal = document.getElementById("user-form-modal");
  const userForm = document.getElementById("user-form");
  const btnCancelForm = document.getElementById("btn-cancel-form");
  const btnCancelFormX = document.getElementById("btn-cancel-form-x"); // Botón 'X'
  const formModalTitle = document.getElementById("form-modal-title");
  const btnSaveForm = document.getElementById("btn-save-form");
  const hiddenUserId = document.getElementById("form-user-id");

  // Campos del formulario de permisos
  const inputNombre = document.getElementById("form-nombre");
  const inputDescripcion = document.getElementById("form-descripcion");

  // Botón "Agregar Permiso" (Header)
  const btnAdd = document.getElementById("btn-add");
  if (btnAdd) {
    btnAdd.addEventListener("click", () => {
      if (userForm) userForm.reset(); // Limpia el formulario
      if (formModalTitle) formModalTitle.textContent = "Agregar Nuevo Permiso";
      if (hiddenUserId) hiddenUserId.value = ""; // Asegura que no haya ID (modo "crear")
      if (inputNombre) inputNombre.focus();
      if (userFormModal) userFormModal.classList.remove("hidden");
      // evitar scroll fondo
      document.documentElement.classList.add("overflow-hidden");
    });
  }

  // Botones "Editar" (delegado)
  document.addEventListener("click", (e) => {
    const editBtn = e.target.closest && e.target.closest(".btn-edit");
    if (!editBtn) return;
    if (userForm) userForm.reset();
    if (formModalTitle) formModalTitle.textContent = "Editar Permiso";
    const dataset = editBtn.dataset || {};
    if (hiddenUserId) hiddenUserId.value = dataset.id || "";
    if (inputNombre) inputNombre.value = dataset.nombre || "";
    if (inputDescripcion) inputDescripcion.value = dataset.descripcion || "";
    if (userFormModal) userFormModal.classList.remove("hidden");
    document.documentElement.classList.add("overflow-hidden");
  });

  // Botones "Cancelar" del formulario (ambos)
  function closeFormModal() {
    if (userFormModal) userFormModal.classList.add("hidden");
    document.documentElement.classList.remove("overflow-hidden");
  }
  if (btnCancelForm) btnCancelForm.addEventListener("click", closeFormModal);
  if (btnCancelFormX) btnCancelFormX.addEventListener("click", closeFormModal);

  // Envío del formulario (SUBMIT) - adaptado a permisos
  if (userForm) {
    userForm.addEventListener("submit", async (e) => {
      e.preventDefault(); // Previene el envío normal

      if (!btnSaveForm) return;
      btnSaveForm.disabled = true;
      const origText = btnSaveForm.textContent;
      btnSaveForm.textContent = "Guardando...";

      // Recoger datos del formulario y preparar payload
      const formData = new FormData(userForm);
      const data = Object.fromEntries(formData.entries());
      const permisoId = hiddenUserId ? hiddenUserId.value : "";
      const isEditing = permisoId !== "";

      // Si es una creación, aseguramos que no se envíe el ID
      if (!isEditing) {
        delete data.id;
      }

      // Define el endpoint al que se enviarán los datos
      const url = isEditing
        ? `/admin/permisos/update`
        : `/admin/permisos/create`;

      try {
        const response = await fetch(url, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken, // Token CSRF de Laravel
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify(data),
        });

        const result = await response.json().catch(() => ({}));
        if (response.ok) {
          alert(
            result.message ||
              (isEditing
                ? "Permiso actualizado con éxito."
                : "Permiso creado con éxito.")
          );
          // Recarga para simplificar sincronización con vista móvil/tablet
          window.location.reload();
        } else {
          let errorMessage = "Error al guardar. ";
          if (result.errors)
            errorMessage += Object.values(result.errors).join(" ");
          else errorMessage += result.message || "Inténtalo de nuevo.";
          alert(errorMessage);
        }
      } catch (error) {
        console.error("Error de red:", error);
        alert("Error de conexión. No se pudo guardar el permiso.");
      } finally {
        btnSaveForm.disabled = false;
        btnSaveForm.textContent = origText;
      }
    });
  }

  // --- B. Lógica de Eliminación (con Modal y Fetch) ---
  const deleteModal = document.getElementById("delete-modal");
  const btnCancelDelete = document.getElementById("btn-cancel-delete");
  const btnConfirmDelete = document.getElementById("btn-confirm-delete");
  const deleteUserName = document.getElementById("delete-user-name");
  let userIdToDelete = null;
  let rowToDelete = null;

  document.querySelectorAll(".btn-delete").forEach((button) => {
    button.addEventListener("click", (e) => {
      userIdToDelete = e.currentTarget.dataset.id;
      const userName = e.currentTarget.dataset.nombre;
      // soporta tanto filas de tabla como tarjetas móviles
      rowToDelete =
        e.currentTarget.closest("tr.user-row") ||
        e.currentTarget.closest("div");
      deleteUserName.textContent = userName;
      if (deleteModal) deleteModal.classList.remove("hidden");
      document.documentElement.classList.add("overflow-hidden");
    });
  });

  if (btnCancelDelete) {
    btnCancelDelete.addEventListener("click", () => {
      deleteModal.classList.add("hidden");
      userIdToDelete = null;
      rowToDelete = null;
      document.documentElement.classList.remove("overflow-hidden");
    });
  }

  if (btnConfirmDelete) {
    btnConfirmDelete.addEventListener("click", async () => {
      if (!userIdToDelete) return;

      btnConfirmDelete.disabled = true;
      btnConfirmDelete.textContent = "Eliminando...";

      try {
        const response = await fetch("/admin/permisos/delete", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
          },
          body: JSON.stringify({ id: userIdToDelete }),
        });

        const result = await response.json().catch(() => ({}));

        if (response.ok) {
          alert(result.message || "Permiso eliminado con éxito.");
          // Eliminación segura del elemento correspondiente en DOM.
          // Recarga para simplificar sincronización con vista móvil/tablet
          window.location.reload();
        } else {
          alert(
            result.error ||
              result.message ||
              "Error al eliminar el permiso. Inténtalo de nuevo."
          );
        }
      } catch (error) {
        console.error("Error de red:", error);
        alert("Error de red al intentar eliminar el permiso.");
      } finally {
        deleteModal.classList.add("hidden");
        btnConfirmDelete.disabled = false;
        btnConfirmDelete.textContent = "Sí, eliminar";
        userIdToDelete = null;
        rowToDelete = null;
        document.documentElement.classList.remove("overflow-hidden");
      }
    });
  }
});
