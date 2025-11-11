<?php

namespace App\Helpers;

use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class BitacoraHelper
{
    /**
     * Registrar una acción en la bitácora
     */
    public static function registrar(
        string $accion,
        ?string $tabla = null,
        ?int $registroId = null,
        string $descripcion = '',
        ?array $datosAnteriores = null,
        ?array $datosNuevos = null,
        ?int $usuarioId = null
    ): void {
        try {
            // Si no se proporciona usuario_id, intentar obtenerlo de Auth
            $usuarioId = $usuarioId ?? Auth::id();
            
            Bitacora::create([
                'usuario_id' => $usuarioId,
                'accion' => $accion,
                'tabla' => $tabla,
                'registro_id' => $registroId,
                'descripcion' => $descripcion ?: self::generarDescripcion($accion, $tabla, $registroId, $usuarioId),
                'datos_anteriores' => $datosAnteriores,
                'datos_nuevos' => $datosNuevos,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'fecha_creacion' => now(),
            ]);
        } catch (\Exception $e) {
            // Log detallado del error para depuración
            \Log::error('Error al registrar en bitácora', [
                'mensaje' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'accion' => $accion,
                'usuario_id' => $usuarioId,
            ]);
        }
    }

    /**
     * Generar descripción automática basada en la acción
     */
    private static function generarDescripcion(string $accion, ?string $tabla, ?int $registroId, ?int $usuarioId = null): string
    {
        $usuario = $usuarioId ? \App\Models\Usuario::with('rol')->find($usuarioId) : Auth::user();
        $nombreUsuario = $usuario ? "{$usuario->nombre} {$usuario->apellido}" : 'Sistema';
        $rolUsuario = $usuario && $usuario->rol ? $usuario->rol->nombre : 'Usuario';
        
        $descripciones = [
            'crear' => "{$nombreUsuario} creó un nuevo registro en {$tabla}",
            'editar' => "{$nombreUsuario} editó el registro #{$registroId} en {$tabla}",
            'eliminar' => "{$nombreUsuario} eliminó el registro #{$registroId} de {$tabla}",
            'login' => "Usuario: {$rolUsuario} - Acción: Login - Descripción: successful",
            'logout' => "Usuario: {$rolUsuario} - Acción: Logout - Descripción: successful",
            'cambiar_contrasena' => "{$nombreUsuario} cambió su contraseña",
            'cambiar_estado' => "{$nombreUsuario} cambió el estado de un usuario",
            'asignar_permisos' => "{$nombreUsuario} asignó permisos a un rol",
        ];

        return $descripciones[$accion] ?? "{$nombreUsuario} realizó la acción: {$accion}";
    }
}

