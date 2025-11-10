<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitácora — Plataforma Universitaria INF342</title>
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

                <!-- Avatar -->
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

    <!-- Panel lateral de usuario (copiado del index, necesario para el avatar) -->
    <aside id="user-aside"
           class="hidden fixed top-16 right-4 w-64 bg-white shadow-2xl rounded-xl border border-gray-200 z-50 transition-all duration-300 opacity-0 scale-95 origin-top-right">
        <div class="p-5 text-sm text-gray-700">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold shadow-sm">
                    <?php echo e(strtoupper(substr($user['nomb_comp'],0,1))); ?>

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

    <!-- Panel lateral de navegación (Sidebar) -->
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
                        <a href="/admin/users" 
                           class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition group">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                            <span class="font-medium">Gestión de Usuarios</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/roles" 
                           class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition group">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span class="font-medium">Gestión de Roles</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/materias" 
                           class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition group">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            <span class="font-medium">Gestión de Materias</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/permisos" 
                           class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition group">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            <span class="font-medium">Gestión de Permisos</span>
                        </a>
                    </li>
                    <!-- Enlace activo para Bitácora -->
                    <li>
                        <a href="/admin/bitacora" 
                           class="flex items-center gap-3 px-4 py-3 text-indigo-700 bg-indigo-50 rounded-lg transition group font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            <span class="font-medium">Consultar Historial de Acciones</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Footer del sidebar -->
            <div class="pt-4 border-t border-gray-200">
                <p class="text-xs text-gray-500 text-center">
                    Módulo Admin v1.0
                </p>
            </div>
        </div>
    </aside>

    <!-- Overlay para móviles -->
    <div id="sidebar-overlay" 
         class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden hidden"></div>

    <!-- Contenido principal -->
    <main class="flex-1 md:ml-64 p-6 transition-all duration-300">
        <!-- Encabezado -->
        <div class="flex flex-col md:flex-row justify-between md:items-center mb-8">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 mb-1">Historial de Acciones (Bitácora)</h2>
                <p class="text-gray-500 text-sm">Registro de actividades del sistema.</p>
            </div>
            <div class="flex items-center gap-4 mt-3 md:mt-0">
                <div id="clock" class="text-sm text-gray-600 font-medium"></div>
                <button id="refresh-btn" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition flex items-center gap-2 border border-gray-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Actualizar
                </button>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 mb-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Filtros</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="filter-status" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select id="filter-status" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Todos los estados</option>
                        <option value="SUCCESS">Éxito</option>
                        <option value="ERROR">Error</option>
                    </select>
                </div>
                <div>
                    <label for="filter-action" class="block text-sm font-medium text-gray-700 mb-2">Acción</label>
                    <input type="text" id="filter-action" placeholder="Buscar por acción..." class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="filter-user" class="block text-sm font-medium text-gray-700 mb-2">Usuario (Código)</label>
                    <input type="text" id="filter-user" placeholder="Buscar por código de usuario..." class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>

        <!-- Tabla de bitácora -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha y Hora</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario (Código)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comentario</th>
                        </tr>
                    </thead>
                    <tbody id="bitacora-table-body" class="bg-white divide-y divide-gray-200 text-sm">
                        
                        <!-- Bucle de Blade para renderizar los datos -->
                        <?php $__empty_1 = true; $__currentLoopData = $bitacora; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="log-row hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                    <?php echo e(\Carbon\Carbon::parse($log['fecha_hora'])->format('d/m/Y H:i:s')); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800 font-medium user-cell">
                                    <?php echo e($log['codigo_usuario']); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800 action-cell">
                                    <?php echo e($log['accion']); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap status-cell">
                                    <?php if(strtoupper($log['estado']) == 'SUCCESS'): ?>
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Éxito
                                        </span>
                                    <?php elseif(strtoupper($log['estado']) == 'ERROR'): ?>
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Error
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            <?php echo e($log['estado']); ?>

                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <?php echo e($log['comentario']); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <!-- Estado vacío -->
                            <tr id="no-records">
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    No se encontraron registros en la bitácora.
                                </td>
                            </tr>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>
        </div>

        <!-- Información de paginación -->
        <div class="mt-4 flex items-center justify-between text-sm text-gray-600" id="table-footer-info">
            <div id="total-records">
                Mostrando <?php echo e(count($bitacora)); ?> de los últimos 30 registros.
            </div>
            <div id="last-update" class="text-xs text-gray-500">
                Última carga: <?php echo e(\Carbon\Carbon::now()->format('d/m/Y H:i:s')); ?>

            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center py-4 text-xs text-gray-500 border-t border-gray-200 bg-white mt-10 md:ml-64">
        © <?php echo e(date('Y')); ?> Grupo 32 — UAGRM | INF342 - SA
    </footer>

    <!-- JS: Este archivo ahora debe contener toda la lógica -->
    <script src="<?php echo e(asset('static/scripts/bitacora.js')); ?>"></script>
</body>
</html>
<?php /**PATH C:\Users\migue\OneDrive\Escritorio\projects\inf342_2exa\app\templates/admin_bitacora.blade.php ENDPATH**/ ?>