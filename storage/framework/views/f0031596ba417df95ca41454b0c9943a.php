<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Asistencia — Módulo Docencia | INF342</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
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
          <p class="font-medium text-gray-800"><?php echo e($user['nomb_comp']); ?></p>
          <p class="text-xs text-sky-600 font-medium"><?php echo e(ucfirst($user['rol'])); ?></p>
        </div>

        <div id="user-avatar"
             class="w-10 h-10 rounded-full bg-sky-600 text-white flex items-center justify-center font-semibold shadow-sm cursor-pointer select-none">
          <?php echo e(strtoupper(substr($user['nomb_comp'], 0, 1))); ?>

        </div>

        <a href="/"
           class="text-sm bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-md font-medium transition shadow-sm">
          Inicio
        </a>
      </div>
    </div>
  </header>

  <!-- Sidebar -->
  <aside id="docencia-sidebar"
    class="fixed top-0 left-0 w-64 bg-white shadow-lg h-full z-30 border-r border-gray-200 transform -translate-x-full md:translate-x-0 transition-transform duration-300">
    <div class="flex flex-col h-full overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
      
      <div class="p-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-800">Panel de Docencia</h3>
        <p class="text-xs text-sky-600 mt-1 font-medium">Gestión docente</p>
      </div>

      <nav class="flex-1 p-3">
        <ul class="space-y-1 text-sm">
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

          <li>
            <a href="/docencia/asistencia"
               class="flex items-center gap-2 px-3 py-2 text-sky-700 bg-sky-50 rounded-lg font-semibold hover:bg-sky-100 transition">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
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
    <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-8">
      <h3 class="text-base font-semibold text-gray-800 mb-4">Materias con clases próximas</h3>

      <?php if(count($prox_asist) > 0): ?>
        <?php
          $agrupadas = collect($prox_asist)->groupBy('nombre_materia');
        ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php $__currentLoopData = $agrupadas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nombre => $clases): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div 
              class="materia-card bg-sky-50 border border-sky-200 hover:border-sky-400 rounded-xl p-5 cursor-pointer transition shadow-sm hover:shadow-md"
              data-materia="<?php echo e($nombre); ?>"
              data-clases='<?php echo json_encode($clases, 15, 512) ?>'>
              <h4 class="text-sky-700 font-semibold text-lg mb-2"><?php echo e($nombre); ?></h4>
              <p class="text-sm text-gray-600">
                <?php echo e($clases->first()['sigla_materia']); ?> • Grupo <?php echo e($clases->first()['grupo']); ?>

              </p>
              <p class="text-xs text-gray-500 mt-1">
                Próxima clase: <?php echo e(ucfirst($clases->first()['dia'])); ?> <?php echo e($clases->first()['hora_inicio']); ?>

              </p>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      <?php else: ?>
        <p class="text-sm text-gray-600">No tienes clases próximas programadas.</p>
      <?php endif; ?>
    </section>
  </main>

  <!-- Modal con formulario de asistencia -->
    <div id="modal-clases" class="fixed inset-0 hidden items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6 relative">
        <button id="cerrar-modal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-lg">✕</button>
        <h4 id="modal-titulo" class="text-lg font-semibold text-sky-700 mb-4"></h4>
        <div id="modal-contenido" class="space-y-3"></div>

        <!-- Formulario de registro -->
        <form id="form-asistencia" class="hidden mt-6 border-t pt-5 space-y-5">
        <!-- Fecha -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha actual</label>
            <input type="date" name="fecha" 
                value="<?php echo e(date('Y-m-d')); ?>" 
                readonly
                class="w-full border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-700 cursor-not-allowed text-sm">
        </div>

        <!-- Estado -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
            <select name="estado" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
            <option value="Presente">Presente</option>
            <option value="Ausente">Ausente</option>
            <option value="Retraso">Retraso</option>
            </select>
        </div>

        <!-- Método -->
        <input type="hidden" name="metodo_r" value="Formulario">

        <!-- Observación -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Observación</label>
            <textarea name="observacion" rows="3"
                    placeholder="Ej. Llegó 10 min tarde o registró asistencia normal"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm"></textarea>
        </div>

        <!-- Botón principal -->
        <div class="flex justify-center mt-6">
            <button type="submit"
                    id="btn-generar-qr"
                    class="w-full sm:w-auto px-8 py-3 bg-green-600 hover:bg-green-700 text-white text-base font-semibold rounded-lg shadow-sm transition">
            Generar QR
            </button>
        </div>
        </form>
    </div>
    

</div>

  <footer class="text-center py-4 text-xs text-gray-500 border-t border-gray-200 bg-white mt-10 md:ml-64">
    © <?php echo e(date('Y')); ?> Grupo 32 — UAGRM | INF342 - SA
  </footer>

  <script src="<?php echo e(asset('static/scripts/docen_asist.js')); ?>"></script>
</body>
</html>
<?php /**PATH C:\Users\migue\OneDrive\Escritorio\projects\inf342_2exa\app\templates/docen_asist.blade.php ENDPATH**/ ?>