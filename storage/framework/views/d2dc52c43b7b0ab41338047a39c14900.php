<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios — Plataforma Universitaria INF342</title>
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
                        class="flex items-center gap-3 px-3 py-2.5 bg-gold-500 text-navy-900 border-l-4 border-white font-black hover:bg-gold-600 transition-all">
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
                        class="flex items-center gap-3 px-3 py-2.5 text-slate-300 hover:bg-navy-800 hover:text-white hover:border-l-4 hover:border-gold-500 transition-all font-semibold">
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
                    <h2 class="text-3xl font-black text-navy-900 mb-1 uppercase tracking-tight">Gestión de Usuarios</h2>
                    <p class="text-slate-600 text-sm font-semibold uppercase tracking-wide">Administración de cuentas y roles del sistema</p>
                </div>
                <div class="flex items-center gap-4 mt-3 md:mt-0">
                    <div id="clock" class="text-sm text-navy-800 font-bold bg-slate-100 px-4 py-2 border border-slate-300"></div>
                    <!-- Botón de Agregar Usuario -->
                    <button id="btn-add-user" class="bg-navy-900 hover:bg-navy-800 text-white px-4 py-2.5 text-sm font-bold uppercase tracking-wide transition-all flex items-center gap-2 border-b-4 border-navy-800 hover:border-gold-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Agregar Usuario
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
                    <label for="filter-nombre" class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">Nombre</label>
                    <input type="text" id="filter-nombre" placeholder="Buscar por nombre..." class="w-full border-2 border-slate-300 px-3 py-2.5 text-sm text-navy-900 placeholder-slate-400 font-semibold focus:outline-none focus:border-navy-700 focus:ring-2 focus:ring-navy-700/20 transition-all">
                </div>
                <div>
                    <label for="filter-ci-codigo" class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">CI o Código</label>
                    <input type="text" id="filter-ci-codigo" placeholder="Buscar por CI o Código..." class="w-full border-2 border-slate-300 px-3 py-2.5 text-sm text-navy-900 placeholder-slate-400 font-semibold focus:outline-none focus:border-navy-700 focus:ring-2 focus:ring-navy-700/20 transition-all">
                </div>
                <div>
                    <label for="filter-rol" class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">Rol</label>
                    <select id="filter-rol" class="w-full border-2 border-slate-300 px-3 py-2.5 text-sm text-navy-900 font-semibold focus:outline-none focus:border-navy-700 focus:ring-2 focus:ring-navy-700/20 transition-all">
                        <option value="">Todos los roles</option>
                        <option value="admin">Administrador</option>
                        <option value="docente">Docente</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tabla de Usuarios Corporativa -->
        <div class="bg-white border-2 border-slate-300 shadow-lg overflow-hidden">
            <div class="bg-navy-900 px-6 py-4 border-b-4 border-gold-500">
                <h3 class="text-lg font-black text-white uppercase tracking-wide">Registro de Usuarios</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y-2 divide-slate-300">
                    <thead class="bg-slate-100">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black text-navy-900 uppercase tracking-widest border-r border-slate-300">Código</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black text-navy-900 uppercase tracking-widest border-r border-slate-300">CI</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black text-navy-900 uppercase tracking-widest border-r border-slate-300">Nombre Completo</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black text-navy-900 uppercase tracking-widest border-r border-slate-300">Rol</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black text-navy-900 uppercase tracking-widest border-r border-slate-300">Contacto</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-black text-navy-900 uppercase tracking-widest">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="usuarios-table-body" class="bg-white divide-y divide-slate-200 text-sm">

                        <!-- Bucle de Blade para renderizar los datos -->
                        <?php $__empty_1 = true; $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usuario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="user-row hover:bg-slate-50 transition-colors" data-user-id="<?php echo e($usuario['codigo']); ?>">
                                <td class="px-6 py-4 whitespace-nowrap text-navy-900 font-black codigo-cell border-r border-slate-200">
                                    <?php echo e($usuario['codigo']); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-700 font-semibold ci-cell border-r border-slate-200">
                                    <?php echo e($usuario['ci']); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-navy-900 font-semibold nombre-cell border-r border-slate-200">
                                    <?php echo e($usuario['nomb_comp']); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap rol-cell border-r border-slate-200">
                                    <span class="px-3 py-1.5 inline-flex text-xs font-black uppercase tracking-wider border-2 border-navy-800 bg-navy-50 text-navy-900">
                                        <?php echo e($usuario['rol']); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-700 font-medium border-r border-slate-200">
                                    <div class="flex flex-col">
                                        <span><?php echo e($usuario['correo'] ?? 'Sin correo'); ?></span>
                                        <span class="text-xs text-slate-500"><?php echo e($usuario['tel'] ?? 'Sin teléfono'); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Botón Editar -->
                                        <button data-id="<?php echo e($usuario['codigo']); ?>"
                                                data-ci="<?php echo e($usuario['ci']); ?>"
                                                data-nombre="<?php echo e($usuario['nomb_comp']); ?>"
                                                data-correo="<?php echo e($usuario['correo'] ?? ''); ?>"
                                                data-tel="<?php echo e($usuario['tel'] ?? ''); ?>"
                                                data-rol="<?php echo e($usuario['rol']); ?>"
                                                class="btn-edit bg-navy-900 hover:bg-navy-800 text-white p-2 transition-all border-b-2 border-navy-800 hover:border-gold-500"
                                                title="Editar Usuario">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        <!-- Botón Eliminar -->
                                        <button data-id="<?php echo e($usuario['codigo']); ?>" data-nombre="<?php echo e($usuario['nomb_comp']); ?>" class="btn-delete bg-red-700 hover:bg-red-800 text-white p-2 transition-all border-b-2 border-red-800 hover:border-red-600" title="Eliminar Usuario">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <!-- Estado vacío -->
                            <tr id="no-records">
                                <td colspan="6" class="px-6 py-12 text-center text-slate-600 font-semibold uppercase tracking-wide">
                                    No se encontraron usuarios registrados.
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
                Mostrando <?php echo e(count($usuarios)); ?> registros.
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

    <!-- ====== INICIO DE MODALES CORPORATIVOS ====== -->

    <!-- Modal de Formulario (Agregar/Editar Usuario) -->
    <div id="user-form-modal" class="fixed inset-0 bg-navy-900/70 backdrop-blur-sm z-[60] flex items-center justify-center p-4 hidden">

        <!-- Contenedor del modal -->
        <div class="bg-white border-2 border-slate-300 shadow-2xl w-full max-w-2xl flex flex-col max-h-[90vh]">

            <!-- Encabezado del Modal corporativo -->
            <div class="flex-shrink-0 bg-navy-900 px-6 py-4 border-b-4 border-gold-500 flex items-center justify-between">
                <h3 id="form-modal-title" class="text-lg font-black text-white uppercase tracking-wide">Agregar Nuevo Usuario</h3>
                <button id="btn-cancel-form-x" class="text-slate-300 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Formulario -->
            <form id="user-form" class="flex-1 flex flex-col min-h-0">
                <input type="hidden" id="form-user-id" name="id" value="">

                <!-- Área de campos con scroll -->
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 overflow-y-auto">
                    <!-- CI -->
                    <div>
                        <label for="form-ci" class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">CI</label>
                        <input type="text" id="form-ci" name="ci" class="w-full border-2 border-slate-300 px-3 py-2.5 text-sm text-navy-900 font-semibold focus:outline-none focus:border-navy-700 focus:ring-2 focus:ring-navy-700/20 transition-all" required>
                    </div>
                    <!-- Nombre Completo -->
                    <div>
                        <label for="form-nomb_comp" class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">Nombre Completo</label>
                        <input type="text" id="form-nomb_comp" name="nomb_comp" class="w-full border-2 border-slate-300 px-3 py-2.5 text-sm text-navy-900 font-semibold focus:outline-none focus:border-navy-700 focus:ring-2 focus:ring-navy-700/20 transition-all" required>
                    </div>
                    <!-- Fecha Nacimiento -->
                    <div>
                        <label for="form-fecha_nac" class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">Fecha Nacimiento</label>
                        <input type="date" id="form-fecha_nac" name="fecha_nac" class="w-full border-2 border-slate-300 px-3 py-2.5 text-sm text-navy-900 font-semibold focus:outline-none focus:border-navy-700 focus:ring-2 focus:ring-navy-700/20 transition-all">
                    </div>
                    <!-- Profesión -->
                    <div>
                        <label for="form-profesion" class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">Profesión</label>
                        <input type="text" id="form-profesion" name="profesion" class="w-full border-2 border-slate-300 px-3 py-2.5 text-sm text-navy-900 font-semibold focus:outline-none focus:border-navy-700 focus:ring-2 focus:ring-navy-700/20 transition-all">
                    </div>
                    <!-- Correo -->
                    <div class="md:col-span-2">
                        <label for="form-correo" class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">Correo</label>
                        <input type="email" id="form-correo" name="correo" class="w-full border-2 border-slate-300 px-3 py-2.5 text-sm text-navy-900 font-semibold focus:outline-none focus:border-navy-700 focus:ring-2 focus:ring-navy-700/20 transition-all">
                    </div>
                    <!-- Teléfono -->
                    <div>
                        <label for="form-tel" class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">Teléfono</label>
                        <input type="tel" id="form-tel" name="tel" class="w-full border-2 border-slate-300 px-3 py-2.5 text-sm text-navy-900 font-semibold focus:outline-none focus:border-navy-700 focus:ring-2 focus:ring-navy-700/20 transition-all">
                    </div>
                    <!-- Rol -->
                    <div>
                        <label for="form-rol" class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">Rol</label>
                        <select id="form-rol" name="rol" class="w-full border-2 border-slate-300 px-3 py-2.5 text-sm text-navy-900 font-semibold focus:outline-none focus:border-navy-700 focus:ring-2 focus:ring-navy-700/20 transition-all" required>
                            <option value="">Seleccione un rol...</option>
                            <option value="admin">Administrador</option>
                            <option value="docente">Docente</option>
                        </select>
                    </div>
                    <!-- Contraseña -->
                    <div class="md:col-span-2">
                        <label for="form-password" class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">Contraseña</label>
                        <input type="password" id="form-password" name="password" class="w-full border-2 border-slate-300 px-3 py-2.5 text-sm text-navy-900 font-semibold focus:outline-none focus:border-navy-700 focus:ring-2 focus:ring-navy-700/20 transition-all">
                        <p id="password-help-text" class="text-xs text-slate-500 mt-1 hidden font-medium">Dejar en blanco para no cambiar la contraseña.</p>
                    </div>
                </div>

                <!-- Footer del Formulario (Acciones) -->
                <div class="flex-shrink-0 bg-slate-50 px-6 py-4 flex justify-end gap-3 border-t border-slate-200">
                    <button type="button" id="btn-cancel-form" class="text-sm font-bold text-navy-900 bg-white border-2 border-slate-300 px-5 py-2.5 hover:bg-slate-100 transition-all uppercase tracking-wide">
                        Cancelar
                    </button>
                    <button type="submit" id="btn-save-form" class="text-sm font-bold text-white bg-navy-900 px-5 py-2.5 hover:bg-navy-800 transition-all border-b-4 border-navy-800 hover:border-gold-500 uppercase tracking-wide">
                        Guardar
                    </button>
                </div>
            </form>
        </div>

    </div>


    <!-- Modal de Confirmación de Eliminación -->
    <div id="delete-modal" class="fixed inset-0 bg-navy-900/70 backdrop-blur-sm z-[60] flex items-center justify-center p-4 hidden">
        <div class="bg-white border-2 border-slate-300 shadow-2xl w-full max-w-md">
            <div class="bg-navy-900 px-6 py-4 border-b-4 border-gold-500">
                <h3 class="text-lg font-black text-white uppercase tracking-wide">Eliminar Usuario</h3>
            </div>
            <div class="p-6">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-red-100 border-2 border-red-600">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-700 font-semibold leading-relaxed">
                            ¿Estás seguro de que deseas eliminar al usuario <strong id="delete-user-name" class="font-black text-navy-900">...</strong>? Esta acción no se puede deshacer.
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 border-t border-slate-200">
                <button id="btn-cancel-delete" class="text-sm font-bold text-navy-900 bg-white border-2 border-slate-300 px-5 py-2.5 hover:bg-slate-100 transition-all uppercase tracking-wide">
                    Cancelar
                </button>
                <button id="btn-confirm-delete" class="text-sm font-bold text-white bg-red-700 px-5 py-2.5 hover:bg-red-800 transition-all border-b-4 border-red-800 hover:border-red-600 uppercase tracking-wide">
                    Sí, eliminar
                </button>
            </div>
        </div>
    </div>

    <!-- ====== FIN DE MODALES ====== -->


    <script src="<?php echo e(asset('static/scripts/admin_users.js')); ?>"></script>
</body>
</html>
<?php /**PATH C:\Users\diego\OneDrive\Escritorio\exa2_inf342\app\templates/admin_users.blade.php ENDPATH**/ ?>