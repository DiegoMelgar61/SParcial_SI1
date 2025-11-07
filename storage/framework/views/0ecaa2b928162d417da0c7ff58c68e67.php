<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Permisos — Plataforma Universitaria INF342</title>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
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

                <div id="user-avatar"
                     class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold shadow-sm cursor-pointer select-none">
                    <?php echo e(strtoupper(substr($user['nomb_comp'], 0, 1))); ?>

                </div>

                <a href="/"
                   class="text-sm bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 px-4 py-2 rounded-md font-medium transition">
                    Inicio
                </a>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <aside id="admin-sidebar" class="fixed top-0 left-0 w-64 bg-white shadow-lg h-full z-30 border-r border-gray-200 transition-transform duration-300 transform -translate-x-full md:translate-x-0">
        <div class="p-6 h-full flex flex-col">
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800">Panel de Administración</h3>
                <p class="text-sm text-gray-500 mt-3">Gestión completa del sistema</p>
            </div>

            <nav class="flex-1">
                <ul class="space-y-2">
                    <li><a href="/admin/mod-adm" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition"><span class="font-medium">Panel Administrador</span></a></li>
                    <li><a href="/admin/users" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition"><span class="font-medium">Gestión de Usuarios</span></a></li>
                    <li><a href="/admin/roles" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition"><span class="font-medium">Gestión de Roles</span></a></li>
                    <li><a href="/admin/materias" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition"><span class="font-medium">Gestión de Materias</span></a></li>
                    <li><a href="/admin/permisos" class="flex items-center gap-3 px-4 py-3 text-indigo-700 bg-indigo-50 rounded-lg font-semibold transition"><span class="font-medium">Gestión de Permisos</span></a></li>
                    <li><a href="/admin/bitacora" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition"><span class="font-medium">Consultar Bitácora</span></a></li>
                </ul>
            </nav>

            <div class="pt-4 border-t border-gray-200 text-xs text-gray-500 text-center">
                Módulo Admin v1.0
            </div>
        </div>
    </aside>

    <!-- Contenido principal -->
    <main class="flex-1 md:ml-64 p-6 transition-all duration-300">
        <div class="flex flex-col md:flex-row justify-between md:items-center mb-8">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 mb-1">Gestión de Permisos</h2>
                <p class="text-gray-500 text-sm">Administración de accesos y privilegios del sistema.</p>
            </div>
            <div class="flex items-center gap-4 mt-3 md:mt-0">
                <div id="clock" class="text-sm text-gray-600 font-medium"></div>
                <button id="btn-add-permiso" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Agregar Permiso
                </button>
            </div>
        </div>

        <!-- Tabla de Permisos -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre del Permiso</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asignado a Rol</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="permisos-table-body" class="bg-white divide-y divide-gray-200 text-sm">
                        <?php $__empty_1 = true; $__currentLoopData = $permisos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permiso): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800 font-medium"><?php echo e($permiso['id']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800"><?php echo e($permiso['nombre']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600"><?php echo e($permiso['descripcion']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700"><?php echo e($permiso['rol'] ?? 'No asignado'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex justify-end gap-2">
                                        <button data-id="<?php echo e($permiso['id']); ?>" data-nombre="<?php echo e($permiso['nombre']); ?>" data-desc="<?php echo e($permiso['descripcion']); ?>"
                                            class="btn-edit text-indigo-600 hover:text-indigo-900 p-1 rounded-md hover:bg-indigo-100 transition" title="Editar Permiso">
                                            ✏️
                                        </button>
                                        <button data-id="<?php echo e($permiso['id']); ?>" data-nombre="<?php echo e($permiso['nombre']); ?>"
                                            class="btn-delete text-red-600 hover:text-red-900 p-1 rounded-md hover:bg-red-100 transition" title="Eliminar Permiso">
                                            🗑️
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No se encontraron permisos registrados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 flex justify-between text-sm text-gray-600">
            <div>Mostrando <?php echo e(count($permisos)); ?> permisos.</div>
            <div class="text-xs text-gray-500">Última carga: <?php echo e(\Carbon\Carbon::now()->format('d/m/Y H:i:s')); ?></div>
        </div>
    </main>

    <footer class="text-center py-4 text-xs text-gray-500 border-t border-gray-200 mt-auto">
        © 2025 Plataforma Universitaria — Todos los derechos reservados
    </footer>

    <script>
        // Mostrar hora actual
        function updateClock() {
            const clock = document.getElementById('clock');
            if (clock) clock.textContent = new Date().toLocaleString();
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>
<?php /**PATH D:\whatever that twas, scarcely worth my notice\Brillo\app\templates/admin_permiso.blade.php ENDPATH**/ ?>