<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio — Plataforma Universitaria INF342</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col font-sans antialiased">

  <!-- Barra superior -->
  <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-3">
      <h1 class="text-lg md:text-xl font-semibold text-gray-700 tracking-wide">
        Plataforma Universitaria
      </h1>

      <div class="flex items-center gap-4">
        <div class="hidden sm:block text-right">
          <p class="font-medium text-gray-800">{{ $user['nomb_comp'] }}</p>
          <p class="text-xs text-gray-500">{{ ucfirst($user['rol']) }}</p>
        </div>

        <!-- Avatar con hover -->
        <div id="user-avatar"
             class="w-10 h-10 rounded-full bg-indigo-500 text-white flex items-center justify-center font-semibold shadow-sm cursor-pointer select-none">
          {{ strtoupper(substr($user['nomb_comp'],0,1)) }}
        </div>

        <!-- Logout -->
        <form action="/logout" method="POST">
          @csrf
          <button type="submit"
                  class="ml-2 text-sm bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 px-3 py-1.5 rounded-md font-medium transition">
            Cerrar sesión
          </button>
        </form>
      </div>
    </div>
  </header>

  <!-- Panel lateral de usuario -->
  <aside id="user-aside"
         class="hidden fixed top-16 right-4 w-64 bg-white shadow-lg rounded-xl border border-gray-200 z-50 transition-all duration-300 opacity-0 scale-95 origin-top-right">
    <div class="p-5 text-sm text-gray-700">
      <div class="flex items-center gap-3 mb-3">
        <div class="w-10 h-10 rounded-full bg-indigo-500 text-white flex items-center justify-center font-semibold shadow-sm">
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

  <!-- Contenido principal -->
  <main class="flex-1 max-w-7xl mx-auto w-full py-10 px-6">

    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row justify-between md:items-center mb-8">
      <div>
        <h2 class="text-2xl font-semibold text-gray-800 mb-1">Panel principal</h2>
        <p class="text-gray-500 text-sm">Gestión académica y control docente</p>
      </div>
      <div id="clock" class="text-sm text-gray-600 font-medium mt-3 md:mt-0"></div>
    </div>

    <!-- Tarjetas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

      <!-- Datos personales -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition">
        <h3 class="text-base font-semibold text-indigo-700 mb-3">Datos personales</h3>
        <ul class="text-sm text-gray-600 space-y-1">
          <li><span class="font-medium">CI:</span> {{ $user['ci'] }}</li>
          <li><span class="font-medium">Correo:</span> {{ $user['correo'] ?? '—' }}</li>
          <li><span class="font-medium">Teléfono:</span> {{ $user['tel'] ?? '—' }}</li>
          <li><span class="font-medium">Rol:</span> {{ ucfirst($user['rol']) }}</li>
        </ul>
      </div>

      @php $rol = strtolower($user['rol']); @endphp

      <!-- ADMIN -->
      @if ($rol === 'admin')
        <div id="import-users-card" 
             class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-indigo-200 transition cursor-pointer">
          <h3 class="text-base font-semibold text-gray-800 mb-2">Modulo de Administracion</h3>
          <p class="text-sm text-gray-600 mb-4">
            Accede al modulo administrativo para gestionar usuarios,docentes, materias, etc.
            <span class="font-medium">.xlsx</span> o <span class="font-medium">.csv</span>.
          </p>
          <button id="btn-mod-adm"
                  class="w-full text-center py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition">
            Ir al Modulo de Administracion
          </button>
        </div>

        <div id="import-users-card" 
             class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-indigo-200 transition cursor-pointer">
          <h3 class="text-base font-semibold text-gray-800 mb-2">Registro masivo de usuarios</h3>
          <p class="text-sm text-gray-600 mb-4">
            Permite cargar nuevos usuarios al sistema mediante archivos 
            <span class="font-medium">.xlsx</span> o <span class="font-medium">.csv</span>.
          </p>
          <button id="btn-import-users"
                  class="w-full text-center py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition">
            Ir al Modulo de Importación
          </button>
        </div>
      @elseif ($rol === 'autoridad')
        <div class="bg-gradient-to-br from-purple-50 to-gray-50 p-6 rounded-xl border border-purple-100 shadow-sm hover:shadow-md transition">
          <h3 class="text-base font-semibold text-purple-700 mb-3">Panel de autoridad</h3>
          <ul class="text-sm text-gray-600 space-y-1 leading-relaxed">
            <li>Estadísticas de asistencia y desempeño</li>
            <li>Reportes comparativos por docente</li>
            <li>Monitoreo de horarios y aulas</li>
          </ul>
        </div>
      @elseif ($rol === 'docente')
        <div class="bg-gradient-to-br from-sky-50 to-gray-50 p-6 rounded-xl border border-sky-100 shadow-sm hover:shadow-md transition">
          <h3 class="text-base font-semibold text-sky-700 mb-3">Panel docente</h3>
          <ul class="text-sm text-gray-600 space-y-1 leading-relaxed">
            <li>Materias y grupos asignados</li>
            <li>Registro de asistencia y licencias</li>
            <li>Consulta de horarios académicos</li>
          </ul>
        </div>
      @elseif ($rol === 'administrativo')
        <div class="bg-gradient-to-br from-gray-100 to-indigo-50 p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition">
          <h3 class="text-base font-semibold text-gray-700 mb-3">Panel administrativo</h3>
          <ul class="text-sm text-gray-600 space-y-1 leading-relaxed">
            <li>Verificación de registros docentes</li>
            <li>Control de usuarios y documentación</li>
            <li>Gestión de aulas y materiales</li>
          </ul>
        </div>
      @endif

      <!-- Avisos -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition">
        <h3 class="text-base font-semibold text-indigo-700 mb-3">Avisos institucionales</h3>
        <ul id="news-list" class="text-sm text-gray-600 space-y-2 leading-relaxed">
          <li>Nueva gestión académica activa: <span class="font-medium text-indigo-700">2025-I</span></li>
          <li>Se habilitó el registro de asistencia docente.</li>
          <li>Actualización en reportes de aula y horario.</li>
        </ul>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="text-center py-4 text-xs text-gray-500 border-t border-gray-200 bg-white mt-10">
    © {{ date('Y') }} Grupo 32 — UAGRM | INF342 - SA
  </footer>

  <script src="{{ secure_asset('static/scripts/index.js') }}"></script>
</body>
</html>
