document.addEventListener('DOMContentLoaded', () => {

  console.log('DOM CARGADO EXITOSAMENTE.');

  // --- RELOJ ---
  const clock = document.getElementById('clock');
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

  //BOTONES MODULOS
  const importBtn = document.getElementById('btn-import-users');
  const mod_adm=document.getElementById('btn-mod-adm');
  
  importBtn.addEventListener('click', () => 
  {
    window.location.href = '/admin/import-users';
  });
  
  mod_adm.addEventListener('click',()=>
  {
    window.location.href='/admin/mod-adm';
  });

  // --- AVATAR & PANEL USUARIO ---
  const avatar = document.getElementById('user-avatar');
  const aside = document.getElementById('user-aside');
  let hoverTimeout;
  let isVisible = false;

  if (avatar && aside) {

    // Mostrar / ocultar con click (para móviles)
    avatar.addEventListener('click', (e) => {
      e.stopPropagation();
      isVisible = !isVisible;
      aside.classList.toggle('hidden', !isVisible);
      aside.classList.toggle('opacity-0', !isVisible);
      aside.classList.toggle('scale-95', !isVisible);
    });

    // Cerrar al hacer click fuera (móviles)
    document.addEventListener('click', (e) => {
      if (isVisible && !aside.contains(e.target) && e.target !== avatar) {
        aside.classList.add('hidden', 'opacity-0', 'scale-95');
        isVisible = false;
      }
    });

    // Hover (para escritorio)
    avatar.addEventListener('mouseenter', () => {
      if (window.innerWidth >= 768) {
        clearTimeout(hoverTimeout);
        aside.classList.remove('hidden');
        setTimeout(() => {
          aside.classList.add('opacity-100', 'scale-100');
          aside.classList.remove('opacity-0', 'scale-95');
        }, 10);
      }
    });

    avatar.addEventListener('mouseleave', () => {
      if (window.innerWidth >= 768) {
        hoverTimeout = setTimeout(() => {
          aside.classList.add('opacity-0', 'scale-95');
          aside.classList.remove('opacity-100', 'scale-100');
          setTimeout(() => aside.classList.add('hidden'), 150);
        }, 200);
      }
    });

    aside.addEventListener('mouseenter', () => clearTimeout(hoverTimeout));
    aside.addEventListener('mouseleave', () => {
      if (window.innerWidth >= 768) {
        hoverTimeout = setTimeout(() => {
          aside.classList.add('opacity-0', 'scale-95');
          aside.classList.remove('opacity-100', 'scale-100');
          setTimeout(() => aside.classList.add('hidden'), 150);
        }, 200);
      }
    });
  }
});
