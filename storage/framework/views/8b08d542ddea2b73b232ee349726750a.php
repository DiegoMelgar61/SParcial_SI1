<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo Admin — Plataforma Universitaria INF342</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        navy: {
                            900: '#0f2942',
                            800: '#1e3a5f',
                        },
                        gold: {
                            500: '#c9a961',
                            600: '#b8974f',
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col font-sans antialiased">

    <!-- Barra superior - Mantiene colores característicos -->
    <header class="bg-navy-900 border-b-4 border-gold-500 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-3">
            <div class="flex items-center gap-4">
                <!-- Botón de menú lateral para móviles -->
                <button id="menu-toggle"
                    class="block md:hidden p-2 text-gold-500 hover:text-gold-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h1 class="text-lg md:text-xl font-semibold text-white tracking-wide">
                    Plataforma Universitaria
                </h1>
            </div>

            <div class="flex items-center gap-4">
                <div class="hidden sm:block text-right">
                    <p class="font-medium text-white"><?php echo e($user['nomb_comp']); ?></p>
                    <p class="text-xs text-gold-500 font-medium"><?php echo e(ucfirst($user['rol'])); ?></p>
                </div>

                <!-- Avatar con colores característicos -->
                <div id="user-avatar"
                    class="w-10 h-10 bg-gold-500 text-navy-900 flex items-center justify-center font-bold shadow-sm cursor-pointer select-none">
                    <?php echo e(strtoupper(substr($user['nomb_comp'], 0, 1))); ?>

                </div>

                <!-- Botón de inicio -->
                <a href="/"
                    class="text-sm bg-gold-500 hover:bg-gold-600 text-navy-900 px-4 py-2 font-semibold transition border-b-4 border-gold-600 hover:border-navy-900">
                    Inicio
                </a>
            </div>
        </div>
    </header>

    <!-- Panel lateral de usuario -->
    <aside id="admin-sidebar"
        class="fixed top-0 left-0 w-64 bg-navy-900 shadow-lg h-full z-30 border-r-4 border-gold-500 transform -translate-x-full md:translate-x-0 transition-transform duration-300">

        <!-- Contenedor con scroll -->
        <div
            class="flex flex-col h-full overflow-y-auto scrollbar-thin scrollbar-thumb-gold-500 scrollbar-track-navy-800">

            <!-- Encabezado -->
            <div class="p-4 border-b-2 border-gold-500">
                <h3 class="text-sm font-bold text-white">Panel de Administración</h3>
                <p class="text-xs text-gold-500 mt-1 font-medium">Gestión completa del sistema</p>
            </div>

            <!-- Navegación -->
            <nav class="flex-1 p-3">
                <ul class="space-y-1 text-sm">

                    <li>
                        <a href="/admin/mod-adm"
                            class="flex items-center gap-2 px-3 py-2 text-gold-500 bg-navy-800 border-l-4 border-gold-500 font-bold hover:bg-navy-800 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Panel Administrador</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/users"
                            class="flex items-center gap-2 px-3 py-2 text-gray-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5M16 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>Gestión de Usuarios</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/roles"
                            class="flex items-center gap-2 px-3 py-2 text-gray-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2v1a2 2 0 01-2 2H9a2 2 0 01-2-2V9a2 2 0 012-2h6zm6 11h-3a2 2 0 01-2-2v-1a2 2 0 012-2h3v5zM6 18H3v-5h3a2 2 0 012 2v1a2 2 0 01-2 2z" />
                            </svg>
                            <span>Gestión de Roles y Permisos</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/grupos"
                            class="flex items-center gap-2 px-3 py-2 text-gray-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5M16 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>Gestión de Grupos</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/aulas"
                            class="flex items-center gap-2 px-3 py-2 text-gray-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7h18M3 12h18M3 17h18" />
                            </svg>
                            <span>Gestión de Aulas</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/materias"
                            class="flex items-center gap-2 px-3 py-2 text-gray-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 20l9-5-9-5-9 5 9 5zM12 12V4m0 8l9-5M12 12L3 7" />
                            </svg>
                            <span>Gestión de Materias</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/gestiones"
                            class="flex items-center gap-2 px-3 py-2 text-gray-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Gestión de Gestiones</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/carga-horaria"
                            class="flex items-center gap-2 px-3 py-2 text-gray-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-6h13v6M9 17H5v-6h4m0 6V7m0 0H3v4h2m4-4v4h2" />
                            </svg>
                            <span>Carga Horaria del Docente</span>
                        </a>
                    </li>

                    <li>
                        <a href="/auto/generar-horario"
                            class="flex items-center gap-2 px-3 py-2 text-gray-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Generar Horario</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/bitacora"
                            class="flex items-center gap-2 px-3 py-2 text-gray-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                            </svg>
                            <span>Consultar Historial de Acciones</span>
                        </a>
                    </li>

                </ul>
            </nav>

            <!-- Footer -->
            <div class="p-3 border-t-2 border-gold-500 text-center text-[11px] text-gray-400">
                Módulo Admin v1.1
            </div>
        </div>
    </aside>


    <!-- Overlay para móviles -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden hidden"></div>

    <!-- Contenido principal -->
    <main class="flex-1 md:ml-64 p-6 transition-all duration-300">
        <!-- Encabezado -->
        <div class="flex flex-col md:flex-row justify-between md:items-center mb-8 bg-white border-b-4 border-gold-500 p-6 shadow-sm">
            <div>
                <h2 class="text-2xl font-bold text-navy-900 mb-1">Módulo de Administración</h2>
                <p class="text-gray-600 text-sm">Vista general y métricas del sistema</p>
            </div>
            <div id="clock" class="text-sm text-navy-900 font-semibold mt-3 md:mt-0"></div>
        </div>

        <!-- Resumen general -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Total de Usuarios -->
            <div class="bg-white p-6 shadow-sm border-l-4 border-navy-900 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-800">Total de Usuarios</h3>
                    <div class="w-10 h-10 bg-navy-900 flex items-center justify-center">
                        <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800 mb-2"><?php echo e($cant_usuarios ?? '—'); ?></p>
                <p class="text-sm text-gray-500">Usuarios registrados en el sistema</p>
            </div>

            <!-- Total de Docentes -->
            <div class="bg-white p-6 shadow-sm border-l-4 border-navy-900 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-800">Total de Docentes</h3>
                    <div class="w-10 h-10 bg-navy-900 flex items-center justify-center">
                        <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800 mb-2"><?php echo e($cant_docente ?? '—'); ?></p>
                <p class="text-sm text-gray-500">Docentes activos en el sistema</p>
            </div>

            <!-- Gestión Actual -->
            <div class="bg-white p-6 shadow-sm border-l-4 border-navy-900 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-800">Gestión Actual</h3>
                    <div class="w-10 h-10 bg-navy-900 flex items-center justify-center">
                        <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-800 mb-2"><?php echo e($gestion_actual ?? '2025-II'); ?></p>
                <p class="text-sm text-gray-500">Periodo académico vigente</p>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Avisos del sistema -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h3 class="text-base font-semibold text-gray-800 mb-4">Avisos del Sistema</h3>
                <ul class="text-sm text-gray-600 space-y-3">
                    <li class="flex items-start gap-2">
                        <span class="w-2 h-2 bg-indigo-600 rounded-full mt-1.5 flex-shrink-0"></span>
                        <span>Se recomienda realizar backup semanal de la base de datos</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-2 h-2 bg-indigo-600 rounded-full mt-1.5 flex-shrink-0"></span>
                        <span>Nueva actualización disponible para el módulo de reportes</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-2 h-2 bg-indigo-600 rounded-full mt-1.5 flex-shrink-0"></span>
                        <span>Recordatorio: Revisar logs de actividad periódicamente</span>
                    </li>
                </ul>
            </div>

            <!-- Estado del sistema -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h3 class="text-base font-semibold text-gray-800 mb-4">Estado del Sistema</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Servidor web</span>
                        <span
                            class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Activo</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Base de datos</span>
                        <span
                            class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Conectada</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Almacenamiento</span>
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">65%</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center py-4 text-xs text-gray-300 border-t-4 border-gold-500 bg-navy-900 mt-10 md:ml-64">
        © <?php echo e(date('Y')); ?> Grupo 32 — UAGRM | INF342 - SA
    </footer>
    <script src="<?php echo e(asset('static/scripts/admin.js')); ?>"></script>
</body>

</html><?php /**PATH C:\Users\diego\OneDrive\Escritorio\exa2_inf342\app\templates/mod_admin.blade.php ENDPATH**/ ?>