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

  // --- MÓDULO 3: FILTRADO ---
  const filterSigla = document.getElementById("filter-sigla");
  const filterNombre = document.getElementById("filter-nombre");
  const filterSemestre = document.getElementById("filter-semestre");
  const tableBody = document.getElementById("materias-table-body");
  const cardsContainer = document.getElementById("materias-cards");
  const totalRecordsDisplay = document.getElementById("total-records");

  if (tableBody && filterSigla && filterNombre && filterSemestre) {
    const allRows = tableBody.querySelectorAll("tr.materia-row");
    const allCards = cardsContainer?.querySelectorAll("div.materia-card");
    const noRecordsRow = document.getElementById("no-records");
    const noRecordsMobile = document.getElementById("no-records-mobile");

    function applyMateriaFilters() {
      const siglaValue = (filterSigla.value || "").toLowerCase();
      const nombreValue = (filterNombre.value || "").toLowerCase();
      const semestreValue = (filterSemestre.value || "").trim();
      let visibleRows = 0;

      // Filtrar filas de tabla (Desktop)
      allRows.forEach((row) => {
        const siglaCell = (
          row.querySelector(".sigla-cell")?.textContent || ""
        ).toLowerCase();
        const nombreCell = (
          row.querySelector(".nombre-cell")?.textContent || ""
        ).toLowerCase();
        const semestreCell = (
          row.querySelector(".semestre-cell")?.textContent || ""
        ).trim();

        const siglaMatch = siglaCell.includes(siglaValue);
        const nombreMatch = nombreCell.includes(nombreValue);
        const semestreMatch =
          !semestreValue || semestreCell === semestreValue;

        if (siglaMatch && nombreMatch && semestreMatch) {
          row.style.display = "";
          visibleRows++;
        } else {
          row.style.display = "none";
        }
      });

      // Filtrar tarjetas (Móvil)
      if (allCards) {
        allCards.forEach((card) => {
          const siglaCell = (
            card.querySelector(".sigla-cell")?.textContent || ""
          ).toLowerCase();
          const nombreCell = (
            card.querySelector(".nombre-cell")?.textContent || ""
          ).toLowerCase();
          const semestreCell = (
            card.querySelector(".semestre-cell")?.textContent || ""
          )
            .replace("Semestre:", "")
            .trim();

          const siglaMatch = siglaCell.includes(siglaValue);
          const nombreMatch = nombreCell.includes(nombreValue);
          const semestreMatch =
            !semestreValue || semestreCell === semestreValue;

          if (siglaMatch && nombreMatch && semestreMatch) {
            card.style.display = "";
          } else {
            card.style.display = "none";
          }
        });
      }

      // Mostrar/ocultar mensajes de "sin registros"
      if (noRecordsRow && allRows.length > 0) {
        noRecordsRow.style.display = visibleRows === 0 ? "" : "none";
      }

      if (noRecordsMobile && allCards) {
        const visibleCards = Array.from(allCards).filter(
          (card) => card.style.display !== "none"
        ).length;
        noRecordsMobile.style.display = visibleCards === 0 ? "" : "none";
      }

      // Actualizar contador
      if (totalRecordsDisplay) {
        const totalRecords = allRows.length;
        if (
          visibleRows === totalRecords &&
          !siglaValue &&
          !nombreValue &&
          !semestreValue
        ) {
          totalRecordsDisplay.textContent = `Mostrando ${totalRecords} registros.`;
        } else {
          totalRecordsDisplay.textContent = `Mostrando ${visibleRows} de ${totalRecords} registros encontrados.`;
        }
      }
    }

    filterSigla.addEventListener("keyup", applyMateriaFilters);
    filterNombre.addEventListener("keyup", applyMateriaFilters);
    filterSemestre.addEventListener("input", applyMateriaFilters);
  }

  // --- MÓDULO 4: LÓGICA DE MODALES CRUD (AGREGAR, EDITAR, ELIMINAR) ---

  // --- A. Lógica de Agregar/Editar ---
  const materiaFormModal = document.getElementById("materia-form-modal");
  const materiaForm = document.getElementById("materia-form");
  const btnCancelForm = document.getElementById("btn-cancel-form");
  const btnCancelFormX = document.getElementById("btn-cancel-form-x");
  const formModalTitle = document.getElementById("form-modal-title");
  const btnSaveForm = document.getElementById("btn-save-form");
  const hiddenSiglaOriginal = document.getElementById(
    "form-materia-sigla-original"
  );

  // Campos del formulario
  const inputSigla = document.getElementById("form-sigla");
  const inputNombre = document.getElementById("form-nombre");
  const inputSemestre = document.getElementById("form-semestre");
  const inputCargaHoraria = document.getElementById("form-carga-horaria");

  // Botón "Agregar Materia"
  const btnAdd = document.getElementById("btn-add");
  if (btnAdd) {
    btnAdd.addEventListener("click", () => {
      if (materiaForm) materiaForm.reset();
      if (formModalTitle) formModalTitle.textContent = "Agregar Nueva Materia";
      if (hiddenSiglaOriginal) hiddenSiglaOriginal.value = "";
      if (inputSigla) {
        inputSigla.readOnly = false;
        inputSigla.focus();
      }
      if (materiaFormModal) materiaFormModal.classList.remove("hidden");
      document.documentElement.classList.add("overflow-hidden");
    });
  }

  // Botones "Editar" (delegado)
  document.addEventListener("click", (e) => {
    const editBtn = e.target.closest && e.target.closest(".btn-edit");
    if (!editBtn) return;

    if (materiaForm) materiaForm.reset();
    if (formModalTitle) formModalTitle.textContent = "Editar Materia";

    const dataset = editBtn.dataset || {};
    if (hiddenSiglaOriginal) hiddenSiglaOriginal.value = dataset.sigla || "";
    if (inputSigla) {
      inputSigla.value = dataset.sigla || "";
      inputSigla.readOnly = true; // No se puede cambiar la sigla al editar
    }
    if (inputNombre) inputNombre.value = dataset.nombre || "";
    if (inputSemestre) inputSemestre.value = dataset.semestre || "";
    if (inputCargaHoraria) inputCargaHoraria.value = dataset.carga || "";

    if (materiaFormModal) materiaFormModal.classList.remove("hidden");
    document.documentElement.classList.add("overflow-hidden");
  });

  // Botones "Cancelar"
  function closeFormModal() {
    if (materiaFormModal) materiaFormModal.classList.add("hidden");
    document.documentElement.classList.remove("overflow-hidden");
  }
  if (btnCancelForm) btnCancelForm.addEventListener("click", closeFormModal);
  if (btnCancelFormX) btnCancelFormX.addEventListener("click", closeFormModal);

  // Envío del formulario (SUBMIT)
  if (materiaForm) {
    materiaForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      if (!btnSaveForm) return;
      btnSaveForm.disabled = true;
      const origText = btnSaveForm.textContent;
      btnSaveForm.textContent = "Guardando...";

      // Recoger datos del formulario
      const formData = new FormData(materiaForm);
      const data = Object.fromEntries(formData.entries());
      const siglaOriginal = hiddenSiglaOriginal
        ? hiddenSiglaOriginal.value
        : "";
      const isEditing = siglaOriginal !== "";

      // Si es creación, removemos sigla_original
      if (!isEditing) {
        delete data.sigla_original;
      }

      // Validaciones
      if (!data.sigla || data.sigla.trim() === "") {
        alert("La sigla es obligatoria");
        btnSaveForm.disabled = false;
        btnSaveForm.textContent = origText;
        return;
      }

      if (!data.nombre || data.nombre.trim() === "") {
        alert("El nombre de la materia es obligatorio");
        btnSaveForm.disabled = false;
        btnSaveForm.textContent = origText;
        return;
      }

      if (!data.semestre || parseInt(data.semestre) < 1) {
        alert("El semestre debe ser mayor a 0");
        btnSaveForm.disabled = false;
        btnSaveForm.textContent = origText;
        return;
      }

      if (!data.carga_horaria || parseInt(data.carga_horaria) < 1) {
        alert("La carga horaria debe ser mayor a 0");
        btnSaveForm.disabled = false;
        btnSaveForm.textContent = origText;
        return;
      }

      // Define el endpoint
      const url = isEditing
        ? `/admin/materias/update`
        : `/admin/materias/create`;

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
              (isEditing
                ? "Materia actualizada con éxito."
                : "Materia creada con éxito.")
          );
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
        alert("Error de conexión. No se pudo guardar la materia.");
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
  const deleteMateriaNombre = document.getElementById("delete-materia-nombre");
  let materiaSiglaToDelete = null;

  document.querySelectorAll(".btn-delete").forEach((button) => {
    button.addEventListener("click", (e) => {
      materiaSiglaToDelete = e.currentTarget.dataset.sigla;
      const materiaNombre = e.currentTarget.dataset.nombre;
      deleteMateriaNombre.textContent = materiaNombre;
      if (deleteModal) deleteModal.classList.remove("hidden");
      document.documentElement.classList.add("overflow-hidden");
    });
  });

  if (btnCancelDelete) {
    btnCancelDelete.addEventListener("click", () => {
      deleteModal.classList.add("hidden");
      materiaSiglaToDelete = null;
      document.documentElement.classList.remove("overflow-hidden");
    });
  }

  if (btnConfirmDelete) {
    btnConfirmDelete.addEventListener("click", async () => {
      if (!materiaSiglaToDelete) return;

      btnConfirmDelete.disabled = true;
      btnConfirmDelete.textContent = "Eliminando...";

      try {
        const response = await fetch("/admin/materias/delete", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
          },
          body: JSON.stringify({ sigla: materiaSiglaToDelete }),
        });

        const result = await response.json().catch(() => ({}));

        if (response.ok) {
          alert(result.message || "Materia eliminada con éxito.");
          window.location.reload();
        } else {
          alert(
            result.error ||
              result.message ||
              "Error al eliminar la materia. Inténtalo de nuevo."
          );
        }
      } catch (error) {
        console.error("Error de red:", error);
        alert("Error de red al intentar eliminar la materia.");
      } finally {
        deleteModal.classList.add("hidden");
        btnConfirmDelete.disabled = false;
        btnConfirmDelete.textContent = "Sí, eliminar";
        materiaSiglaToDelete = null;
        document.documentElement.classList.remove("overflow-hidden");
      }
    });
  }
  
  // --- C. Lógica de Asignación de Grupos ---
  const assignGroupsModal = document.getElementById("assign-groups-modal");
  const assignGroupsForm = document.getElementById("assign-groups-form");
  const btnCancelAssign = document.getElementById("btn-cancel-assign");
  const btnCancelAssignX = document.getElementById("btn-cancel-assign-x");
  const btnSaveAssign = document.getElementById("btn-save-assign");
  const assignGroupsSigla = document.getElementById("assign-groups-sigla");
  const assignGroupsMateriaNombre = document.getElementById("assign-groups-materia-nombre");
  const gruposLista = document.getElementById("grupos-lista");
  const gruposLoading = document.getElementById("grupos-loading");
  const gruposEmpty = document.getElementById("grupos-empty");
  const gruposSelectedCount = document.getElementById("grupos-selected-count");

  let allGrupos = []; // Cache de todos los grupos

  // Función para cargar todos los grupos disponibles
  async function loadAllGrupos() {
    if (allGrupos.length > 0) {
      return allGrupos; // Usar cache si ya existe
    }

    try {
      const response = await fetch("/admin/materias/get-grupos", {
        method: "GET",
        headers: {
          "X-CSRF-TOKEN": csrfToken,
          "X-Requested-With": "XMLHttpRequest",
        },
      });

      if (response.ok) {
        const data = await response.json();
        allGrupos = data;
        return allGrupos;
      } else {
        console.error("Error al cargar grupos");
        return [];
      }
    } catch (error) {
      console.error("Error de red al cargar grupos:", error);
      return [];
    }
  }

  // Función para cargar grupos asignados a una materia
  async function loadGruposAsignados(siglaMateria) {
    try {
      const response = await fetch(
        `/admin/materias/get-grupos-asignados?sigla_materia=${encodeURIComponent(
          siglaMateria
        )}`,
        {
          method: "GET",
          headers: {
            "X-CSRF-TOKEN": csrfToken,
            "X-Requested-With": "XMLHttpRequest",
          },
        }
      );

      if (response.ok) {
        const data = await response.json();
        return Array.isArray(data) ? data : [];
      } else {
        console.error("Error al cargar grupos asignados");
        return [];
      }
    } catch (error) {
      console.error("Error de red al cargar grupos asignados:", error);
      return [];
    }
  }

  // Función para renderizar la lista de grupos con checkboxes
  function renderGruposCheckboxes(grupos, gruposAsignados = []) {
    gruposLista.innerHTML = "";

    if (grupos.length === 0) {
      gruposEmpty.classList.remove("hidden");
      gruposLista.classList.add("hidden");
      return;
    }

    gruposEmpty.classList.add("hidden");
    gruposLista.classList.remove("hidden");

    grupos.forEach((grupo) => {
      const siglaGrupo = grupo.sigla;
      const isChecked = gruposAsignados.includes(siglaGrupo);

      const checkboxDiv = document.createElement("div");
      checkboxDiv.className = "flex items-center";

      const checkbox = document.createElement("input");
      checkbox.type = "checkbox";
      checkbox.id = `grupo-${siglaGrupo}`;
      checkbox.name = "grupos[]";
      checkbox.value = siglaGrupo;
      checkbox.checked = isChecked;
      checkbox.className =
        "w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer grupo-checkbox";

      const label = document.createElement("label");
      label.htmlFor = `grupo-${siglaGrupo}`;
      label.textContent = siglaGrupo;
      label.className =
        "ml-2 text-sm font-medium text-gray-700 cursor-pointer select-none";

      checkboxDiv.appendChild(checkbox);
      checkboxDiv.appendChild(label);
      gruposLista.appendChild(checkboxDiv);
    });

    updateSelectedCount();
  }

  // Actualizar contador de grupos seleccionados
  function updateSelectedCount() {
    const checkboxes = document.querySelectorAll(".grupo-checkbox:checked");
    if (gruposSelectedCount) {
      gruposSelectedCount.textContent = checkboxes.length;
    }
  }

  // Delegación de eventos para actualizar el contador cuando cambian los checkboxes
  if (gruposLista) {
    gruposLista.addEventListener("change", (e) => {
      if (e.target.classList.contains("grupo-checkbox")) {
        updateSelectedCount();
      }
    });
  }

  // Botones "Asignar Grupos" (delegado)
  document.addEventListener("click", async (e) => {
    const assignBtn =
      e.target.closest && e.target.closest(".btn-assign-groups");
    if (!assignBtn) return;

    const siglaMateria = assignBtn.dataset.sigla;
    const nombreMateria = assignBtn.dataset.nombre;

    if (!siglaMateria) {
      alert("No se pudo obtener la sigla de la materia");
      return;
    }

    // Configurar modal
    if (assignGroupsSigla) assignGroupsSigla.value = siglaMateria;
    if (assignGroupsMateriaNombre)
      assignGroupsMateriaNombre.textContent = `${siglaMateria} - ${nombreMateria}`;

    // Mostrar loading
    gruposLoading.classList.remove("hidden");
    gruposLista.classList.add("hidden");
    gruposEmpty.classList.add("hidden");

    // Abrir modal
    if (assignGroupsModal) assignGroupsModal.classList.remove("hidden");
    document.documentElement.classList.add("overflow-hidden");

    // Cargar datos
    try {
      const [grupos, gruposAsignados] = await Promise.all([
        loadAllGrupos(),
        loadGruposAsignados(siglaMateria),
      ]);

      gruposLoading.classList.add("hidden");
      renderGruposCheckboxes(grupos, gruposAsignados);
    } catch (error) {
      console.error("Error al cargar datos:", error);
      gruposLoading.classList.add("hidden");
      gruposEmpty.classList.remove("hidden");
    }
  });

  // Cerrar modal de asignación
  function closeAssignModal() {
    if (assignGroupsModal) assignGroupsModal.classList.add("hidden");
    document.documentElement.classList.remove("overflow-hidden");
    if (gruposLista) gruposLista.innerHTML = "";
  }

  if (btnCancelAssign)
    btnCancelAssign.addEventListener("click", closeAssignModal);
  if (btnCancelAssignX)
    btnCancelAssignX.addEventListener("click", closeAssignModal);

  // Envío del formulario de asignación
  if (assignGroupsForm) {
    assignGroupsForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      if (!btnSaveAssign) return;
      btnSaveAssign.disabled = true;
      const origText = btnSaveAssign.textContent;
      btnSaveAssign.textContent = "Guardando...";

      const siglaMateria = assignGroupsSigla ? assignGroupsSigla.value : "";
      const checkboxes = document.querySelectorAll(".grupo-checkbox:checked");
      const gruposSeleccionados = Array.from(checkboxes).map((cb) => cb.value);

      if (!siglaMateria) {
        alert("Error: No se pudo obtener la sigla de la materia");
        btnSaveAssign.disabled = false;
        btnSaveAssign.textContent = origText;
        return;
      }

      try {
        const response = await fetch("/admin/materias/asignar-grupos", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify({
            sigla_materia: siglaMateria,
            grupos: gruposSeleccionados,
          }),
        });

        const result = await response.json().catch(() => ({}));

        if (response.ok) {
          alert(
            result.message || "Grupos asignados exitosamente a la materia."
          );
          window.location.reload();
        } else {
          alert(
            result.message || "Error al asignar grupos. Inténtalo de nuevo."
          );
        }
      } catch (error) {
        console.error("Error de red:", error);
        alert("Error de conexión. No se pudo asignar los grupos.");
      } finally {
        btnSaveAssign.disabled = false;
        btnSaveAssign.textContent = origText;
      }
    });
  }
});
