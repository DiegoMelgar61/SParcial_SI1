document.addEventListener('DOMContentLoaded', () => {
  console.log('docen_asist.js cargado correctamente con validación precisa.');

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
  // PANEL LATERAL RESPONSIVE
  // -------------------------------
  const toggleBtn = document.getElementById('menu-toggle');
  const sidebar = document.getElementById('docencia-sidebar');
  const overlay = document.getElementById('sidebar-overlay');

  if (toggleBtn && sidebar && overlay) {
    toggleBtn.addEventListener('click', () => {
      const hidden = sidebar.classList.contains('-translate-x-full');
      sidebar.classList.toggle('-translate-x-full', !hidden);
      overlay.classList.toggle('hidden', !hidden);
    });
    overlay.addEventListener('click', () => {
      sidebar.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
    });
  }

  // -------------------------------
  // TARJETAS Y MODALES
  // -------------------------------
  const cards = document.querySelectorAll('.materia-card');
  const modal = document.getElementById('modal-clases');
  const modalTitulo = document.getElementById('modal-titulo');
  const modalContenido = document.getElementById('modal-contenido');
  const cerrarModal = document.getElementById('cerrar-modal');
  const modalOpciones = document.getElementById('modal-opciones');
  const cerrarOpciones = document.getElementById('cerrar-opciones');
  const btnFormulario = document.getElementById('btn-formulario');
  const btnQr = document.getElementById('btn-qr');
  const formAsistencia = document.getElementById('form-asistencia');

  let claseSeleccionada = null;

  // Mostrar listado de clases
  if (cards.length > 0 && modal) {
    cards.forEach(card => {
      card.addEventListener('click', () => {
        const materia = card.dataset.materia;
        const clases = JSON.parse(card.dataset.clases);
        modalTitulo.textContent = materia;
        modalContenido.innerHTML = '';
        formAsistencia?.classList.add('hidden');

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

    // Cerrar modal
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

    // -------------------------------
    // ABRIR SUBMODAL DE OPCIONES
    // -------------------------------
    document.addEventListener('click', e => {
      if (e.target.classList.contains('btn-marcar')) {
        claseSeleccionada = e.target.dataset.id;
        if (!claseSeleccionada) {
          alert('Esta clase no tiene un identificador válido.');
          return;
        }
        modal.classList.add('hidden');
        modalOpciones.classList.remove('hidden');
        modalOpciones.classList.add('flex');
      }
    });

    cerrarOpciones?.addEventListener('click', () => {
      modalOpciones.classList.add('hidden');
      modalOpciones.classList.remove('flex');
    });
    modalOpciones?.addEventListener('click', e => {
      if (e.target === modalOpciones) {
        modalOpciones.classList.add('hidden');
        modalOpciones.classList.remove('flex');
      }
    });

    // -------------------------------
    // OPCIÓN: FORMULARIO
    // -------------------------------
    btnFormulario?.addEventListener('click', () => {
      modalOpciones.classList.add('hidden');
      modal.classList.remove('hidden');
      modal.classList.add('flex');

      modalTitulo.textContent = 'Registro de asistencia';
      modalContenido.innerHTML = `
        <p class="text-sm text-gray-700 mb-3">Registrando asistencia para clase ID: <strong>${claseSeleccionada}</strong></p>
      `;
      formAsistencia.classList.remove('hidden');
      formAsistencia.dataset.id = claseSeleccionada;

      const claseData = Array.from(document.querySelectorAll('.materia-card'))
        .flatMap(card => JSON.parse(card.dataset.clases))
        .find(c => String(c.id_clase) === String(claseSeleccionada));

      if (!claseData) return;

      const ahora = new Date();
      const [h, m, s] = claseData.hora_inicio.split(':').map(Number);
      const horaInicio = new Date();
      horaInicio.setHours(h, m, s || 0, 0);
      const diffMin = Math.round((ahora - horaInicio) / 60000);

      let estado;
      if (diffMin >= -10 && diffMin <= 10) estado = 'Presente';
      else if (diffMin > 10 && diffMin <= 15) estado = 'Retraso';
      else estado = 'Ausente';

      const estadoSelect = formAsistencia.querySelector('select[name="estado"]');
      if (estadoSelect) {
        estadoSelect.value = estado;
        estadoSelect.disabled = true;
        estadoSelect.classList.add('bg-gray-100', 'cursor-not-allowed', 'text-gray-700');
      }

      console.log(`Estado calculado: ${estado} (diferencia ${diffMin} min)`);
    });

    // -------------------------------
    // ENVÍO DEL FORMULARIO
    // -------------------------------
    formAsistencia?.addEventListener('submit', async e => {
      e.preventDefault();

      const idClase = formAsistencia.dataset.id;
      const formData = new FormData(formAsistencia);
      formData.append('id_clase', idClase);

      const submitBtn = formAsistencia.querySelector('button[type="submit"]');
      const originalText = submitBtn.textContent;
      submitBtn.textContent = 'Registrando...';
      submitBtn.disabled = true;
      submitBtn.classList.add('opacity-70', 'cursor-not-allowed');

      try {
        const response = await fetch('/docen/asistencia/marcar', {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
          body: formData
        });

        const result = await response.json();
        if (result.success) {
          alert('Asistencia registrada correctamente.');
          modal.classList.add('hidden');
        } else {
          alert(result.message);
        }
      } catch (error) {
        console.error('Error al enviar asistencia:', error);
        alert('Error al registrar asistencia.');
      } finally {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
      }
    });


    // OPCIÓN: GENERAR QR
   
    btnQr?.addEventListener('click', async () => {
      if (!claseSeleccionada) {
        alert('No hay clase seleccionada');
        return;
      }

      modalOpciones.classList.add('hidden');
      modal.classList.remove('hidden');
      modal.classList.add('flex');

      modalTitulo.textContent = 'Generando código QR...';
      modalContenido.innerHTML = `
        <div class="flex items-center justify-center py-8">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-sky-600"></div>
        </div>
      `;

      try {
        const formData = new FormData();
        formData.append('id_clase', claseSeleccionada);

        const response = await fetch('/docen/asistencia/generar-qr', {
          method: 'POST',
          headers: { 
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
          },
          body: formData
        });

        const result = await response.json();

        if (result.success) {
          modalTitulo.textContent = 'Código QR generado';
          
          // Generar QR usando una librería externa (QRCode.js)
          modalContenido.innerHTML = `
            <div class="text-center">
              <p class="text-sm text-gray-700 mb-4">
                Escanea este código QR para registrar asistencia automáticamente
              </p>
              <div id="qrcode" class="flex justify-center mb-4"></div>
              <p class="text-xs text-gray-500 mb-2">
                <strong>Válido por:</strong> ${result.expira_en}
              </p>
              <p class="text-xs text-gray-400">
                Al escanear, la asistencia se registrará automáticamente como "Presente"<br>
                con la observación "Asistencia por QR"
              </p>
              <div class="mt-4 p-3 bg-sky-50 rounded-lg border border-sky-200">
                <p class="text-xs text-sky-800 font-mono break-all">${result.qr_url}</p>
              </div>
            </div>
          `;

          // Generar el QR visual usando QRCode.js
          // Nota: Asegúrate de incluir la librería en el HTML
          if (typeof QRCode !== 'undefined') {
            new QRCode(document.getElementById('qrcode'), {
              text: result.qr_url,
              width: 256,
              height: 256,
              colorDark: '#0369a1',
              colorLight: '#ffffff',
              correctLevel: QRCode.CorrectLevel.H
            });
          } else {
            // Fallback: usar API de Google Charts (deprecado pero funcional)
            const qrcodeDiv = document.getElementById('qrcode');
            qrcodeDiv.innerHTML = `
              <img src="https://api.qrserver.com/v1/create-qr-code/?size=256x256&data=${encodeURIComponent(result.qr_url)}" 
                   alt="Código QR" 
                   class="mx-auto shadow-lg rounded-lg border-4 border-white">
            `;
          }

        } else {
          modalTitulo.textContent = 'Error';
          modalContenido.innerHTML = `
            <div class="text-center py-8">
              <p class="text-red-600">${result.message}</p>
            </div>
          `;
        }

      } catch (error) {
        console.error('Error al generar QR:', error);
        modalTitulo.textContent = 'Error';
        modalContenido.innerHTML = `
          <div class="text-center py-8">
            <p class="text-red-600">Error al generar el código QR</p>
          </div>
        `;
      }
    });
  }
});
