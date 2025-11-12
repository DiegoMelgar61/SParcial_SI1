<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo de Reportes — Plataforma Universitaria INF342</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col font-sans antialiased">

    <!-- Barra superior -->
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-3">
            <div class="flex items-center gap-4">
                <button id="menu-toggle"
                    class="block md:hidden p-2 text-gray-600 hover:text-indigo-600 rounded-md transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h1 class="text-lg md:text-xl font-semibold text-gray-800 tracking-wide">
                    Plataforma Universitaria
                </h1>
            </div>

            <div class="flex items-center gap-4">
                <div class="hidden sm:block text-right">
                    <p class="font-medium text-gray-800">{{ $user['nomb_comp'] }}</p>
                    <p class="text-xs text-indigo-600 font-medium">{{ ucfirst($user['rol']) }}</p>
                </div>

                <div id="user-avatar"
                    class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold shadow-sm cursor-pointer select-none">
                    {{ strtoupper(substr($user['nomb_comp'], 0, 1)) }}
                </div>

                <a href="/"
                    class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-medium transition shadow-sm">
                    Inicio
                </a>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <aside id="admin-sidebar"
        class="fixed top-0 left-0 w-64 bg-white shadow-lg h-full z-30 border-r border-gray-200 transform -translate-x-full md:translate-x-0 transition-transform duration-300">

        <div class="flex flex-col h-full overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
            <div class="p-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-800">Panel de Reportes</h3>
                <p class="text-xs text-indigo-600 mt-1 font-medium">Consultas y descargas académicas</p>
            </div>

            <nav class="flex-1 p-3">
                <ul class="space-y-1 text-sm">
                    <li>
                        <a href="/reportes"
                            class="flex items-center gap-2 px-3 py-2 text-indigo-700 bg-indigo-50 rounded-lg font-semibold hover:bg-indigo-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622z"/>
                            </svg>
                            <span>Reportes Académicos</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="p-3 border-t border-gray-100 text-center text-[11px] text-gray-500">
                Módulo Reportes v1.0
            </div>
        </div>
    </aside>

    <!-- Overlay para móviles -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden hidden"></div>

    <!-- Contenido principal -->
    <main class="flex-1 md:ml-64 p-6 transition-all duration-300">

        <div class="flex flex-col md:flex-row justify-between md:items-center mb-8">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 mb-1">Módulo de Reportes Académicos</h2>
                <p class="text-gray-600 text-sm">Visualización y descarga de informes por docente</p>
            </div>
            <div id="clock" class="text-sm text-gray-600 font-medium mt-3 md:mt-0"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Reporte Asistencia -->
            <div id="card-asistencia" class="bg-white p-6 rounded-xl border border-gray-200 hover:shadow-md transition cursor-pointer">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Reporte de Asistencia Docente</h3>
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-5">
                    Permite revisar el registro completo de asistencia docente por materia y grupo, con opción de visualización o exportación.
                </p>
                <div class="flex justify-end gap-3">
                    <button id="btn-ver-asistencia" class="text-sm bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 px-4 py-2 rounded-md font-medium transition">
                        Ver en la Web
                    </button>
                    <button id="btn-descargar-asistencia" class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-medium transition">
                        Descargar
                    </button>
                </div>
            </div>

            <!-- Reporte Licencias -->
            <div id="card-licencia" class="bg-white p-6 rounded-xl border border-gray-200 hover:shadow-md transition cursor-pointer">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Reporte de Licencias Docentes</h3>
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2v1a2 2 0 01-2 2H9a2 2 0 01-2-2V9a2 2 0 012-2h6zm6 11h-3a2 2 0 01-2-2v-1a2 2 0 012-2h3v5zM6 18H3v-5h3a2 2 0 012 2v1a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-5">
                    Muestra las licencias registradas por los docentes, con información detallada sobre duración, motivo y estado de aprobación.
                </p>
                <div class="flex justify-end gap-3">
                    <button id="btn-ver-licencia" class="text-sm bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-700 px-4 py-2 rounded-md font-medium transition">
                        Ver en la Web
                    </button>
                    <button id="btn-descargar-licencia" class="text-sm bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-md font-medium transition">
                        Descargar
                    </button>
                </div>
            </div>
        </div>
    </main>

    <footer class="text-center py-4 text-xs text-gray-500 border-t border-gray-200 bg-white mt-10 md:ml-64">
        © {{ date('Y') }} Universidad Autónoma Gabriel René Moreno — Sistema de Reportes Académicos INF342
    </footer>

    <script src="{{ secure_asset('static/scripts/reportes.js') }}"></script>
</body>
</html>
