<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Inicio de Sesión — Sistema FICCT</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center font-sans antialiased">

  <!-- Contenedor principal -->
  <div class="w-full max-w-md bg-white shadow-md rounded-2xl p-10 border border-gray-200 relative">

    <!-- Loader -->
    <div id="loader" class="hidden absolute inset-0 flex flex-col items-center justify-center bg-white/70 backdrop-blur-sm rounded-2xl z-10">
      <div class="h-6 w-6 border-2 border-gray-400 border-t-transparent rounded-full animate-spin mb-2"></div>
      <p class="text-xs text-gray-600">Verificando credenciales...</p>
    </div>

    <!-- Logo y encabezado -->
    <div class="text-center mb-8">
      <div class="flex justify-center mb-3">
        <img src="{{ secure_asset('static/images/logo2.png') }}" alt="FICCT Logo"
             class="w-16 h-16 rounded-full shadow-sm border border-gray-200">
      </div>
      <h1 class="text-xl font-semibold text-gray-800">Sistema de Gestión Facultativa</h1>
      <p class="text-sm text-gray-500 mt-1 leading-tight">
        Accede al Portal Web para la Gestion Facultativa - Universitaria.
      </p>
    </div>

    <!-- Alerta de error -->
    <div id="alert-error" class="hidden bg-red-50 text-red-700 px-4 py-2 rounded-md mb-5 text-sm border border-red-200 text-center">
      Credenciales incorrectas. Intente nuevamente.
    </div>

    <!-- Formulario -->
    <form id="loginForm" class="space-y-6">
      <div>
        <label for="codigo" class="block text-sm font-medium text-gray-700 mb-1">Código de Usuario</label>
        <input type="text" id="codigo" name="codigo" required
               placeholder="Ej: 202112345"
               class="w-full px-4 py-2 border border-gray-300 rounded-md text-gray-700 placeholder-gray-400 
                      focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition">
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
        <input type="password" id="password" name="password" required
               placeholder="••••••••"
               class="w-full px-4 py-2 border border-gray-300 rounded-md text-gray-700 placeholder-gray-400 
                      focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition">
      </div>

      <button type="submit"
              class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md 
                     shadow-sm transition duration-200 focus:ring-4 focus:ring-indigo-300">
        Iniciar Sesión
      </button>
    </form>

    <!-- Footer -->
    <div class="mt-8 text-center text-xs text-gray-500">
      <p>© {{ date('Y') }} Facultad de Ingeniería — UAGRM</p>
      <p class="mt-1">
        <a href="#" id="reset-password" class="text-indigo-600 hover:underline">Restablecer contraseña</a>
      </p>
    </div>
  </div>

  <!-- Modal elegante -->
  <div id="modal-result" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white rounded-lg border border-gray-200 shadow-lg w-full max-w-sm p-6 text-center">
      <h3 id="modal-title" class="text-lg font-semibold text-gray-800 mb-2">Mensaje</h3>
      <p id="modal-message" class="text-sm text-gray-600 mb-5">Texto del sistema.</p>
      <button id="modal-close"
              class="px-5 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition font-medium">
        Aceptar
      </button>
    </div>
  </div>

  <script src="{{ secure_asset('static/scripts/login.js') }}"></script>
</body>
</html>
