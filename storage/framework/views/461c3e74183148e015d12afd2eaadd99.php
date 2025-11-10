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
        <button id="menu-toggle" class="block md:hidden p-2 text-gray-600 hover:text-indigo-600 rounded-md transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
        <h1 class="text-lg md:text-xl font-semibold text-gray-800 tracking-wide">
          Plataforma Universitaria
        </h1>
      </div>

      <div class="flex items-center gap-4">
        <div class="hidden sm:block text-right">
          <p class="font-medium text-gray-800"><?php echo e($user['nomb_comp']); ?></p>
          <p class="text-xs text-indigo-600 font-medium"><?php echo e(ucfirst($user['rol'])); ?></p>
        </div>

        <div id="user-avatar"
          class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold shadow-sm cursor-pointer select-none">
          <?php echo e(strtoupper(substr($user['nomb_comp'], 0, 1))); ?>

        </div>

        <a href="/"
          class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-medium transition shadow-sm">
          Inicio
        </a>
      </div>
    </div>
  </header>

  <!-- Sidebar -->
  <aside id="admin-sidebar"
    class="fixed top-0 left-0 w-64 bg-white shadow-lg h-full z-30 transition-transform duration-300 transform -translate-x-full md:translate-x-0 border-r border-gray-200">
    <div class="p-6 h-full flex flex-col">
      <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-800">Panel de Administración</h3>
        <p class="text-sm text-indigo-600 mt-2 font-medium">Gestión completa del sistema</p>
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
                           class="flex items-center gap-3 px-4 py-3 text-indigo-700 bg-indigo-50 rounded-lg transition group font-semibold">
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
    <div class="flex flex-col md:flex-row justify-between md:items-center mb-8">
      <div>
        <h2 class="text-2xl font-semibold text-gray-800 mb-1">Gestión de Roles y Permisos</h2>
        <p class="text-gray-600 text-sm">Administre roles y asigne permisos a los usuarios del sistema.</p>
      </div>
      <button id="btn-add-rol"
        class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium shadow-sm transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/>
        </svg>
        Agregar Rol
      </button>
    </div>

    <!-- Tabla principal -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Rol</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Descripción</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Permisos</th>
              <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Acciones</th>
            </tr>
          </thead>
          <tbody id="tabla-roles" class="bg-white divide-y divide-gray-200 text-sm">
            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $rol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="hover:bg-gray-50 transition">
              <td class="px-6 py-4"><?php echo e($index + 1); ?></td>
              <td class="px-6 py-4 font-medium text-gray-800"><?php echo e($rol['nombre']); ?></td>
              <td class="px-6 py-4 text-gray-600"><?php echo e($rol['descripcion'] ?? '—'); ?></td>
              <td class="px-6 py-4">
                <?php if(!empty($rol['permisos'])): ?>
                  <?php $__currentLoopData = $rol['permisos']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="inline-block bg-indigo-50 text-indigo-700 text-xs px-2 py-1 rounded-md mr-1 mb-1"><?php echo e($perm['nombre']); ?></span>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                  <span class="text-gray-400 italic">Sin permisos</span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 text-center">
                <div class="flex justify-center gap-3">
                  <button data-id="<?php echo e($rol['id']); ?>" class="btn-edit text-indigo-600 hover:text-indigo-800 p-1 rounded-md hover:bg-indigo-50 transition" title="Editar Rol">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11"/>
                    </svg>
                  </button>
                  <button data-id="<?php echo e($rol['id']); ?>" class="btn-permisos text-blue-600 hover:text-blue-800 p-1 rounded-md hover:bg-blue-50 transition" title="Gestionar Permisos">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-9-9 9 9 0 019 9z"/>
                    </svg>
                  </button>
                  <button data-id="<?php echo e($rol['id']); ?>" class="btn-delete text-red-600 hover:text-red-800 p-1 rounded-md hover:bg-red-50 transition" title="Eliminar Rol">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="text-center py-4 text-xs text-gray-500 border-t border-gray-200 bg-white mt-10 md:ml-64">
    © <?php echo e(date('Y')); ?> Grupo 32 — UAGRM | INF342 - SA
  </footer>

  <script src="<?php echo e(asset('static/scripts/roles_permisos.js')); ?>"></script>
</body>
</html>
<?php /**PATH C:\Users\migue\OneDrive\Escritorio\projects\inf342_2exa\app\templates/roles_permisos.blade.php ENDPATH**/ ?>