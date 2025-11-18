<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio — Plataforma Universitaria INF342</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    body { font-family: 'Inter', sans-serif; }
  </style>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'navy': {
              900: '#0f2942',
              800: '#1e3a5f',
              700: '#2c5f8d',
            },
            'gold': {
              500: '#c9a961',
              600: '#b8974f',
            }
          }
        }
      }
    }
  </script>
</head>

<body class="bg-slate-100 text-slate-800 min-h-screen flex flex-col">

  <!-- Barra superior corporativa -->
  <header class="bg-navy-900 border-b-4 border-gold-500 sticky top-0 z-40 shadow-lg">
    <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-gold-500 flex items-center justify-center font-black text-navy-900 text-lg border-2 border-white">
          FI
        </div>
        <div>
          <h1 class="text-lg md:text-xl font-bold text-white tracking-wide uppercase">
            Plataforma Universitaria
          </h1>
          <p class="text-xs text-slate-300 uppercase tracking-widest">Sistema de Gestión FICCT</p>
        </div>
      </div>

      <div class="flex items-center gap-4">
        <div class="hidden sm:block text-right">
          <p class="font-bold text-white"><?php echo e($user['nomb_comp']); ?></p>
          <p class="text-xs text-gold-500 uppercase tracking-wider font-semibold"><?php echo e(ucfirst($user['rol'])); ?></p>
        </div>

        <!-- Avatar corporativo -->
        <div id="user-avatar"
             class="w-11 h-11 bg-gold-500 text-navy-900 flex items-center justify-center font-black text-lg border-2 border-white shadow-md cursor-pointer select-none hover:bg-gold-600 transition-all">
          <?php echo e(strtoupper(substr($user['nomb_comp'],0,1))); ?>

        </div>

        <!-- Logout corporativo -->
        <form action="/logout" method="POST">
          <?php echo csrf_field(); ?>
          <button type="submit"
                  class="text-sm bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 font-bold uppercase tracking-wide transition-all border-l-4 border-gold-500 hover:border-gold-400">
            Cerrar sesión
          </button>
        </form>
      </div>
    </div>
  </header>

  <!-- Panel lateral de usuario corporativo -->
  <aside id="user-aside"
         class="hidden fixed top-20 right-4 w-72 bg-white shadow-2xl border-2 border-slate-300 z-50 transition-all duration-300 opacity-0 scale-95 origin-top-right">
    <div class="bg-navy-900 px-5 py-4 border-b-4 border-gold-500">
      <div class="flex items-center gap-3">
        <div class="w-12 h-12 bg-gold-500 text-navy-900 flex items-center justify-center font-black text-xl border-2 border-white shadow-md">
          <?php echo e(strtoupper(substr($user['nomb_comp'],0,1))); ?>

        </div>
        <div>
          <p class="font-bold text-white leading-tight"><?php echo e($user['nomb_comp']); ?></p>
          <span class="text-xs px-2 py-1 bg-gold-500 text-navy-900 font-bold uppercase tracking-wider inline-block mt-1">
            <?php echo e(ucfirst($user['rol'])); ?>

          </span>
        </div>
      </div>
    </div>
    <div class="p-5 text-sm">
      <div class="space-y-3 bg-slate-50 p-4 border border-slate-200">
        <div class="flex justify-between">
          <span class="font-bold text-slate-600 uppercase text-xs tracking-wider">CI:</span>
          <span class="text-navy-900 font-semibold"><?php echo e($user['ci']); ?></span>
        </div>
        <div class="flex justify-between">
          <span class="font-bold text-slate-600 uppercase text-xs tracking-wider">Correo:</span>
          <span class="text-navy-900 font-semibold"><?php echo e($user['correo'] ?? '—'); ?></span>
        </div>
        <div class="flex justify-between">
          <span class="font-bold text-slate-600 uppercase text-xs tracking-wider">Teléfono:</span>
          <span class="text-navy-900 font-semibold"><?php echo e($user['tel'] ?? '—'); ?></span>
        </div>
      </div>
      <div class="mt-4">
        <a href="/perfil"
           class="block w-full text-center bg-navy-900 hover:bg-navy-800 text-white px-4 py-2.5 font-bold uppercase tracking-wide transition-all border-b-4 border-navy-800 hover:border-gold-500">
          Ver perfil completo
        </a>
      </div>
    </div>
  </aside>

  <!-- Contenido principal -->
  <main class="flex-1 max-w-7xl mx-auto w-full py-8 px-6">

    <!-- Encabezado corporativo -->
    <div class="bg-white border-l-4 border-gold-500 shadow-md mb-8 px-6 py-5">
      <div class="flex flex-col md:flex-row justify-between md:items-center">
        <div>
          <h2 class="text-3xl font-black text-navy-900 mb-1 uppercase tracking-tight">Panel Principal</h2>
          <p class="text-slate-600 text-sm font-semibold uppercase tracking-wide">Gestión Académica y Control Docente</p>
        </div>
        <div id="clock" class="text-sm text-navy-800 font-bold mt-3 md:mt-0 bg-slate-100 px-4 py-2 border border-slate-300"></div>
      </div>
    </div>

    <!-- Tarjetas corporativas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

      <!-- Datos personales corporativo -->
      <div class="bg-white p-6 border-2 border-slate-300 shadow-lg hover:shadow-xl transition-all">
        <div class="border-b-2 border-gold-500 pb-3 mb-4">
          <h3 class="text-base font-black text-navy-900 uppercase tracking-wide">Datos Personales</h3>
        </div>
        <ul class="text-sm space-y-2.5">
          <li class="flex justify-between border-b border-slate-200 pb-2">
            <span class="font-bold text-slate-600 uppercase text-xs">CI:</span>
            <span class="text-navy-900 font-semibold"><?php echo e($user['ci']); ?></span>
          </li>
          <li class="flex justify-between border-b border-slate-200 pb-2">
            <span class="font-bold text-slate-600 uppercase text-xs">Correo:</span>
            <span class="text-navy-900 font-semibold"><?php echo e($user['correo'] ?? '—'); ?></span>
          </li>
          <li class="flex justify-between border-b border-slate-200 pb-2">
            <span class="font-bold text-slate-600 uppercase text-xs">Teléfono:</span>
            <span class="text-navy-900 font-semibold"><?php echo e($user['tel'] ?? '—'); ?></span>
          </li>
          <li class="flex justify-between">
            <span class="font-bold text-slate-600 uppercase text-xs">Rol:</span>
            <span class="text-navy-900 font-black uppercase"><?php echo e(ucfirst($user['rol'])); ?></span>
          </li>
        </ul>
      </div>

      <?php $rol = strtolower($user['rol']); ?>

      <!-- ADMIN -->
      <?php if($rol === 'admin'): ?>
        <div id="admin-card"
             class="bg-white p-6 border-2 border-navy-800 shadow-lg hover:shadow-xl hover:border-gold-500 transition-all cursor-pointer">
          <div class="border-b-2 border-gold-500 pb-3 mb-4">
            <h3 class="text-base font-black text-navy-900 uppercase tracking-wide">Módulo de Administración</h3>
          </div>
          <p class="text-sm text-slate-700 mb-5 leading-relaxed font-medium">
            Accede al módulo administrativo para gestionar usuarios, docentes, materias, etc.
          </p>
          <button id="btn-mod-adm"
                  class="w-full text-center py-3 bg-navy-900 hover:bg-navy-800 text-white font-bold uppercase tracking-wide transition-all border-b-4 border-navy-800 hover:border-gold-500">
            Ingresar al Módulo
          </button>
        </div>

        <div id="import-users-card"
             class="bg-white p-6 border-2 border-navy-800 shadow-lg hover:shadow-xl hover:border-gold-500 transition-all cursor-pointer">
          <div class="border-b-2 border-gold-500 pb-3 mb-4">
            <h3 class="text-base font-black text-navy-900 uppercase tracking-wide">Módulo Reportes</h3>
          </div>
          <p class="text-sm text-slate-700 mb-5 leading-relaxed font-medium">
            Accede a los reportes que se podrán descargar desde este módulo.
          </p>
          <button id="btn-mod-report"
                  class="w-full text-center py-3 bg-navy-900 hover:bg-navy-800 text-white font-bold uppercase tracking-wide transition-all border-b-4 border-navy-800 hover:border-gold-500">
            Ingresar al Módulo
          </button>
        </div>

        <div id="import-users-card"
             class="bg-white p-6 border-2 border-navy-800 shadow-lg hover:shadow-xl hover:border-gold-500 transition-all cursor-pointer">
          <div class="border-b-2 border-gold-500 pb-3 mb-4">
            <h3 class="text-base font-black text-navy-900 uppercase tracking-wide">Registro Masivo de Usuarios</h3>
          </div>
          <p class="text-sm text-slate-700 mb-5 leading-relaxed font-medium">
            Permite cargar nuevos usuarios al sistema mediante archivos
            <span class="font-black text-navy-900">.xlsx</span> o <span class="font-black text-navy-900">.csv</span>.
          </p>
          <button id="btn-import-users"
                  class="w-full text-center py-3 bg-navy-900 hover:bg-navy-800 text-white font-bold uppercase tracking-wide transition-all border-b-4 border-navy-800 hover:border-gold-500">
            Ingresar al Módulo
          </button>
        </div>
      <?php elseif($rol === 'autoridad'): ?>
        <div class="bg-slate-50 border-l-4 border-navy-700 p-6 shadow-lg hover:shadow-xl transition-all">
          <h3 class="text-base font-black text-navy-900 mb-4 uppercase tracking-wide">Panel de Autoridad</h3>
          <ul class="text-sm text-slate-700 space-y-2.5 leading-relaxed font-medium">
            <li class="flex items-start gap-2">
              <span class="text-gold-500 font-black">▪</span>
              <span>Estadísticas de asistencia y desempeño</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="text-gold-500 font-black">▪</span>
              <span>Reportes comparativos por docente</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="text-gold-500 font-black">▪</span>
              <span>Monitoreo de horarios y aulas</span>
            </li>
          </ul>
        </div>
      <?php elseif($rol === 'docente'): ?>
        <div class="bg-slate-50 border-l-4 border-navy-700 p-6 shadow-lg hover:shadow-xl transition-all">
          <h3 class="text-base font-black text-navy-900 mb-4 uppercase tracking-wide">Panel Docente</h3>
          <ul class="text-sm text-slate-700 space-y-2.5 leading-relaxed font-medium">
            <li class="flex items-start gap-2">
              <span class="text-gold-500 font-black">▪</span>
              <span>Materias y grupos asignados</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="text-gold-500 font-black">▪</span>
              <span>Registro de asistencia y licencias</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="text-gold-500 font-black">▪</span>
              <span>Consulta de horarios académicos</span>
            </li>
          </ul>
        </div>
      <?php elseif($rol === 'administrativo'): ?>
        <div class="bg-slate-50 border-l-4 border-navy-700 p-6 shadow-lg hover:shadow-xl transition-all">
          <h3 class="text-base font-black text-navy-900 mb-4 uppercase tracking-wide">Panel Administrativo</h3>
          <ul class="text-sm text-slate-700 space-y-2.5 leading-relaxed font-medium">
            <li class="flex items-start gap-2">
              <span class="text-gold-500 font-black">▪</span>
              <span>Verificación de registros docentes</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="text-gold-500 font-black">▪</span>
              <span>Control de usuarios y documentación</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="text-gold-500 font-black">▪</span>
              <span>Gestión de aulas y materiales</span>
            </li>
          </ul>
        </div>
      <?php endif; ?>

      <?php if($rol == 'admin' or $rol=='docente'): ?>
        <div id="import-users-card"
             class="bg-white p-6 border-2 border-navy-800 shadow-lg hover:shadow-xl hover:border-gold-500 transition-all cursor-pointer">
          <div class="border-b-2 border-gold-500 pb-3 mb-4">
            <h3 class="text-base font-black text-navy-900 uppercase tracking-wide">Módulo de Docencia</h3>
          </div>
          <p class="text-sm text-slate-700 mb-5 leading-relaxed font-medium">
            Accede al módulo docencia para gestionar asistencias, licencias, consulta de horarios, etc.
          </p>
          <button id="btn-mod-docen"
                  class="w-full text-center py-3 bg-navy-900 hover:bg-navy-800 text-white font-bold uppercase tracking-wide transition-all border-b-4 border-navy-800 hover:border-gold-500">
            Ingresar al Módulo
          </button>
        </div>
      <?php endif; ?>

      <!-- Avisos corporativos -->
      <div class="bg-white p-6 border-2 border-slate-300 shadow-lg hover:shadow-xl transition-all">
        <div class="border-b-2 border-gold-500 pb-3 mb-4">
          <h3 class="text-base font-black text-navy-900 uppercase tracking-wide">Avisos Institucionales</h3>
        </div>
        <ul id="news-list" class="text-sm text-slate-700 space-y-2.5 leading-relaxed font-medium">
          <li class="flex items-start gap-2">
            <span class="text-gold-500 font-black">▪</span>
            <span>Nueva gestión académica activa: <span class="font-black text-navy-900">2025-I</span></span>
          </li>
          <li class="flex items-start gap-2">
            <span class="text-gold-500 font-black">▪</span>
            <span>Se habilitó el registro de asistencia docente.</span>
          </li>
          <li class="flex items-start gap-2">
            <span class="text-gold-500 font-black">▪</span>
            <span>Actualización en reportes de aula y horario.</span>
          </li>
        </ul>
      </div>
    </div>
  </main>

  <!-- Footer corporativo -->
  <footer class="text-center py-5 text-xs bg-navy-900 text-slate-300 border-t-4 border-gold-500 mt-12">
    <p class="font-bold uppercase tracking-widest">© <?php echo e(date('Y')); ?> Grupo 32 — UAGRM | INF342 - SA</p>
  </footer>

  <script src="<?php echo e(asset('static/scripts/index.js')); ?>"></script>
</body>
</html>
<?php /**PATH C:\Users\diego\OneDrive\Escritorio\exa2_inf342\app\templates/index.blade.php ENDPATH**/ ?>