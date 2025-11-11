<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo Docencia — Plataforma Universitaria INF342</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col font-sans antialiased">

    <!-- Barra superior -->
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-3">
            <div class="flex items-center gap-4">
                <!-- Botón menú lateral -->
                <button id="menu-toggle" class="block md:hidden p-2 text-gray-600 hover:text-sky-600 rounded-md transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-lg md:text-xl font-semibold text-gray-800 tracking-wide">
                    Plataforma Universitaria
                </h1>
            </div>

            <div class="flex items-center gap-4">
                <div class="hidden sm:block text-right">
                    <p class="font-medium text-gray-800">{{ $user['nomb_comp'] }}</p>
                    <p class="text-xs text-sky-600 font-medium">{{ ucfirst($user['rol']) }}</p>
                </div>

                <div id="user-avatar"
                     class="w-10 h-10 rounded-full bg-sky-600 text-white flex items-center justify-center font-semibold shadow-sm cursor-pointer select-none">
                    {{ strtoupper(substr($user['nomb_comp'], 0, 1)) }}
                </div>

                <a href="/"
                   class="text-sm bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-md font-medium transition shadow-sm">
                    Inicio
                </a>
            </div>
        </div>
    </header>

    <!-- Sidebar Docencia -->
    <aside id="docencia-sidebar"
        class="fixed top-0 left-0 w-64 bg-white shadow-lg h-full z-30 border-r border-gray-200 transform -translate-x-full md:translate-x-0 transition-transform duration-300">

        <div class="flex flex-col h-full overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
            
            <!-- Encabezado -->
            <div class="p-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-800">Panel de Docencia</h3>
                <p class="text-xs text-sky-600 mt-1 font-medium">Gestión docente</p>
            </div>

            <!-- Navegación -->
            <nav class="flex-1 p-3">
                <ul class="space-y-1 text-sm">

                    <li>
                        <a href="/docencia/asistencia"
                           class="flex items-center gap-2 px-3 py-2 text-sky-700 bg-sky-50 rounded-lg font-semibold hover:bg-sky-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Asistencia</span>
                        </a>
                    </li>

                    <li>
                        <a href="/docencia/licencia"
                           class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-sky-50 hover:text-sky-700 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Licencia</span>
                        </a>
                    </li>

                </ul>
            </nav>

            <!-- Footer -->
            <div class="p-3 border-t border-gray-100 text-center text-[11px] text-gray-500">
                Módulo Docencia v1.0
            </div>
        </div>
    </aside>

    <!-- Overlay móvil -->
    <div id="sidebar-overlay"
         class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden hidden"></div>

    <!-- Contenido principal -->
    <main class="flex-1 md:ml-64 p-6 transition-all duration-300">
        <div class="flex flex-col md:flex-row justify-between md:items-center mb-8">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 mb-1">Módulo de Docencia</h2>
                <p class="text-gray-600 text-sm">Gestión de asistencia y licencias docentes</p>
            </div>
            <div id="clock" class="text-sm text-gray-600 font-medium mt-3 md:mt-0"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h3 class="text-base font-semibold text-gray-800 mb-2">Asistencia</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Permite registrar y consultar la asistencia del docente a sus clases asignadas.
                </p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h3 class="text-base font-semibold text-gray-800 mb-2">Licencia</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Permite gestionar las licencias docentes y su aprobación por parte de la autoridad correspondiente.
                </p>
            </div>
        </div>
    </main>

    <footer class="text-center py-4 text-xs text-gray-500 border-t border-gray-200 bg-white mt-10 md:ml-64">
        © {{ date('Y') }} Grupo 32 — UAGRM | INF342 - SA
    </footer>
    <script src="asset{'static/scripts/mod_docencia.js'}"></script>
</body>
</html>
