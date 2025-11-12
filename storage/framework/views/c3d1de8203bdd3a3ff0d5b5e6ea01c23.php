<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($success ? 'Asistencia Registrada' : 'Error'); ?> | Módulo Docencia</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    
    <div class="max-w-md w-full">
        <!-- Card de Resultado -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            
            <!-- Header con color según resultado -->
            <div class="<?php if($success): ?> bg-gradient-to-r from-green-500 to-green-600 <?php else: ?> bg-gradient-to-r from-red-500 to-red-600 <?php endif; ?> p-6 text-white text-center">
                <div class="mb-4">
                    <?php if($success): ?>
                        <!-- Ícono de éxito -->
                        <div class="mx-auto w-20 h-20 bg-white rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    <?php else: ?>
                        <!-- Ícono de error -->
                        <div class="mx-auto w-20 h-20 bg-white rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                    <?php endif; ?>
                </div>
                
                <h1 class="text-2xl font-bold mb-2">
                    <?php echo e($success ? '¡Éxito!' : 'Error'); ?>

                </h1>
                <p class="text-lg opacity-95"><?php echo e($message); ?></p>
            </div>
            
            <!-- Contenido -->
            <div class="p-6">
                <?php if(isset($detalle)): ?>
                    <div class="mb-6">
                        <p class="text-center text-gray-700 font-medium">
                            <?php echo e($detalle); ?>

                        </p>
                    </div>
                <?php endif; ?>
                
                <?php if($success && isset($fecha)): ?>
                    <!-- Detalles del registro exitoso -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Fecha y hora:</span>
                            <span class="font-semibold text-gray-800"><?php echo e($fecha); ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Estado:</span>
                            <span class="font-semibold text-green-600"><?php echo e($estado); ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Método:</span>
                            <span class="font-semibold text-blue-600"><?php echo e($metodo); ?></span>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Botón de regresar -->
                <a href="<?php echo e($redirect_url); ?>" 
                   class="block w-full text-center bg-sky-600 hover:bg-sky-700 text-white font-semibold py-3 px-4 rounded-lg transition shadow-sm">
                    <?php if($success): ?>
                        Volver a Asistencia
                    <?php else: ?>
                        Intentar de nuevo
                    <?php endif; ?>
                </a>
                
                <!-- Auto-redirect después de 5 segundos (solo si es éxito) -->
                <?php if($success): ?>
                    <p class="text-center text-xs text-gray-500 mt-4">
                        Redirigiendo en <span id="countdown">5</span> segundos...
                    </p>
                <?php endif; ?>
            </div>
            
        </div>
        
        <!-- Información adicional para errores -->
        <?php if(!$success): ?>
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Si el problema persiste, contacta al administrador
                </p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Script para countdown y auto-redirect -->
    <?php if($success): ?>
    <script>
        let seconds = 5;
        const countdownEl = document.getElementById('countdown');
        
        const interval = setInterval(() => {
            seconds--;
            countdownEl.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(interval);
                window.location.href = '<?php echo e($redirect_url); ?>';
            }
        }, 1000);
    </script>
    <?php endif; ?>
    
    <!-- Script para notificar al parent window (si se abrió desde el modal) -->
    <script>
        // Si esta ventana fue abierta desde el modal, notificar el resultado
        if (window.opener && !window.opener.closed) {
            try {
                window.opener.postMessage({
                    type: 'qr_result',
                    success: <?php echo e($success ? 'true' : 'false'); ?>,
                    message: '<?php echo e($message); ?>'
                }, '*');
            } catch (e) {
                console.log('No se pudo comunicar con la ventana padre');
            }
        }
    </script>

</body>
</html>
<?php /**PATH C:\Users\migue\OneDrive\Escritorio\projects\inf342_2exa\app\templates/qr_result.blade.php ENDPATH**/ ?>