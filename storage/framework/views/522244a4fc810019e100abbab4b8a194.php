<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo de Reportes — Plataforma Universitaria INF342</title>
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

    <!-- Barra superior -->
    <header class="bg-navy-900 border-b-4 border-gold-500 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-3">
            <div class="flex items-center gap-4">
                <button id="menu-toggle"
                    class="block md:hidden p-2 text-gold-500 hover:text-gold-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h1 class="text-lg md:text-xl font-semibold text-white tracking-wide">
                    Plataforma Universitaria
                </h1>
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

    <!-- Sidebar -->
    <aside id="admin-sidebar"
        class="fixed top-0 left-0 w-64 bg-navy-900 shadow-lg h-full z-30 border-r-4 border-gold-500 transform -translate-x-full md:translate-x-0 transition-transform duration-300">

        <div class="flex flex-col h-full overflow-y-auto scrollbar-thin scrollbar-thumb-gold-500 scrollbar-track-navy-800">
            <div class="p-4 border-b-2 border-gold-500">
                <h3 class="text-sm font-semibold text-white">Panel de Reportes</h3>
                <p class="text-xs text-gold-500 mt-1 font-medium">Consultas y descargas académicas</p>
            </div>

            <nav class="flex-1 p-3">
                <ul class="space-y-1 text-sm">
                    <li>
                        <a href="/reportes"
                            class="flex items-center gap-2 px-3 py-2 text-navy-900 bg-gold-500 border-l-4 border-gold-600 font-semibold hover:bg-gold-600 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622z"/>
                            </svg>
                            <span>Reportes Académicos</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="p-3 border-t-2 border-gold-500 text-center text-[11px] text-gold-500">
                Módulo Reportes v1.0
            </div>
        </div>
    </aside>

    <!-- Overlay para móviles -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden hidden"></div>

    <!-- Contenido principal -->
    <main class="flex-1 md:ml-64 p-6 transition-all duration-300">

        <div class="flex flex-col md:flex-row justify-between md:items-center mb-8">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 mb-1">Módulo de Reportes Académicos</h2>
                <p class="text-gray-600 text-sm">Visualización y descarga de informes por docente</p>
            </div>
            <div id="clock" class="text-sm text-gray-600 font-medium mt-3 md:mt-0"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Reporte Asistencia -->
            <div id="card-asistencia" class="bg-white p-6 shadow-sm border-l-4 border-navy-900 hover:shadow-md transition cursor-pointer">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Reporte de Asistencia Docente</h3>
                    <div class="w-10 h-10 bg-navy-900  flex items-center justify-center">
                        <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-5">
                    Permite revisar el registro completo de asistencia docente por materia y grupo, con opción de visualización o exportación.
                </p>
                <div class="flex justify-end gap-3">
                    <button id="btn-ver-asistencia" class="text-sm bg-gray-100 hover:bg-gold-500 text-gray-700 hover:text-white px-4 py-2 border-b-4 border-gray-300 hover:border-gold-600 font-medium transition">
                        Ver en la Web
                    </button>
                    <button id="btn-descargar-asistencia" class="text-sm bg-navy-900 hover:bg-navy-800 text-white px-4 py-2 border-b-4 border-navy-800 font-medium transition">
                        Descargar
                    </button>
                </div>
            </div>

            <!-- Reporte Licencias -->
            <div id="card-licencia" class="bg-white p-6 shadow-sm border-l-4 border-gold-500 hover:shadow-md transition cursor-pointer">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Reporte de Licencias Docentes</h3>
                    <div class="w-10 h-10 bg-gold-500  flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2v1a2 2 0 01-2 2H9a2 2 0 01-2-2V9a2 2 0 012-2h6zm6 11h-3a2 2 0 01-2-2v-1a2 2 0 012-2h3v5zM6 18H3v-5h3a2 2 0 012 2v1a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-5">
                    Muestra las licencias registradas por los docentes, con información detallada sobre duración, motivo y estado de aprobación.
                </p>
                <div class="flex justify-end gap-3">
                    <button id="btn-ver-licencia" class="text-sm bg-gray-100 hover:bg-gold-500 text-gray-700 hover:text-white px-4 py-2 border-b-4 border-gray-300 hover:border-gold-600 font-medium transition">
                        Ver en la Web
                    </button>
                    <button id="btn-descargar-licencia" class="text-sm bg-gold-500 hover:bg-gold-600 text-navy-900 px-4 py-2 border-b-4 border-gold-600 font-medium transition">
                        Descargar
                    </button>
                </div>
            </div>
        </div>
    </main>

    <footer class="text-center py-4 text-xs text-gold-500 border-t-4 border-gold-500 bg-navy-900 mt-10 md:ml-64">
        © <?php echo e(date('Y')); ?> Universidad Autónoma Gabriel René Moreno — Sistema de Reportes Académicos INF342
    </footer>

    <script src="<?php echo e(asset('static/scripts/reportes.js')); ?>"></script>
</body>
</html>
<?php /**PATH C:\Users\diego\OneDrive\Escritorio\exa2_inf342\app\templates/reportes.blade.php ENDPATH**/ ?>