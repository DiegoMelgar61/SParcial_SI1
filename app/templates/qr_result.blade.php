<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $success ? 'Asistencia Registrada' : 'Error' }} | Módulo Docencia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    
    <div class="max-w-md w-full">
        <!-- Card de Resultado -->
        <div class="bg-white  shadow-xl overflow-hidden">
            
            <!-- Header con color según resultado -->
            <div class="@if($success) bg-gradient-to-r from-green-500 to-green-600 @else bg-gradient-to-r from-red-500 to-red-600 @endif p-6 text-white text-center">
                <div class="mb-4">
                    @if($success)
                        <!-- Ícono de éxito -->
                        <div class="mx-auto w-20 h-20 bg-white  flex items-center justify-center">
                            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    @else
                        <!-- Ícono de error -->
                        <div class="mx-auto w-20 h-20 bg-white  flex items-center justify-center">
                            <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                    @endif
                </div>
                
                <h1 class="text-2xl font-bold mb-2">
                    {{ $success ? '¡Éxito!' : 'Error' }}
                </h1>
                <p class="text-lg opacity-95">{{ $message }}</p>
            </div>
            
            <!-- Contenido -->
            <div class="p-6">
                @if(isset($detalle))
                    <div class="mb-6">
                        <p class="text-center text-gray-700 font-medium">
                            {{ $detalle }}
                        </p>
                    </div>
                @endif
                
                @if($success && isset($fecha))
                    <!-- Detalles del registro exitoso -->
                    <div class="bg-gray-50  p-4 mb-6 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Fecha y hora:</span>
                            <span class="font-semibold text-gray-800">{{ $fecha }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Estado:</span>
                            <span class="font-semibold text-green-600">{{ $estado }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Método:</span>
                            <span class="font-semibold text-blue-600">{{ $metodo }}</span>
                        </div>
                    </div>
                @endif
                
                <!-- Botón de regresar -->
                <a href="{{ $redirect_url }}" 
                   class="block w-full text-center bg-navy-900 hover:bg-navy-800 text-white font-semibold py-3 px-4  transition shadow-sm">
                    @if($success)
                        Volver a Asistencia
                    @else
                        Intentar de nuevo
                    @endif
                </a>
                
                <!-- Auto-redirect después de 5 segundos (solo si es éxito) -->
                @if($success)
                    <p class="text-center text-xs text-gray-500 mt-4">
                        Redirigiendo en <span id="countdown">5</span> segundos...
                    </p>
                @endif
            </div>
            
        </div>
        
        <!-- Información adicional para errores -->
        @if(!$success)
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Si el problema persiste, contacta al administrador
                </p>
            </div>
        @endif
    </div>

    <!-- Script para countdown y auto-redirect -->
    @if($success)
    <script>
        let seconds = 5;
        const countdownEl = document.getElementById('countdown');
        
        const interval = setInterval(() => {
            seconds--;
            countdownEl.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(interval);
                window.location.href = '{{ $redirect_url }}';
            }
        }, 1000);
    </script>
    @endif
    
    <!-- Script para notificar al parent window (si se abrió desde el modal) -->
    <script>
        // Si esta ventana fue abierta desde el modal, notificar el resultado
        if (window.opener && !window.opener.closed) {
            try {
                window.opener.postMessage({
                    type: 'qr_result',
                    success: {{ $success ? 'true' : 'false' }},
                    message: '{{ $message }}'
                }, '*');
            } catch (e) {
                console.log('No se pudo comunicar con la ventana padre');
            }
        }
    </script>

</body>
</html>
