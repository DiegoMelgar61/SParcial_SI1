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
  const filterSigla = document.getElementById("filter-sigla");
  const tableBody = document.getElementById("grupos-table-body");
  if (tableBody && filterSigla) {
    const allRows = tableBody.querySelectorAll("tr.grupo-row");
    const noRecordsRow = document.getElementById("no-records");
    const totalRecordsDisplay = document.getElementById("total-records");

    function applyGrupoFilters() {
      const siglaValue = (filterSigla.value || "").toLowerCase();
      let visibleRows = 0;

      allRows.forEach((row) => {
        const siglaCell = (
          row.querySelector(".sigla-cell")?.textContent || ""
        ).toLowerCase();

        const siglaMatch = siglaCell.includes(siglaValue);

        if (siglaMatch) {
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
        if (visibleRows === allRows.length && !siglaValue) {
          totalRecordsDisplay.textContent = `Mostrando ${allRows.length} registros.`;
        } else {
          totalRecordsDisplay.textContent = `Mostrando ${visibleRows} de ${allRows.length} registros encontrados.`;
        }
      }
    }

    filterSigla.addEventListener("keyup", applyGrupoFilters);
  }

  // --- MÓDULO 5: LÓGICA DE MODALES CRUD (AGREGAR, EDITAR, ELIMINAR) ---

  // --- A. Lógica de Agregar/Editar (adaptada a grupos) ---
  const grupoFormModal = document.getElementById("grupo-form-modal");
  const grupoForm = document.getElementById("grupo-form");
  const btnCancelForm = document.getElementById("btn-cancel-form");
  const btnCancelFormX = document.getElementById("btn-cancel-form-x"); // Botón 'X'
  const formModalTitle = document.getElementById("form-modal-title");
  const btnSaveForm = document.getElementById("btn-save-form");
  const hiddenGrupoId = document.getElementById("form-grupo-id");

  // Campo del formulario de grupos
  const inputSigla = document.getElementById("form-sigla");

  // Botón "Agregar Grupo" (Header)
  const btnAdd = document.getElementById("btn-add");
  if (btnAdd) {
    btnAdd.addEventListener("click", () => {
      if (grupoForm) grupoForm.reset(); // Limpia el formulario
      if (formModalTitle) formModalTitle.textContent = "Agregar Nuevo Grupo";
      if (hiddenGrupoId) hiddenGrupoId.value = ""; // Asegura que no haya ID (modo "crear")
      if (inputSigla) inputSigla.focus();
      if (grupoFormModal) grupoFormModal.classList.remove("hidden");
      // evitar scroll fondo
      document.documentElement.classList.add("overflow-hidden");
    });
  }

  // Botones "Editar" (delegado)
  document.addEventListener("click", (e) => {
    const editBtn = e.target.closest && e.target.closest(".btn-edit");
    if (!editBtn) return;
    if (grupoForm) grupoForm.reset();
    if (formModalTitle) formModalTitle.textContent = "Editar Grupo";
    const dataset = editBtn.dataset || {};
    if (hiddenGrupoId) hiddenGrupoId.value = dataset.id || "";
    if (inputSigla) inputSigla.value = dataset.sigla || "";
    if (grupoFormModal) grupoFormModal.classList.remove("hidden");
    document.documentElement.classList.add("overflow-hidden");
  });

  // Botones "Cancelar" del formulario (ambos)
  function closeFormModal() {
    if (grupoFormModal) grupoFormModal.classList.add("hidden");
    document.documentElement.classList.remove("overflow-hidden");
  }
  if (btnCancelForm) btnCancelForm.addEventListener("click", closeFormModal);
  if (btnCancelFormX) btnCancelFormX.addEventListener("click", closeFormModal);

  // Envío del formulario (SUBMIT) - adaptado a grupos
  if (grupoForm) {
    grupoForm.addEventListener("submit", async (e) => {
      e.preventDefault(); // Previene el envío normal

      if (!btnSaveForm) return;
      btnSaveForm.disabled = true;
      const origText = btnSaveForm.textContent;
      btnSaveForm.textContent = "Guardando...";

      // Recoger datos del formulario y preparar payload
      const formData = new FormData(grupoForm);
      const data = Object.fromEntries(formData.entries());
      const grupoId = hiddenGrupoId ? hiddenGrupoId.value : "";
      const isEditing = grupoId !== "";

      // Si es una creación, aseguramos que no se envíe el ID
      if (!isEditing) {
        delete data.id;
      }

      // Validación de la sigla
      if (!data.sigla || data.sigla.trim() === "") {
        alert("La sigla del grupo es obligatoria");
        btnSaveForm.disabled = false;
        btnSaveForm.textContent = origText;
        return;
      }

      // Define el endpoint al que se enviarán los datos
      const url = isEditing
        ? `/admin/grupos/update`
        : `/admin/grupos/create`;

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
                ? "Grupo actualizado con éxito."
                : "Grupo creado con éxito.")
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
        alert("Error de conexión. No se pudo guardar el grupo.");
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
  const deleteGrupoSigla = document.getElementById("delete-grupo-sigla");
  let grupoIdToDelete = null;
  let rowToDelete = null;

  document.querySelectorAll(".btn-delete").forEach((button) => {
    button.addEventListener("click", (e) => {
      grupoIdToDelete = e.currentTarget.dataset.id;
      const grupoSigla = e.currentTarget.dataset.sigla;
      // soporta tanto filas de tabla como tarjetas móviles
      rowToDelete =
        e.currentTarget.closest("tr.grupo-row") ||
        e.currentTarget.closest("div");
      deleteGrupoSigla.textContent = grupoSigla;
      if (deleteModal) deleteModal.classList.remove("hidden");
      document.documentElement.classList.add("overflow-hidden");
    });
  });

  if (btnCancelDelete) {
    btnCancelDelete.addEventListener("click", () => {
      deleteModal.classList.add("hidden");
      grupoIdToDelete = null;
      rowToDelete = null;
      document.documentElement.classList.remove("overflow-hidden");
    });
  }

  if (btnConfirmDelete) {
    btnConfirmDelete.addEventListener("click", async () => {
      if (!grupoIdToDelete) return;

      btnConfirmDelete.disabled = true;
      btnConfirmDelete.textContent = "Eliminando...";

      try {
        const response = await fetch("/admin/grupos/delete", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
          },
          body: JSON.stringify({ id: grupoIdToDelete }),
        });

        const result = await response.json().catch(() => ({}));

        if (response.ok) {
          alert(result.message || "Grupo eliminado con éxito.");
          // Eliminación segura del elemento correspondiente en DOM.
          // Recarga para simplificar sincronización con vista móvil/tablet
          window.location.reload();
        } else {
          alert(
            result.error ||
              result.message ||
              "Error al eliminar el grupo. Inténtalo de nuevo."
          );
        }
      } catch (error) {
        console.error("Error de red:", error);
        alert("Error de red al intentar eliminar el grupo.");
      } finally {
        deleteModal.classList.add("hidden");
        btnConfirmDelete.disabled = false;
        btnConfirmDelete.textContent = "Sí, eliminar";
        grupoIdToDelete = null;
        rowToDelete = null;
        document.documentElement.classList.remove("overflow-hidden");
      }
    });
  }
});