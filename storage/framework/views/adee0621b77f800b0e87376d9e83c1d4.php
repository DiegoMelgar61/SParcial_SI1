<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Gestiones — Plataforma Universitaria INF342</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
</head>

<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col font-sans antialiased">

    <!-- Barra superior -->
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-3">
            <div class="flex items-center gap-4">
                <!-- Botón de menú lateral para móviles -->
                <button id="menu-toggle"
                    class="block md:hidden p-2 text-gray-600 hover:text-indigo-600 rounded-md transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
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

    <!-- Panel lateral de usuario -->
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

    <!-- Sidebar -->
    <aside id="admin-sidebar"
        class="fixed top-0 left-0 w-64 bg-white shadow-lg h-full z-30 border-r border-gray-200 transform -translate-x-full md:translate-x-0 transition-transform duration-300">

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
                        class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition">
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
                        <a href="/admin/gestiones"
                        class="flex items-center gap-2 px-3 py-2 text-indigo-700 bg-indigo-50 rounded-lg font-semibold hover:bg-indigo-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Gestión de Gestiones</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/carga-horaria"
                        class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-6h13v6M9 17H5v-6h4m0 6V7m0 0H3v4h2m4-4v4h2"/>
                            </svg>
                            <span>Carga Horaria del Docente</span>
                        </a>
                    </li>

                    <li>
                        <a href="/auto/generar-horario"
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

            <!-- Logout -->
            <div class="p-3 border-t border-gray-100 mt-auto">
                <form action="/logout" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit"
                        class="w-full flex items-center gap-2 px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg transition font-medium text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Cerrar sesión</span>
                    </button>
                </form>
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
                <h2 class="text-2xl font-semibold text-gray-800 mb-1">Gestión de Gestiones Académicas</h2>
                <p class="text-gray-600 text-sm">Administra las gestiones y períodos académicos</p>
            </div>
            <button id="btn-crear-gestion" 
                    class="mt-4 md:mt-0 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium shadow-sm transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva Gestión
            </button>
        </div>

        <!-- Tabla de Gestiones -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Gestiones Registradas</h3>
                <p class="text-sm text-gray-600 mt-1">Lista de todas las gestiones académicas del sistema</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">ID</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Nombre</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Fecha Inicio</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Fecha Fin</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-700">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-gestiones" class="divide-y divide-gray-200">
                        <!-- Se carga dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="md:ml-64 bg-white border-t border-gray-200 py-4 text-center text-xs text-gray-500">
        © 2025 Plataforma Universitaria — Sistema de Gestión Académica
    </footer>

    <!-- Modal Crear/Editar Gestión -->
    <div id="modal-gestion" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
            
            <!-- Header -->
            <div class="p-6 border-b border-gray-200">
                <h3 id="modal-title" class="text-xl font-semibold text-gray-900">Nueva Gestión</h3>
                <p class="text-sm text-gray-600 mt-1">Complete los datos de la gestión académica</p>
            </div>

            <!-- Body -->
            <form id="form-gestion" class="p-6 space-y-5">
                
                <!-- Campo oculto para ID (al editar) -->
                <input type="hidden" id="gestion-id" value="">

                <!-- Selector de Semestre -->
                <div>
                    <label for="input-semestre" class="block text-sm font-medium text-gray-700 mb-2">
                        Semestre <span class="text-red-500">*</span>
                    </label>
                    <select id="input-semestre" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Seleccione un semestre...</option>
                        <option value="1">1 - Primer Semestre</option>
                        <option value="2">2 - Segundo Semestre</option>
                        <option value="3">3 - Verano </option>
                        <option value="4">4 - Mesa</option>
                    </select>
                </div>

                <!-- Selector de Año -->
                <div>
                    <label for="input-año" class="block text-sm font-medium text-gray-700 mb-2">
                        Año <span class="text-red-500">*</span>
                    </label>
                    <select id="input-año" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Seleccione un año...</option>
                        <!-- Se llena dinámicamente con año anterior, actual y siguiente -->
                    </select>
                </div>

                <!-- Vista previa del nombre -->
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                    <p class="text-xs font-medium text-indigo-700 mb-1">Nombre de la Gestión:</p>
                    <p id="preview-nombre" class="text-lg font-bold text-indigo-900">-</p>
                </div>

                <!-- Fecha Inicio -->
                <div>
                    <label for="input-fecha-inicio" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Inicio <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="input-fecha-inicio" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Fecha Fin -->
                <div>
                    <label for="input-fecha-fin" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Fin <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="input-fecha-fin" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

            </form>

            <!-- Footer -->
            <div class="p-6 border-t border-gray-200 flex gap-3 justify-end">
                <button type="button" id="btn-cancelar-modal"
                        class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium text-sm">
                    Cancelar
                </button>
                <button type="submit" form="form-gestion" id="btn-guardar-gestion"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition font-medium text-sm shadow-sm">
                    Guardar Gestión
                </button>
            </div>

        </div>
    </div>

    <script src="/static/scripts/admin_gestiones.js"></script>
</body>

</html>
<?php /**PATH D:\whatever that twas, scarcely worth my notice\Brillo\app\templates/admin_gestiones.blade.php ENDPATH**/ ?>