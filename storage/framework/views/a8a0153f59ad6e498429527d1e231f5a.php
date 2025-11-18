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

    <?php $rol = strtolower($user['rol']); ?>

    <!-- MÓDULOS EN FORMA DE PAQUETES -->

    <?php if($rol === 'admin'): ?>
      <!-- Sección: Módulos Principales -->
      <div class="mb-6">
        <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest mb-4 border-l-4 border-gold-500 pl-3">Módulos Principales</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

          <!-- Paquete: Administración -->
          <div id="admin-card" class="group bg-white border-4 border-navy-900 shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 cursor-pointer overflow-hidden">
            <div class="bg-navy-900 p-6 text-center border-b-4 border-gold-500">
              <div class="w-20 h-20 mx-auto bg-gold-500 flex items-center justify-center text-5xl mb-3">
                ⚙️
              </div>
              <span class="inline-block bg-gold-500 text-navy-900 px-3 py-1 text-xs font-black uppercase tracking-wider">Sistema</span>
            </div>
            <div class="p-6">
              <h3 class="text-xl font-black text-navy-900 uppercase tracking-wide mb-3">Administración</h3>
              <p class="text-sm text-slate-700 mb-5 leading-relaxed">
                Gestión integral de usuarios, docentes, materias, grupos, aulas y horarios del sistema.
              </p>
              <button id="btn-mod-adm" class="w-full text-center py-3 bg-navy-900 hover:bg-navy-800 text-white font-bold uppercase tracking-wide transition-all border-b-4 border-navy-800 group-hover:border-gold-500">
                Acceder al Módulo
              </button>
            </div>
          </div>

          <!-- Paquete: Docencia -->
          <div id="docen-card" class="group bg-white border-4 border-gold-500 shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 cursor-pointer overflow-hidden">
            <div class="bg-gold-500 p-6 text-center border-b-4 border-navy-900">
              <div class="w-20 h-20 mx-auto bg-navy-900 flex items-center justify-center text-5xl mb-3">
                📚
              </div>
              <span class="inline-block bg-navy-900 text-gold-500 px-3 py-1 text-xs font-black uppercase tracking-wider">Académico</span>
            </div>
            <div class="p-6">
              <h3 class="text-xl font-black text-navy-900 uppercase tracking-wide mb-3">Docencia</h3>
              <p class="text-sm text-slate-700 mb-5 leading-relaxed">
                Registro de asistencias, gestión de licencias, consulta de horarios y materias asignadas.
              </p>
              <button id="btn-mod-docen" class="w-full text-center py-3 bg-gold-500 hover:bg-gold-600 text-navy-900 font-bold uppercase tracking-wide transition-all border-b-4 border-gold-600 group-hover:border-navy-900">
                Acceder al Módulo
              </button>
            </div>
          </div>

        </div>
      </div>

      <!-- Sección: Herramientas y Utilidades -->
      <div class="mb-6">
        <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest mb-4 border-l-4 border-gold-500 pl-3">Herramientas y Utilidades</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

          <!-- Paquete: Reportes -->
          <div id="report-card" class="group bg-navy-900 border-4 border-navy-900 shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 cursor-pointer">
            <div class="p-6 text-center">
              <div class="w-16 h-16 mx-auto bg-gold-500 flex items-center justify-center text-4xl mb-3 border-4 border-white">
                📊
              </div>
              <span class="inline-block bg-white text-navy-900 px-3 py-1 text-xs font-black uppercase tracking-wider mb-4">Reportes</span>
              <h3 class="text-lg font-black text-white uppercase tracking-wide mb-3">Generación de Reportes</h3>
              <p class="text-sm text-slate-300 mb-5 leading-relaxed">
                Descarga reportes de asistencias, licencias y estadísticas académicas.
              </p>
              <button id="btn-mod-report" class="w-full text-center py-2.5 bg-gold-500 hover:bg-gold-600 text-navy-900 font-bold uppercase tracking-wide transition-all border-b-4 border-gold-600">
                Acceder
              </button>
            </div>
          </div>

          <!-- Paquete: Importación Masiva -->
          <div id="import-card" class="group bg-white border-4 border-slate-400 shadow-lg hover:shadow-2xl hover:-translate-y-1 hover:border-gold-500 transition-all duration-300 cursor-pointer">
            <div class="p-6 text-center">
              <div class="w-16 h-16 mx-auto bg-navy-900 flex items-center justify-center text-4xl mb-3 border-4 border-gold-500">
                📥
              </div>
              <span class="inline-block bg-navy-900 text-gold-500 px-3 py-1 text-xs font-black uppercase tracking-wider mb-4">Importación</span>
              <h3 class="text-lg font-black text-navy-900 uppercase tracking-wide mb-3">Carga Masiva</h3>
              <p class="text-sm text-slate-700 mb-5 leading-relaxed">
                Importa usuarios mediante archivos <span class="font-black">.xlsx</span> o <span class="font-black">.csv</span>
              </p>
              <button id="btn-import-users" class="w-full text-center py-2.5 bg-navy-900 hover:bg-navy-800 text-white font-bold uppercase tracking-wide transition-all border-b-4 border-navy-800 group-hover:border-gold-500">
                Acceder
              </button>
            </div>
          </div>

          <!-- Paquete: Datos Personales (compacto) -->
          <div class="bg-white border-4 border-slate-300 shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="bg-slate-100 px-4 py-3 border-b-4 border-gold-500 text-center">
              <div class="w-12 h-12 mx-auto bg-navy-900 text-gold-500 flex items-center justify-center text-2xl font-black border-2 border-gold-500">
                <?php echo e(strtoupper(substr($user['nomb_comp'],0,1))); ?>

              </div>
            </div>
            <div class="p-4">
              <h3 class="text-center text-sm font-black text-navy-900 uppercase tracking-wide mb-3">Mi Información</h3>
              <ul class="text-xs space-y-2">
                <li class="flex justify-between border-b border-slate-200 pb-1.5">
                  <span class="font-bold text-slate-600 uppercase">CI:</span>
                  <span class="text-navy-900 font-semibold"><?php echo e($user['ci']); ?></span>
                </li>
                <li class="flex justify-between border-b border-slate-200 pb-1.5">
                  <span class="font-bold text-slate-600 uppercase">Correo:</span>
                  <span class="text-navy-900 font-semibold text-right break-all"><?php echo e($user['correo'] ?? '—'); ?></span>
                </li>
                <li class="flex justify-between border-b border-slate-200 pb-1.5">
                  <span class="font-bold text-slate-600 uppercase">Teléfono:</span>
                  <span class="text-navy-900 font-semibold"><?php echo e($user['tel'] ?? '—'); ?></span>
                </li>
                <li class="flex justify-between">
                  <span class="font-bold text-slate-600 uppercase">Rol:</span>
                  <span class="text-gold-500 font-black uppercase"><?php echo e(ucfirst($user['rol'])); ?></span>
                </li>
              </ul>
            </div>
          </div>

        </div>
      </div>

      <!-- Sección: Avisos -->
      <div class="mb-6">
        <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest mb-4 border-l-4 border-gold-500 pl-3">Avisos Institucionales</h3>
        <div class="bg-white border-l-4 border-gold-500 shadow-md p-6">
          <ul id="news-list" class="text-sm text-slate-700 space-y-3 leading-relaxed">
            <li class="flex items-start gap-3 p-3 bg-slate-50 border-l-2 border-navy-900">
              <span class="text-2xl">📌</span>
              <span>Nueva gestión académica activa: <span class="font-black text-navy-900">2025-I</span></span>
            </li>
            <li class="flex items-start gap-3 p-3 bg-slate-50 border-l-2 border-gold-500">
              <span class="text-2xl">✅</span>
              <span>Se habilitó el registro de asistencia docente.</span>
            </li>
            <li class="flex items-start gap-3 p-3 bg-slate-50 border-l-2 border-navy-900">
              <span class="text-2xl">🔄</span>
              <span>Actualización en reportes de aula y horario.</span>
            </li>
          </ul>
        </div>
      </div>

    <?php elseif($rol === 'docente'): ?>
      <!-- Vista Docente -->
      <div class="mb-6">
        <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest mb-4 border-l-4 border-gold-500 pl-3">Mi Módulo Principal</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

          <!-- Paquete: Docencia (destacado para docente) -->
          <div id="docen-card" class="group bg-white border-4 border-gold-500 shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 cursor-pointer overflow-hidden md:col-span-2">
            <div class="bg-gold-500 p-8 text-center border-b-4 border-navy-900">
              <div class="w-24 h-24 mx-auto bg-navy-900 flex items-center justify-center text-6xl mb-4">
                📚
              </div>
              <span class="inline-block bg-navy-900 text-gold-500 px-4 py-2 text-sm font-black uppercase tracking-wider">Académico</span>
            </div>
            <div class="p-8">
              <h3 class="text-2xl font-black text-navy-900 uppercase tracking-wide mb-4 text-center">Módulo de Docencia</h3>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="text-center p-4 bg-slate-50 border-l-4 border-navy-900">
                  <p class="text-xs uppercase font-bold text-slate-600 mb-1">Asistencias</p>
                  <p class="text-lg font-black text-navy-900">✓</p>
                </div>
                <div class="text-center p-4 bg-slate-50 border-l-4 border-gold-500">
                  <p class="text-xs uppercase font-bold text-slate-600 mb-1">Licencias</p>
                  <p class="text-lg font-black text-navy-900">📋</p>
                </div>
                <div class="text-center p-4 bg-slate-50 border-l-4 border-navy-900">
                  <p class="text-xs uppercase font-bold text-slate-600 mb-1">Horarios</p>
                  <p class="text-lg font-black text-navy-900">🕐</p>
                </div>
              </div>
              <button id="btn-mod-docen" class="w-full text-center py-4 bg-gold-500 hover:bg-gold-600 text-navy-900 font-bold uppercase tracking-wide transition-all border-b-4 border-gold-600 text-lg">
                Acceder al Módulo
              </button>
            </div>
          </div>

        </div>
      </div>

      <!-- Sección: Información -->
      <div class="mb-6">
        <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest mb-4 border-l-4 border-gold-500 pl-3">Mi Información</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

          <!-- Datos Personales -->
          <div class="bg-white border-4 border-slate-300 shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="bg-slate-100 px-6 py-4 border-b-4 border-gold-500">
              <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-navy-900 text-gold-500 flex items-center justify-center text-3xl font-black border-2 border-gold-500">
                  <?php echo e(strtoupper(substr($user['nomb_comp'],0,1))); ?>

                </div>
                <div>
                  <h3 class="text-base font-black text-navy-900 uppercase tracking-wide">Datos Personales</h3>
                  <p class="text-xs text-slate-600 uppercase"><?php echo e($user['nomb_comp']); ?></p>
                </div>
              </div>
            </div>
            <div class="p-6">
              <ul class="text-sm space-y-3">
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
                  <span class="text-gold-500 font-black uppercase"><?php echo e(ucfirst($user['rol'])); ?></span>
                </li>
              </ul>
            </div>
          </div>

          <!-- Avisos -->
          <div class="bg-white border-l-4 border-gold-500 shadow-lg p-6">
            <h3 class="text-base font-black text-navy-900 uppercase tracking-wide mb-4">Avisos Institucionales</h3>
            <ul id="news-list" class="text-sm text-slate-700 space-y-3 leading-relaxed">
              <li class="flex items-start gap-3 p-3 bg-slate-50 border-l-2 border-navy-900">
                <span class="text-2xl">📌</span>
                <span>Nueva gestión académica activa: <span class="font-black text-navy-900">2025-I</span></span>
              </li>
              <li class="flex items-start gap-3 p-3 bg-slate-50 border-l-2 border-gold-500">
                <span class="text-2xl">✅</span>
                <span>Se habilitó el registro de asistencia docente.</span>
              </li>
              <li class="flex items-start gap-3 p-3 bg-slate-50 border-l-2 border-navy-900">
                <span class="text-2xl">🔄</span>
                <span>Actualización en reportes de aula y horario.</span>
              </li>
            </ul>
          </div>

        </div>
      </div>

    <?php elseif($rol === 'autoridad'): ?>
      <!-- Vista Autoridad -->
      <div class="mb-6">
        <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest mb-4 border-l-4 border-gold-500 pl-3">Panel de Autoridad</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

          <div class="bg-white border-4 border-navy-900 shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="bg-navy-900 p-6 text-center border-b-4 border-gold-500">
              <div class="w-20 h-20 mx-auto bg-gold-500 flex items-center justify-center text-5xl mb-3">
                📈
              </div>
              <span class="inline-block bg-gold-500 text-navy-900 px-3 py-1 text-xs font-black uppercase tracking-wider">Supervisión</span>
            </div>
            <div class="p-6">
              <h3 class="text-xl font-black text-navy-900 mb-4 uppercase tracking-wide">Gestión de Autoridad</h3>
              <ul class="text-sm text-slate-700 space-y-3 leading-relaxed">
                <li class="flex items-start gap-2 p-2 bg-slate-50 border-l-2 border-gold-500">
                  <span class="text-gold-500 font-black">▪</span>
                  <span>Estadísticas de asistencia y desempeño</span>
                </li>
                <li class="flex items-start gap-2 p-2 bg-slate-50 border-l-2 border-navy-900">
                  <span class="text-gold-500 font-black">▪</span>
                  <span>Reportes comparativos por docente</span>
                </li>
                <li class="flex items-start gap-2 p-2 bg-slate-50 border-l-2 border-gold-500">
                  <span class="text-gold-500 font-black">▪</span>
                  <span>Monitoreo de horarios y aulas</span>
                </li>
              </ul>
            </div>
          </div>

          <!-- Datos Personales -->
          <div class="bg-white border-4 border-slate-300 shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="bg-slate-100 px-6 py-4 border-b-4 border-gold-500">
              <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-navy-900 text-gold-500 flex items-center justify-center text-3xl font-black border-2 border-gold-500">
                  <?php echo e(strtoupper(substr($user['nomb_comp'],0,1))); ?>

                </div>
                <div>
                  <h3 class="text-base font-black text-navy-900 uppercase tracking-wide">Datos Personales</h3>
                  <p class="text-xs text-slate-600 uppercase"><?php echo e($user['nomb_comp']); ?></p>
                </div>
              </div>
            </div>
            <div class="p-6">
              <ul class="text-sm space-y-3">
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
                  <span class="text-gold-500 font-black uppercase"><?php echo e(ucfirst($user['rol'])); ?></span>
                </li>
              </ul>
            </div>
          </div>

        </div>
      </div>

      <!-- Avisos -->
      <div class="mb-6">
        <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest mb-4 border-l-4 border-gold-500 pl-3">Avisos Institucionales</h3>
        <div class="bg-white border-l-4 border-gold-500 shadow-md p-6">
          <ul id="news-list" class="text-sm text-slate-700 space-y-3 leading-relaxed">
            <li class="flex items-start gap-3 p-3 bg-slate-50 border-l-2 border-navy-900">
              <span class="text-2xl">📌</span>
              <span>Nueva gestión académica activa: <span class="font-black text-navy-900">2025-I</span></span>
            </li>
            <li class="flex items-start gap-3 p-3 bg-slate-50 border-l-2 border-gold-500">
              <span class="text-2xl">✅</span>
              <span>Se habilitó el registro de asistencia docente.</span>
            </li>
            <li class="flex items-start gap-3 p-3 bg-slate-50 border-l-2 border-navy-900">
              <span class="text-2xl">🔄</span>
              <span>Actualización en reportes de aula y horario.</span>
            </li>
          </ul>
        </div>
      </div>

    <?php elseif($rol === 'administrativo'): ?>
      <!-- Vista Administrativo -->
      <div class="mb-6">
        <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest mb-4 border-l-4 border-gold-500 pl-3">Panel Administrativo</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

          <div class="bg-white border-4 border-gold-500 shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="bg-gold-500 p-6 text-center border-b-4 border-navy-900">
              <div class="w-20 h-20 mx-auto bg-navy-900 flex items-center justify-center text-5xl mb-3">
                📋
              </div>
              <span class="inline-block bg-navy-900 text-gold-500 px-3 py-1 text-xs font-black uppercase tracking-wider">Administración</span>
            </div>
            <div class="p-6">
              <h3 class="text-xl font-black text-navy-900 mb-4 uppercase tracking-wide">Gestión Administrativa</h3>
              <ul class="text-sm text-slate-700 space-y-3 leading-relaxed">
                <li class="flex items-start gap-2 p-2 bg-slate-50 border-l-2 border-navy-900">
                  <span class="text-gold-500 font-black">▪</span>
                  <span>Verificación de registros docentes</span>
                </li>
                <li class="flex items-start gap-2 p-2 bg-slate-50 border-l-2 border-gold-500">
                  <span class="text-gold-500 font-black">▪</span>
                  <span>Control de usuarios y documentación</span>
                </li>
                <li class="flex items-start gap-2 p-2 bg-slate-50 border-l-2 border-navy-900">
                  <span class="text-gold-500 font-black">▪</span>
                  <span>Gestión de aulas y materiales</span>
                </li>
              </ul>
            </div>
          </div>

          <!-- Datos Personales -->
          <div class="bg-white border-4 border-slate-300 shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="bg-slate-100 px-6 py-4 border-b-4 border-gold-500">
              <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-navy-900 text-gold-500 flex items-center justify-center text-3xl font-black border-2 border-gold-500">
                  <?php echo e(strtoupper(substr($user['nomb_comp'],0,1))); ?>

                </div>
                <div>
                  <h3 class="text-base font-black text-navy-900 uppercase tracking-wide">Datos Personales</h3>
                  <p class="text-xs text-slate-600 uppercase"><?php echo e($user['nomb_comp']); ?></p>
                </div>
              </div>
            </div>
            <div class="p-6">
              <ul class="text-sm space-y-3">
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
                  <span class="text-gold-500 font-black uppercase"><?php echo e(ucfirst($user['rol'])); ?></span>
                </li>
              </ul>
            </div>
          </div>

        </div>
      </div>

      <!-- Avisos -->
      <div class="mb-6">
        <h3 class="text-xs font-black text-navy-900 uppercase tracking-widest mb-4 border-l-4 border-gold-500 pl-3">Avisos Institucionales</h3>
        <div class="bg-white border-l-4 border-gold-500 shadow-md p-6">
          <ul id="news-list" class="text-sm text-slate-700 space-y-3 leading-relaxed">
            <li class="flex items-start gap-3 p-3 bg-slate-50 border-l-2 border-navy-900">
              <span class="text-2xl">📌</span>
              <span>Nueva gestión académica activa: <span class="font-black text-navy-900">2025-I</span></span>
            </li>
            <li class="flex items-start gap-3 p-3 bg-slate-50 border-l-2 border-gold-500">
              <span class="text-2xl">✅</span>
              <span>Se habilitó el registro de asistencia docente.</span>
            </li>
            <li class="flex items-start gap-3 p-3 bg-slate-50 border-l-2 border-navy-900">
              <span class="text-2xl">🔄</span>
              <span>Actualización en reportes de aula y horario.</span>
            </li>
          </ul>
        </div>
      </div>

    <?php endif; ?>

  </main>

  <!-- Footer corporativo -->
  <footer class="text-center py-5 text-xs bg-navy-900 text-slate-300 border-t-4 border-gold-500 mt-12">
    <p class="font-bold uppercase tracking-widest">© <?php echo e(date('Y')); ?> Grupo 32 — UAGRM | INF342 - SA</p>
  </footer>

  <script src="<?php echo e(asset('static/scripts/index.js')); ?>"></script>
</body>
</html>
<?php /**PATH C:\Users\diego\OneDrive\Escritorio\exa2_inf342\app\templates/index.blade.php ENDPATH**/ ?>