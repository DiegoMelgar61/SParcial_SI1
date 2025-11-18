<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Gestiones — Plataforma Universitaria INF342</title>
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
                <button id="menu-toggle"
                    class="block md:hidden p-2 text-gold-500 hover:text-gold-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
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

    <!-- Panel lateral de usuario -->
    <aside id="user-aside"
        class="hidden fixed top-16 right-4 w-64 bg-white shadow-2xl border-2 border-slate-300 z-50 transition-all duration-300 opacity-0 scale-95 origin-top-right">
        <div class="p-5 text-sm text-slate-700">
            <div class="flex items-center gap-3 mb-3">
                <div
                    class="w-10 h-10 border-2 border-gold-500 bg-navy-900 text-gold-500 flex items-center justify-center font-black shadow-sm">
                    <?php echo e(strtoupper(substr($user['nomb_comp'], 0, 1))); ?>

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

    <!-- Sidebar -->
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
                        class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 border-l-4 border-transparent hover:border-gold-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7h18M3 12h18M3 17h18"/>
                            </svg>
                            <span class="font-semibold">Gestión de Aulas</span>
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
                        class="flex items-center gap-2 px-3 py-2 text-gold-500 bg-navy-800 border-l-4 border-gold-500 font-black hover:bg-navy-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Gestión de Gestiones</span>
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
        <div class="flex flex-col md:flex-row justify-between md:items-center mb-8">
            <div class="bg-white border-l-4 border-gold-500 shadow-md px-6 py-5">
                <h2 class="text-2xl md:text-3xl font-black text-navy-900 uppercase tracking-tight mb-1">Gestión de Gestiones Académicas</h2>
                <p class="text-slate-600 text-sm font-semibold uppercase tracking-wide">Administra las gestiones y períodos académicos</p>
            </div>
            <button id="btn-crear-gestion"
                    class="mt-4 md:mt-0 bg-navy-900 hover:bg-navy-800 text-white px-5 py-3 border-b-4 border-gold-500 font-bold uppercase tracking-wide shadow-md transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva Gestión
            </button>
        </div>

        <!-- Tabla de Gestiones -->
        <div class="bg-white shadow-md border-2 border-slate-300">
            <div class="bg-navy-900 px-6 py-4 border-b-4 border-gold-500">
                <h3 class="text-lg font-black text-white uppercase tracking-wide">Gestiones Registradas</h3>
                <p class="text-sm text-gold-500 mt-1 font-semibold uppercase tracking-wide">Lista de todas las gestiones académicas del sistema</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-100 border-b-2 border-slate-300">
                        <tr>
                            <th class="px-6 py-3 text-left font-black text-navy-900 uppercase tracking-wide">ID</th>
                            <th class="px-6 py-3 text-left font-black text-navy-900 uppercase tracking-wide">Nombre</th>
                            <th class="px-6 py-3 text-left font-black text-navy-900 uppercase tracking-wide">Fecha Inicio</th>
                            <th class="px-6 py-3 text-left font-black text-navy-900 uppercase tracking-wide">Fecha Fin</th>
                            <th class="px-6 py-3 text-center font-black text-navy-900 uppercase tracking-wide">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-gestiones" class="divide-y divide-slate-200">
                        <!-- Se carga dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="md:ml-64 bg-white border-t-2 border-slate-200 py-4 text-center text-xs text-slate-500 font-semibold uppercase tracking-wide">
        © 2025 Plataforma Universitaria — Sistema de Gestión Académica
    </footer>

    <!-- Modal Crear/Editar Gestión -->
    <div id="modal-gestion" class="hidden fixed inset-0 bg-navy-900 bg-opacity-70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white border-2 border-slate-300 shadow-2xl w-full max-w-3xl">

        <!-- Header -->
        <div class="bg-navy-900 px-6 py-4 border-b-4 border-gold-500">
        <h3 id="modal-title" class="text-xl font-black text-white uppercase tracking-wide">Nueva Gestión</h3>
        <p class="text-sm text-gold-500 mt-1 font-semibold uppercase tracking-wide">Complete los datos de la gestión académica</p>
        </div>

        <!-- Body -->
        <form id="form-gestion" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <input type="hidden" id="gestion-id" value="">

        <!-- Selector de Semestre -->
        <div class="col-span-1">
            <label for="input-semestre" class="block text-sm font-bold text-navy-900 mb-2 uppercase tracking-wide">
            Semestre <span class="text-red-500">*</span>
            </label>
            <select id="input-semestre" required
            class="w-full border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
            <option value="">Seleccione un semestre...</option>
            <option value="1">1 - Primer Semestre</option>
            <option value="2">2 - Segundo Semestre</option>
            <option value="3">3 - Verano</option>
            <option value="4">4 - Mesa</option>
            </select>
        </div>

        <!-- Selector de Año -->
        <div class="col-span-1">
            <label for="input-año" class="block text-sm font-bold text-navy-900 mb-2 uppercase tracking-wide">
            Año <span class="text-red-500">*</span>
            </label>
            <select id="input-año" required
            class="w-full border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
            <option value="">Seleccione un año...</option>
            </select>
        </div>

        <!-- Vista previa del nombre -->
        <div class="col-span-2">
            <div class="bg-navy-900 border-2 border-gold-500 p-4">
            <p class="text-xs font-bold text-gold-500 mb-1 uppercase tracking-wide">Nombre de la Gestión:</p>
            <p id="preview-nombre" class="text-lg font-black text-white uppercase tracking-wide">-</p>
            </div>
        </div>

        <!-- Fechas -->
        <div>
            <label for="input-fecha-inicio" class="block text-sm font-bold text-navy-900 mb-2 uppercase tracking-wide">
            Fecha de Inicio <span class="text-red-500">*</span>
            </label>
            <input type="date" id="input-fecha-inicio" required
            class="w-full border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
        </div>

        <div>
            <label for="input-fecha-fin" class="block text-sm font-bold text-navy-900 mb-2 uppercase tracking-wide">
            Fecha de Fin <span class="text-red-500">*</span>
            </label>
            <input type="date" id="input-fecha-fin" required
            class="w-full border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
        </div>
        </form>

        <!-- Footer -->
        <div class="bg-slate-100 px-6 py-4 border-t-2 border-slate-300 flex gap-3 justify-end">
        <button type="button" id="btn-cancelar-modal"
            class="px-5 py-2.5 border-2 border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition font-bold text-sm uppercase tracking-wide">
            Cancelar
        </button>
        <button type="submit" form="form-gestion" id="btn-guardar-gestion"
            class="px-5 py-2.5 bg-navy-900 hover:bg-navy-800 text-white border-b-4 border-gold-500 transition font-bold text-sm shadow-md uppercase tracking-wide">
            Guardar Gestión
        </button>
        </div>
    </div>
    </div>


    <script src="/static/scripts/admin_gestiones.js"></script>
</body>

</html>
<?php /**PATH C:\Users\diego\OneDrive\Escritorio\exa2_inf342\app\templates/admin_gestiones.blade.php ENDPATH**/ ?>