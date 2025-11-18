<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Aulas — Plataforma Universitaria INF342</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'navy': {
                            900: '#0f2942',
                            800: '#1e3a5f',
                            700: '#2c5f8d'
                        },
                        'gold': {
                            500: '#c9a961',
                            600: '#b8974f'
                        }
                    },
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
</head>

<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col font-sans antialiased">

     <!-- Barra superior -->
    <header class="bg-navy-900 border-b-4 border-gold-500 sticky top-0 z-40 shadow-lg">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-3">
            <div class="flex items-center gap-4">
                <!-- Botón de menú lateral para móviles -->
                <button id="menu-toggle" class="block md:hidden p-2 text-gold-500 hover:text-gold-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h1 class="text-lg md:text-xl font-black text-white uppercase tracking-wide">
                    Plataforma Universitaria
                </h1>
            </div>

            <div class="flex items-center gap-4">
                <div class="hidden sm:block text-right">
                    <p class="font-bold text-white text-sm"><?php echo e($user['nomb_comp']); ?></p>
                    <p class="text-xs text-gold-500 uppercase tracking-wide font-semibold"><?php echo e(ucfirst($user['rol'])); ?></p>
                </div>

                <!-- Avatar -->
                <div id="user-avatar"
                     class="w-10 h-10 border-2 border-gold-500 bg-navy-800 text-gold-500 flex items-center justify-center font-black shadow-md cursor-pointer select-none">
                    <?php echo e(strtoupper(substr($user['nomb_comp'], 0, 1))); ?>

                </div>

                <!-- Botón de inicio -->
                <a href="/"
                   class="text-sm bg-gold-500 hover:bg-gold-600 text-navy-900 px-4 py-2 border-b-4 border-gold-600 font-bold uppercase tracking-wide transition">
                    Inicio
                </a>
            </div>
        </div>
    </header>
     <!-- Panel lateral de usuario (copiado del index, necesario para el avatar) -->
    <aside id="user-aside"
           class="hidden fixed top-16 right-4 w-64 bg-white shadow-2xl border-2 border-slate-300 z-50 transition-all duration-300 opacity-0 scale-95 origin-top-right">
        <div class="p-5 text-sm text-slate-700">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 border-2 border-gold-500 bg-navy-900 text-gold-500 flex items-center justify-center font-black shadow-sm">
                    <?php echo e(strtoupper(substr($user['nomb_comp'],0,1))); ?>

                </div>
                <div>
                    <p class="font-bold text-navy-900 leading-tight uppercase tracking-wide text-xs"><?php echo e($user['nomb_comp']); ?></p>
                    <span class="text-xs px-2 py-0.5 border border-gold-500 bg-navy-900 text-gold-500 font-bold uppercase tracking-wide">
                        <?php echo e(ucfirst($user['rol'])); ?>

                    </span>
                </div>
            </div>
            <hr class="my-3 border-slate-300">
            <ul class="space-y-2 text-sm">
                <li><span class="font-bold text-slate-600 uppercase tracking-wide text-xs">CI:</span> <?php echo e($user['ci']); ?></li>
                <li><span class="font-bold text-slate-600 uppercase tracking-wide text-xs">Correo:</span> <?php echo e($user['correo'] ?? '—'); ?></li>
                <li><span class="font-bold text-slate-600 uppercase tracking-wide text-xs">Teléfono:</span> <?php echo e($user['tel'] ?? '—'); ?></li>
            </ul>
            <div class="mt-4 pt-3 border-t-2 border-gold-500">
                <a href="/perfil"
                   class="text-navy-900 text-sm font-bold uppercase tracking-wide hover:text-gold-600 transition">
                    Ver perfil completo →
                </a>
            </div>
        </div>
    </aside>

    <!-- Panel lateral de usuario -->
    <aside id="admin-sidebar"
        class="fixed top-0 left-0 w-64 bg-navy-900 shadow-2xl h-full z-30 border-r-4 border-gold-500 transform -translate-x-full md:translate-x-0 transition-transform duration-300">

        <!-- Contenedor con scroll -->
        <div class="flex flex-col h-full overflow-y-auto scrollbar-thin scrollbar-thumb-gold-500 scrollbar-track-navy-800">

            <!-- Encabezado -->
            <div class="p-4 border-b-2 border-gold-500">
                <h3 class="text-sm font-black text-white uppercase tracking-wide">Panel de Administración</h3>
                <p class="text-xs text-gold-500 mt-1 font-bold uppercase tracking-wide">Gestión completa del sistema</p>
            </div>

            <!-- Navegación -->
            <nav class="flex-1 p-3">
                <ul class="space-y-1 text-sm">

                    <li>
                        <a href="/admin/mod-adm"
                        class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 border-l-4 border-transparent hover:border-gold-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span class="font-semibold">Panel Administrador</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/users"
                        class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 border-l-4 border-transparent hover:border-gold-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5M16 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span class="font-semibold">Gestión de Usuarios</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/roles"
                        class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 border-l-4 border-transparent hover:border-gold-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2v1a2 2 0 01-2 2H9a2 2 0 01-2-2V9a2 2 0 012-2h6zm6 11h-3a2 2 0 01-2-2v-1a2 2 0 012-2h3v5zM6 18H3v-5h3a2 2 0 012 2v1a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="font-semibold">Gestión de Roles y Permisos</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/grupos"
                        class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 border-l-4 border-transparent hover:border-gold-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5M16 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span class="font-semibold">Gestión de Grupos</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/aulas"
                        class="flex items-center gap-2 px-3 py-2 text-gold-500 bg-navy-800 border-l-4 border-gold-500 font-black hover:bg-navy-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7h18M3 12h18M3 17h18"/>
                            </svg>
                            <span>Gestión de Aulas</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/materias"
                        class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 border-l-4 border-transparent hover:border-gold-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 20l9-5-9-5-9 5 9 5zM12 12V4m0 8l9-5M12 12L3 7"/>
                            </svg>
                            <span class="font-semibold">Gestión de Materias</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/gestiones"
                        class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 border-l-4 border-transparent hover:border-gold-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="font-semibold">Gestión de Gestiones</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/carga-horaria"
                        class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 border-l-4 border-transparent hover:border-gold-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-6h13v6M9 17H5v-6h4m0 6V7m0 0H3v4h2m4-4v4h2"/>
                            </svg>
                            <span class="font-semibold">Carga Horaria del Docente</span>
                        </a>
                    </li>

                    <li>
                        <a href="/auto/generar-horario"
                        class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 border-l-4 border-transparent hover:border-gold-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="font-semibold">Generar Horario</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/bitacora"
                        class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 border-l-4 border-transparent hover:border-gold-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                            </svg>
                            <span class="font-semibold">Consultar Historial de Acciones</span>
                        </a>
                    </li>

                </ul>
            </nav>

            <!-- Footer -->
            <div class="p-3 border-t-2 border-gold-500 text-center text-[11px] text-gold-500 font-bold uppercase tracking-wide">
                Módulo Admin v1.1
            </div>
        </div>
    </aside>

       <!-- Overlay para móviles -->
    <div id="sidebar-overlay"
         class="fixed inset-0 bg-navy-900 bg-opacity-70 backdrop-blur-sm z-20 md:hidden hidden"></div>

    <!-- Contenido principal -->
    <main class="flex-1 md:ml-64 p-6 transition-all duration-300">
        <!-- Encabezado -->


        <!-- Encabezado -->
        <div class="flex flex-col md:flex-row justify-between md:items-center mb-8 gap-4">
            <div class="bg-white border-l-4 border-gold-500 shadow-md px-6 py-5">
                <h2 class="text-2xl md:text-3xl font-black text-navy-900 uppercase tracking-tight mb-1">Aulas del Sistema</h2>
                <p class="text-slate-600 text-sm font-semibold uppercase tracking-wide">Cree, edite o elimine aulas según sea necesario.</p>
            </div>
            <div class="flex gap-3">
                <button id="btn-consultar-horarios" class="bg-green-600 hover:bg-green-700 text-white px-5 py-3 border-b-4 border-green-700 text-sm font-bold uppercase tracking-wide transition flex items-center gap-2 shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Consultar Horarios
                </button>
                <button id="btn-add" class="bg-navy-900 hover:bg-navy-800 text-white px-5 py-3 border-b-4 border-gold-500 text-sm font-bold uppercase tracking-wide transition flex items-center gap-2 shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Agregar Aula
                </button>
            </div>
        </div>
        <!-- Tabla de aulas -->
        <div class="bg-white shadow-md border-2 border-slate-300 overflow-hidden">
            <div class="bg-navy-900 px-6 py-4 border-b-4 border-gold-500">
                <h3 class="text-lg font-black text-white uppercase tracking-wide">Registro de Aulas</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-100">
                    <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-black text-navy-900 uppercase tracking-wider">#</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-black text-navy-900 uppercase tracking-wider">Número de Aula</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-black text-navy-900 uppercase tracking-wider">Capacidad</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-black text-navy-900 uppercase tracking-wider">Módulo</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-black text-navy-900 uppercase tracking-wider">Tipo</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-black text-navy-900 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                       <tbody id="aulas-table-body" class="bg-white divide-y divide-slate-200 text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $aulas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $aula): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="aula-row hover:bg-slate-50" data-aula-nro="<?php echo e($aula['nro']); ?>">
                            <td class="px-6 py-4 text-slate-700 font-semibold"><?php echo e($index + 1); ?></td>
                            <td class="px-6 py-4 text-navy-900 font-bold"><?php echo e($aula['nro']); ?></td>
                            <td class="px-6 py-4 text-slate-600 font-medium"><?php echo e($aula['capacidad']); ?></td>
                            <td class="px-6 py-4 text-slate-600 font-medium"><?php echo e($aula['modulo']); ?></td>
                            <td class="px-6 py-4 text-slate-600">
                                <span class="inline-flex items-center px-3 py-1 border border-gold-500 text-xs font-bold bg-navy-900 text-gold-500 uppercase tracking-wide">
                                    <?php echo e($aula['tipo']); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button data-nro="<?php echo e($aula['nro']); ?>" data-capacidad="<?php echo e($aula['capacidad']); ?>" data-modulo="<?php echo e($aula['modulo']); ?>" data-tipo="<?php echo e($aula['tipo']); ?>"
                                    class="btn-edit text-navy-900 hover:text-gold-600 p-1 hover:bg-slate-100 transition" title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                </button>
                                <button  data-nro="<?php echo e($aula['nro']); ?>"
                                    class="btn-ver-horario text-green-600 hover:text-green-800 p-1 hover:bg-green-50 transition" title="Ver Horario">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                                <button data-nro="<?php echo e($aula['nro']); ?>" data-capacidad="<?php echo e($aula['capacidad']); ?>"
                                    class="btn-delete text-red-600 hover:text-red-800 p-1 hover:bg-red-50 transition" title="Eliminar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="px-6 py-8 text-center text-slate-500 font-medium">No hay aulas registradas.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4 text-sm text-slate-600 font-semibold">Mostrando <?php echo e(count($aulas)); ?> aulas.</div>
    </main>

    <!-- Pie de página -->
    <footer class="text-center py-4 text-xs text-slate-500 border-t-2 border-slate-200 mt-auto font-semibold uppercase tracking-wide">
        © 2025 Plataforma Universitaria — Todos los derechos reservados
    </footer>

    <!-- Modal de Formulario (Agregar/Editar Aula) -->
    <div id="aula-form-modal" class="fixed inset-0 bg-navy-900 bg-opacity-70 backdrop-blur-sm z-[60] flex items-center justify-center p-4 hidden">

        <!-- Contenedor del modal con altura máxima y flex-col -->
        <div class="bg-white border-2 border-slate-300 shadow-2xl w-full max-w-2xl flex flex-col max-h-[90vh]">

            <!-- Encabezado del Modal (fijo) -->
            <div class="flex-shrink-0 bg-navy-900 px-6 py-4 border-b-4 border-gold-500 flex items-center justify-between">
                <h3 id="form-modal-title" class="text-lg font-black text-white uppercase tracking-wide">Agregar Nueva Aula</h3>
                <button id="btn-cancel-form-x" class="text-gold-500 hover:text-gold-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Formulario (con contenedor flex para scroll) -->
            <form id="aula-form" class="flex-1 flex flex-col min-h-0">
                <input type="hidden" id="form-aula-nro" name="nro" value="">

                <!-- Área de campos con scroll -->
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 overflow-y-auto">

                    <!-- Número de Aula -->
                    <div>
                        <label for="form-nro" class="block text-sm font-bold text-navy-900 mb-2 uppercase tracking-wide">Número de Aula</label>
                        <input type="text" id="form-nro" name="nro" class="w-full border-2 border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500" required>
                    </div>

                    <!-- Capacidad -->
                    <div>
                        <label for="form-capacidad" class="block text-sm font-bold text-navy-900 mb-2 uppercase tracking-wide">Capacidad</label>
                        <input type="number" id="form-capacidad" name="capacidad" class="w-full border-2 border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500" required min="1">
                    </div>

                    <!-- Módulo -->
                    <div>
                        <label for="form-modulo" class="block text-sm font-bold text-navy-900 mb-2 uppercase tracking-wide">Módulo</label>
                        <input type="text" id="form-modulo" name="modulo" class="w-full border-2 border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500" required>
                    </div>

                    <!-- Tipo -->
                    <div>
                        <label for="form-tipo" class="block text-sm font-bold text-navy-900 mb-2 uppercase tracking-wide">Tipo</label>
                        <select id="form-tipo" name="tipo" class="w-full border-2 border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500" required>
                            <option value="">Seleccione un tipo</option>
                            <option value="Teórica">Teórica</option>
                            <option value="Práctica">Práctica</option>
                            <option value="Laboratorio">Laboratorio</option>
                            <option value="Mixta">Mixta</option>
                        </select>
                    </div>

                </div>

                <!-- Footer del Formulario (Acciones) (fijo) -->
                <div class="flex-shrink-0 bg-slate-100 px-6 py-4 flex justify-end gap-3 border-t-2 border-slate-300">
                    <button type="button" id="btn-cancel-form" class="text-sm font-bold text-slate-700 bg-white border-2 border-slate-300 px-5 py-2 hover:bg-slate-50 transition uppercase tracking-wide">
                        Cancelar
                    </button>
                    <button type="submit" id="btn-save-form" class="text-sm font-bold text-white bg-navy-900 px-5 py-2 border-b-4 border-gold-500 hover:bg-navy-800 transition uppercase tracking-wide">
                        Guardar
                    </button>
                </div>
            </form>
        </div>

    </div>


    <!-- Modal de Confirmación de Eliminación -->
    <div id="delete-modal" class="fixed inset-0 bg-navy-900 bg-opacity-70 backdrop-blur-sm z-[60] flex items-center justify-center p-4 hidden">
        <div class="bg-white border-2 border-slate-300 shadow-2xl w-full max-w-md">
            <div class="bg-navy-900 px-6 py-4 border-b-4 border-gold-500">
                <h3 class="text-lg font-black text-white uppercase tracking-wide">Eliminar Aula</h3>
            </div>
            <div class="p-6">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center border-2 border-red-600 bg-red-100">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-700 font-medium">
                            ¿Estás seguro de que deseas eliminar el aula <strong id="delete-aula-nro" class="font-black text-navy-900">...</strong> con capacidad para <strong id="delete-aula-capacidad" class="font-black text-navy-900">...</strong> personas? Esta acción no se puede deshacer.
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-slate-100 px-6 py-4 flex justify-end gap-3 border-t-2 border-slate-300">
                <button id="btn-cancel-delete" class="text-sm font-bold text-slate-700 bg-white border-2 border-slate-300 px-5 py-2 hover:bg-slate-50 transition uppercase tracking-wide">
                    Cancelar
                </button>
                <button id="btn-confirm-delete" class="text-sm font-bold text-white bg-red-600 px-5 py-2 border-b-4 border-red-700 hover:bg-red-700 transition uppercase tracking-wide">
                    Sí, eliminar
                </button>
            </div>
        </div>
    </div>

    <!-- ====== FIN DE MODALES ====== -->

    <!-- Modal de Selección de Aulas para Consultar Horario -->
    <div id="seleccion-aulas-modal" class="fixed inset-0 bg-navy-900 bg-opacity-70 backdrop-blur-sm z-[60] flex items-center justify-center p-4 hidden">
        <div class="bg-white border-2 border-slate-300 shadow-2xl w-full max-w-4xl flex flex-col max-h-[90vh]">

            <!-- Encabezado -->
            <div class="flex-shrink-0 bg-navy-900 px-6 py-4 border-b-4 border-gold-500 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-white uppercase tracking-wide">Consultar Horarios de Aulas</h3>
                    <p class="text-sm text-gold-500 mt-1 font-semibold uppercase tracking-wide">Seleccione un aula para ver su horario semanal</p>
                </div>
                <button id="btn-close-seleccion" class="text-gold-500 hover:text-gold-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Lista de Aulas en formato Paquetes -->
            <div class="p-6 overflow-y-auto">
                <div id="aulas-lista-horarios" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php $__currentLoopData = $aulas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $aula): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $isNavy = $index % 2 === 0; ?>
                        <button data-aula-nro="<?php echo e($aula['nro']); ?>" data-aula-capacidad="<?php echo e($aula['capacidad']); ?>" data-aula-modulo="<?php echo e($aula['modulo']); ?>" data-aula-tipo="<?php echo e($aula['tipo']); ?>"
                            class="aula-card-selectable group bg-white border-4 <?php echo e($isNavy ? 'border-navy-900 hover:border-gold-500' : 'border-gold-500 hover:border-navy-900'); ?> shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 overflow-hidden text-left">
                            <!-- Header del Paquete -->
                            <div class="p-5 text-center <?php echo e($isNavy ? 'bg-navy-900 border-b-4 border-gold-500' : 'bg-gold-500 border-b-4 border-navy-900'); ?>">
                                <div class="w-16 h-16 mx-auto <?php echo e($isNavy ? 'bg-gold-500' : 'bg-navy-900'); ?> flex items-center justify-center text-4xl mb-3 border-4 border-white">
                                    <svg class="w-8 h-8 <?php echo e($isNavy ? 'text-navy-900' : 'text-gold-500'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <span class="inline-block <?php echo e($isNavy ? 'bg-gold-500 text-navy-900' : 'bg-navy-900 text-gold-500'); ?> px-3 py-1 text-xs font-black uppercase tracking-wider">
                                    <?php echo e($aula['tipo']); ?>

                                </span>
                            </div>

                            <!-- Contenido del Paquete -->
                            <div class="p-5">
                                <h4 class="text-2xl font-black text-navy-900 mb-3 uppercase tracking-wide text-center">Aula <?php echo e($aula['nro']); ?></h4>

                                <!-- Mini estadísticas -->
                                <div class="grid grid-cols-2 gap-3 mb-4">
                                    <div class="bg-slate-50 p-3 border-l-4 border-navy-900 text-center">
                                        <p class="text-xs uppercase font-bold text-slate-600 mb-1">Capacidad</p>
                                        <p class="text-xl font-black text-navy-900"><?php echo e($aula['capacidad']); ?></p>
                                    </div>
                                    <div class="bg-slate-50 p-3 border-l-4 border-gold-500 text-center">
                                        <p class="text-xs uppercase font-bold text-slate-600 mb-1">Módulo</p>
                                        <p class="text-xl font-black text-gold-600"><?php echo e($aula['modulo']); ?></p>
                                    </div>
                                </div>

                                <!-- Botón de acción -->
                                <div class="w-full py-2.5 <?php echo e($isNavy ? 'bg-navy-900' : 'bg-gold-500'); ?> <?php echo e($isNavy ? 'text-white' : 'text-navy-900'); ?> font-bold uppercase tracking-wide text-center text-sm border-b-4 <?php echo e($isNavy ? 'border-navy-800' : 'border-gold-600'); ?> flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Ver Horario</span>
                                </div>
                            </div>
                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex-shrink-0 bg-slate-100 px-6 py-4 flex justify-end border-t-2 border-slate-300">
                <button id="btn-cancelar-seleccion" class="text-sm font-bold text-slate-700 bg-white border-2 border-slate-300 px-5 py-2 hover:bg-slate-50 transition uppercase tracking-wide">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Horario del Aula -->
    <div id="horario-aula-modal" class="fixed inset-0 bg-navy-900 bg-opacity-70 backdrop-blur-sm z-[70] flex items-center justify-center p-4 hidden">
        <div class="bg-white border-2 border-slate-300 shadow-2xl w-full max-w-7xl flex flex-col max-h-[95vh]">

            <!-- Encabezado -->
            <div class="flex-shrink-0 flex items-center justify-between p-5 border-b-4 border-gold-500 bg-navy-900">
                <div class="flex-1">
                    <h3 class="text-xl font-black text-white uppercase tracking-wide">Horario del Aula <span id="horario-aula-numero" class="text-gold-500"></span></h3>
                    <p class="text-sm text-gold-500 mt-1 font-semibold uppercase tracking-wide">
                        <span id="horario-aula-info"></span>
                    </p>
                </div>

                <!-- Selector de Gestión -->
                <div class="flex items-center gap-3 mr-4">
                    <label for="select-gestion-horario" class="text-sm font-bold text-white uppercase tracking-wide">Gestión:</label>
                    <select id="select-gestion-horario"
                            class="border-2 border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
                        <option value="">Seleccione gestión...</option>
                        <?php if(isset($gestiones) && is_array($gestiones) && count($gestiones) > 0): ?>
                            <?php $__currentLoopData = $gestiones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gestion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($gestion['id']); ?>"><?php echo e($gestion['nombre']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </select>
                </div>

                <button id="btn-close-horario" class="text-gold-500 hover:text-gold-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Mensaje: Seleccione Gestión -->
            <div id="horario-sin-gestion" class="flex-1 flex items-center justify-center py-12">
                <div class="text-center">
                    <svg class="w-16 h-16 mx-auto text-navy-900 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-navy-900 text-lg font-black uppercase tracking-wide">Seleccione una gestión para ver el horario</p>
                    <p class="text-slate-600 text-sm mt-2 font-semibold">Use el selector de gestión arriba</p>
                </div>
            </div>

            <!-- Horario -->
            <div class="flex-1 overflow-auto p-6 hidden" id="horario-content-wrapper">
                <div id="horario-loading" class="text-center py-12 hidden">
                    <svg class="animate-spin h-12 w-12 mx-auto text-navy-900" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="mt-4 text-navy-900 font-bold uppercase tracking-wide">Cargando horario...</p>
                </div>

                <div id="horario-vacio" class="text-center py-12 hidden">
                    <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-navy-900 text-lg font-black uppercase tracking-wide">Esta aula no tiene clases asignadas</p>
                    <p class="text-slate-600 text-sm mt-2 font-semibold">El aula está completamente disponible</p>
                </div>

                <!-- Horario en formato Timeline por Día -->
                <div id="horario-tabla-container" class="hidden">
                    <div id="horario-timeline-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Se llenará dinámicamente con JS -->
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex-shrink-0 bg-slate-100 px-6 py-4 flex justify-between items-center border-t-2 border-slate-300">
                <div class="flex gap-4 text-xs">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-navy-900 border-2 border-gold-500"></div>
                        <span class="text-slate-700 font-bold uppercase tracking-wide">Bloques ocupados</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-slate-100 border-2 border-slate-300"></div>
                        <span class="text-slate-700 font-bold uppercase tracking-wide">Bloques libres</span>
                    </div>
                </div>
                <button id="btn-cerrar-horario" class="text-sm font-bold text-white bg-navy-900 px-5 py-2 border-b-4 border-gold-500 hover:bg-navy-800 transition uppercase tracking-wide">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- JavaScript Externo -->
    <script src="/static/scripts/admin_aulas.js"></script>

</body>
</html>
<?php /**PATH C:\Users\diego\OneDrive\Escritorio\exa2_inf342\app\templates/admin_aulas.blade.php ENDPATH**/ ?>