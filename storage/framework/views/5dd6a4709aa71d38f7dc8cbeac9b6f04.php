<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Roles y Permisos — Plataforma Universitaria INF342</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
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
          <p class="font-medium text-gray-800"><?php echo e($user['nomb_comp']); ?></p>
          <p class="text-xs text-gray-500"><?php echo e(ucfirst($user['rol'])); ?></p>
        </div>

        <!-- Avatar del usuario -->
        <div id="user-avatar"
          class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold shadow-sm cursor-pointer select-none">
          <?php echo e(strtoupper(substr($user['nomb_comp'], 0, 1))); ?>

        </div>

        <!-- Botón de inicio -->
        <a href="/"
          class="text-sm bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 px-4 py-2 rounded-md font-medium transition">
          Inicio
        </a>
      </div>
    </div>
  </header>

  <!-- Panel lateral de usuario (desplegable) -->
  <aside id="user-aside"
    class="hidden fixed top-16 right-4 w-64 bg-white shadow-2xl rounded-xl border border-gray-200 z-50 transition-all duration-300 opacity-0 scale-95 origin-top-right">
    <div class="p-5 text-sm text-gray-700">
      <div class="flex items-center gap-3 mb-3">
        <div
          class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold shadow-sm">
          <?php echo e(strtoupper(substr($user['nomb_comp'], 0, 1))); ?>

        </div>
        <div>
          <p class="font-semibold text-gray-800 leading-tight"><?php echo e($user['nomb_comp']); ?></p>
          <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 font-medium">
            <?php echo e(ucfirst($user['rol'])); ?>

          </span>
        </div>
      </div>
      <hr class="my-3 border-gray-200">
      <ul class="space-y-2 text-sm">
        <li><span class="font-medium text-gray-600">CI:</span> <?php echo e($user['ci']); ?></li>
        <li><span class="font-medium text-gray-600">Correo:</span> <?php echo e($user['correo'] ?? '—'); ?></li>
        <li><span class="font-medium text-gray-600">Teléfono:</span> <?php echo e($user['tel'] ?? '—'); ?></li>
      </ul>
      <div class="mt-4 pt-3 border-t border-gray-100">
        <a href="/perfil"
          class="text-indigo-600 text-sm font-medium hover:underline hover:text-indigo-700 transition">
          Ver perfil completo →
        </a>
      </div>
    </div>
  </aside>


  <!-- Sidebar (Panel lateral de navegación) -->
  <aside id="admin-sidebar"
    class="fixed top-0 left-0 w-64 bg-white shadow-lg h-full z-30 transition-transform duration-300 transform -translate-x-full md:translate-x-0 border-r border-gray-200">
    <div class="p-6 h-full flex flex-col">
      <!-- Encabezado del sidebar -->
      <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-800">Panel de Administración</h3>
        <p class="text-sm text-gray-500 mt-3">Gestión completa del sistema</p>
      </div>

      <!-- Navegación -->
      <nav class="flex-1">
        <ul class="space-y-2">
          <li>
            <a href="/admin/mod-adm" 
               class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition group border-l-4 border-transparent hover:border-indigo-600">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
              <span class="font-medium">Panel Administrador</span>
            </a>
          </li>
          <li>
            <a href="/admin/users" 
               class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition group border-l-4 border-transparent hover:border-indigo-600">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
              </svg>
              <span class="font-medium">Gestión de Usuarios</span>
            </a>
          </li>
          <li>
            <a href="/admin/roles" 
               class="flex items-center gap-3 px-4 py-3 text-indigo-700 bg-indigo-50 rounded-lg transition group font-semibold border-l-4 border-indigo-600">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
              </svg>
              <span class="font-medium">Gestión de Roles y Permisos</span>
            </a>
          </li>
          <li>
            <a href="/admin/materias" 
               class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition group border-l-4 border-transparent hover:border-indigo-600">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
              </svg>
              <span class="font-medium">Gestión de Materias</span>
            </a>
          </li>
          <li>
            <a href="/admin/bitacora" 
               class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition group border-l-4 border-transparent hover:border-indigo-600">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
              </svg>
              <span class="font-medium">Consultar Historial de Acciones</span>
            </a>
          </li>
        </ul>
      </nav>

      <div class="pt-4 border-t border-gray-200">
        <p class="text-xs text-gray-500 text-center">Módulo Admin v1.0</p>
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
              <?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $rol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr class="hover:bg-gray-50 transition rol-row">
                <td class="px-6 py-4 text-gray-700"><?php echo e($index + 1); ?></td>
                <td class="px-6 py-4 font-medium text-gray-800 nombre-cell"><?php echo e($rol['nombre']); ?></td>
                <td class="px-6 py-4 text-gray-600 descripcion-cell"><?php echo e($rol['descripcion'] ?? '—'); ?></td>
                <td class="px-6 py-4">
                  <?php if(!empty($rol['permisos'])): ?>
                    <?php $__currentLoopData = $rol['permisos']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <span class="inline-block bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded-md mr-1 mb-1"><?php echo e($perm['nombre']); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  <?php else: ?>
                    <span class="text-gray-400 italic">Sin permisos asignados</span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4 text-center">
                  <div class="flex justify-center gap-3">
                    <!-- Botón Editar Rol -->
                    <button data-id="<?php echo e($rol['id']); ?>" 
                            data-nombre="<?php echo e($rol['nombre']); ?>" 
                            data-descripcion="<?php echo e($rol['descripcion'] ?? ''); ?>"
                            class="btn-edit-rol text-indigo-600 hover:text-indigo-800 p-1 rounded-md hover:bg-indigo-50 transition" 
                            title="Editar Rol">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </button>
                    <!-- Botón Eliminar Rol -->
                    <button data-id="<?php echo e($rol['id']); ?>" 
                            data-nombre="<?php echo e($rol['nombre']); ?>"
                            class="btn-delete-rol text-red-600 hover:text-red-800 p-1 rounded-md hover:bg-red-50 transition" 
                            title="Eliminar Rol">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr id="no-roles-record">
                <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">
                  No hay roles registrados en el sistema.
                </td>
              </tr>
              <?php endif; ?>
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
    © <?php echo e(date('Y')); ?> Grupo 32 — UAGRM | INF342 - SA
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
  <script src="<?php echo e(asset('static/scripts/roles_permisos.js')); ?>"></script>
</body>
</html>
<?php /**PATH D:\whatever that twas, scarcely worth my notice\Brillo\app\templates/roles_permisos.blade.php ENDPATH**/ ?>