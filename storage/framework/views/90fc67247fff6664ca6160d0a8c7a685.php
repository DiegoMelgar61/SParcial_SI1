<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carga Horaria Docente — Plataforma Universitaria INF342</title>
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
                <h1 class="text-lg md:text-xl font-semibold text-gray-800 tracking-wide">
                    Plataforma Universitaria
                </h1>
            </div>

            <div class="flex items-center gap-4">
                <div class="hidden sm:block text-right">
                    <p class="font-medium text-gray-800"><?php echo e($user['nomb_comp'] ?? 'Usuario'); ?></p>
                    <p class="text-xs text-indigo-600 font-medium">Admin</p>
                </div>

                <div id="user-avatar"
                     class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold shadow-sm cursor-pointer select-none">
                    <?php echo e(isset($user['nomb_comp']) ? strtoupper(substr($user['nomb_comp'], 0, 1)) : '?'); ?>

                </div>

                <a href="/"
                   class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-medium transition shadow-sm">
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
                <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold shadow-sm">
                    <?php echo e(isset($user['nomb_comp']) ? strtoupper(substr($user['nomb_comp'], 0, 1)) : '?'); ?>

                </div>
                <div>
                    <p class="font-semibold text-gray-800 leading-tight"><?php echo e($user['nomb_comp'] ?? 'Usuario'); ?></p>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 font-medium">
                        Admin
                    </span>
                </div>
            </div>
            <hr class="my-3 border-gray-200">
            <ul class="space-y-2 text-sm">
                <li><span class="font-medium text-gray-600">CI:</span> <?php echo e($user['ci'] ?? '—'); ?></li>
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

        <!-- Contenedor con scroll -->
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
                        class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Gestión de Gestiones</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/carga-horaria"
                        class="flex items-center gap-2 px-3 py-2 text-indigo-700 bg-indigo-50 rounded-lg font-semibold hover:bg-indigo-100 transition">
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

            <!-- Footer -->
            <div class="p-3 border-t border-gray-100 text-center text-[11px] text-gray-500">
                Módulo Admin v1.1
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
                <h2 class="text-2xl font-semibold text-gray-800 mb-1">Carga Horaria Docente</h2>
                <p class="text-gray-600 text-sm">Visualiza y administra la carga horaria de los docentes</p>
            </div>
            <div class="mt-4 md:mt-0 flex items-center gap-3">
                <label for="select-gestion-carga" class="text-sm font-medium text-gray-700">Gestión:</label>
                <select id="select-gestion-carga" 
                        class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 min-w-[200px]">
                    <option value="">Seleccione gestión...</option>
                    <?php if(isset($gestiones) && is_array($gestiones) && count($gestiones) > 0): ?>
                        <?php $__currentLoopData = $gestiones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gestion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($gestion['id']); ?>"><?php echo e($gestion['nombre']); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <!-- Mensaje: Seleccione Gestión -->
        <div id="mensaje-seleccionar-gestion" class="bg-white rounded-xl shadow-sm border border-gray-200 p-12">
            <div class="text-center">
                <svg class="w-20 h-20 mx-auto text-indigo-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Seleccione una gestión para ver la carga horaria</h3>
                <p class="text-gray-500">Use el selector de gestión en la parte superior para cargar los datos de los docentes</p>
            </div>
        </div>

        <!-- Contenedor de estadísticas y tabla (oculto inicialmente) -->
        <div id="carga-content-wrapper" class="hidden">
        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Total Docentes -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-800">Total Docentes</h3>
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900" data-stat="total">0</p>
                <p class="text-sm text-gray-600 mt-1">En el sistema</p>
            </div>

            <!-- Carga Total -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-800">Carga Total</h3>
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">
                    <span data-stat="horas">0</span>
                    <span class="text-lg text-gray-600 font-normal">hrs</span>
                </p>
                <p class="text-sm text-gray-600 mt-1">Horas acumuladas en el sistema</p>
            </div>

            <!-- Promedio -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-800">Promedio</h3>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">
                    <span data-stat="promedio">0</span>
                    <span class="text-lg text-gray-600 font-normal">hrs</span>
                </p>
                <p class="text-sm text-gray-600 mt-1">Por docente</p>
            </div>
        </div>

        <!-- Tabla de Docentes -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Listado de Docentes</h3>
                <p class="text-sm text-gray-600 mt-1">Haz clic en "Ver Detalle" para revisar la carga horaria completa</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">#</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Docente</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-700">Materias</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-700">Carga Horaria</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-700">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-docentes" class="divide-y divide-gray-200">
                        <!-- Los datos se cargan dinámicamente por JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
        </div><!-- Fin carga-content-wrapper -->
    </main>

    <!-- Footer -->
    <footer class="md:ml-64 bg-white border-t border-gray-200 py-4 text-center text-xs text-gray-500">
        © 2025 Plataforma Universitaria — Sistema de Gestión Académica
    </footer>

    <!-- Modal Detalle Carga Horaria -->
    <div id="modal-detalle" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden">
            <!-- Header Modal -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-2xl font-bold" id="modal-docente-nombre">Carga Horaria Docente</h3>
                        <p class="text-sm text-indigo-100 mt-1" id="modal-docente-info">Información detallada</p>
                    </div>
                    <button id="btn-cerrar-modal" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Contenido Modal -->
            <div class="p-6 overflow-y-auto max-h-[70vh]">
                <!-- Estadísticas -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-200">
                        <p class="text-sm text-indigo-700 font-medium">Carga Total</p>
                        <p class="text-2xl font-bold text-indigo-600 mt-1" id="detalle-carga-total">0 hrs</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                        <p class="text-sm text-green-700 font-medium">Horas Semanales</p>
                        <p class="text-2xl font-bold text-green-600 mt-1" id="detalle-horas-semanales">0 hrs</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                        <p class="text-sm text-purple-700 font-medium">Total Materias</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1" id="detalle-total-materias">0</p>
                    </div>
                </div>

                <!-- Tabla Materias -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-gray-900 mb-4">Materias Asignadas</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm" id="tabla-materias-detalle">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="px-4 py-2 text-left font-semibold">Sigla</th>
                                    <th class="px-4 py-2 text-left font-semibold">Materia</th>
                                    <th class="px-4 py-2 text-center font-semibold">Grupo</th>
                                    <th class="px-4 py-2 text-center font-semibold">Semestre</th>
                                    <th class="px-4 py-2 text-center font-semibold">Carga Horaria</th>
                                    <th class="px-4 py-2 text-center font-semibold">Clases</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-materias-detalle">
                                <!-- Llenado dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Horario Semanal -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-4">Horario Semanal</h4>
                    <div id="contenedor-horario" class="overflow-x-auto">
                        <!-- Llenado dinámicamente -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Externo -->
    <script src="/static/scripts/admin_carga_horaria.js"></script>

</body>
</html>
<?php /**PATH D:\whatever that twas, scarcely worth my notice\Brillo\app\templates/admin_carga_horaria.blade.php ENDPATH**/ ?>