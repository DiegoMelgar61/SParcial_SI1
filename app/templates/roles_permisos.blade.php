<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Roles y Permisos — Plataforma Universitaria INF342</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="csrf-token" content="{{ csrf_token() }}">
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
      <div class="flex items-center gap-4">
        <!-- Botón de menú lateral para móviles -->
        <button id="menu-toggle" class="block md:hidden p-2 text-white hover:text-gold-500 transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
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
      </div>

      <div class="flex items-center gap-4">
        <div class="hidden sm:block text-right">
          <p class="font-bold text-white">{{ $user['nomb_comp'] }}</p>
          <p class="text-xs text-gold-500 uppercase tracking-wider font-semibold">{{ ucfirst($user['rol']) }}</p>
        </div>

        <!-- Avatar del usuario -->
        <div id="user-avatar"
          class="w-11 h-11 bg-gold-500 text-navy-900 flex items-center justify-center font-black text-lg border-2 border-white shadow-md cursor-pointer select-none hover:bg-gold-600 transition-all">
          {{ strtoupper(substr($user['nomb_comp'], 0, 1)) }}
        </div>

        <!-- Botón de inicio -->
        <a href="/"
          class="text-sm bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 font-bold uppercase tracking-wide transition-all border-l-4 border-gold-500 hover:border-gold-400">
          Inicio
        </a>
      </div>
    </div>
  </header>

  <!-- Panel lateral corporativo -->
  <aside id="admin-sidebar"
    class="fixed top-0 left-0 w-64 bg-navy-900 shadow-2xl h-full z-30 border-r-4 border-gold-500 transform -translate-x-full md:translate-x-0 transition-transform duration-300">

    <!-- Contenedor con scroll -->
    <div class="flex flex-col h-full overflow-y-auto scrollbar-thin scrollbar-thumb-gold-500 scrollbar-track-navy-800">

      <!-- Encabezado -->
      <div class="p-4 border-b-2 border-gold-500">
        <h3 class="text-sm font-black text-white uppercase tracking-wide">Panel de Administración</h3>
        <p class="text-xs text-gold-500 mt-1 font-semibold uppercase tracking-wider">Gestión completa del sistema</p>
      </div>

      <!-- Navegación -->
      <nav class="flex-1 p-3">
        <ul class="space-y-1 text-sm">

          <li>
            <a href="/admin/mod-adm"
              class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
              <span class="font-semibold">Panel Administrador</span>
            </a>
          </li>

          <li>
            <a href="/admin/users"
              class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5M16 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              <span class="font-semibold">Gestión de Usuarios</span>
            </a>
          </li>

          <li>
            <a href="/admin/roles"
              class="flex items-center gap-2 px-3 py-2 text-gold-500 bg-navy-800 font-black border-l-4 border-gold-500 transition">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 7a2 2 0 012 2v1a2 2 0 01-2 2H9a2 2 0 01-2-2V9a2 2 0 012-2h6zm6 11h-3a2 2 0 01-2-2v-1a2 2 0 012-2h3v5zM6 18H3v-5h3a2 2 0 012 2v1a2 2 0 01-2 2z" />
              </svg>
              <span>Gestión de Roles y Permisos</span>
            </a>
          </li>

          <li>
            <a href="/admin/grupos"
              class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5M16 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              <span class="font-semibold">Gestión de Grupos</span>
            </a>
          </li>

          <li>
            <a href="/admin/aulas"
              class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
              </svg>
              <span class="font-semibold">Gestión de Aulas</span>
            </a>
          </li>

          <li>
            <a href="/admin/materias"
              class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 20l9-5-9-5-9 5 9 5zM12 12V4m0 8l9-5M12 12L3 7" />
              </svg>
              <span class="font-semibold">Gestión de Materias</span>
            </a>
          </li>
          <li>
            <a href="/admin/gestiones"
              class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span class="font-semibold">Gestión de Gestiones</span>
            </a>
          </li>
          <li>
            <a href="/admin/carga-horaria"
              class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 17v-6h13v6M9 17H5v-6h4m0 6V7m0 0H3v4h2m4-4v4h2" />
              </svg>
              <span class="font-semibold">Carga Horaria del Docente</span>
            </a>
          </li>

          <li>
            <a href="/auto/generar-horario"
              class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span class="font-semibold">Generar Horario</span>
            </a>
          </li>

          <li>
            <a href="/admin/bitacora"
              class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
              </svg>
              <span class="font-semibold">Consultar Historial de Acciones</span>
            </a>
          </li>

        </ul>
      </nav>

      <!-- Footer -->
      <div class="p-3 border-t-2 border-gold-500 text-center text-[11px] text-slate-400 uppercase tracking-widest">
        Módulo Admin v1.1
      </div>
    </div>
  </aside>

  <!-- Overlay para móviles -->
  <div id="sidebar-overlay" class="fixed inset-0 bg-navy-900 bg-opacity-70 z-20 md:hidden hidden"></div>


  <!-- Contenido principal -->
  <main class="flex-1 md:ml-64 p-6 transition-all duration-300">

    <!-- ======================== SECCIÓN DE ROLES ======================== -->
    <section class="mb-12">
      <div class="flex flex-col md:flex-row justify-between md:items-center mb-6 bg-white border-l-4 border-gold-500 shadow-md px-6 py-5">
        <div>
          <h2 class="text-2xl md:text-3xl font-black text-navy-900 uppercase tracking-tight mb-1">Gestión de Roles</h2>
          <p class="text-slate-600 text-sm font-semibold uppercase tracking-wide">Administre los roles del sistema y sus permisos asociados</p>
        </div>
        <button id="btn-add-rol"
          class="mt-4 md:mt-0 flex items-center gap-2 bg-navy-900 hover:bg-navy-800 text-white px-5 py-3 text-sm font-bold uppercase tracking-wide transition-all shadow-md border-b-4 border-navy-800 hover:border-gold-500">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6" />
          </svg>
          Agregar Rol
        </button>
      </div>

      <!-- Tabla de Roles -->
      <div class="bg-white border-2 border-slate-300 shadow-lg overflow-hidden">
        <div class="bg-navy-900 px-6 py-4 border-b-4 border-gold-500">
          <h3 class="text-lg font-black text-white uppercase tracking-wide">Registro de Roles</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-100">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-black text-navy-900 uppercase tracking-widest border-r border-slate-300">#</th>
                <th class="px-6 py-4 text-left text-xs font-black text-navy-900 uppercase tracking-widest border-r border-slate-300">Nombre del Rol</th>
                <th class="px-6 py-4 text-left text-xs font-black text-navy-900 uppercase tracking-widest border-r border-slate-300">Descripción</th>
                <th class="px-6 py-4 text-left text-xs font-black text-navy-900 uppercase tracking-widest border-r border-slate-300">Permisos Asignados</th>
                <th class="px-6 py-4 text-center text-xs font-black text-navy-900 uppercase tracking-widest">Acciones</th>
              </tr>
            </thead>
            <tbody id="tabla-roles" class="bg-white divide-y divide-slate-200 text-sm">
              @forelse($roles as $index => $rol)
                <tr class="hover:bg-slate-50 transition-all rol-row">
                  <td class="px-6 py-4 text-slate-700 font-semibold border-r border-slate-200">{{ $index + 1 }}</td>
                  <td class="px-6 py-4 font-black text-navy-900 nombre-cell border-r border-slate-200">{{ $rol['nombre'] }}</td>
                  <td class="px-6 py-4 text-slate-700 font-semibold descripcion-cell border-r border-slate-200">{{ $rol['descripcion'] ?? '—' }}</td>
                  <td class="px-6 py-4 border-r border-slate-200">
                    @if(!empty($rol['permisos']))
                      @foreach($rol['permisos'] as $perm)
                        <span class="inline-block bg-navy-900 text-gold-500 text-xs px-2 py-1 border border-gold-500 mr-1 mb-1 font-bold">{{ $perm['nombre'] }}</span>
                      @endforeach
                    @else
                      <span class="text-slate-400 italic font-semibold">Sin permisos asignados</span>
                    @endif
                  </td>
                  <td class="px-6 py-4 text-center">
                    <div class="flex justify-center gap-3">
                      <!-- Botón Editar Rol -->
                      <button data-id="{{ $rol['id'] }}" data-nombre="{{ $rol['nombre'] }}"
                        data-descripcion="{{ $rol['descripcion'] ?? '' }}"
                        class="btn-edit-rol text-blue-600 hover:text-white hover:bg-blue-600 p-2 border-2 border-blue-600 transition-all"
                        title="Editar Rol">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </button>
                      <!-- Botón Eliminar Rol -->
                      <button data-id="{{ $rol['id'] }}" data-nombre="{{ $rol['nombre'] }}"
                        class="btn-delete-rol text-red-600 hover:text-white hover:bg-red-600 p-2 border-2 border-red-600 transition-all"
                        title="Eliminar Rol">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr id="no-roles-record">
                  <td colspan="5" class="px-6 py-8 text-center text-slate-500 font-semibold uppercase tracking-wide">
                    No hay roles registrados en el sistema.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <!-- ======================== SECCIÓN DE PERMISOS ======================== -->
    <section>
      <div class="flex flex-col md:flex-row justify-between md:items-center mb-6 bg-white border-l-4 border-gold-500 shadow-md px-6 py-5">
        <div>
          <h2 class="text-2xl md:text-3xl font-black text-navy-900 uppercase tracking-tight mb-1">Gestión de Permisos</h2>
          <p class="text-slate-600 text-sm font-semibold uppercase tracking-wide">Administre los permisos disponibles y asígnelos a roles</p>
        </div>
        <button id="btn-add-permiso"
          class="mt-4 md:mt-0 flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-3 text-sm font-bold uppercase tracking-wide transition-all shadow-md border-b-4 border-green-700 hover:border-green-800">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6" />
          </svg>
          Agregar Permiso
        </button>
      </div>

      <!-- Tabla de Permisos -->
      <div class="bg-white border-2 border-slate-300 shadow-lg overflow-hidden">
        <div class="bg-navy-900 px-6 py-4 border-b-4 border-gold-500">
          <h3 class="text-lg font-black text-white uppercase tracking-wide">Registro de Permisos</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-100">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-black text-navy-900 uppercase tracking-widest border-r border-slate-300">#</th>
                <th class="px-6 py-4 text-left text-xs font-black text-navy-900 uppercase tracking-widest border-r border-slate-300">Nombre del Permiso</th>
                <th class="px-6 py-4 text-left text-xs font-black text-navy-900 uppercase tracking-widest border-r border-slate-300">Descripción</th>
                <th class="px-6 py-4 text-center text-xs font-black text-navy-900 uppercase tracking-widest">Acciones</th>
              </tr>
            </thead>
            <tbody id="tabla-permisos" class="bg-white divide-y divide-slate-200 text-sm">
              <!-- Los permisos se cargarán dinámicamente desde el backend -->
              <tr id="permisos-loading">
                <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                  <svg class="animate-spin h-5 w-5 mx-auto text-navy-900" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                  </svg>
                  <p class="mt-2 font-semibold uppercase tracking-wide">Cargando permisos...</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

  </main>


  <!-- Footer corporativo -->
  <footer class="text-center py-5 text-xs bg-navy-900 text-slate-300 border-t-4 border-gold-500 mt-12 md:ml-64">
    <p class="font-bold uppercase tracking-widest">© {{ date('Y') }} Grupo 32 — UAGRM | INF342 - SA</p>
  </footer>

  <!-- ======================== MODALES ======================== -->

  <!-- Modal para Agregar/Editar Rol -->
  <div id="rol-form-modal"
    class="hidden fixed inset-0 bg-navy-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white border-2 border-slate-300 shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
      <div class="sticky top-0 bg-navy-900 border-b-4 border-gold-500 px-6 py-4 flex justify-between items-center">
        <h3 id="rol-form-modal-title" class="text-xl font-black text-white uppercase tracking-wide">Agregar Nuevo Rol</h3>
        <button id="btn-cancel-rol-form-x" class="text-slate-300 hover:text-gold-500 transition-all">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form id="rol-form" class="p-6 space-y-4">
        <input type="hidden" id="form-rol-id" name="id">

        <!-- Nombre del Rol -->
        <div>
          <label for="form-rol-nombre" class="block text-sm font-black text-navy-900 mb-2 uppercase tracking-wide">
            Nombre del Rol <span class="text-red-600">*</span>
          </label>
          <input type="text" id="form-rol-nombre" name="nombre" required
            class="w-full px-4 py-3 border-2 border-slate-300 text-navy-900 focus:outline-none focus:border-navy-700 focus:ring-2 focus:ring-navy-700/20 transition-all"
            placeholder="Ej: Docente, Coordinador, etc.">
        </div>

        <!-- Descripción -->
        <div>
          <label for="form-rol-descripcion" class="block text-sm font-black text-navy-900 mb-2 uppercase tracking-wide">
            Descripción
          </label>
          <textarea id="form-rol-descripcion" name="descripcion" rows="3"
            class="w-full px-4 py-3 border-2 border-slate-300 text-navy-900 focus:outline-none focus:border-navy-700 focus:ring-2 focus:ring-navy-700/20 transition-all"
            placeholder="Descripción del rol (opcional)"></textarea>
        </div>

        <!-- Permisos Asociados -->
        <div>
          <label class="block text-sm font-black text-navy-900 mb-2 uppercase tracking-wide">
            Permisos Asociados
          </label>
          <div id="permisos-checkboxes"
            class="space-y-2 max-h-60 overflow-y-auto border-2 border-slate-300 p-4 bg-slate-50">
            <!-- Los checkboxes se cargarán dinámicamente -->
            <p class="text-slate-500 text-sm font-semibold">Cargando permisos...</p>
          </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-3 pt-4 border-t-2 border-slate-300">
          <button type="button" id="btn-cancel-rol-form"
            class="px-5 py-2.5 text-slate-700 bg-white border-2 border-slate-300 font-bold uppercase tracking-wide hover:bg-slate-100 transition-all">
            Cancelar
          </button>
          <button type="submit" id="btn-save-rol"
            class="px-5 py-2.5 bg-navy-900 hover:bg-navy-800 text-white font-bold uppercase tracking-wide transition-all shadow-md border-b-4 border-navy-800 hover:border-gold-500">
            Guardar Rol
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal para Eliminar Rol -->
  <div id="delete-rol-modal"
    class="hidden fixed inset-0 bg-navy-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white border-2 border-slate-300 shadow-2xl max-w-md w-full">
      <div class="p-6">
        <div class="flex items-center justify-center w-12 h-12 mx-auto border-2 border-red-600 bg-red-50 mb-4">
          <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </div>
        <h3 class="text-lg font-black text-navy-900 text-center mb-2 uppercase tracking-wide">¿Eliminar Rol?</h3>
        <p class="text-sm text-slate-700 text-center mb-6 leading-relaxed">
          ¿Estás seguro de que deseas eliminar el rol <strong id="delete-rol-nombre" class="text-navy-900 font-black"></strong>?
          Esta acción no se puede deshacer.
        </p>
        <div class="flex gap-3">
          <button id="btn-cancel-delete-rol"
            class="flex-1 px-4 py-2.5 text-slate-700 bg-white border-2 border-slate-300 font-bold uppercase tracking-wide hover:bg-slate-100 transition-all">
            Cancelar
          </button>
          <button id="btn-confirm-delete-rol"
            class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold uppercase tracking-wide transition-all shadow-md border-b-4 border-red-700 hover:border-red-800">
            Eliminar
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para Agregar/Editar Permiso -->
  <div id="permiso-form-modal"
    class="hidden fixed inset-0 bg-navy-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white border-2 border-slate-300 shadow-2xl max-w-xl w-full">
      <div class="bg-navy-900 border-b-4 border-gold-500 px-6 py-4 flex justify-between items-center">
        <h3 id="permiso-form-modal-title" class="text-xl font-black text-white uppercase tracking-wide">Agregar Nuevo Permiso</h3>
        <button id="btn-cancel-permiso-form-x" class="text-slate-300 hover:text-gold-500 transition-all">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form id="permiso-form" class="p-6 space-y-4">
        <input type="hidden" id="form-permiso-id" name="id">

        <!-- Nombre del Permiso -->
        <div>
          <label for="form-permiso-nombre" class="block text-sm font-black text-navy-900 mb-2 uppercase tracking-wide">
            Nombre del Permiso <span class="text-red-600">*</span>
          </label>
          <input type="text" id="form-permiso-nombre" name="nombre" required
            class="w-full px-4 py-3 border-2 border-slate-300 text-navy-900 focus:outline-none focus:border-navy-700 focus:ring-2 focus:ring-navy-700/20 transition-all"
            placeholder="Ej: crear_usuarios, ver_reportes, etc.">
        </div>

        <!-- Descripción -->
        <div>
          <label for="form-permiso-descripcion" class="block text-sm font-black text-navy-900 mb-2 uppercase tracking-wide">
            Descripción
          </label>
          <textarea id="form-permiso-descripcion" name="descripcion" rows="3"
            class="w-full px-4 py-3 border-2 border-slate-300 text-navy-900 focus:outline-none focus:border-navy-700 focus:ring-2 focus:ring-navy-700/20 transition-all"
            placeholder="Descripción del permiso (opcional)"></textarea>
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-3 pt-4 border-t-2 border-slate-300">
          <button type="button" id="btn-cancel-permiso-form"
            class="px-5 py-2.5 text-slate-700 bg-white border-2 border-slate-300 font-bold uppercase tracking-wide hover:bg-slate-100 transition-all">
            Cancelar
          </button>
          <button type="submit" id="btn-save-permiso"
            class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold uppercase tracking-wide transition-all shadow-md border-b-4 border-green-700 hover:border-green-800">
            Guardar Permiso
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal para Eliminar Permiso -->
  <div id="delete-permiso-modal"
    class="hidden fixed inset-0 bg-navy-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white border-2 border-slate-300 shadow-2xl max-w-md w-full">
      <div class="p-6">
        <div class="flex items-center justify-center w-12 h-12 mx-auto border-2 border-red-600 bg-red-50 mb-4">
          <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </div>
        <h3 class="text-lg font-black text-navy-900 text-center mb-2 uppercase tracking-wide">¿Eliminar Permiso?</h3>
        <p class="text-sm text-slate-700 text-center mb-6 leading-relaxed">
          ¿Estás seguro de que deseas eliminar el permiso <strong id="delete-permiso-nombre"
            class="text-navy-900 font-black"></strong>?
          Esta acción no se puede deshacer.
        </p>
        <div class="flex gap-3">
          <button id="btn-cancel-delete-permiso"
            class="flex-1 px-4 py-2.5 text-slate-700 bg-white border-2 border-slate-300 font-bold uppercase tracking-wide hover:bg-slate-100 transition-all">
            Cancelar
          </button>
          <button id="btn-confirm-delete-permiso"
            class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold uppercase tracking-wide transition-all shadow-md border-b-4 border-red-700 hover:border-red-800">
            Eliminar
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para Asignar Roles a un Permiso -->
  <div id="assign-roles-modal"
    class="hidden fixed inset-0 bg-navy-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white border-2 border-slate-300 shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
      <div class="sticky top-0 bg-navy-900 border-b-4 border-gold-500 px-6 py-4 flex justify-between items-center">
        <h3 class="text-xl font-black text-white uppercase tracking-wide">
          Asignar Roles a: <span id="assign-roles-permiso-nombre" class="text-gold-500"></span>
        </h3>
        <button id="btn-cancel-assign-roles-x" class="text-slate-300 hover:text-gold-500 transition-all">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="p-6">
        <input type="hidden" id="assign-roles-permiso-id">

        <p class="text-sm text-slate-700 mb-4 font-semibold">
          Seleccione los roles a los que desea asignar este permiso:
        </p>

        <div id="roles-checkboxes"
          class="space-y-2 max-h-96 overflow-y-auto border-2 border-slate-300 p-4 bg-slate-50">
          <!-- Los checkboxes de roles se cargarán dinámicamente -->
          <p class="text-slate-500 text-sm font-semibold">Cargando roles...</p>
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-3 pt-6 border-t-2 border-slate-300 mt-6">
          <button type="button" id="btn-cancel-assign-roles"
            class="px-5 py-2.5 text-slate-700 bg-white border-2 border-slate-300 font-bold uppercase tracking-wide hover:bg-slate-100 transition-all">
            Cancelar
          </button>
          <button type="button" id="btn-save-assign-roles"
            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold uppercase tracking-wide transition-all shadow-md border-b-4 border-blue-700 hover:border-blue-800">
            Guardar Asignación
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Script principal -->
  <script src="{{ asset('static/scripts/roles_permisos.js') }}"></script>
</body>

</html>
