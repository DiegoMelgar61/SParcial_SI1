<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Gestión de Licencias — Plataforma Universitaria INF342</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['Inter', 'system-ui', 'sans-serif']
            },
            colors: {
              navy: {
                900: '#0f2942',
                800: '#1e3a5f'
              },
              gold: {
                500: '#c9a961',
                600: '#b8974f'
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
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
        <h1 class="text-lg md:text-xl font-semibold text-white tracking-wide">Módulo Docencia</h1>
      </div>

      <div class="flex items-center gap-4">
        <div class="hidden sm:block text-right">
          <p class="font-medium text-white"><?php echo e($user['nomb_comp']); ?></p>
          <p class="text-xs text-gold-500 font-medium"><?php echo e(ucfirst($user['rol'])); ?></p>
        </div>

        <div id="user-avatar"
             class="w-10 h-10 bg-gold-500 text-navy-900 flex items-center justify-center font-semibold shadow-sm cursor-pointer select-none">
          <?php echo e(strtoupper(substr($user['nomb_comp'], 0, 1))); ?>

        </div>

        <a href="/"
           class="text-sm bg-gold-500 hover:bg-gold-600 text-navy-900 px-4 py-2 border-b-4 border-gold-600 font-medium transition shadow-sm">
          Inicio
        </a>
      </div>
    </div>
  </header>

    <!-- Panel lateral de usuario -->
    <aside id="user-aside"
        class="hidden fixed top-16 right-4 w-64 bg-white shadow-2xl  border border-gray-200 z-50 transition-all duration-300 opacity-0 scale-95 origin-top-right">
        <div class="p-5 text-sm text-white">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10  bg-navy-900 text-white flex items-center justify-center font-semibold shadow-sm">
                    <?php echo e(strtoupper(substr($user['nomb_comp'], 0, 1))); ?>

                </div>
                <div>
                    <p class="font-semibold text-gray-800 leading-tight"><?php echo e($user['nomb_comp']); ?></p>
                    <span class="text-xs px-2 py-0.5  bg-gold-500 text-navy-900 font-medium">
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
                    class="text-gold-500 text-sm font-medium hover:underline hover:text-navy-900 transition">
                    Ver perfil completo →
                </a>
            </div>
        </div>
    </aside>

    <!-- Sidebar -->
    <aside id="docencia-sidebar"
    class="fixed top-0 left-0 w-64 bg-navy-900 shadow-lg h-full z-30 border-r-4 border-gold-500 transform -translate-x-full md:translate-x-0 transition-transform duration-300">
        <div class="flex flex-col h-full overflow-y-auto scrollbar-thin scrollbar-thumb-gold-500 scrollbar-track-navy-800">
            
            <!-- Encabezado -->
            <div class="p-4 border-b-2 border-gold-500">
            <h3 class="text-sm font-semibold text-white">Panel de Docencia</h3>
            <p class="text-xs text-gold-500 mt-1 font-medium">Gestión docente</p>
            </div>

            <!-- Navegación -->
            <nav class="flex-1 p-3">
            <ul class="space-y-1 text-sm">
                
                <!-- Panel principal -->
                <li>
                <a href="/docen/mod-doc"
                    class="flex items-center gap-2 px-3 py-2 text-white hover:bg-gold-500 hover:text-navy-900  transition">
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
                    class="flex items-center gap-2 px-3 py-2 text-white hover:bg-gold-500 hover:text-navy-900  transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Asistencia</span>
                </a>
                </li>

                <!-- Licencia -->
                <li>
                <a href="/docencia/licencia"
                    class="flex items-center gap-2 px-3 py-2 text-navy-900 bg-gold-500  font-semibold hover:bg-sky-100 transition">
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
            <div class="p-3 border-t-2 border-gold-500 text-center text-[11px] text-gold-500">
            Módulo Docencia v1.0
            </div>
        </div>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="md:ml-64 flex-1 p-6">

        <!-- Header del módulo -->
        <div class="mb-8">
            <div class="bg-white border-l-4 border-gold-500 shadow-md px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-black text-navy-900 uppercase tracking-tight">Gestión de Licencias</h2>
                    <p class="text-sm text-slate-600 font-semibold uppercase tracking-wide">Administre sus solicitudes de licencia docente</p>
                </div>
                <button id="btnNuevaLicencia" class="flex items-center justify-center gap-2 bg-navy-900 hover:bg-navy-800 text-white px-6 py-3 border-b-4 border-gold-500 font-bold uppercase tracking-wide transition shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Nueva Licencia</span>
                </button>
            </div>
        </div>

        <!-- GRID DE ESTADÍSTICAS (REORGANIZADO) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

            <!-- Widget: Días Disponibles (DESTACADO) -->
            <div class="bg-white border-4 border-gold-500 shadow-lg hover:shadow-2xl transition-all duration-300">
                <div class="bg-gold-500 p-5 text-center border-b-4 border-navy-900">
                    <div class="w-16 h-16 mx-auto bg-navy-900 flex items-center justify-center text-4xl mb-3 border-4 border-white">
                        📅
                    </div>
                    <span class="inline-block bg-navy-900 text-gold-500 px-3 py-1 text-xs font-black uppercase tracking-wider">Principal</span>
                </div>
                <div class="p-6 text-center">
                    <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wide mb-3">Días Disponibles</h3>
                    <div class="text-6xl font-black text-gold-500 mb-3" id="diasDisponibles">-</div>
                    <p class="text-sm text-navy-900 font-bold">de 7 días este mes</p>
                </div>
            </div>

            <!-- Widget: Días Usados -->
            <div class="bg-white border-4 border-navy-900 shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="bg-navy-900 p-5 text-center border-b-4 border-gold-500">
                    <div class="w-16 h-16 mx-auto bg-gold-500 flex items-center justify-center text-4xl mb-3 border-4 border-white">
                        ⏱️
                    </div>
                    <span class="inline-block bg-gold-500 text-navy-900 px-3 py-1 text-xs font-black uppercase tracking-wider">Consumo</span>
                </div>
                <div class="p-6 text-center">
                    <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wide mb-3">Días Usados</h3>
                    <div class="text-6xl font-black text-navy-900 mb-3" id="diasUsados">-</div>
                    <p class="text-sm text-slate-600 font-bold">días en este mes</p>
                </div>
            </div>

            <!-- Widget: Total Licencias -->
            <div class="bg-white border-4 border-slate-300 shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="bg-slate-100 p-5 text-center border-b-4 border-gold-500">
                    <div class="w-16 h-16 mx-auto bg-navy-900 flex items-center justify-center text-4xl mb-3 border-2 border-gold-500">
                        📋
                    </div>
                    <span class="inline-block bg-navy-900 text-gold-500 px-3 py-1 text-xs font-black uppercase tracking-wider">Historial</span>
                </div>
                <div class="p-6 text-center">
                    <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wide mb-3">Total Licencias</h3>
                    <div class="text-6xl font-black text-navy-900 mb-3" id="totalLicencias">-</div>
                    <p class="text-sm text-slate-600 font-bold">registradas</p>
                </div>
            </div>
        </div>

        <!-- SECCIÓN DE LICENCIAS CON DISEÑO DE PAQUETES -->
        <div class="bg-white shadow-md border-2 border-navy-900">
            <!-- HEADER -->
            <div class="bg-navy-900 px-6 py-4 border-b-4 border-gold-500">
                <h3 class="text-lg font-black text-white uppercase tracking-wide">
                    📄 Mis Solicitudes de Licencia
                </h3>
                <p class="text-sm text-gold-500 mt-1 font-semibold uppercase tracking-wide">Últimas licencias solicitadas</p>
            </div>

            <!-- SPINNER DE CARGA -->
            <div id="loadingSpinner" class="flex justify-center items-center py-16">
                <div class="flex flex-col items-center gap-3">
                    <div class="animate-spin h-12 w-12 border-b-4 border-navy-900"></div>
                    <span class="text-sm text-gray-600 font-semibold">Cargando licencias...</span>
                </div>
            </div>

            <!-- GRID DE CARDS (Reemplaza la tabla) -->
            <div id="gridLicencias" class="hidden p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Cards generados por JavaScript -->
            </div>

            <!-- MENSAJE CUANDO NO HAY LICENCIAS -->
            <div id="noLicencias" class="hidden text-center py-16">
                <div class="flex flex-col items-center gap-3">
                    <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-lg font-bold text-navy-900">No tienes licencias registradas</p>
                    <p class="text-sm text-gray-600">Haz clic en "Nueva Licencia" para crear una solicitud</p>
                </div>
            </div>
        </div>
    </main>

    <!-- PIE DE PÁGINA -->
    <footer class="md:ml-64 text-center py-4 text-xs text-gold-500 border-t-4 border-gold-500 bg-navy-900 mt-auto">
        © 2025 Plataforma Universitaria INF342 — Sistema de Gestión de Licencias
    </footer>

    <!-- MODAL: CREAR/EDITAR LICENCIA -->
    <div id="modalLicencia" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white shadow-2xl border-2 border-navy-900 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="bg-navy-900 border-b-4 border-gold-500 text-white px-6 py-4 flex justify-between items-center sticky top-0">
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
                    <label for="inputDescripcion" class="block text-sm font-semibold text-white mb-2">
                        <i class="fas fa-align-left mr-2 text-blue-600"></i>
                        Descripción de la Licencia *
                    </label>
                    <textarea 
                        id="inputDescripcion" 
                        name="descripcion" 
                        rows="3" 
                        class="w-full px-4 py-2 border border-gray-300  focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                        placeholder="Ej: Cita médica, asuntos personales, etc."
                        required
                        maxlength="500"
                    ></textarea>
                    <p class="text-xs text-gray-500 mt-1">Máximo 500 caracteres</p>
                </div>

                <!-- Campo: Fecha Inicio -->
                <div>
                    <label for="inputFechaInicio" class="block text-sm font-semibold text-white mb-2">
                        <i class="fas fa-calendar-day mr-2 text-blue-600"></i>
                        Fecha de Inicio *
                    </label>
                    <input 
                        type="date" 
                        id="inputFechaInicio" 
                        name="fecha_inicio"
                        class="w-full px-4 py-2 border border-gray-300  focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                    <p class="text-xs text-gray-500 mt-1">No puede ser anterior a hoy</p>
                </div>

                <!-- Campo: Días de Licencia (Selector) -->
                <div>
                    <label for="inputDias" class="block text-sm font-semibold text-white mb-2">
                        <i class="fas fa-clock mr-2 text-blue-600"></i>
                        Cantidad de Días *
                    </label>
                    <select 
                        id="inputDias" 
                        name="dias"
                        class="w-full px-4 py-2 border border-gray-300  focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                        <option value="">Selecciona los días...</option>
                        <!-- Las opciones se llenarán dinámicamente según días disponibles -->
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Máximo según días disponibles este mes</p>
                </div>

                <!-- Campo: Fecha Fin (Calculada automáticamente) -->
                <div>
                    <label class="block text-sm font-semibold text-white mb-2">
                        <i class="fas fa-calendar-check mr-2 text-green-600"></i>
                        Fecha de Fin (Calculada)
                    </label>
                    <div class="bg-gray-50 px-4 py-3 border border-gray-200 ">
                        <p id="fechaFinCalculada" class="text-white font-semibold">
                            <i class="fas fa-calculator mr-2 text-gray-500"></i>
                            Selecciona la fecha de inicio y los días
                        </p>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Se calcula automáticamente según la fecha de inicio y los días seleccionados</p>
                </div>

                <!-- Botones del Modal -->
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" id="btnCancelarModal" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-white  font-medium transition">
                        <i class="fas fa-times mr-2"></i>
                        Cancelar
                    </button>
                    <button type="submit" id="btnGuardarLicencia" class="px-6 py-2.5 bg-navy-900 hover:bg-navy-800 text-white  font-medium transition shadow-sm">
                        <i class="fas fa-save mr-2"></i>
                        Guardar Licencia
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: CONFIRMACIÓN DE ELIMINACIÓN -->
    <div id="modalConfirmacion" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white  shadow-2xl max-w-md w-full">
            <div class="bg-red-600 text-white px-6 py-4 rounded-t-lg">
                <h3 class="text-xl font-semibold">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Confirmar Eliminación
                </h3>
            </div>
            <div class="p-6">
                <p class="text-white mb-6">
                    ¿Estás seguro de que deseas eliminar esta licencia? Esta acción no se puede deshacer.
                </p>
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 mb-6">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        Solo puedes eliminar licencias durante la primera hora después de crearlas.
                    </p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button id="btnCancelarEliminar" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-white  font-medium transition">
                        <i class="fas fa-times mr-2"></i>
                        Cancelar
                    </button>
                    <button id="btnConfirmarEliminar" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white  font-medium transition">
                        <i class="fas fa-trash mr-2"></i>
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: MENSAJES DE ÉXITO/ERROR -->
    <div id="modalMensaje" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white  shadow-2xl max-w-md w-full">
            <div id="mensajeHeader" class="px-6 py-4 rounded-t-lg flex items-center space-x-3">
                <i id="mensajeIcono" class="text-3xl"></i>
                <h3 id="mensajeTitulo" class="text-xl font-semibold"></h3>
            </div>
            <div class="p-6">
                <p id="mensajeTexto" class="text-white mb-6"></p>
                <div class="flex justify-end">
                    <button id="btnCerrarMensaje" class="px-6 py-2.5 bg-navy-900 hover:bg-navy-800 text-white  font-medium transition">
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
<?php /**PATH C:\Users\diego\OneDrive\Escritorio\exa2_inf342\app\templates/docente_licencia.blade.php ENDPATH**/ ?>