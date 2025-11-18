<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reporte de Asistencia — Plataforma Universitaria INF342</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
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

  <!-- Encabezado -->
  <header class="bg-navy-900 border-b-4 border-gold-500 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-3">
      <div class="flex items-center gap-4">
        <h1 class="text-lg md:text-xl font-semibold text-white tracking-wide">
          Reporte de Asistencia Docente
        </h1>
      </div>

      <a href="/reportes"
        class="text-sm bg-gold-500 hover:bg-gold-600 text-navy-900 px-4 py-2 border-b-4 border-gold-600 font-medium transition shadow-sm">
        Volver
      </a>
    </div>
  </header>

  <!-- Contenido -->
  <main class="flex-1 max-w-7xl mx-auto w-full p-6">
    <div class="flex flex-col md:flex-row justify-between md:items-center mb-8">
      <div>
        <h2 class="text-xl font-semibold text-gray-800 mb-1">Asistencias registradas</h2>
        <p class="text-gray-600 text-sm">Visualización institucional de la asistencia docente</p>
      </div>
      <div id="clock" class="text-sm text-gray-600 font-medium mt-3 md:mt-0"></div>
    </div>

    <div class="bg-white shadow-sm border-2 border-navy-900 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-gray-700">
          <thead class="bg-navy-900 text-white uppercase text-xs font-semibold">
            <tr>
              <th scope="col" class="px-6 py-3">Fecha</th>
              <th scope="col" class="px-6 py-3">Docente</th>
              <th scope="col" class="px-6 py-3">Materia</th>
              <th scope="col" class="px-6 py-3">Grupo</th>
              <th scope="col" class="px-6 py-3">Estado</th>
              <th scope="col" class="px-6 py-3">Método</th>
            </tr>
          </thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $asistencias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr class="border-b hover:bg-gray-50 transition">
                <td class="px-6 py-3"><?php echo e($a['fecha'] ?? '—'); ?></td>
                <td class="px-6 py-3"><?php echo e($a['docente'] ?? '—'); ?></td>
                <td class="px-6 py-3"><?php echo e($a['materia'] ?? '—'); ?></td>
                <td class="px-6 py-3"><?php echo e($a['grupo'] ?? '—'); ?></td>
                <td class="px-6 py-3">
                  <?php
                    $estado = strtolower($a['estado'] ?? '');
                    $color = match(true) {
                      str_contains($estado, 'presente') => 'bg-green-100 text-green-700',
                      str_contains($estado, 'falta') => 'bg-red-100 text-red-700',
                      str_contains($estado, 'atraso') => 'bg-yellow-100 text-yellow-700',
                      default => 'bg-gray-100 text-gray-700'
                    };
                  ?>
                  <span class="px-2 py-1  text-xs font-semibold <?php echo e($color); ?>">
                    <?php echo e(ucfirst($a['estado'] ?? '—')); ?>

                  </span>
                </td>
                <td class="px-6 py-3"><?php echo e($a['metodo_r'] ?? '—'); ?></td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr>
                <td colspan="6" class="text-center py-6 text-gray-500">No se encontraron registros de asistencia.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <footer class="text-center py-4 text-xs text-gold-500 border-t-4 border-gold-500 bg-navy-900 mt-10">
    © <?php echo e(date('Y')); ?> Universidad Autónoma Gabriel René Moreno — INF342
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const clock = document.getElementById('clock');
      if (clock) {
        const updateClock = () => {
          const now = new Date();
          clock.textContent = now.toLocaleString('es-BO', { dateStyle: 'medium', timeStyle: 'short' });
        };
        updateClock();
        setInterval(updateClock, 60000);
      }
    });
  </script>
</body>
</html>
<?php /**PATH C:\Users\diego\OneDrive\Escritorio\exa2_inf342\app\templates/reportes_asistencias.blade.php ENDPATH**/ ?>