<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitácora — Plataforma Universitaria INF342</title>
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
            <div class="flex items-center gap-4">
                <!-- Botón de menú lateral para móviles -->
                <button id="menu-toggle" class="block md:hidden p-2 text-white hover:text-gold-500 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
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
                        <p class="text-xs text-slate-300 uppercase tracking-widest hidden md:block">Sistema de Gestión FICCT</p>
                    </div>
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
                    <?php echo e(strtoupper(substr($user['nomb_comp'], 0, 1))); ?>

                </div>

                <!-- Botón de inicio corporativo -->
                <a href="/"
                   class="text-sm bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 font-bold uppercase tracking-wide transition-all border-l-4 border-gold-500 hover:border-gold-400">
                    Inicio
                </a>
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

    <!-- Sidebar Administrativo Corporativo -->
    <aside id="admin-sidebar"
        class="fixed top-0 left-0 w-64 bg-navy-900 shadow-2xl h-full z-30 border-r-4 border-gold-500 transform -translate-x-full md:translate-x-0 transition-transform duration-300">

        <!-- Contenedor con scroll -->
        <div class="flex flex-col h-full overflow-y-auto">

            <!-- Encabezado corporativo -->
            <div class="p-5 border-b-4 border-gold-500 bg-navy-800">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-gold-500 flex items-center justify-center font-black text-navy-900 text-lg border-2 border-white">
                        FI
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-white uppercase tracking-wide">Panel de Administración</h3>
                        <p class="text-xs text-gold-500 mt-1 font-bold uppercase tracking-widest">Gestión del Sistema</p>
                    </div>
                </div>
            </div>

            <!-- Navegación corporativa -->
            <nav class="flex-1 p-3">
                <ul class="space-y-1 text-sm">

                    <li>
                        <a href="/admin/mod-adm"
                        class="flex items-center gap-3 px-3 py-2.5 text-slate-300 hover:bg-navy-800 hover:text-white hover:border-l-4 hover:border-gold-500 transition-all font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span class="text-xs uppercase tracking-wide">Panel Administrador</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/users"
                        class="flex items-center gap-3 px-3 py-2.5 text-slate-300 hover:bg-navy-800 hover:text-white hover:border-l-4 hover:border-gold-500 transition-all font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5M16 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span class="text-xs uppercase tracking-wide">Gestión de Usuarios</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/roles"
                        class="flex items-center gap-3 px-3 py-2.5 text-slate-300 hover:bg-navy-800 hover:text-white hover:border-l-4 hover:border-gold-500 transition-all font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2v1a2 2 0 01-2 2H9a2 2 0 01-2-2V9a2 2 0 012-2h6zm6 11h-3a2 2 0 01-2-2v-1a2 2 0 012-2h3v5zM6 18H3v-5h3a2 2 0 012 2v1a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="text-xs uppercase tracking-wide">Roles y Permisos</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/grupos"
                        class="flex items-center gap-3 px-3 py-2.5 text-slate-300 hover:bg-navy-800 hover:text-white hover:border-l-4 hover:border-gold-500 transition-all font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5M16 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span class="text-xs uppercase tracking-wide">Gestión de Grupos</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/aulas"
                        class="flex items-center gap-3 px-3 py-2.5 text-slate-300 hover:bg-navy-800 hover:text-white hover:border-l-4 hover:border-gold-500 transition-all font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7h18M3 12h18M3 17h18"/>
                            </svg>
                            <span class="text-xs uppercase tracking-wide">Gestión de Aulas</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/materias"
                        class="flex items-center gap-3 px-3 py-2.5 text-slate-300 hover:bg-navy-800 hover:text-white hover:border-l-4 hover:border-gold-500 transition-all font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 20l9-5-9-5-9 5 9 5zM12 12V4m0 8l9-5M12 12L3 7"/>
                            </svg>
                            <span class="text-xs uppercase tracking-wide">Gestión de Materias</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/gestiones"
                        class="flex items-center gap-3 px-3 py-2.5 text-slate-300 hover:bg-navy-800 hover:text-white hover:border-l-4 hover:border-gold-500 transition-all font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-xs uppercase tracking-wide">Gestiones</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/carga-horaria"
                        class="flex items-center gap-3 px-3 py-2.5 text-slate-300 hover:bg-navy-800 hover:text-white hover:border-l-4 hover:border-gold-500 transition-all font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-6h13v6M9 17H5v-6h4m0 6V7m0 0H3v4h2m4-4v4h2"/>
                            </svg>
                            <span class="text-xs uppercase tracking-wide">Carga Horaria</span>
                        </a>
                    </li>

                    <li>
                        <a href="/auto/generar-horario"
                        class="flex items-center gap-3 px-3 py-2.5 text-slate-300 hover:bg-navy-800 hover:text-white hover:border-l-4 hover:border-gold-500 transition-all font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-xs uppercase tracking-wide">Generar Horario</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/bitacora"
                        class="flex items-center gap-3 px-3 py-2.5 bg-gold-500 text-navy-900 border-l-4 border-white font-black hover:bg-gold-600 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                            </svg>
                            <span class="text-xs uppercase tracking-wide">Historial de Acciones</span>
                        </a>
                    </li>

                </ul>
            </nav>

            <!-- Footer corporativo -->
            <div class="p-4 border-t-2 border-gold-500 bg-navy-800 text-center">
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Módulo Admin v1.1</p>
            </div>
        </div>
    </aside>

    <!-- Overlay para móviles -->
    <div id="sidebar-overlay" 
         class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden hidden"></div>

    <!-- Contenido principal corporativo -->
    <main class="flex-1 md:ml-64 p-6 transition-all duration-300">
        <!-- Encabezado corporativo -->
        <div class="bg-white border-l-4 border-gold-500 shadow-md mb-8 px-6 py-5">
            <div class="flex flex-col md:flex-row justify-between md:items-center">
                <div>
                    <h2 class="text-3xl font-black text-navy-900 mb-1 uppercase tracking-tight">Historial de Acciones (Bitácora)</h2>
                    <p class="text-slate-600 text-sm font-semibold uppercase tracking-wide">Registro de actividades del sistema</p>
                </div>
                <div class="flex items-center gap-4 mt-3 md:mt-0">
                    <div id="clock" class="text-sm text-navy-800 font-bold bg-slate-100 px-4 py-2 border border-slate-300"></div>
                    <button id="refresh-btn" class="bg-navy-900 hover:bg-navy-800 text-white px-4 py-2.5 text-sm font-bold uppercase tracking-wide transition-all flex items-center gap-2 border-b-4 border-navy-800 hover:border-gold-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Actualizar
                    </button>
                </div>
            </div>
        </div>

        <!-- Filtros corporativos -->
        <div class="bg-white p-6 border-2 border-slate-300 shadow-lg mb-6">
            <div class="border-b-2 border-gold-500 pb-3 mb-5">
                <h3 class="text-lg font-black text-navy-900 uppercase tracking-wide">Filtros de Búsqueda</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="filter-status" class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">Estado</label>
                    <select id="filter-status" class="w-full border-2 border-slate-300 px-3 py-2.5 text-sm text-navy-900 font-semibold focus:outline-none focus:border-navy-700 focus:ring-2 focus:ring-navy-700/20 transition-all">
                        <option value="">Todos los estados</option>
                        <option value="SUCCESS">Éxito</option>
                        <option value="ERROR">Error</option>
                    </select>
                </div>
                <div>
                    <label for="filter-action" class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">Acción</label>
                    <input type="text" id="filter-action" placeholder="Buscar por acción..." class="w-full border-2 border-slate-300 px-3 py-2.5 text-sm text-navy-900 placeholder-slate-400 font-semibold focus:outline-none focus:border-navy-700 focus:ring-2 focus:ring-navy-700/20 transition-all">
                </div>
                <div>
                    <label for="filter-user" class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">Usuario (Código)</label>
                    <input type="text" id="filter-user" placeholder="Buscar por código de usuario..." class="w-full border-2 border-slate-300 px-3 py-2.5 text-sm text-navy-900 placeholder-slate-400 font-semibold focus:outline-none focus:border-navy-700 focus:ring-2 focus:ring-navy-700/20 transition-all">
                </div>
            </div>
        </div>

        <!-- Tabla de bitácora corporativa -->
        <div class="bg-white border-2 border-slate-300 shadow-lg overflow-hidden">
            <div class="bg-navy-900 px-6 py-4 border-b-4 border-gold-500">
                <h3 class="text-lg font-black text-white uppercase tracking-wide">Registro de Bitácora</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y-2 divide-slate-300">
                    <thead class="bg-slate-100">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black text-navy-900 uppercase tracking-widest border-r border-slate-300">Fecha y Hora</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black text-navy-900 uppercase tracking-widest border-r border-slate-300">Usuario (Código)</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black text-navy-900 uppercase tracking-widest border-r border-slate-300">Acción</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black text-navy-900 uppercase tracking-widest border-r border-slate-300">Estado</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black text-navy-900 uppercase tracking-widest">Comentario</th>
                        </tr>
                    </thead>
                    <tbody id="bitacora-table-body" class="bg-white divide-y divide-slate-200 text-sm">

                        <!-- Bucle de Blade para renderizar los datos -->
                        <?php $__empty_1 = true; $__currentLoopData = $bitacora; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="log-row hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-slate-700 font-semibold border-r border-slate-200">
                                    <?php echo e(\Carbon\Carbon::parse($log['fecha_hora'])->format('d/m/Y H:i:s')); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-navy-900 font-black user-cell border-r border-slate-200">
                                    <?php echo e($log['codigo_usuario']); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-navy-900 font-semibold action-cell border-r border-slate-200">
                                    <?php echo e($log['accion']); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap status-cell border-r border-slate-200">
                                    <?php if(strtoupper($log['estado']) == 'SUCCESS'): ?>
                                        <span class="px-3 py-1.5 inline-flex text-xs font-black uppercase tracking-wider border-2 border-green-600 bg-green-50 text-green-800">
                                            Éxito
                                        </span>
                                    <?php elseif(strtoupper($log['estado']) == 'ERROR'): ?>
                                        <span class="px-3 py-1.5 inline-flex text-xs font-black uppercase tracking-wider border-2 border-red-600 bg-red-50 text-red-800">
                                            Error
                                        </span>
                                    <?php else: ?>
                                        <span class="px-3 py-1.5 inline-flex text-xs font-black uppercase tracking-wider border-2 border-slate-400 bg-slate-100 text-slate-800">
                                            <?php echo e($log['estado']); ?>

                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-slate-700 font-medium">
                                    <?php echo e($log['comentario']); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <!-- Estado vacío -->
                            <tr id="no-records">
                                <td colspan="5" class="px-6 py-12 text-center text-slate-600 font-semibold uppercase tracking-wide">
                                    No se encontraron registros en la bitácora.
                                </td>
                            </tr>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>
        </div>

        <!-- Información de paginación corporativa -->
        <div class="mt-6 bg-white border-2 border-slate-300 px-6 py-4 flex items-center justify-between text-sm shadow-md" id="table-footer-info">
            <div id="total-records" class="font-bold text-navy-900 uppercase tracking-wide">
                Mostrando <?php echo e(count($bitacora)); ?> de los últimos 30 registros.
            </div>
            <div id="last-update" class="text-xs text-slate-600 font-semibold bg-slate-100 px-3 py-2 border border-slate-300">
                Última carga: <?php echo e(\Carbon\Carbon::now()->format('d/m/Y H:i:s')); ?>

            </div>
        </div>
    </main>

    <!-- Footer corporativo -->
    <footer class="text-center py-5 text-xs bg-navy-900 text-slate-300 border-t-4 border-gold-500 mt-12 md:ml-64">
        <p class="font-bold uppercase tracking-widest">© <?php echo e(date('Y')); ?> Grupo 31 — UAGRM | INF342 - SA</p>
    </footer>

    <script src="<?php echo e(asset('static/scripts/bitacora.js')); ?>"></script>
</body>
</html>
<?php /**PATH C:\Users\diego\OneDrive\Escritorio\exa2_inf342\app\templates/admin_bitacora.blade.php ENDPATH**/ ?>