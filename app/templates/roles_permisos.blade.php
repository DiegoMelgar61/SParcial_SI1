<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Roles y Permisos — Plataforma Universitaria INF342</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col font-sans antialiased">

  <!-- Barra superior -->
  <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-3">
      <div class="flex items-center gap-4">
        <!-- Botón de menú lateral para móviles -->
        <button id="menu-toggle" class="block md:hidden p-2 text-gray-600 hover:text-indigo-600 rounded-md transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
        <h1 class="text-lg md:text-xl font-semibold text-gray-700 tracking-wide">
          Plataforma Universitaria
        </h1>
      </div>

      <div class="flex items-center gap-4">
        <div class="hidden sm:block text-right">
          <p class="font-medium text-gray-800">{{ $user['nomb_comp'] }}</p>
          <p class="text-xs text-gray-500">{{ ucfirst($user['rol']) }}</p>
        </div>

        <!-- Avatar del usuario -->
        <div id="user-avatar"
          class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold shadow-sm cursor-pointer select-none">
          {{ strtoupper(substr($user['nomb_comp'], 0, 1)) }}
        </div>

        <!-- Botón de inicio -->
        <a href="/"
          class="text-sm bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 px-4 py-2 rounded-md font-medium transition">
          Inicio
        </a>
      </div>
    </div>
  </header>

  <!-- Panel lateral de usuario -->
    <aside id="admin-sidebar"
        class="fixed top-0 left-0 w-64 bg-white shadow-lg h-full z-30 border-r border-gray-200 transform -translate-x-full md:translate-x-0 transition-transform duration-300">

        <!-- Contenedor con scroll -->
        <div class="flex flex-col h-full overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">

            <!-- Encabezado -->
            <div class="p-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-800">Panel de Administración</h3>
                <p class="text-xs text-indigo-600 mt-1 font-medium">Gestión completa del sistema</p>
            </div>

            <!-- Navegación -->
            <nav class="flex-1 p-3">
                <ul class="space-y-1 text-sm">

                    <li>
                        <a href="/admin/mod-adm"
                        class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>Panel Administrador</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/users"
                        class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5M16 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span>Gestión de Usuarios</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/roles"
                        class="flex items-center gap-2 px-3 py-2 text-indigo-700 bg-indigo-50 rounded-lg font-semibold hover:bg-indigo-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2v1a2 2 0 01-2 2H9a2 2 0 01-2-2V9a2 2 0 012-2h6zm6 11h-3a2 2 0 01-2-2v-1a2 2 0 012-2h3v5zM6 18H3v-5h3a2 2 0 012 2v1a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Gestión de Roles y Permisos</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/grupos"
                        class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5M16 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span>Gestión de Grupos</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/aulas"
                        class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7h18M3 12h18M3 17h18"/>
                            </svg>
                            <span>Gestión de Aulas</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/materias"
                        class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 20l9-5-9-5-9 5 9 5zM12 12V4m0 8l9-5M12 12L3 7"/>
                            </svg>
                            <span>Gestión de Materias</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/carga-docente"
                        class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-6h13v6M9 17H5v-6h4m0 6V7m0 0H3v4h2m4-4v4h2"/>
                            </svg>
                            <span>Carga Horaria del Docente</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/horarios"
                        class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Generar Horario</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/bitacora"
                        class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                            </svg>
                            <span>Consultar Historial de Acciones</span>
                        </a>
                    </li>

                </ul>
            </nav>

            <!-- Footer -->
            <div class="p-3 border-t border-gray-100 text-center text-[11px] text-gray-500">
                Módulo Admin v1.1
            </div>
        </div>
    </aside>

  <!-- Overlay para móviles -->
  <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden hidden"></div>


  <!-- Contenido principal -->
  <main class="flex-1 md:ml-64 p-6 transition-all duration-300">
    
    <!-- ======================== SECCIÓN DE ROLES ======================== -->
    <section class="mb-12">
      <div class="flex flex-col md:flex-row justify-between md:items-center mb-6">
        <div>
          <h2 class="text-2xl font-semibold text-gray-800 mb-1">Gestión de Roles</h2>
          <p class="text-gray-600 text-sm">Administre los roles del sistema y sus permisos asociados.</p>
        </div>
        <button id="btn-add-rol"
          class="mt-4 md:mt-0 flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium shadow-sm transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/>
          </svg>
          Agregar Rol
        </button>
      </div>

      <!-- Tabla de Roles -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nombre del Rol</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Descripción</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Permisos Asignados</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>
            <tbody id="tabla-roles" class="bg-white divide-y divide-gray-200 text-sm">
              @forelse($roles as $index => $rol)
              <tr class="hover:bg-gray-50 transition rol-row">
                <td class="px-6 py-4 text-gray-700">{{ $index + 1 }}</td>
                <td class="px-6 py-4 font-medium text-gray-800 nombre-cell">{{ $rol['nombre'] }}</td>
                <td class="px-6 py-4 text-gray-600 descripcion-cell">{{ $rol['descripcion'] ?? '—' }}</td>
                <td class="px-6 py-4">
                  @if(!empty($rol['permisos']))
                    @foreach($rol['permisos'] as $perm)
                      <span class="inline-block bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded-md mr-1 mb-1">{{ $perm['nombre'] }}</span>
                    @endforeach
                  @else
                    <span class="text-gray-400 italic">Sin permisos asignados</span>
                  @endif
                </td>
                <td class="px-6 py-4 text-center">
                  <div class="flex justify-center gap-3">
                    <!-- Botón Editar Rol -->
                    <button data-id="{{ $rol['id'] }}" 
                            data-nombre="{{ $rol['nombre'] }}" 
                            data-descripcion="{{ $rol['descripcion'] ?? '' }}"
                            class="btn-edit-rol text-indigo-600 hover:text-indigo-800 p-1 rounded-md hover:bg-indigo-50 transition" 
                            title="Editar Rol">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </button>
                    <!-- Botón Eliminar Rol -->
                    <button data-id="{{ $rol['id'] }}" 
                            data-nombre="{{ $rol['nombre'] }}"
                            class="btn-delete-rol text-red-600 hover:text-red-800 p-1 rounded-md hover:bg-red-50 transition" 
                            title="Eliminar Rol">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              @empty
              <tr id="no-roles-record">
                <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">
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
      <div class="flex flex-col md:flex-row justify-between md:items-center mb-6">
        <div>
          <h2 class="text-2xl font-semibold text-gray-800 mb-1">Gestión de Permisos</h2>
          <p class="text-gray-600 text-sm">Administre los permisos disponibles y asígnelos a roles.</p>
        </div>
        <button id="btn-add-permiso"
          class="mt-4 md:mt-0 flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium shadow-sm transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/>
          </svg>
          Agregar Permiso
        </button>
      </div>

      <!-- Tabla de Permisos -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nombre del Permiso</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Descripción</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>
            <tbody id="tabla-permisos" class="bg-white divide-y divide-gray-200 text-sm">
              <!-- Los permisos se cargarán dinámicamente desde el backend -->
              <tr id="permisos-loading">
                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                  <svg class="animate-spin h-5 w-5 mx-auto text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <p class="mt-2">Cargando permisos...</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

  </main>


  <!-- Footer -->
  <footer class="text-center py-4 text-xs text-gray-500 border-t border-gray-200 bg-white mt-10 md:ml-64">
    © {{ date('Y') }} Grupo 32 — UAGRM | INF342 - SA
  </footer>

  <!-- ======================== MODALES ======================== -->

  <!-- Modal para Agregar/Editar Rol -->
  <div id="rol-form-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
      <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center rounded-t-xl">
        <h3 id="rol-form-modal-title" class="text-xl font-semibold text-gray-800">Agregar Nuevo Rol</h3>
        <button id="btn-cancel-rol-form-x" class="text-gray-400 hover:text-gray-600 transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
      
      <form id="rol-form" class="p-6 space-y-4">
        <input type="hidden" id="form-rol-id" name="id">
        
        <!-- Nombre del Rol -->
        <div>
          <label for="form-rol-nombre" class="block text-sm font-medium text-gray-700 mb-1">
            Nombre del Rol <span class="text-red-500">*</span>
          </label>
          <input type="text" id="form-rol-nombre" name="nombre" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
            placeholder="Ej: Docente, Coordinador, etc.">
        </div>

        <!-- Descripción -->
        <div>
          <label for="form-rol-descripcion" class="block text-sm font-medium text-gray-700 mb-1">
            Descripción
          </label>
          <textarea id="form-rol-descripcion" name="descripcion" rows="3"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
            placeholder="Descripción del rol (opcional)"></textarea>
        </div>

        <!-- Permisos Asociados -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Permisos Asociados
          </label>
          <div id="permisos-checkboxes" class="space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-4 bg-gray-50">
            <!-- Los checkboxes se cargarán dinámicamente -->
            <p class="text-gray-500 text-sm">Cargando permisos...</p>
          </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
          <button type="button" id="btn-cancel-rol-form"
            class="px-5 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">
            Cancelar
          </button>
          <button type="submit" id="btn-save-rol"
            class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition shadow-sm">
            Guardar Rol
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal para Eliminar Rol -->
  <div id="delete-rol-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
      <div class="p-6">
        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
          <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-800 text-center mb-2">¿Eliminar Rol?</h3>
        <p class="text-sm text-gray-600 text-center mb-6">
          ¿Estás seguro de que deseas eliminar el rol <strong id="delete-rol-nombre" class="text-gray-800"></strong>? 
          Esta acción no se puede deshacer.
        </p>
        <div class="flex gap-3">
          <button id="btn-cancel-delete-rol"
            class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">
            Cancelar
          </button>
          <button id="btn-confirm-delete-rol"
            class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition shadow-sm">
            Eliminar
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para Agregar/Editar Permiso -->
  <div id="permiso-form-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-xl w-full">
      <div class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center rounded-t-xl">
        <h3 id="permiso-form-modal-title" class="text-xl font-semibold text-gray-800">Agregar Nuevo Permiso</h3>
        <button id="btn-cancel-permiso-form-x" class="text-gray-400 hover:text-gray-600 transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
      
      <form id="permiso-form" class="p-6 space-y-4">
        <input type="hidden" id="form-permiso-id" name="id">
        
        <!-- Nombre del Permiso -->
        <div>
          <label for="form-permiso-nombre" class="block text-sm font-medium text-gray-700 mb-1">
            Nombre del Permiso <span class="text-red-500">*</span>
          </label>
          <input type="text" id="form-permiso-nombre" name="nombre" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
            placeholder="Ej: crear_usuarios, ver_reportes, etc.">
        </div>

        <!-- Descripción -->
        <div>
          <label for="form-permiso-descripcion" class="block text-sm font-medium text-gray-700 mb-1">
            Descripción
          </label>
          <textarea id="form-permiso-descripcion" name="descripcion" rows="3"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
            placeholder="Descripción del permiso (opcional)"></textarea>
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
          <button type="button" id="btn-cancel-permiso-form"
            class="px-5 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">
            Cancelar
          </button>
          <button type="submit" id="btn-save-permiso"
            class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition shadow-sm">
            Guardar Permiso
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal para Eliminar Permiso -->
  <div id="delete-permiso-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
      <div class="p-6">
        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
          <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-800 text-center mb-2">¿Eliminar Permiso?</h3>
        <p class="text-sm text-gray-600 text-center mb-6">
          ¿Estás seguro de que deseas eliminar el permiso <strong id="delete-permiso-nombre" class="text-gray-800"></strong>? 
          Esta acción no se puede deshacer.
        </p>
        <div class="flex gap-3">
          <button id="btn-cancel-delete-permiso"
            class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">
            Cancelar
          </button>
          <button id="btn-confirm-delete-permiso"
            class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition shadow-sm">
            Eliminar
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para Asignar Roles a un Permiso -->
  <div id="assign-roles-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
      <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center rounded-t-xl">
        <h3 class="text-xl font-semibold text-gray-800">
          Asignar Roles a: <span id="assign-roles-permiso-nombre" class="text-indigo-600"></span>
        </h3>
        <button id="btn-cancel-assign-roles-x" class="text-gray-400 hover:text-gray-600 transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
      
      <div class="p-6">
        <input type="hidden" id="assign-roles-permiso-id">
        
        <p class="text-sm text-gray-600 mb-4">
          Seleccione los roles a los que desea asignar este permiso:
        </p>
        
        <div id="roles-checkboxes" class="space-y-2 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-4 bg-gray-50">
          <!-- Los checkboxes de roles se cargarán dinámicamente -->
          <p class="text-gray-500 text-sm">Cargando roles...</p>
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 mt-6">
          <button type="button" id="btn-cancel-assign-roles"
            class="px-5 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">
            Cancelar
          </button>
          <button type="button" id="btn-save-assign-roles"
            class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition shadow-sm">
            Guardar Asignación
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Script principal -->
  <script src="{{ secure_asset('static/scripts/roles_permisos.js') }}"></script>
</body>
</html>
