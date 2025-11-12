<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Aulas — Plataforma Universitaria INF342</title>
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

    <!-- Panel lateral de usuario -->
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
                        class="flex items-center gap-2 px-3 py-2 text-indigo-700 bg-indigo-50 rounded-lg font-semibold hover:bg-indigo-100 transition">
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
       

        <!-- Encabezado -->
        <div class="flex flex-col md:flex-row justify-between md:items-center mb-8 gap-4">
            <div>
                <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-1">Aulas del Sistema</h2>
                <p class="text-gray-500 text-sm">Cree, edite o elimine aulas según sea necesario.</p>
            </div>
            <div class="flex gap-3">
                <button id="btn-consultar-horarios" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>    
                    Consultar Horarios
                </button>
                <button id="btn-add" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>    
                    Agregar Aula
                </button>
            </div>
        </div>
        <!-- Tabla de aulas -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <thead class="bg-gray-50">
                    <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número de Aula</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacidad</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Módulo</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                       <tbody id="aulas-table-body" class="bg-white divide-y divide-gray-200 text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $aulas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $aula): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="aula-row hover:bg-gray-50" data-aula-nro="<?php echo e($aula['nro']); ?>">
                            <td class="px-6 py-4 text-gray-700"><?php echo e($index + 1); ?></td>
                            <td class="px-6 py-4 text-gray-800 font-medium"><?php echo e($aula['nro']); ?></td>
                            <td class="px-6 py-4 text-gray-600"><?php echo e($aula['capacidad']); ?></td>
                            <td class="px-6 py-4 text-gray-600"><?php echo e($aula['modulo']); ?></td>
                            <td class="px-6 py-4 text-gray-600">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <?php echo e($aula['tipo']); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button data-nro="<?php echo e($aula['nro']); ?>" data-capacidad="<?php echo e($aula['capacidad']); ?>" data-modulo="<?php echo e($aula['modulo']); ?>" data-tipo="<?php echo e($aula['tipo']); ?>"
                                    class="btn-edit text-indigo-600 hover:text-indigo-900 p-1 rounded-md hover:bg-indigo-100 transition" title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                </button>
                                <button  data-nro="<?php echo e($aula['nro']); ?>"
                                    class="btn-ver-horario text-green-600 hover:text-green-900 p-1 rounded-md hover:bg-green-100 transition" title="Ver Horario">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                                <button data-nro="<?php echo e($aula['nro']); ?>" data-capacidad="<?php echo e($aula['capacidad']); ?>"
                                    class="btn-delete text-red-600 hover:text-red-900 p-1 rounded-md hover:bg-red-100 transition" title="Eliminar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No hay aulas registradas.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4 text-sm text-gray-500">Mostrando <?php echo e(count($aulas)); ?> aulas.</div>
    </main>

    <!-- Pie de página -->
    <footer class="text-center py-4 text-xs text-gray-500 border-t border-gray-200 mt-auto">
        © 2025 Plataforma Universitaria — Todos los derechos reservados
    </footer>

    <!-- Modal de Formulario (Agregar/Editar Aula) -->
    <div id="aula-form-modal" class="fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center p-4 hidden">
        
        <!-- Contenedor del modal con altura máxima y flex-col -->
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl flex flex-col max-h-[90vh]">
            
            <!-- Encabezado del Modal (fijo) -->
            <div class="flex-shrink-0 flex items-center justify-between p-5 border-b border-gray-200">
                <h3 id="form-modal-title" class="text-lg font-semibold text-gray-900">Agregar Nueva Aula</h3>
                <button id="btn-cancel-form-x" class="text-gray-400 hover:text-gray-600 transition">
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
                        <label for="form-nro" class="block text-sm font-medium text-gray-700 mb-2">Número de Aula</label>
                        <input type="text" id="form-nro" name="nro" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                   
                    <!-- Capacidad -->
                    <div>
                        <label for="form-capacidad" class="block text-sm font-medium text-gray-700 mb-2">Capacidad</label>
                        <input type="number" id="form-capacidad" name="capacidad" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required min="1">
                    </div>

                    <!-- Módulo -->
                    <div>
                        <label for="form-modulo" class="block text-sm font-medium text-gray-700 mb-2">Módulo</label>
                        <input type="text" id="form-modulo" name="modulo" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                    </div>

                    <!-- Tipo -->
                    <div>
                        <label for="form-tipo" class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                        <select id="form-tipo" name="tipo" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            <option value="">Seleccione un tipo</option>
                            <option value="Teórica">Teórica</option>
                            <option value="Práctica">Práctica</option>
                            <option value="Laboratorio">Laboratorio</option>
                            <option value="Mixta">Mixta</option>
                        </select>
                    </div>
              
                </div>

                <!-- Footer del Formulario (Acciones) (fijo) -->
                <div class="flex-shrink-0 bg-gray-50 px-6 py-4 flex justify-end gap-3 rounded-b-xl border-t border-gray-200">
                    <button type="button" id="btn-cancel-form" class="text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="submit" id="btn-save-form" class="text-sm font-medium text-white bg-indigo-600 rounded-lg px-4 py-2 hover:bg-indigo-700 transition">
                        Guardar
                    </button>
                </div>
            </form>
        </div>

    </div>


    <!-- Modal de Confirmación de Eliminación -->
    <div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
            <div class="p-6">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full bg-red-100">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Eliminar Aula</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            ¿Estás seguro de que deseas eliminar el aula <strong id="delete-aula-nro" class="font-bold">...</strong> con capacidad para <strong id="delete-aula-capacidad" class="font-bold">...</strong> personas? Esta acción no se puede deshacer.
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 rounded-b-xl">
                <button id="btn-cancel-delete" class="text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button id="btn-confirm-delete" class="text-sm font-medium text-white bg-red-600 rounded-lg px-4 py-2 hover:bg-red-700 transition">
                    Sí, eliminar
                </button>
            </div>
        </div>
    </div>
    
    <!-- ====== FIN DE MODALES ====== -->

    <!-- Modal de Selección de Aulas para Consultar Horario -->
    <div id="seleccion-aulas-modal" class="fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl flex flex-col max-h-[90vh]">
            
            <!-- Encabezado -->
            <div class="flex-shrink-0 flex items-center justify-between p-5 border-b border-gray-200">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Consultar Horarios de Aulas</h3>
                    <p class="text-sm text-gray-600 mt-1">Seleccione un aula para ver su horario semanal</p>
                </div>
                <button id="btn-close-seleccion" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Lista de Aulas -->
            <div class="p-6 overflow-y-auto">
                <div id="aulas-lista-horarios" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <?php $__currentLoopData = $aulas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $aula): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button data-aula-nro="<?php echo e($aula['nro']); ?>" data-aula-capacidad="<?php echo e($aula['capacidad']); ?>" data-aula-modulo="<?php echo e($aula['modulo']); ?>" data-aula-tipo="<?php echo e($aula['tipo']); ?>"
                            class="aula-card-selectable group bg-gray-50 hover:bg-indigo-50 border-2 border-gray-200 hover:border-indigo-500 rounded-lg p-4 transition-all duration-200 text-left">
                            <div class="flex items-center justify-between mb-2">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 group-hover:bg-indigo-200 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700"><?php echo e($aula['tipo']); ?></span>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900 mb-1">Aula <?php echo e($aula['nro']); ?></h4>
                            <p class="text-sm text-gray-600">Capacidad: <?php echo e($aula['capacidad']); ?> personas</p>
                            <p class="text-sm text-gray-600">Módulo: <?php echo e($aula['modulo']); ?></p>
                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex-shrink-0 bg-gray-50 px-6 py-4 flex justify-end rounded-b-xl border-t border-gray-200">
                <button id="btn-cancelar-seleccion" class="text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50 transition">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Horario del Aula -->
    <div id="horario-aula-modal" class="fixed inset-0 bg-black bg-opacity-50 z-[70] flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-7xl flex flex-col max-h-[95vh]">
            
            <!-- Encabezado -->
            <div class="flex-shrink-0 flex items-center justify-between p-5 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-gray-900">Horario del Aula <span id="horario-aula-numero" class="text-indigo-600"></span></h3>
                    <p class="text-sm text-gray-600 mt-1">
                        <span id="horario-aula-info"></span>
                    </p>
                </div>
                
                <!-- Selector de Gestión -->
                <div class="flex items-center gap-3 mr-4">
                    <label for="select-gestion-horario" class="text-sm font-medium text-gray-700">Gestión:</label>
                    <select id="select-gestion-horario" 
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Seleccione gestión...</option>
                        <?php if(isset($gestiones) && is_array($gestiones) && count($gestiones) > 0): ?>
                            <?php $__currentLoopData = $gestiones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gestion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($gestion['id']); ?>"><?php echo e($gestion['nombre']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <button id="btn-close-horario" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Mensaje: Seleccione Gestión -->
            <div id="horario-sin-gestion" class="flex-1 flex items-center justify-center py-12">
                <div class="text-center">
                    <svg class="w-16 h-16 mx-auto text-indigo-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-gray-600 text-lg font-medium">Seleccione una gestión para ver el horario</p>
                    <p class="text-gray-400 text-sm mt-2">Use el selector de gestión arriba</p>
                </div>
            </div>

            <!-- Horario -->
            <div class="flex-1 overflow-auto p-6 hidden" id="horario-content-wrapper">
                <div id="horario-loading" class="text-center py-12 hidden">
                    <svg class="animate-spin h-12 w-12 mx-auto text-indigo-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="mt-4 text-gray-600">Cargando horario...</p>
                </div>

                <div id="horario-vacio" class="text-center py-12 hidden">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-gray-500 text-lg font-medium">Esta aula no tiene clases asignadas</p>
                    <p class="text-gray-400 text-sm mt-2">El aula está completamente disponible</p>
                </div>

                <!-- Tabla de Horario -->
                <div id="horario-tabla-container" class="overflow-x-auto hidden">
                    <table class="min-w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700 w-32">HORARIO</th>
                                <th class="border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700">Lun</th>
                                <th class="border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700">Mar</th>
                                <th class="border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700">Mie</th>
                                <th class="border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700">Jue</th>
                                <th class="border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700">Vie</th>
                                <th class="border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700">Sab</th>
                            </tr>
                        </thead>
                        <tbody id="horario-tbody">
                            <!-- Se llenará dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex-shrink-0 bg-gray-50 px-6 py-4 flex justify-between items-center rounded-b-xl border-t border-gray-200">
                <div class="flex gap-4 text-xs">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded bg-yellow-200 border border-yellow-400"></div>
                        <span class="text-gray-600">Clases asignadas</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded bg-white border border-gray-300"></div>
                        <span class="text-gray-600">Disponible</span>
                    </div>
                </div>
                <button id="btn-cerrar-horario" class="text-sm font-medium text-white bg-indigo-600 rounded-lg px-4 py-2 hover:bg-indigo-700 transition">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- JavaScript Externo -->
    <script src="/static/scripts/admin_aulas.js"></script>

</body>
</html><?php /**PATH C:\Users\migue\OneDrive\Escritorio\projects\inf342_2exa\app\templates/admin_aulas.blade.php ENDPATH**/ ?>