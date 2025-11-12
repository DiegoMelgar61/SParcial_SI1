<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Horario — Plataforma Universitaria INF342</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- CSRF Token para peticiones AJAX -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <!-- Estilos personalizados -->
    <style>
        /* Animación para elementos que aparecen */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }

        /* Personalización del scrollbar */
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col font-sans antialiased">

    <!-- ============================================
         BARRA SUPERIOR (HEADER)
         ============================================
         - Logo y título del sistema
         - Información del usuario logueado
         - Botón de menú para móviles
    ============================================ -->
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-3">
            
            <!-- Logo y botón de menú móvil -->
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

            <!-- Información del usuario y botones -->
            <div class="flex items-center gap-4">
                <!-- Nombre del usuario -->
                <div class="hidden sm:block text-right">
                    <p class="font-medium text-gray-800"><?php echo e($user['nomb_comp'] ?? 'Usuario'); ?></p>
                    <p class="text-xs text-indigo-600 font-medium"><?php echo e(isset($user['rol']) ? ucfirst($user['rol']) : 'Sin rol'); ?></p>
                </div>

                <!-- Avatar del usuario -->
                <div id="user-avatar"
                     class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold shadow-sm cursor-pointer select-none">
                    <?php echo e(isset($user['nomb_comp']) ? strtoupper(substr($user['nomb_comp'], 0, 1)) : '?'); ?>

                </div>

                <!-- Botón de inicio -->
                <a href="/"
                   class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-medium transition shadow-sm">
                    Inicio
                </a>
            </div>
        </div>
    </header>

    <!-- ============================================
         SIDEBAR DE NAVEGACIÓN
         ============================================
         - Menú lateral con links a todas las secciones
         - Responsive: oculto en móviles, visible en desktop
         - Con scroll interno para muchos elementos
    ============================================ -->
    <aside id="admin-sidebar"
        class="fixed top-0 left-0 w-64 bg-white shadow-lg h-full z-30 border-r border-gray-200 transform -translate-x-full md:translate-x-0 transition-transform duration-300">

        <div class="flex flex-col h-full overflow-y-auto scrollbar-thin">

            <!-- Encabezado del sidebar -->
            <div class="p-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-800">Panel de Administración</h3>
                <p class="text-xs text-indigo-600 mt-1 font-medium">Gestión completa del sistema</p>
            </div>

            <!-- Enlaces de navegación -->
            <nav class="flex-1 p-3">
                <ul class="space-y-1 text-sm">

                    <!-- Panel Administrador -->
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

                    <!-- Gestión de Usuarios -->
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

                    <!-- Gestión de Roles -->
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

                    <!-- Gestión de Grupos -->
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

                    <!-- Gestión de Aulas -->
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

                    <!-- Gestión de Materias -->
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
                    <!-- Carga Horaria del Docente -->
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

                    <!-- Generar Horario (ACTIVO) -->
                    <li>
                        <a href="/auto/generar-horario"
                        class="flex items-center gap-2 px-3 py-2 text-indigo-700 bg-indigo-50 rounded-lg font-semibold hover:bg-indigo-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Generar Horario</span>
                        </a>
                    </li>

                    <!-- Bitácora del Sistema -->
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

            <!-- Footer del sidebar -->
            <div class="p-3 border-t border-gray-100 text-center text-[11px] text-gray-500">
                Módulo Admin v1.1
            </div>
        </div>
    </aside>

    <!-- Overlay oscuro para cerrar sidebar en móviles -->
    <div id="sidebar-overlay" 
         class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden hidden"></div>

    <!-- ============================================
         CONTENIDO PRINCIPAL
         ============================================
         Contiene todo el formulario y paneles de la
         funcionalidad de generación de horarios
    ============================================ -->
    <main class="flex-1 md:ml-64 p-6 transition-all duration-300">
        
        <!-- Encabezado de la página -->
        <div class="flex flex-col md:flex-row justify-between md:items-center mb-8">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 mb-1">Generación Automática de Horarios</h2>
                <p class="text-gray-600 text-sm">Sistema inteligente de asignación de aulas, horarios y docentes</p>
            </div>
            <!-- Reloj en tiempo real (actualizado por JavaScript) -->
            <div id="clock" class="text-sm text-gray-600 font-medium mt-3 md:mt-0"></div>
        </div>

        <!-- ============================================
             PASO 1: SELECTOR DE GESTIÓN ACADÉMICA
             ============================================
             Permite seleccionar el período académico
             para el cual se generará el horario
        ============================================ -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Seleccionar Gestión</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gestión Académica</label>
                    <select id="select-gestion" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">-- Seleccione una gestión --</option>
                        <?php $__currentLoopData = $gestiones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gestion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($gestion['id']); ?>"><?php echo e($gestion['nombre']); ?> (<?php echo e(date('Y', strtotime($gestion['fecha_i']))); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="flex items-end">
                    <button id="btn-cargar-datos" 
                            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed" 
                            disabled>
                        Cargar Datos
                    </button>
                </div>
            </div>
        </div>

        <!-- Información cargada -->
        <div id="panel-datos" class="hidden">
            <!-- Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-medium text-gray-600">Materias</h4>
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-gray-900" id="stat-materias">0</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-medium text-gray-600">Docentes</h4>
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-gray-900" id="stat-docentes">0</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-medium text-gray-600">Aulas</h4>
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-gray-900" id="stat-aulas">0</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-medium text-gray-600">Bloques Horarios</h4>
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-gray-900" id="stat-horarios">0</p>
                </div>
            </div>

            <!-- Panel de configuración de generación -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Configuración de Generación</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Criterios de Carga Horaria -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-800 mb-3">Criterios de Carga Horaria Semanal</h4>
                        <ul class="space-y-2 text-sm text-gray-700">
                            <li class="flex items-start gap-2">
                                <span class="text-blue-600 mt-0.5">●</span>
                                <span><strong>4.5 hrs/semana:</strong> ≈60% de materias (carga 135 hrs)</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-green-600 mt-0.5">●</span>
                                <span><strong>6 hrs/semana:</strong> ≈25% de materias (>135 hrs + lab)</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-600 mt-0.5">●</span>
                                <span><strong>3-3.75 hrs/semana:</strong> ≈10% de materias (90 hrs)</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-orange-600 mt-0.5">●</span>
                                <span><strong>5.25 hrs/semana:</strong> ≈5% electivas (90 hrs)</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Restricciones -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-800 mb-3">Restricciones del Sistema</h4>
                        <ul class="space-y-2 text-sm text-gray-700">
                            <li class="flex items-start gap-2">
                                <span class="text-red-600">✓</span>
                                <span>Un aula no puede tener 2 clases en el mismo horario</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-red-600">✓</span>
                                <span>Las restricciones son por gestión académica</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-red-600">✓</span>
                                <span>Laboratorios priorizados para materias >135 hrs</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-red-600">✓</span>
                                <span>Verificación de horas semanales por materia</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- 
                    ========================================
                    GENERACIÓN AUTOMÁTICA - DESHABILITADA TEMPORALMENTE
                    ========================================
                    Motivo: Funcionalidad en desarrollo/pruebas
                    TODO: Habilitar cuando esté completamente funcional
                    ========================================
                -->
                <!-- 
                <div class="bg-indigo-50 rounded-lg p-6 border-2 border-indigo-200">
                    <h4 class="font-semibold text-indigo-900 mb-4">🤖 Generar Horario Automáticamente</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Materia</label>
                            <select id="auto-materia" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Seleccione materia --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Grupo</label>
                            <select id="auto-grupo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Seleccione grupo --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Docente</label>
                            <select id="auto-docente" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Seleccione docente --</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button id="btn-generar-auto" class="w-full px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg font-semibold transition shadow-md">
                                Generar Horarios
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-indigo-700">
                        El sistema generará automáticamente los horarios necesarios según la carga horaria de la materia
                    </p>
                </div>
                -->

                <div class="mt-6 flex gap-4">
                    <button id="btn-asignacion-manual" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition shadow-sm">
                         Asignación Manual
                    </button>
                    <button id="btn-ver-horario" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition shadow-sm">
                         Ver Horario Generado
                    </button>
                </div>
            </div>

            <!-- Panel de asignación manual -->
            <div id="panel-manual" class="hidden bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Asignación Manual de Clases</h3>
                <p class="text-sm text-gray-600 mb-6">
                    Seleccione hasta <strong>4 horarios diferentes</strong> para cada materia-grupo. 
                    Puede usar 1, 2, 3 o 4 horarios.
                </p>

                <!-- Formulario de asignación -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Materia</label>
                        <select id="manual-materia" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Seleccione materia --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Grupo</label>
                        <select id="manual-grupo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Seleccione grupo --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Docente</label>
                        <select id="manual-docente" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Seleccione docente --</option>
                        </select>
                    </div>
                </div>

                <!-- Selectores de horarios (4 opciones) -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Horarios (puede seleccionar 1, 2, 3 o 4)
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-gray-600">Horario 1</label>
                            <select id="manual-horario-1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Opcional --</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Horario 2</label>
                            <select id="manual-horario-2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Opcional --</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Horario 3</label>
                            <select id="manual-horario-3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Opcional --</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Horario 4</label>
                            <select id="manual-horario-4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Opcional --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Info de horas calculadas -->
                <div id="info-horas-manual" class="hidden bg-gray-50 border border-gray-300 rounded-lg p-4 mb-4">
                    <p class="text-sm text-gray-700">
                        <strong>Horas semanales seleccionadas:</strong> <span id="horas-seleccionadas" class="font-bold text-indigo-600">0</span> hrs
                    </p>
                    <p class="text-xs text-gray-600 mt-1" id="validacion-horas-mensaje"></p>
                </div>

                <!-- Botón de asignación -->
                <button id="btn-asignar-manual" class="w-full md:w-auto px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition shadow-sm">
                    ✓ Asignar Clases
                </button>
            </div>
        </div>

        <!-- Resultado de generación -->
        <div id="panel-resultado" class="hidden bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Resultado de Generación</h3>
            <div id="resultado-contenido">
                <!-- Se llenará dinámicamente -->
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="md:ml-64 bg-white border-t border-gray-200 py-4 text-center text-xs text-gray-500">
        © 2025 Plataforma Universitaria — Sistema de Gestión Académica
    </footer>

    <!-- Modal Ver Horario -->
    <div id="modal-horario" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-7xl w-full max-h-[90vh] overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 text-white flex justify-between items-center">
                <div>
                    <h3 class="text-2xl font-bold">Horario Generado</h3>
                    <p class="text-sm text-indigo-100 mt-1">Vista completa del horario académico</p>
                </div>
                <button id="btn-cerrar-modal-horario" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[75vh]" id="contenedor-horario-generado">
                <p class="text-center text-gray-400 py-8">Cargando horario...</p>
            </div>
        </div>
    </div>

    <!-- JavaScript Externo -->
    <script src="/static/scripts/auto_generar_horario.js"></script>

</body>
</html>
<?php /**PATH C:\Users\migue\OneDrive\Escritorio\projects\inf342_2exa\app\templates/auto_generar_horario.blade.php ENDPATH**/ ?>