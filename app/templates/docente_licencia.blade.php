<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Licencias — Plataforma Universitaria INF342</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col font-sans antialiased">

    <!-- Barra superior -->
  <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-3">
      <div class="flex items-center gap-4">
        <button id="menu-toggle" class="block md:hidden p-2 text-gray-600 hover:text-sky-600 rounded-md transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
        <h1 class="text-lg md:text-xl font-semibold text-gray-800 tracking-wide">Módulo Docencia</h1>
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

    <!-- Panel lateral de usuario -->
    <aside id="user-aside"
        class="hidden fixed top-16 right-4 w-64 bg-white shadow-2xl rounded-xl border border-gray-200 z-50 transition-all duration-300 opacity-0 scale-95 origin-top-right">
        <div class="p-5 text-sm text-gray-700">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold shadow-sm">
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

    <!-- Sidebar -->
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
                
                <!-- Panel principal -->
                <li>
                <a href="/docen/mod-doc"
                    class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-sky-50 hover:text-sky-700 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h7v7H3V3zm0 11h7v7H3v-7zm11-11h7v7h-7V3zm0 11h7v7h-7v-7z"/>
                    </svg>
                    <span>Panel Docencia</span>
                </a>
                </li>

                <!-- Asistencia -->
                <li>
                <a href="/docen/asistencia"
                    class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-sky-50 hover:text-sky-700 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Asistencia</span>
                </a>
                </li>

                <!-- Licencia -->
                <li>
                <a href="/docencia/licencia"
                    class="flex items-center gap-2 px-3 py-2 text-sky-700 bg-sky-50 rounded-lg font-semibold hover:bg-sky-100 transition">
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

    <!-- CONTENIDO PRINCIPAL -->
    <main class="md:ml-64 flex-1 p-6">
        
        <!-- Header del módulo -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Gestión de Licencias</h2>
                    <p class="text-sm text-gray-600 mt-1">Administre sus solicitudes de licencia</p>
                </div>
            </div>
        </div>
        
        <!-- TARJETA DE DÍAS DISPONIBLES -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 rounded-xl bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-calendar-check text-4xl text-indigo-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Días Disponibles</h3>
                        <p class="text-sm text-gray-600 mt-1">Límite mensual de licencias</p>
                    </div>
                </div>
                <div class="text-center md:text-right">
                    <div class="text-6xl font-bold text-indigo-600" id="diasDisponibles">-</div>
                    <p class="text-base text-gray-700 mt-2 font-medium">de 7 días este mes</p>
                    <p class="text-sm text-gray-500 mt-1">Días usados: <span id="diasUsados" class="font-semibold">-</span></p>
                </div>
            </div>
        </div>

        <!-- SECCIÓN DE LICENCIAS -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- HEADER -->
            <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-list text-indigo-600 mr-2"></i>
                        Mis Licencias
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Últimas 5 licencias solicitadas</p>
                </div>
                <button id="btnNuevaLicencia" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2.5 rounded-lg shadow-md hover:bg-indigo-700 transition font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Pedir Licencia</span>
                </button>
            </div>

            <!-- SPINNER DE CARGA -->
            <div id="loadingSpinner" class="flex justify-center items-center py-16">
                <div class="flex flex-col items-center gap-3">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                    <span class="text-sm text-gray-600">Cargando licencias...</span>
                </div>
            </div>

            <!-- TABLA RESPONSIVE -->
            <div id="tablaContainer" class="hidden">
                <!-- Vista de tabla para pantallas grandes -->
                <div class="overflow-x-auto block">
                    <table class="min-w-full divide-y divide-gray-200 hidden md:table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Solicitud</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Inicio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Fin</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Días</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaLicencias" class="bg-white divide-y divide-gray-200">
                            <!-- Filas generadas por JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Vista de cards para móviles -->
                <div id="cardsLicencias" class="md:hidden p-4 space-y-4">
                    <!-- Cards generados por JavaScript -->
                </div>
            </div>

            <!-- MENSAJE CUANDO NO HAY LICENCIAS -->
            <div id="noLicencias" class="hidden text-center py-16">
                <div class="flex flex-col items-center gap-3">
                    <i class="fas fa-inbox text-6xl text-gray-300"></i>
                    <p class="text-lg font-semibold text-gray-600">No tienes licencias registradas</p>
                    <p class="text-sm text-gray-500">Haz clic en "Pedir Licencia" para crear una nueva</p>
                </div>
            </div>
        </div>
    </main>

    <!-- PIE DE PÁGINA -->
    <footer class="md:ml-64 text-center py-4 text-xs text-gray-500 border-t border-gray-200 mt-auto">
        © 2025 Plataforma Universitaria INF342 — Sistema de Gestión de Licencias
    </footer>

    <!-- MODAL: CREAR/EDITAR LICENCIA -->
    <div id="modalLicencia" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="bg-indigo-600 text-white px-6 py-4 flex justify-between items-center sticky top-0">
                <h3 id="modalTitulo" class="text-xl font-semibold">
                    <i class="fas fa-file-medical mr-2"></i>
                    Nueva Solicitud de Licencia
                </h3>
                <button id="btnCerrarModal" class="text-white hover:text-gray-200 text-2xl transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Cuerpo del Modal -->
            <form id="formLicencia" class="p-6 space-y-6">
                <input type="hidden" id="licenciaNro" value="">
                <input type="hidden" id="modoEdicion" value="crear">

                <!-- Alerta de días disponibles -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-500 text-xl mr-3"></i>
                        <div>
                            <p class="text-sm font-semibold text-blue-800">Información de Días Disponibles</p>
                            <p class="text-xs text-blue-600 mt-1">
                                Tienes <span id="diasDisponiblesModal" class="font-bold">-</span> días disponibles este mes
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Campo: Descripción -->
                <div>
                    <label for="inputDescripcion" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-align-left mr-2 text-blue-600"></i>
                        Descripción de la Licencia *
                    </label>
                    <textarea 
                        id="inputDescripcion" 
                        name="descripcion" 
                        rows="3" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                        placeholder="Ej: Cita médica, asuntos personales, etc."
                        required
                        maxlength="500"
                    ></textarea>
                    <p class="text-xs text-gray-500 mt-1">Máximo 500 caracteres</p>
                </div>

                <!-- Campo: Fecha Inicio -->
                <div>
                    <label for="inputFechaInicio" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar-day mr-2 text-blue-600"></i>
                        Fecha de Inicio *
                    </label>
                    <input 
                        type="date" 
                        id="inputFechaInicio" 
                        name="fecha_inicio"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                    <p class="text-xs text-gray-500 mt-1">No puede ser anterior a hoy</p>
                </div>

                <!-- Campo: Días de Licencia (Selector) -->
                <div>
                    <label for="inputDias" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-clock mr-2 text-blue-600"></i>
                        Cantidad de Días *
                    </label>
                    <select 
                        id="inputDias" 
                        name="dias"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                        <option value="">Selecciona los días...</option>
                        <!-- Las opciones se llenarán dinámicamente según días disponibles -->
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Máximo según días disponibles este mes</p>
                </div>

                <!-- Campo: Fecha Fin (Calculada automáticamente) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar-check mr-2 text-green-600"></i>
                        Fecha de Fin (Calculada)
                    </label>
                    <div class="bg-gray-50 px-4 py-3 border border-gray-200 rounded-lg">
                        <p id="fechaFinCalculada" class="text-gray-700 font-semibold">
                            <i class="fas fa-calculator mr-2 text-gray-500"></i>
                            Selecciona la fecha de inicio y los días
                        </p>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Se calcula automáticamente según la fecha de inicio y los días seleccionados</p>
                </div>

                <!-- Botones del Modal -->
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" id="btnCancelarModal" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition">
                        <i class="fas fa-times mr-2"></i>
                        Cancelar
                    </button>
                    <button type="submit" id="btnGuardarLicencia" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition shadow-sm">
                        <i class="fas fa-save mr-2"></i>
                        Guardar Licencia
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: CONFIRMACIÓN DE ELIMINACIÓN -->
    <div id="modalConfirmacion" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-2xl max-w-md w-full">
            <div class="bg-red-600 text-white px-6 py-4 rounded-t-lg">
                <h3 class="text-xl font-semibold">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Confirmar Eliminación
                </h3>
            </div>
            <div class="p-6">
                <p class="text-gray-700 mb-6">
                    ¿Estás seguro de que deseas eliminar esta licencia? Esta acción no se puede deshacer.
                </p>
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 mb-6">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        Solo puedes eliminar licencias durante la primera hora después de crearlas.
                    </p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button id="btnCancelarEliminar" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition">
                        <i class="fas fa-times mr-2"></i>
                        Cancelar
                    </button>
                    <button id="btnConfirmarEliminar" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition">
                        <i class="fas fa-trash mr-2"></i>
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: MENSAJES DE ÉXITO/ERROR -->
    <div id="modalMensaje" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-2xl max-w-md w-full">
            <div id="mensajeHeader" class="px-6 py-4 rounded-t-lg flex items-center space-x-3">
                <i id="mensajeIcono" class="text-3xl"></i>
                <h3 id="mensajeTitulo" class="text-xl font-semibold"></h3>
            </div>
            <div class="p-6">
                <p id="mensajeTexto" class="text-gray-700 mb-6"></p>
                <div class="flex justify-end">
                    <button id="btnCerrarMensaje" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">
                        <i class="fas fa-check mr-2"></i>
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Incluir el archivo JavaScript -->
    <script src="/static/scripts/docente_licencia.js"></script>
</body>
</html>
