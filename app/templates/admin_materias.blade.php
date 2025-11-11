<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Materias — Plataforma Universitaria INF342</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Animación suave para checkboxes */
        .grupo-checkbox {
            transition: all 0.2s ease;
        }
        .grupo-checkbox:hover {
            transform: scale(1.1);
        }
        /* Scrollbar personalizado */
        #grupos-lista::-webkit-scrollbar {
            width: 8px;
        }
        #grupos-lista::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        #grupos-lista::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        #grupos-lista::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
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
                    <p class="font-medium text-gray-800">{{ $user['nomb_comp'] }}</p>
                    <p class="text-xs text-gray-500">{{ ucfirst($user['rol']) }}</p>
                </div>

                <!-- Avatar -->
                <div id="user-avatar"
                    class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold shadow-sm cursor-pointer select-none">
                    {{ strtoupper(substr($user['nomb_comp'], 0, 1)) }}
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
                    {{ strtoupper(substr($user['nomb_comp'], 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-800 leading-tight">{{ $user['nomb_comp'] }}</p>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 font-medium">
                        {{ ucfirst($user['rol']) }}
                    </span>
                </div>
            </div>
            <hr class="my-3 border-gray-200">
            <ul class="space-y-2 text-sm">
                <li><span class="font-medium text-gray-600">CI:</span> {{ $user['ci'] }}</li>
                <li><span class="font-medium text-gray-600">Correo:</span> {{ $user['correo'] ?? '—' }}</li>
                <li><span class="font-medium text-gray-600">Teléfono:</span> {{ $user['tel'] ?? '—' }}</li>
            </ul>
            <div class="mt-4 pt-3 border-t border-gray-100">
                <a href="/perfil"
                    class="text-indigo-600 text-sm font-medium hover:underline hover:text-indigo-700 transition">
                    Ver perfil completo →
                </a>
            </div>
        </div>
    </aside>

    <!-- Overlay para móviles -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden md:hidden"></div>

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
                        class="flex items-center gap-2 px-3 py-2 text-indigo-700 bg-indigo-50 rounded-lg font-semibold hover:bg-indigo-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 20l9-5-9-5-9 5 9 5zM12 12V4m0 8l9-5M12 12L3 7"/>
                            </svg>
                            <span>Gestión de Materias</span>
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

    <!-- Contenedor principal -->
    <main class="md:ml-64 flex-1 p-6">

        <!-- Header del módulo -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Gestión de Materias</h2>
                    <p class="text-sm text-gray-600 mt-1">Administre las materias del sistema académico</p>
                </div>
                <button id="btn-add"
                    class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-indigo-700 transition font-medium text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Agregar Materia
                </button>
            </div>

            <!-- Filtros -->
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <div>
                        <label for="filter-sigla" class="block text-xs font-medium text-gray-700 mb-1">Filtrar por
                            Sigla</label>
                        <input type="text" id="filter-sigla" placeholder="Ej: INF342"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="filter-nombre" class="block text-xs font-medium text-gray-700 mb-1">Filtrar por
                            Nombre</label>
                        <input type="text" id="filter-nombre" placeholder="Ej: Programación"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="filter-semestre" class="block text-xs font-medium text-gray-700 mb-1">Filtrar por
                            Semestre</label>
                        <input type="number" id="filter-semestre" placeholder="Ej: 3"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Materias (Desktop) -->
        <div class="hidden md:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                #</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sigla</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nombre de la Materia</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Semestre</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Carga Horaria</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Grupos</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="materias-table-body" class="bg-white divide-y divide-gray-200 text-sm">
                        @forelse ($materias as $index => $materia)
                            <tr class="materia-row hover:bg-gray-50" data-materia-id="{{ $materia['sigla'] }}">
                                <td class="px-6 py-4 text-gray-700">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-gray-800 font-medium sigla-cell">{{ $materia['sigla'] }}</td>
                                <td class="px-6 py-4 text-gray-800 nombre-cell">{{ $materia['nombre'] }}</td>
                                <td class="px-6 py-4 text-gray-600 semestre-cell">{{ $materia['semestre'] }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $materia['carga_horaria'] }} hrs</td>
                                <td class="px-6 py-4">
                                    @if (!empty($materia['grupos']) && count($materia['grupos']) > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($materia['grupos'] as $grupo)
                                                <span class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full">{{ $grupo }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic text-sm">Sin grupos</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button data-sigla="{{ $materia['sigla'] }}" data-nombre="{{ $materia['nombre'] }}"
                                        data-semestre="{{ $materia['semestre'] }}"
                                        data-carga="{{ $materia['carga_horaria'] }}"
                                        class="btn-edit text-blue-600 hover:text-blue-900 p-1 rounded-md hover:bg-blue-100 transition"
                                        title="Editar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button data-sigla="{{ $materia['sigla'] }}" data-nombre="{{ $materia['nombre'] }}"
                                        class="btn-assign-groups text-green-600 hover:text-green-900 p-1 rounded-md hover:bg-green-100 transition"
                                        title="Asignar Grupos">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </button>
                                    <button data-sigla="{{ $materia['sigla'] }}" data-nombre="{{ $materia['nombre'] }}"
                                        class="btn-delete text-red-600 hover:text-red-900 p-1 rounded-md hover:bg-red-100 transition"
                                        title="Eliminar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr id="no-records">
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">No hay materias registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Vista Móvil/Tablet (Cards) -->
        <div class="md:hidden space-y-4" id="materias-cards">
            @forelse ($materias as $materia)
                <div class="materia-card bg-white rounded-lg shadow-sm border border-gray-200 p-4"
                    data-materia-id="{{ $materia['sigla'] }}">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 sigla-cell">{{ $materia['sigla'] }}</h3>
                            <p class="text-sm text-gray-700 nombre-cell">{{ $materia['nombre'] }}</p>
                            <p class="text-sm text-gray-600 semestre-cell mt-1">Semestre: {{ $materia['semestre'] }}</p>
                            <div class="mt-2">
                                @if (!empty($materia['grupos']) && count($materia['grupos']) > 0)
                                    <p class="text-xs text-gray-500 mb-1">Grupos:</p>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($materia['grupos'] as $grupo)
                                            <span class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full">{{ $grupo }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Sin grupos asignados</span>
                                @endif
                            </div>
                        </div>
                        <span
                            class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded-full">{{ $materia['carga_horaria'] }}
                            hrs</span>
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                        <button data-sigla="{{ $materia['sigla'] }}" data-nombre="{{ $materia['nombre'] }}"
                            data-semestre="{{ $materia['semestre'] }}" data-carga="{{ $materia['carga_horaria'] }}"
                            class="btn-edit text-blue-600 hover:text-blue-900 p-1" title="Editar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button data-sigla="{{ $materia['sigla'] }}" data-nombre="{{ $materia['nombre'] }}"
                            class="btn-assign-groups text-green-600 hover:text-green-900 p-1" title="Asignar Grupos">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </button>
                        <button data-sigla="{{ $materia['sigla'] }}" data-nombre="{{ $materia['nombre'] }}"
                            class="btn-delete text-red-600 hover:text-red-900 p-1" title="Eliminar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <div id="no-records-mobile" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                    <p class="text-gray-500">No hay materias registradas.</p>
                </div>
            @endforelse
        </div>

        <!-- Contador de registros -->
        <div class="mt-4 text-sm text-gray-500" id="total-records">
            Mostrando {{ count($materias) }} registros.
        </div>

    </main>

    <!-- Pie de página -->
    <footer class="md:ml-64 text-center py-4 text-xs text-gray-500 border-t border-gray-200 mt-auto">
        © 2025 Plataforma Universitaria — Todos los derechos reservados
    </footer>

    <!-- Modal de Formulario (Agregar/Editar Materia) -->
    <div id="materia-form-modal"
        class="fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center p-4 hidden">

        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md flex flex-col max-h-[90vh]">

            <!-- Encabezado del Modal -->
            <div class="flex-shrink-0 flex items-center justify-between p-5 border-b border-gray-200">
                <h3 id="form-modal-title" class="text-lg font-semibold text-gray-900">Agregar Nueva Materia</h3>
                <button id="btn-cancel-form-x" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Formulario -->
            <form id="materia-form" class="flex-1 flex flex-col min-h-0">
                <input type="hidden" id="form-materia-sigla-original" name="sigla_original" value="">

                <!-- Área de campos con scroll -->
                <div class="p-6 overflow-y-auto space-y-4">

                    <!-- Sigla -->
                    <div>
                        <label for="form-sigla" class="block text-sm font-medium text-gray-700 mb-2">Sigla *</label>
                        <input type="text" id="form-sigla" name="sigla"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            required maxlength="10">
                        <p class="text-xs text-gray-500 mt-1">Ej: INF342, MAT101</p>
                    </div>

                    <!-- Nombre -->
                    <div>
                        <label for="form-nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre de la
                            Materia *</label>
                        <input type="text" id="form-nombre" name="nombre"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            required maxlength="100">
                        <p class="text-xs text-gray-500 mt-1">Ej: Programación Web Avanzada</p>
                    </div>

                    <!-- Semestre -->
                    <div>
                        <label for="form-semestre" class="block text-sm font-medium text-gray-700 mb-2">Semestre
                            *</label>
                        <input type="number" id="form-semestre" name="semestre" min="1" max="10"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            required>
                        <p class="text-xs text-gray-500 mt-1">Número del semestre (1-10)</p>
                    </div>

                    <!-- Carga Horaria -->
                    <div>
                        <label for="form-carga-horaria" class="block text-sm font-medium text-gray-700 mb-2">Carga
                            Horaria *</label>
                        <select id="form-carga-horaria" name="carga_horaria"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            required>
                            <option value="">Seleccione...</option>
                            <option value="90">90 horas</option>
                            <option value="135">135 horas</option>
                            <option value="173">173 horas</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Carga horaria del semestre</p>
                    </div>

                    <p class="text-xs text-gray-500">* Campos obligatorios</p>

                </div>

                <!-- Footer del Formulario -->
                <div
                    class="flex-shrink-0 bg-gray-50 px-6 py-4 flex justify-end gap-3 rounded-b-xl border-t border-gray-200">
                    <button type="button" id="btn-cancel-form"
                        class="text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="submit" id="btn-save-form"
                        class="text-sm font-medium text-white bg-indigo-600 rounded-lg px-4 py-2 hover:bg-indigo-700 transition">
                        Guardar
                    </button>
                </div>
            </form>
        </div>

    </div>

    <!-- Modal de Confirmación de Eliminación -->
    <div id="delete-modal"
        class="fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
            <div class="p-6">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full bg-red-100">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Eliminar Materia</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            ¿Estás seguro de que deseas eliminar la materia <strong id="delete-materia-nombre"
                                class="font-bold">...</strong>? Esta acción no se puede deshacer.
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 rounded-b-xl">
                <button id="btn-cancel-delete"
                    class="text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button id="btn-confirm-delete"
                    class="text-sm font-medium text-white bg-red-600 rounded-lg px-4 py-2 hover:bg-red-700 transition">
                    Sí, eliminar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Asignación de Grupos -->
    <div id="assign-groups-modal"
        class="fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center p-4 hidden">
        
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl flex flex-col max-h-[90vh]">
            
            <!-- Encabezado del Modal -->
            <div class="flex-shrink-0 flex items-center justify-between p-5 border-b border-gray-200">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Asignar Grupos</h3>
                    <p class="text-sm text-gray-600 mt-1">Materia: <span id="assign-groups-materia-nombre" class="font-medium text-indigo-600"></span></p>
                </div>
                <button id="btn-cancel-assign-x" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Formulario -->
            <form id="assign-groups-form" class="flex-1 flex flex-col min-h-0">
                <input type="hidden" id="assign-groups-sigla" name="sigla_materia" value="">

                <!-- Área de checkboxes con scroll -->
                <div class="p-6 overflow-y-auto">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Seleccione los grupos a asignar</label>
                    <div id="grupos-lista" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        <!-- Aquí se insertarán dinámicamente los checkboxes de grupos -->
                    </div>
                    <div id="grupos-loading" class="text-center py-8 text-gray-500 hidden">
                        <svg class="animate-spin h-8 w-8 mx-auto text-indigo-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="mt-2">Cargando grupos...</p>
                    </div>
                    <div id="grupos-empty" class="text-center py-8 text-gray-400 hidden">
                        <p>No hay grupos disponibles en el sistema.</p>
                    </div>
                </div>

                <!-- Footer del Modal -->
                <div class="flex-shrink-0 bg-gray-50 px-6 py-4 flex justify-between items-center rounded-b-xl border-t border-gray-200">
                    <div class="text-sm text-gray-600">
                        <span id="grupos-selected-count">0</span> grupos seleccionados
                    </div>
                    <div class="flex gap-3">
                        <button type="button" id="btn-cancel-assign"
                            class="text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50 transition">
                            Cancelar
                        </button>
                        <button type="submit" id="btn-save-assign"
                            class="text-sm font-medium text-white bg-indigo-600 rounded-lg px-4 py-2 hover:bg-indigo-700 transition">
                            Guardar Asignación
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>

    <!-- JS -->
    <script src="{{ secure_asset('static/scripts/admin_materias.js') }}"></script>

</body>

</html>
