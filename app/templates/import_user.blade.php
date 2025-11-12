<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Importar usuarios — Plataforma Universitaria INF342</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col font-sans antialiased">

  <!-- Barra superior -->
  <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-3">
      <h1 class="text-lg md:text-xl font-semibold text-gray-700 tracking-wide">
        Módulo de Importación de Usuarios
      </h1>
      <a href="/" class="text-sm bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 px-3 py-1.5 rounded-md font-medium transition">
        ← Volver al panel
      </a>
    </div>
  </header>

  <!-- Contenido principal -->
  <main class="flex-1 max-w-4xl mx-auto w-full py-10 px-6">

    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200">
      <h2 class="text-2xl font-semibold text-gray-800 mb-2">Registro masivo de usuarios</h2>
      <p class="text-sm text-gray-600 mb-6 leading-relaxed">
        Cargue un archivo en formato <span class="font-medium text-indigo-700">.xlsx</span> o <span class="font-medium text-indigo-700">.csv</span> 
        para registrar múltiples usuarios en el sistema. 
        Asegúrese de que las columnas coincidan con el formato establecido en la plantilla.
      </p>

      <!-- Bloque de descarga de plantilla -->
      <div class="mb-8 bg-indigo-50 border border-indigo-200 rounded-lg p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h3 class="text-base font-semibold text-indigo-700">Descarga la plantilla base</h3>
          <p class="text-sm text-gray-600">
            Rellénala con los datos de los usuarios y luego súbela utilizando el formulario inferior.
          </p>
        </div>
        <a href="{{ secure_asset('static/files/plantilla_usuarios.xlsx') }}" download
           class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0l-3-3m3 3l3-3M4 16h16" />
          </svg>
          Descargar plantilla
        </a>
      </div>

      <!-- Área de carga de archivo -->
      <form id="form-import" enctype="multipart/form-data"
            class="relative border-2 border-dashed border-gray-300 hover:border-indigo-400 rounded-xl bg-gray-50 p-10 text-center transition cursor-pointer">
        <input type="file" name="archivo" id="archivo" accept=".xlsx,.csv"
               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
        <div class="flex flex-col items-center space-y-3 pointer-events-none">
          <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
          </svg>
          <p class="text-sm text-gray-600">Arrastre un archivo aquí o haga clic para seleccionarlo</p>
          <p class="text-xs text-gray-400">Formatos permitidos: .xlsx, .csv (máx. 5MB)</p>
        </div>
      </form>

      <!-- Nombre del archivo cargado -->
      <div id="file-info" class="hidden mt-5 text-sm text-gray-700 bg-gray-50 border border-gray-200 rounded-lg px-4 py-2">
        <p><span class="font-medium">Archivo seleccionado:</span> <span id="file-name" class="text-indigo-700"></span></p>
      </div>

      <!-- Botones -->
      <div class="mt-8 flex justify-end gap-3">
        <button id="btn-cancelar" type="button"
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md font-medium transition">
          Cancelar
        </button>
        <button id="btn-importar" type="button"
                class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-medium transition">
          Importar usuarios
        </button>
      </div>
    </div>
  </main>

  <!-- Loader -->
  <div id="loader" class="hidden fixed inset-0 flex items-center justify-center bg-white/70 backdrop-blur-sm z-50">
    <div class="flex flex-col items-center">
      <div class="h-6 w-6 border-2 border-gray-400 border-t-transparent rounded-full animate-spin mb-2"></div>
      <p class="text-xs text-gray-600">Procesando archivo...</p>
    </div>
  </div>

  <!-- Modal -->
  <div id="modal" class="hidden fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white rounded-lg border border-gray-200 shadow-lg w-full max-w-sm p-6 text-center">
      <h3 id="modal-title" class="text-lg font-semibold text-gray-800 mb-2">Mensaje</h3>
      <p id="modal-message" class="text-sm text-gray-600 mb-5">...</p>
      <button id="modal-close"
              class="px-5 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition font-medium">
        Aceptar
      </button>
    </div>
  </div>

  <script src="{{ secure_asset('static/scripts/import_user.js') }}"></script>
</body>
</html>
