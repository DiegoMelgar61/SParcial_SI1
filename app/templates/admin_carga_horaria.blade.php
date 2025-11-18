<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carga Horaria Docente — Plataforma Universitaria INF342</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    <!-- Barra superior -->
    <header class="bg-navy-900 border-b-4 border-gold-500 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-3">
            <div class="flex items-center gap-4">
                <button id="menu-toggle" class="block md:hidden p-2 text-gold-500 hover:text-gold-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h1 class="text-lg md:text-xl font-semibold text-white tracking-wide">
                    Plataforma Universitaria
                </h1>
            </div>

            <div class="flex items-center gap-4">
                <div class="hidden sm:block text-right">
                    <p class="font-medium text-white">{{ $user['nomb_comp'] ?? 'Usuario' }}</p>
                    <p class="text-xs text-gold-500 font-medium">Admin</p>
                </div>

                <div id="user-avatar"
                     class="w-10 h-10 bg-gold-500 text-navy-900 flex items-center justify-center font-bold shadow-sm cursor-pointer select-none">
                    {{ isset($user['nomb_comp']) ? strtoupper(substr($user['nomb_comp'], 0, 1)) : '?' }}
                </div>

                <a href="/"
                   class="text-sm bg-gold-500 hover:bg-gold-600 text-navy-900 px-4 py-2 font-semibold transition border-b-4 border-gold-600 hover:border-navy-900">
                    Inicio
                </a>
            </div>
        </div>
    </header>

    <!-- Panel lateral de usuario -->
    <aside id="user-aside"
        class="hidden fixed top-16 right-4 w-64 bg-white shadow-2xl border-2 border-navy-900 z-50 transition-all duration-300 opacity-0 scale-95 origin-top-right">
        <div class="p-5 text-sm text-gray-700">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-gold-500 text-navy-900 flex items-center justify-center font-bold shadow-sm">
                    {{ isset($user['nomb_comp']) ? strtoupper(substr($user['nomb_comp'], 0, 1)) : '?' }}
                </div>
                <div>
                    <p class="font-semibold text-gray-800 leading-tight">{{ $user['nomb_comp'] ?? 'Usuario' }}</p>
                    <span class="text-xs px-2 py-0.5 bg-gold-500 text-navy-900 font-semibold">
                        Admin
                    </span>
                </div>
            </div>
            <hr class="my-3 border-gold-500">
            <ul class="space-y-2 text-sm">
                <li><span class="font-medium text-gray-600">CI:</span> {{ $user['ci'] ?? '—' }}</li>
                <li><span class="font-medium text-gray-600">Correo:</span> {{ $user['correo'] ?? '—' }}</li>
                <li><span class="font-medium text-gray-600">Teléfono:</span> {{ $user['tel'] ?? '—' }}</li>
            </ul>
            <div class="mt-4 pt-3 border-t border-gold-500">
                <a href="/perfil"
                    class="text-navy-900 text-sm font-semibold hover:text-gold-600 transition">
                    Ver perfil completo →
                </a>
            </div>
        </div>
    </aside>

    <!-- Sidebar -->
    <aside id="admin-sidebar"
        class="fixed top-0 left-0 w-64 bg-navy-900 shadow-lg h-full z-30 border-r-4 border-gold-500 transform -translate-x-full md:translate-x-0 transition-transform duration-300">

        <!-- Contenedor con scroll -->
        <div class="flex flex-col h-full overflow-y-auto scrollbar-thin scrollbar-thumb-gold-500 scrollbar-track-navy-800">

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
                        class="flex items-center gap-2 px-3 py-2 text-gray-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>Panel Administrador</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/users"
                        class="flex items-center gap-2 px-3 py-2 text-gray-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5M16 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span>Gestión de Usuarios</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/roles"
                        class="flex items-center gap-2 px-3 py-2 text-gray-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2v1a2 2 0 01-2 2H9a2 2 0 01-2-2V9a2 2 0 012-2h6zm6 11h-3a2 2 0 01-2-2v-1a2 2 0 012-2h3v5zM6 18H3v-5h3a2 2 0 012 2v1a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Gestión de Roles y Permisos</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/grupos"
                        class="flex items-center gap-2 px-3 py-2 text-gray-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5M16 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span>Gestión de Grupos</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/aulas"
                        class="flex items-center gap-2 px-3 py-2 text-gray-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7h18M3 12h18M3 17h18"/>
                            </svg>
                            <span>Gestión de Aulas</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/materias"
                        class="flex items-center gap-2 px-3 py-2 text-gray-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 20l9-5-9-5-9 5 9 5zM12 12V4m0 8l9-5M12 12L3 7"/>
                            </svg>
                            <span>Gestión de Materias</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/gestiones"
                        class="flex items-center gap-2 px-3 py-2 text-gray-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Gestión de Gestiones</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/carga-horaria"
                        class="flex items-center gap-2 px-3 py-2 text-gold-500 bg-navy-800 border-l-4 border-gold-500 font-bold hover:bg-navy-800 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-6h13v6M9 17H5v-6h4m0 6V7m0 0H3v4h2m4-4v4h2"/>
                            </svg>
                            <span>Carga Horaria del Docente</span>
                        </a>
                    </li>

                    <li>
                        <a href="/auto/generar-horario"
                        class="flex items-center gap-2 px-3 py-2 text-gray-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Generar Horario</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/bitacora"
                        class="flex items-center gap-2 px-3 py-2 text-gray-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
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
            <div class="p-3 border-t-2 border-gold-500 text-center text-[11px] text-gray-400">
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
        <div class="flex flex-col md:flex-row justify-between md:items-center mb-8 bg-white border-b-4 border-gold-500 p-6 shadow-sm">
            <div>
                <h2 class="text-2xl font-bold text-navy-900 mb-1">Carga Horaria Docente</h2>
                <p class="text-gray-600 text-sm">Visualiza y administra la carga horaria de los docentes</p>
            </div>
            <div class="mt-4 md:mt-0 flex items-center gap-3">
                <label for="select-gestion-carga" class="text-sm font-semibold text-navy-900">Gestión:</label>
                <select id="select-gestion-carga"
                        class="border-2 border-navy-900 px-4 py-2 text-sm focus:ring-2 focus:ring-gold-500 focus:border-gold-500 min-w-[200px]">
                    <option value="">Seleccione gestión...</option>
                    @if(isset($gestiones) && is_array($gestiones) && count($gestiones) > 0)
                        @foreach($gestiones as $gestion)
                            <option value="{{ $gestion['id'] }}">{{ $gestion['nombre'] }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>

        <!-- Mensaje: Seleccione Gestión -->
        <div id="mensaje-seleccionar-gestion" class="bg-white shadow-sm border-2 border-navy-900 p-12">
            <div class="text-center">
                <svg class="w-20 h-20 mx-auto text-gold-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-xl font-bold text-navy-900 mb-2">Seleccione una gestión para ver la carga horaria</h3>
                <p class="text-gray-600">Use el selector de gestión en la parte superior para cargar los datos de los docentes</p>
            </div>
        </div>

        <!-- Contenedor de estadísticas y tabla (oculto inicialmente) -->
        <div id="carga-content-wrapper" class="hidden">
        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Total Docentes -->
            <div class="bg-white p-6 shadow-sm border-l-4 border-navy-900 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-navy-900">Total Docentes</h3>
                    <div class="w-10 h-10 bg-navy-900 flex items-center justify-center">
                        <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-navy-900" data-stat="total">0</p>
                <p class="text-sm text-gray-600 mt-1">En el sistema</p>
            </div>

            <!-- Carga Total -->
            <div class="bg-white p-6 shadow-sm border-l-4 border-gold-500 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-navy-900">Carga Total</h3>
                    <div class="w-10 h-10 bg-gold-500 flex items-center justify-center">
                        <svg class="w-5 h-5 text-navy-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-navy-900">
                    <span data-stat="horas">0</span>
                    <span class="text-lg text-gray-600 font-normal">hrs</span>
                </p>
                <p class="text-sm text-gray-600 mt-1">Horas acumuladas en el sistema</p>
            </div>

            <!-- Promedio -->
            <div class="bg-white p-6 shadow-sm border-l-4 border-navy-900 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-navy-900">Promedio</h3>
                    <div class="w-10 h-10 bg-navy-900 flex items-center justify-center">
                        <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-navy-900">
                    <span data-stat="promedio">0</span>
                    <span class="text-lg text-gray-600 font-normal">hrs</span>
                </p>
                <p class="text-sm text-gray-600 mt-1">Por docente</p>
            </div>
        </div>

        <!-- Grid de Docentes tipo Paquetes -->
        <div class="bg-white shadow-sm border-2 border-navy-900">
            <div class="p-6 border-b-4 border-gold-500 bg-navy-900">
                <h3 class="text-lg font-bold text-white">Listado de Docentes</h3>
                <p class="text-sm text-gold-500 mt-1">Haz clic en cada paquete para revisar la carga horaria completa</p>
            </div>

            <!-- Grid de Cards de Docentes -->
            <div id="grid-docentes" class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Los datos se cargan dinámicamente por JavaScript -->
            </div>
        </div>
        </div><!-- Fin carga-content-wrapper -->
    </main>

    <!-- Footer -->
    <footer class="md:ml-64 bg-navy-900 border-t-4 border-gold-500 py-4 text-center text-xs text-gray-300">
        © 2025 Plataforma Universitaria — Sistema de Gestión Académica
    </footer>

    <!-- Modal Detalle Carga Horaria -->
    <div id="modal-detalle" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white shadow-2xl border-4 border-navy-900 max-w-6xl w-full max-h-[90vh] overflow-hidden">
            <!-- Header Modal -->
            <div class="bg-navy-900 border-b-4 border-gold-500 p-6 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-2xl font-bold" id="modal-docente-nombre">Carga Horaria Docente</h3>
                        <p class="text-sm text-gold-500 mt-1" id="modal-docente-info">Información detallada</p>
                    </div>
                    <button id="btn-cerrar-modal" class="text-gold-500 hover:bg-gold-500 hover:text-navy-900 p-2 transition">
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
                    <div class="bg-navy-900 p-4 border-l-4 border-gold-500">
                        <p class="text-sm text-gold-500 font-semibold">Carga Total</p>
                        <p class="text-2xl font-bold text-white mt-1" id="detalle-carga-total">0 hrs</p>
                    </div>
                    <div class="bg-gold-500 p-4 border-l-4 border-navy-900">
                        <p class="text-sm text-navy-900 font-semibold">Horas Semanales</p>
                        <p class="text-2xl font-bold text-navy-900 mt-1" id="detalle-horas-semanales">0 hrs</p>
                    </div>
                    <div class="bg-navy-900 p-4 border-l-4 border-gold-500">
                        <p class="text-sm text-gold-500 font-semibold">Total Materias</p>
                        <p class="text-2xl font-bold text-white mt-1" id="detalle-total-materias">0</p>
                    </div>
                </div>

                <!-- Tabla Materias -->
                <div class="bg-gray-50 p-4 mb-6 border-2 border-navy-900">
                    <h4 class="font-bold text-navy-900 mb-4">Materias Asignadas</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm" id="tabla-materias-detalle">
                            <thead class="bg-navy-900">
                                <tr>
                                    <th class="px-4 py-2 text-left font-bold text-white">Sigla</th>
                                    <th class="px-4 py-2 text-left font-bold text-white">Materia</th>
                                    <th class="px-4 py-2 text-center font-bold text-white">Grupo</th>
                                    <th class="px-4 py-2 text-center font-bold text-white">Semestre</th>
                                    <th class="px-4 py-2 text-center font-bold text-white">Carga Horaria</th>
                                    <th class="px-4 py-2 text-center font-bold text-white">Clases</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-materias-detalle">
                                <!-- Llenado dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Horario Semanal -->
                <div class="bg-gray-50 p-4 border-2 border-navy-900">
                    <h4 class="font-bold text-navy-900 mb-4">Horario Semanal</h4>
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
