<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Grupos — Plataforma Universitaria INF342</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'navy': {
                            900: '#0f2942',
                            800: '#1e3a5f',
                            700: '#2c5f8d',
                        },
                        'gold': {
                            500: '#c9a961',
                            600: '#b8974f',
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-slate-100 text-slate-800 min-h-screen flex flex-col">

    <!-- Barra superior corporativa -->
    <header class="bg-navy-900 border-b-4 border-gold-500 sticky top-0 z-40 shadow-lg">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-4">
            <div class="flex items-center gap-4">
                <!-- Botón de menú lateral para móviles -->
                <button id="menu-toggle"
                    class="block md:hidden p-2 text-white hover:text-gold-500 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gold-500 flex items-center justify-center font-black text-navy-900 text-lg border-2 border-white">
                        FI
                    </div>
                    <div>
                        <h1 class="text-lg md:text-xl font-bold text-white tracking-wide uppercase">
                            Plataforma Universitaria
                        </h1>
                        <p class="text-xs text-slate-300 uppercase tracking-widest">Sistema de Gestión FICCT</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="hidden sm:block text-right">
                    <p class="font-bold text-white">{{ $user['nomb_comp'] }}</p>
                    <p class="text-xs text-gold-500 uppercase tracking-wider font-semibold">{{ ucfirst($user['rol']) }}</p>
                </div>

                <!-- Avatar corporativo -->
                <div id="user-avatar"
                    class="w-11 h-11 bg-gold-500 text-navy-900 flex items-center justify-center font-black text-lg border-2 border-white shadow-md cursor-pointer select-none hover:bg-gold-600 transition-all">
                    {{ strtoupper(substr($user['nomb_comp'], 0, 1)) }}
                </div>

                <!-- Botón de inicio -->
                <a href="/"
                    class="text-sm bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 font-bold uppercase tracking-wide transition-all border-l-4 border-gold-500 hover:border-gold-400">
                    Inicio
                </a>
            </div>
        </div>
    </header>

    <!-- Panel lateral de usuario corporativo -->
    <aside id="user-aside"
        class="hidden fixed top-20 right-4 w-72 bg-white shadow-2xl border-2 border-slate-300 z-50 transition-all duration-300 opacity-0 scale-95 origin-top-right">
        <div class="bg-navy-900 px-5 py-4 border-b-4 border-gold-500">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gold-500 text-navy-900 flex items-center justify-center font-black text-xl border-2 border-white shadow-md">
                    {{ strtoupper(substr($user['nomb_comp'], 0, 1)) }}
                </div>
                <div>
                    <p class="font-bold text-white leading-tight">{{ $user['nomb_comp'] }}</p>
                    <span class="text-xs px-2 py-1 bg-gold-500 text-navy-900 font-bold uppercase tracking-wider inline-block mt-1">
                        {{ ucfirst($user['rol']) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="p-5 text-sm">
            <div class="space-y-3 bg-slate-50 p-4 border border-slate-200">
                <div class="flex justify-between">
                    <span class="font-bold text-slate-600 uppercase text-xs tracking-wider">CI:</span>
                    <span class="text-navy-900 font-semibold">{{ $user['ci'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-bold text-slate-600 uppercase text-xs tracking-wider">Correo:</span>
                    <span class="text-navy-900 font-semibold">{{ $user['correo'] ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-bold text-slate-600 uppercase text-xs tracking-wider">Teléfono:</span>
                    <span class="text-navy-900 font-semibold">{{ $user['tel'] ?? '—' }}</span>
                </div>
            </div>
            <div class="mt-4">
                <a href="/perfil"
                    class="block w-full text-center bg-navy-900 hover:bg-navy-800 text-white px-4 py-2.5 font-bold uppercase tracking-wide transition-all border-b-4 border-navy-800 hover:border-gold-500">
                    Ver perfil completo
                </a>
            </div>
        </div>
    </aside>


    <!-- Panel lateral corporativo -->
    <aside id="admin-sidebar"
        class="fixed top-0 left-0 w-64 bg-navy-900 shadow-2xl h-full z-30 border-r-4 border-gold-500 transform -translate-x-full md:translate-x-0 transition-transform duration-300">

        <!-- Contenedor con scroll -->
        <div class="flex flex-col h-full overflow-y-auto scrollbar-thin scrollbar-thumb-gold-500 scrollbar-track-navy-800">

            <!-- Encabezado -->
            <div class="p-4 border-b-2 border-gold-500">
                <h3 class="text-sm font-black text-white uppercase tracking-wide">Panel de Administración</h3>
                <p class="text-xs text-gold-500 mt-1 font-semibold uppercase tracking-wider">Gestión completa del sistema</p>
            </div>

            <!-- Navegación -->
            <nav class="flex-1 p-3">
                <ul class="space-y-1 text-sm">

                    <li>
                        <a href="/admin/mod-adm"
                        class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span class="font-semibold">Panel Administrador</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/users"
                        class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5M16 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span class="font-semibold">Gestión de Usuarios</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/roles"
                        class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2v1a2 2 0 01-2 2H9a2 2 0 01-2-2V9a2 2 0 012-2h6zm6 11h-3a2 2 0 01-2-2v-1a2 2 0 012-2h3v5zM6 18H3v-5h3a2 2 0 012 2v1a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="font-semibold">Gestión de Roles y Permisos</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/grupos"
                        class="flex items-center gap-2 px-3 py-2 text-gold-500 bg-navy-800 font-black border-l-4 border-gold-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5M16 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span>Gestión de Grupos</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/aulas"
                        class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7h18M3 12h18M3 17h18"/>
                            </svg>
                            <span class="font-semibold">Gestión de Aulas</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/materias"
                        class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 20l9-5-9-5-9 5 9 5zM12 12V4m0 8l9-5M12 12L3 7"/>
                            </svg>
                            <span class="font-semibold">Gestión de Materias</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/gestiones"
                        class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="font-semibold">Gestión de Gestiones</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/carga-horaria"
                        class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-6h13v6M9 17H5v-6h4m0 6V7m0 0H3v4h2m4-4v4h2"/>
                            </svg>
                            <span class="font-semibold">Carga Horaria del Docente</span>
                        </a>
                    </li>

                    <li>
                        <a href="/auto/generar-horario"
                        class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="font-semibold">Generar Horario</span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/bitacora"
                        class="flex items-center gap-2 px-3 py-2 text-slate-300 hover:bg-navy-800 hover:text-gold-500 transition border-l-4 border-transparent hover:border-gold-500">
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
            <div class="p-3 border-t-2 border-gold-500 text-center text-[11px] text-slate-400 uppercase tracking-widest">
                Módulo Admin v1.1
            </div>
        </div>
    </aside>


    <!-- Overlay para móviles -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-navy-900 bg-opacity-70 z-20 md:hidden hidden"></div>

    <!-- Contenido principal -->
    <main class="flex-1 md:ml-64 p-6 transition-all duration-300">
        <!-- Encabezado -->


        <!-- Encabezado corporativo -->
        <div class="flex flex-col md:flex-row justify-between md:items-center mb-8 bg-white border-l-4 border-gold-500 shadow-md px-6 py-5">
            <div>
                <h2 class="text-2xl md:text-3xl font-black text-navy-900 mb-1 uppercase tracking-tight">Grupos del Sistema</h2>
                <p class="text-slate-600 text-sm font-semibold uppercase tracking-wide">Cree, edite o elimine grupos según sea necesario</p>
            </div>
            <button id="btn-add"
                class="mt-4 md:mt-0 bg-navy-900 hover:bg-navy-800 text-white px-5 py-3 text-sm font-bold uppercase tracking-wide transition-all flex items-center gap-2 shadow-md border-b-4 border-navy-800 hover:border-gold-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Agregar Grupo
            </button>
        </div>

        <!-- Tabla de grupos corporativa -->
        <div class="bg-white border-2 border-slate-300 shadow-lg overflow-hidden">
            <div class="bg-navy-900 px-6 py-4 border-b-4 border-gold-500">
                <h3 class="text-lg font-black text-white uppercase tracking-wide">Registro de Grupos</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-100">
                        <tr>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-black text-navy-900 uppercase tracking-widest border-r border-slate-300">
                                #</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-black text-navy-900 uppercase tracking-widest border-r border-slate-300">
                                Sigla del Grupo</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-black text-navy-900 uppercase tracking-widest">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="grupos-table-body" class="bg-white divide-y divide-slate-200 text-sm">
                        @forelse ($grupos as $index => $grupo)
                            <tr class="grupo-row hover:bg-slate-50 transition-all" data-grupo-id="{{ $grupo['sigla'] }}">
                                <td class="px-6 py-4 text-slate-700 font-semibold border-r border-slate-200">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-navy-900 font-bold border-r border-slate-200">{{ $grupo['sigla'] }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button data-id="{{ $grupo['sigla'] }}" data-sigla="{{ $grupo['sigla'] }}"
                                        class="btn-delete text-red-600 hover:text-white hover:bg-red-600 p-2 border-2 border-red-600 transition-all"
                                        title="Eliminar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-slate-500 font-semibold uppercase tracking-wide">No hay grupos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t-2 border-slate-300">
                <p class="text-sm text-slate-600 font-semibold">Mostrando {{ count($grupos) }} grupos.</p>
            </div>
        </div>
    </main>

    <!-- Pie de página corporativo -->
    <footer class="text-center py-5 text-xs bg-navy-900 text-slate-300 border-t-4 border-gold-500 mt-12">
        <p class="font-bold uppercase tracking-widest">© {{ date('Y') }} Grupo 31 — UAGRM | INF342 - SA</p>
    </footer>

    <!-- Modal de Formulario (Agregar/Editar Grupo) -->
    <div id="grupo-form-modal"
        class="fixed inset-0 bg-navy-900/70 backdrop-blur-sm z-[60] flex items-center justify-center p-4 hidden">

        <!-- Contenedor del modal corporativo -->
        <div class="bg-white border-2 border-slate-300 shadow-2xl w-full max-w-md flex flex-col max-h-[90vh]">

            <!-- Encabezado del Modal corporativo -->
            <div class="flex-shrink-0 bg-navy-900 px-6 py-4 border-b-4 border-gold-500 flex items-center justify-between">
                <h3 id="form-modal-title" class="text-lg font-black text-white uppercase tracking-wide">Agregar Nuevo Grupo</h3>
                <button id="btn-cancel-form-x" class="text-slate-300 hover:text-gold-500 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Formulario -->
            <form id="grupo-form" class="flex-1 flex flex-col min-h-0">
                <input type="hidden" id="form-grupo-id" name="id" value="">

                <!-- Área de campos con scroll -->
                <div class="p-6 overflow-y-auto">

                    <!-- Sigla -->
                    <div>
                        <label for="form-sigla" class="block text-sm font-black text-navy-900 mb-2 uppercase tracking-wide">Sigla del Grupo</label>
                        <input type="text" id="form-sigla" name="sigla"
                            class="w-full border-2 border-slate-300 px-4 py-3 text-sm text-navy-900 focus:outline-none focus:border-navy-700 focus:ring-2 focus:ring-navy-700/20 transition-all"
                            required maxlength="2">
                        <p class="text-xs text-slate-600 mt-2 font-semibold">Máximo 2 caracteres</p>
                    </div>

                </div>

                <!-- Footer del Formulario (Acciones) -->
                <div class="flex-shrink-0 bg-slate-50 px-6 py-4 flex justify-end gap-3 border-t-2 border-slate-300">
                    <button type="button" id="btn-cancel-form"
                        class="text-sm font-bold text-slate-700 bg-white border-2 border-slate-300 px-5 py-2.5 hover:bg-slate-100 transition-all uppercase tracking-wide">
                        Cancelar
                    </button>
                    <button type="submit" id="btn-save-form"
                        class="text-sm font-bold text-white bg-navy-900 px-5 py-2.5 hover:bg-navy-800 transition-all uppercase tracking-wide border-b-4 border-navy-800 hover:border-gold-500">
                        Guardar
                    </button>
                </div>
            </form>
        </div>

    </div>


    <!-- Modal de Confirmación de Eliminación -->
    <div id="delete-modal"
        class="fixed inset-0 bg-navy-900/70 backdrop-blur-sm z-[60] flex items-center justify-center p-4 hidden">
        <div class="bg-white border-2 border-slate-300 shadow-2xl w-full max-w-md">
            <div class="p-6">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center border-2 border-red-600 bg-red-50">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-navy-900 uppercase tracking-wide">Eliminar Grupo</h3>
                        <p class="text-sm text-slate-700 mt-2 leading-relaxed">
                            ¿Estás seguro de que deseas eliminar el grupo <strong id="delete-grupo-sigla"
                                class="font-black text-navy-900">...</strong>? Esta acción no se puede deshacer.
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 border-t-2 border-slate-300">
                <button id="btn-cancel-delete"
                    class="text-sm font-bold text-slate-700 bg-white border-2 border-slate-300 px-5 py-2.5 hover:bg-slate-100 transition-all uppercase tracking-wide">
                    Cancelar
                </button>
                <button id="btn-confirm-delete"
                    class="text-sm font-bold text-white bg-red-600 px-5 py-2.5 hover:bg-red-700 transition-all uppercase tracking-wide border-b-4 border-red-700 hover:border-red-800">
                    Sí, eliminar
                </button>
            </div>
        </div>
    </div>

    <!-- ====== FIN DE MODALES ====== -->
    <!-- JS: Este archivo ahora debe contener toda la lógica -->
    <script src="{{ asset('static/scripts/admin_grupos.js') }}">
    </script>

</body>

</html>
