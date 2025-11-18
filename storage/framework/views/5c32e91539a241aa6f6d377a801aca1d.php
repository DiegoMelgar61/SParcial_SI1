<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Inicio de Sesión — Sistema FICCT</title>
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
    body { font-family: 'Inter', sans-serif; }
  </style>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'navy': {
              900: '#0f2942',
              800: '#1e3a5f',
              700: '#2c5f8d',
            },
            'gold': {
              500: '#c9a961',
              600: '#b8974f',
            }
          }
        }
      }
    }
  </script>
</head>

<body class="min-h-screen flex bg-gradient-to-br from-slate-100 via-slate-200 to-slate-300">

  <!-- Panel izquierdo decorativo -->
  <div class="hidden lg:flex lg:w-1/2 bg-navy-900 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
      <div class="absolute top-0 left-0 w-96 h-96 bg-gold-500 rounded-full blur-3xl"></div>
      <div class="absolute bottom-0 right-0 w-96 h-96 bg-navy-700 rounded-full blur-3xl"></div>
    </div>
    <div class="relative z-10 flex flex-col justify-center items-start px-16 text-white">
      <div class="mb-8">
        <div class="w-20 h-1 bg-gold-500 mb-6"></div>
        <h1 class="text-4xl font-bold mb-4 leading-tight tracking-tight">Sistema de Gestión<br/>Facultativa</h1>
        <p class="text-slate-300 text-lg leading-relaxed">
          Portal corporativo para la administración académica y control docente de la UAGRM.
        </p>
      </div>
      <div class="mt-12 space-y-3 text-sm text-slate-400">
        <div class="flex items-center gap-3">
          <div class="w-2 h-2 bg-gold-500 rounded-full"></div>
          <span>Gestión integral de usuarios y docentes</span>
        </div>
        <div class="flex items-center gap-3">
          <div class="w-2 h-2 bg-gold-500 rounded-full"></div>
          <span>Control de asistencias y horarios</span>
        </div>
        <div class="flex items-center gap-3">
          <div class="w-2 h-2 bg-gold-500 rounded-full"></div>
          <span>Reportes y estadísticas en tiempo real</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Panel derecho - Formulario -->
  <div class="flex-1 flex items-center justify-center p-8">
    <div class="w-full max-w-md">

      <!-- Card principal -->
      <div class="bg-white shadow-xl border border-slate-200 relative">

        <!-- Loader -->
        <div id="loader" class="hidden absolute inset-0 flex flex-col items-center justify-center bg-white/90 z-10">
          <div class="h-8 w-8 border-3 border-navy-700 border-t-transparent rounded-full animate-spin mb-3"></div>
          <p class="text-sm text-navy-800 font-medium">Verificando credenciales...</p>
        </div>

        <!-- Header corporativo -->
        <div class="bg-navy-900 px-8 py-6 border-b-4 border-gold-500">
          <div class="flex items-center gap-4 mb-3">
            <img src="<?php echo e(asset('static/images/logo2.png')); ?>" alt="FICCT Logo"
                 class="w-14 h-14 border-2 border-gold-500 bg-white p-1">
            <div>
              <h2 class="text-white text-xl font-bold tracking-wide">FICCT - UAGRM</h2>
              <p class="text-slate-400 text-xs uppercase tracking-widest">Portal Académico</p>
            </div>
          </div>
        </div>

        <!-- Contenido del formulario -->
        <div class="px-8 py-8">

          <!-- Título -->
          <div class="mb-6">
            <h3 class="text-2xl font-bold text-navy-900 mb-1">Inicio de Sesión</h3>
            <p class="text-slate-600 text-sm">
              Ingrese sus credenciales corporativas para acceder al sistema.
            </p>
          </div>

          <!-- Alerta de error -->
          <div id="alert-error" class="hidden bg-red-50 border-l-4 border-red-600 text-red-800 px-4 py-3 mb-5 text-sm">
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
              </svg>
              <span class="font-medium">Credenciales incorrectas. Intente nuevamente.</span>
            </div>
          </div>

          <!-- Formulario -->
          <form id="loginForm" class="space-y-5">
            <div>
              <label for="codigo" class="block text-sm font-semibold text-navy-900 mb-2 uppercase tracking-wide">Código de Usuario</label>
              <input type="text" id="codigo" name="codigo" required
                     placeholder="Ej: 202112345"
                     class="w-full px-4 py-3 border-2 border-slate-300 text-navy-900 placeholder-slate-400
                            focus:outline-none focus:border-navy-700 focus:ring-2 focus:ring-navy-700/20 transition-all">
            </div>

            <div>
              <label for="password" class="block text-sm font-semibold text-navy-900 mb-2 uppercase tracking-wide">Contraseña</label>
              <input type="password" id="password" name="password" autocomplete="off" required
                     placeholder="••••••••"
                     class="w-full px-4 py-3 border-2 border-slate-300 text-navy-900 placeholder-slate-400
                            focus:outline-none focus:border-navy-700 focus:ring-2 focus:ring-navy-700/20 transition-all">
            </div>

            <button type="submit"
                    class="w-full py-3.5 bg-navy-900 hover:bg-navy-800 text-white font-bold uppercase tracking-wider
                           shadow-lg hover:shadow-xl transition-all duration-200 border-b-4 border-navy-800 hover:border-gold-600
                           focus:ring-4 focus:ring-navy-700/50">
              Iniciar Sesión
            </button>
          </form>

          <!-- Credenciales de prueba -->
          <div class="mt-6">
            <div class="bg-slate-50 border-2 border-slate-200 px-5 py-4">
              <p class="text-xs text-slate-600 mb-2 font-bold uppercase tracking-wide">Credenciales de Prueba - Administrador</p>
              <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="bg-white px-3 py-2 border border-slate-200">
                  <span class="block text-slate-500 text-xs font-semibold mb-1">CÓDIGO</span>
                  <span class="font-bold text-navy-900">45</span>
                </div>
                <div class="bg-white px-3 py-2 border border-slate-200">
                  <span class="block text-slate-500 text-xs font-semibold mb-1">CONTRASEÑA</span>
                  <span class="font-bold text-navy-900">12345678</span>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Footer corporativo -->
        <div class="bg-slate-50 px-8 py-4 border-t border-slate-200 text-center">
          <p class="text-xs text-slate-600 font-medium">
            © <?php echo e(date('Y')); ?> Facultad de Ingeniería — UAGRM
          </p>
        </div>

      </div>

    </div>
  </div>

  <!-- Modal corporativo -->
  <div id="modal-result" class="hidden fixed inset-0 bg-navy-900/60 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white border-2 border-slate-300 shadow-2xl w-full max-w-sm">
      <div class="bg-navy-900 px-6 py-4 border-b-4 border-gold-500">
        <h3 id="modal-title" class="text-lg font-bold text-white uppercase tracking-wide">Mensaje del Sistema</h3>
      </div>
      <div class="px-6 py-6">
        <p id="modal-message" class="text-sm text-slate-700 leading-relaxed">Texto del sistema.</p>
      </div>
      <div class="px-6 pb-6">
        <button id="modal-close"
                class="w-full px-5 py-3 bg-navy-900 text-white font-bold uppercase tracking-wider
                       hover:bg-navy-800 transition-all border-b-4 border-navy-800 hover:border-gold-600">
          Aceptar
        </button>
      </div>
    </div>
  </div>

  <script src="<?php echo e(asset('static/scripts/login.js')); ?>"></script>
</body>
</html>
<?php /**PATH C:\Users\diego\OneDrive\Escritorio\exa2_inf342\app\templates/login.blade.php ENDPATH**/ ?>