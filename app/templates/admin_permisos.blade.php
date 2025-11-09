<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Permisos — Plataforma Universitaria INF342</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
     <!-- Panel lateral de usuario (copiado del index, necesario para el avatar) -->
    <aside id="user-aside"
           class="hidden fixed top-16 right-4 w-64 bg-white shadow-2xl rounded-xl border border-gray-200 z-50 transition-all duration-300 opacity-0 scale-95 origin-top-right">
        <div class="p-5 text-sm text-gray-700">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold shadow-sm">
                    {{ strtoupper(substr($user['nomb_comp'],0,1)) }}
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

    <!-- Panel lateral de navegación (Sidebar) -->
    <aside id="admin-sidebar" 
           class="fixed top-0 left-0 w-64 bg-white shadow-lg h-full z-30 transition-transform duration-300 transform -translate-x-full md:translate-x-0 border-r border-gray-200">
        <div class="p-6 h-full flex flex-col">
            <!-- Encabezado del sidebar -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800">Panel de Administración</h3>
                <p class="text-sm text-gray-500 mt-3">Gestión completa del sistema</p>
            </div>

            <!-- Navegación -->
            <nav class="flex-1">
                <ul class="space-y-2">
                    <!-- Enlace activo para Gestión de Usuarios -->
                     <li>
                        <a href="/admin/mod-adm" 
                           class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition group">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span class="font-medium">Panel Administrador</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/users" 
                           class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition group">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                            <span class="font-medium">Gestión de Usuarios</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/roles" 
                           class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition group">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span class="font-medium">Gestión de Roles</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/materias" 
                           class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition group">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            <span class="font-medium">Gestión de Materias</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/permisos" 
                           class="flex items-center gap-3 px-4 py-3 text-indigo-700 bg-indigo-50 rounded-lg transition group font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            <span class="font-medium">Gestión de Permisos</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/bitacora" 
                           class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition group">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            <span class="font-medium">Consultar Historial de Acciones</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Footer del sidebar -->
            <div class="pt-4 border-t border-gray-200">
                <p class="text-xs text-gray-500 text-center">
                    Módulo Admin v1.0
                </p>
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
        <div class="flex flex-col md:flex-row justify-between md:items-center mb-8">
            <div>
                <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-1">Permisos del Sistema</h2>
                <p class="text-gray-500 text-sm">Cree, edite o elimine permisos según sea necesario.</p>
            </div>
            <button id="btn-add" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>    
            Agregar Permiso
            </button>
        </div>
        <!-- Tabla de permisos -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <thead class="bg-gray-50">
                    <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre del Permiso</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                       <tbody id="usuarios-table-body" class="bg-white divide-y divide-gray-200 text-sm">
                    @forelse ($permisos as $index => $permiso)
                        <tr class="user-row hover:bg-gray-50" data-perm-id="{{ $permiso['id'] }}">
                            <td class="px-6 py-4 text-gray-700">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-gray-800 font-medium">{{ $permiso['nombre'] }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $permiso['descripcion'] }}</td>
                            <td class="px-6 py-4 text-right">
                                <button data-id="{{ $permiso['id'] }}" data-nombre="{{ $permiso['nombre'] }}" data-descripcion="{{ $permiso['descripcion'] }}"
                                    class="btn-edit text-indigo-600 hover:text-indigo-900 p-1 rounded-md hover:bg-indigo-100 transition" title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                </button>
                                <button data-id="{{ $permiso['id'] }}" data-nombre="{{ $permiso['nombre'] }}"
                                    class="btn-delete text-red-600 hover:text-red-900 p-1 rounded-md hover:bg-red-100 transition" title="Eliminar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">No hay permisos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 text-sm text-gray-500">Mostrando {{ count($permisos) }} permisos.</div>
    </main>

    <!-- Pie de página -->
    <footer class="text-center py-4 text-xs text-gray-500 border-t border-gray-200 mt-auto">
        © 2025 Plataforma Universitaria — Todos los derechos reservados
    </footer>

    <!-- Modal de Formulario (Agregar/Editar Usuario) -->
    <div id="user-form-modal" class="fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center p-4 hidden">
        
        <!-- *** INICIO DE LA CORRECCIÓN RESPONSIVE *** -->
        <!-- Contenedor del modal con altura máxima y flex-col -->
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl flex flex-col max-h-[90vh]">
            
            <!-- Encabezado del Modal (fijo) -->
            <div class="flex-shrink-0 flex items-center justify-between p-5 border-b border-gray-200">
                <h3 id="form-modal-title" class="text-lg font-semibold text-gray-900">Agregar Nuevo Permiso</h3>
                <button id="btn-cancel-form-x" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <!-- Formulario (con contenedor flex para scroll) -->
            <form id="user-form" class="flex-1 flex flex-col min-h-0">
                <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}"> -->
                <input type="hidden" id="form-user-id" name="id" value="">

                <!-- Área de campos con scroll -->
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 overflow-y-auto">
                   
                    <!-- Nombre -->
                    <div>
                        <label for="form-nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                        <input type="text" id="form-nombre" name="nombre" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                   
                    <!-- Descripcion -->
                    <div>
                        <label for="form-descripcion" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                        <input type="text" id="form-descripcion" name="descripcion" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
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
        <!-- *** FIN DE LA CORRECCIÓN RESPONSIVE *** -->

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
                        <h3 class="text-lg font-semibold text-gray-900">Eliminar Usuario</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            ¿Estás seguro de que deseas eliminar el permiso <strong id="delete-user-name" class="font-bold">...</strong>? Esta acción no se puede deshacer.
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
    <!-- JS: Este archivo ahora debe contener toda la lógica -->
    <script src="{{ asset('static/scripts/admin_permisos.js') }}"></script>

</body>
</html>
