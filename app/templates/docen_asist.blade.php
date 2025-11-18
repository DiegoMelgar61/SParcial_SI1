<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Asistencia — Módulo Docencia | INF342</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">
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
          <p class="font-medium text-white">{{ $user['nomb_comp'] }}</p>
          <p class="text-xs text-gold-500 font-medium">{{ ucfirst($user['rol']) }}</p>
        </div>

        <div id="user-avatar"
             class="w-10 h-10 bg-gold-500 text-navy-900 flex items-center justify-center font-semibold shadow-sm cursor-pointer select-none">
          {{ strtoupper(substr($user['nomb_comp'], 0, 1)) }}
        </div>

        <a href="/"
           class="text-sm bg-gold-500 hover:bg-gold-600 text-navy-900 px-4 py-2 border-b-4 border-gold-600 font-medium transition shadow-sm">
          Inicio
        </a>
      </div>
    </div>
  </header>

  <!-- Sidebar -->
  <aside id="docencia-sidebar"
    class="fixed top-0 left-0 w-64 bg-navy-900 shadow-lg h-full z-30 border-r-4 border-gold-500 transform -translate-x-full md:translate-x-0 transition-transform duration-300">
    <div class="flex flex-col h-full overflow-y-auto scrollbar-thin scrollbar-thumb-gold-500 scrollbar-track-navy-800">

      <div class="p-4 border-b-2 border-gold-500">
        <h3 class="text-sm font-semibold text-white">Panel de Docencia</h3>
        <p class="text-xs text-gold-500 mt-1 font-medium">Gestión docente</p>
      </div>

      <nav class="flex-1 p-3">
        <ul class="space-y-1 text-sm">
          <li>
            <a href="/docen/mod-doc"
               class="flex items-center gap-2 px-3 py-2 text-white hover:bg-navy-800 hover:border-l-4 hover:border-gold-500 transition">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 3h7v7H3V3zm0 11h7v7H3v-7zm11-11h7v7h-7V3zm0 11h7v7h-7v-7z"/>
              </svg>
              <span>Panel Docencia</span>
            </a>
          </li>

          <li>
            <a href="/docencia/asistencia"
               class="flex items-center gap-2 px-3 py-2 text-navy-900 bg-gold-500 border-l-4 border-gold-600 font-semibold hover:bg-gold-600 transition">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              <span>Asistencia</span>
            </a>
          </li>

          <li>
            <a href="/docencia/licencia"
               class="flex items-center gap-2 px-3 py-2 text-white hover:bg-navy-800 hover:border-l-4 hover:border-gold-500 transition">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span>Licencia</span>
            </a>
          </li>
        </ul>
      </nav>

      <div class="p-3 border-t-2 border-gold-500 text-center text-[11px] text-gold-500">
        Módulo Docencia v1.0
      </div>
    </div>
  </aside>

  <!-- Overlay móvil -->
  <div id="sidebar-overlay"
       class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden hidden"></div>

  <!-- Contenido principal -->
  <main class="flex-1 md:ml-64 p-6 transition-all duration-300">
    <section class="bg-white shadow-sm border-l-4 border-navy-900 p-5 mb-8">
      <h3 class="text-base font-semibold text-navy-900 mb-4 border-b-2 border-gold-500 pb-2">Materias con clases próximas</h3>

      @if (count($prox_asist) > 0)
        @php
          $agrupadas = collect($prox_asist)->groupBy('nombre_materia');
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach ($agrupadas as $nombre => $clases)
            <div
              class="materia-card bg-white border-2 border-navy-900 hover:border-gold-500 p-5 cursor-pointer transition shadow-sm hover:shadow-md"
              data-materia="{{ $nombre }}"
              data-clases='@json($clases)'>
              <h4 class="text-navy-900 font-semibold text-lg mb-2">{{ $nombre }}</h4>
              <p class="text-sm text-gray-600">
                {{ $clases->first()['sigla_materia'] }} • Grupo {{ $clases->first()['grupo'] }}
              </p>
              <p class="text-xs text-gray-500 mt-1">
                Próxima clase: {{ ucfirst($clases->first()['dia']) }} {{ $clases->first()['hora_inicio'] }}
              </p>
            </div>
          @endforeach
        </div>
      @else
        <p class="text-sm text-gray-600">No tienes clases próximas programadas.</p>
      @endif
    </section>

    <section class="bg-white shadow-sm border-l-4 border-gold-500 p-5 mb-8">
      <h3 class="text-base font-semibold text-navy-900 mb-4 border-b-2 border-gold-500 pb-2">Historial de asistencias</h3>

      @if (count($asistencias) > 0)
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm text-left border-2 border-navy-900">
            <thead class="bg-navy-900 text-white font-semibold">
              <tr>
                <th class="px-4 py-2 border-b-2 border-gold-500">Fecha</th>
                <th class="px-4 py-2 border-b-2 border-gold-500">Materia</th>
                <th class="px-4 py-2 border-b-2 border-gold-500">Grupo</th>
                <th class="px-4 py-2 border-b-2 border-gold-500">Día</th>
                <th class="px-4 py-2 border-b-2 border-gold-500">Horario</th>
                <th class="px-4 py-2 border-b-2 border-gold-500">Estado</th>
              </tr>
            </thead>
            <tbody class="text-gray-700">
              @foreach ($asistencias as $a)
                <tr class="hover:bg-gray-50 transition">
                  <td class="px-4 py-2 border-b">{{ $a['fecha'] ?? '—' }}</td>
                  <td class="px-4 py-2 border-b">{{ $a['nombre_materia'] }}</td>
                  <td class="px-4 py-2 border-b">{{ $a['grupo'] }}</td>
                  <td class="px-4 py-2 border-b">{{ ucfirst($a['dia']) }}</td>
                  <td class="px-4 py-2 border-b">
                    {{ $a['hora_inicio'] }} - {{ $a['hora_final'] }}
                  </td>
                  <td class="px-4 py-2 border-b font-medium
                            @if($a['estado'] === 'Presente') text-green-600
                            @elseif($a['estado'] === 'Retraso') text-amber-600
                            @else text-red-600 @endif">
                    {{ $a['estado'] }}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <p class="text-sm text-gray-600">Aún no registraste asistencias.</p>
      @endif
    </section>


  </main>

  <!-- MODAL PRINCIPAL -->
  <div id="modal-clases" class="fixed inset-0 hidden items-center justify-center bg-black/40 backdrop-blur-sm z-50">
    <div class="bg-white shadow-2xl w-full max-w-lg relative border-2 border-navy-900">

      <!-- Encabezado -->
      <div class="flex justify-between items-center px-5 py-3 bg-navy-900 border-b-4 border-gold-500">
        <h4 id="modal-titulo" class="text-lg font-semibold text-white tracking-wide"></h4>
        <button id="cerrar-modal"
                class="text-gold-500 hover:text-gold-600 focus:outline-none text-xl leading-none">
          ×
        </button>
      </div>

      <!-- Contenido dinámico -->
      <div id="modal-contenido" class="p-6 space-y-3 text-gray-700 text-sm"></div>

      <!-- Formulario -->
      <form id="form-asistencia" class="hidden border-t-2 border-gold-500 p-6 space-y-5 bg-gray-50">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
            <input type="date" name="fecha" value="{{ date('Y-m-d') }}" readonly
                  class="w-full border-2 border-gray-300 bg-gray-100 text-gray-700 cursor-not-allowed px-2 py-1.5 text-sm">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
            <select name="estado_asist"
                    class="w-full border-2 border-navy-900 focus:ring-1 focus:ring-gold-500 px-2 py-1.5 text-sm">
              <option value="Presente">Presente</option>
              <option value="Ausente">Ausente</option>
              <option value="Retraso">Retraso</option>
            </select>
          </div>
        </div>

        <input type="hidden" name="metodo_r" value="Formulario">

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Observación</label>
          <textarea name="observacion" rows="3"
                    placeholder="Comentario adicional (opcional)"
                    class="w-full border-2 border-navy-900 focus:ring-1 focus:ring-gold-500 px-2 py-1.5 text-sm"></textarea>
        </div>

        <div class="flex justify-end pt-3 border-t-2 border-gray-200">
          <button type="submit"
                  class="px-6 py-2 bg-navy-900 hover:bg-navy-800 text-white text-sm font-medium border-b-4 border-navy-800 shadow-sm transition">
            Registrar asistencia
          </button>
        </div>
      </form>
    </div>
  </div>


  <!-- MODAL OPCIONES -->
  <div id="modal-opciones" class="fixed inset-0 hidden items-center justify-center bg-black/40 backdrop-blur-sm z-60">
    <div class="bg-white shadow-2xl w-full max-w-sm relative border-2 border-navy-900">

      <!-- Encabezado -->
      <div class="flex justify-between items-center px-5 py-3 bg-navy-900 border-b-4 border-gold-500">
        <h4 class="text-base font-semibold text-white tracking-wide">Seleccionar método de registro</h4>
        <button id="cerrar-opciones" class="text-gold-500 hover:text-gold-600 focus:outline-none text-xl leading-none">
          ×
        </button>
      </div>

      <!-- Botones -->
      <div class="p-6 space-y-4">
        <button id="btn-formulario"
                class="w-full py-2.5 bg-navy-900 hover:bg-navy-800 text-white font-medium text-sm border-b-4 border-navy-800 shadow-sm transition">
          Registrar mediante formulario
        </button>
        <button id="btn-qr"
                class="w-full py-2.5 bg-gold-500 hover:bg-gold-600 text-navy-900 font-medium text-sm border-b-4 border-gold-600 shadow-sm transition">
          Generar código QR
        </button>
      </div>
    </div>
  </div>



  <footer class="text-center py-4 text-xs text-gold-500 border-t-4 border-gold-500 bg-navy-900 mt-10 md:ml-64">
    © {{ date('Y') }} Grupo 31 — UAGRM | INF342 - SA
  </footer>

  <!-- Librería para generar códigos QR -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <script src="{{asset('static/scripts/docen_asist.js') }}"></script>
</body>
</html>