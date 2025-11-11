document.addEventListener('DOMContentLoaded', () => {
  console.log('mod_docencia.js cargado correctamente.');

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
  // TARJETAS DE NAVEGACIÓN
  // -------------------------------
  const cardAsistencia = document.getElementById('card-asistencia');
  const cardLicencia = document.getElementById('card-licencia');

  if (cardAsistencia) {
    cardAsistencia.addEventListener('click', () => {
      window.location.href = '/docen/asistencia';
    });
  }

  if (cardLicencia) {
    cardLicencia.addEventListener('click', () => {
      window.location.href = '/docencia/licencia';
    });
  }

  // -------------------------------
  // EFECTO SUAVE AL CARGAR
  // -------------------------------
  document.body.classList.add('opacity-0');
  setTimeout(() => {
    document.body.classList.remove('opacity-0');
    document.body.classList.add('transition-opacity', 'duration-500', 'opacity-100');
  }, 50);
});
