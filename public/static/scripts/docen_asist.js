document.addEventListener('DOMContentLoaded', () => {

  console.log('docen_asist.js cargado correctamente.');

  // -------------------------------
  // RELOJ EN TIEMPO REAL
  // -------------------------------
  const clock = document.getElementById('clock');
  if (clock) {
    const updateClock = () => {
      const now = new Date();
      clock.textContent = now.toLocaleString('es-BO', {
        weekday: 'long',
        hour: '2-digit',
        minute: '2-digit'
      });
    };
    updateClock();
    setInterval(updateClock, 60000);
  }

  // -------------------------------
  // CONTROL DEL PANEL LATERAL
  // -------------------------------
  const toggleBtn = document.getElementById('menu-toggle');
  const sidebar = document.getElementById('docencia-sidebar');
  const overlay = document.getElementById('sidebar-overlay');

  if (toggleBtn && sidebar && overlay) {
    toggleBtn.addEventListener('click', () => {
      const isHidden = sidebar.classList.contains('-translate-x-full');
      sidebar.classList.toggle('-translate-x-full', !isHidden);
      overlay.classList.toggle('hidden', !isHidden);
    });

    overlay.addEventListener('click', () => {
      sidebar.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
    });
  }

  // -------------------------------
  // TARJETAS DE MATERIAS Y MODAL
  // -------------------------------
  const cards = document.querySelectorAll('.materia-card');
  const modal = document.getElementById('modal-clases');
  const modalTitulo = document.getElementById('modal-titulo');
  const modalContenido = document.getElementById('modal-contenido');
  const cerrarModal = document.getElementById('cerrar-modal');
  const formAsistencia = document.getElementById('form-asistencia');

  if (cards.length > 0 && modal) {

    cards.forEach(card => {
      card.addEventListener('click', () => {
        const materia = card.dataset.materia;
        const clases = JSON.parse(card.dataset.clases);

        modalTitulo.textContent = materia;
        modalContenido.innerHTML = '';
        formAsistencia.classList.add('hidden');

        clases.forEach(c => {
          const fila = document.createElement('div');
          fila.className = 'flex justify-between items-center border-b border-gray-100 py-2';
          fila.innerHTML = `
            <div>
              <p class="text-sm font-medium text-gray-800">Día: ${c.dia}</p>
              <p class="text-xs text-gray-600">Hora: ${c.hora_inicio} - ${c.hora_final}</p>
              <p class="text-xs text-gray-500">Grupo: ${c.grupo}</p>
            </div>
            <button class="btn-marcar bg-sky-600 hover:bg-sky-700 text-white text-xs font-medium px-3 py-1.5 rounded-md shadow-sm transition"
                    data-id="${c.id_clase || ''}">
              Marcar
            </button>
          `;
          modalContenido.appendChild(fila);
        });

        modal.classList.remove('hidden');
        modal.classList.add('flex');
      });
    });

    cerrarModal.addEventListener('click', () => {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    });

    modal.addEventListener('click', e => {
      if (e.target === modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }
    });

    // Mostrar formulario al marcar
    document.addEventListener('click', e => {
      if (e.target.classList.contains('btn-marcar')) {
        const idClase = e.target.dataset.id;
        console.log('Marcar asistencia para clase:', idClase);

        modalContenido.innerHTML = `<p class="text-sm text-gray-700 mb-3">Registrando asistencia para clase ID: <strong>${idClase}</strong></p>`;
        formAsistencia.classList.remove('hidden');
        formAsistencia.dataset.id = idClase;
      }
    });

    // Envío del formulario de asistencia
    formAsistencia.addEventListener('submit', async (e) => {
      e.preventDefault();

      const idClase = formAsistencia.dataset.id;
      const formData = new FormData(formAsistencia);
      formData.append('id_clase', idClase);

      try {
        const response = await fetch('/docen/asistencia/marcar', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: formData
        });

        const result = await response.json();
        console.log(result);

        if (result.CODE === 200) {
          alert('Asistencia registrada correctamente.');
          modal.classList.add('hidden');
        } else {
          alert('Error al registrar asistencia: ' + result.MESSAGE);
        }

      } catch (error) {
        console.error('Error al enviar asistencia:', error);
        alert('Ocurrió un error al registrar asistencia.');
      }
    });
  }

});
