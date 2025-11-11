<?php

namespace App\Services;

use App\Models\LogAuditoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditoriaService
{
    /**
     * Registrar un log de auditoría
     */
    public static function log(array $data)
    {
        try {
            // Obtener usuario actual
            $usuarioId = Auth::id();

            // Obtener IP y User Agent
            $ipAddress = Request::ip();
            $userAgent = Request::header('User-Agent');

            // Crear el log
            LogAuditoria::create([
                'usuario_id' => $usuarioId ?? $data['usuario_id'] ?? null,
                'accion' => $data['accion'],
                'tabla' => $data['tabla'] ?? null,
                'registro_id' => $data['registro_id'] ?? null,
                'datos_anteriores' => $data['datos_anteriores'] ?? null,
                'datos_nuevos' => $data['datos_nuevos'] ?? null,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'fecha_creacion' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            // Log del error pero no interrumpir la ejecución
            \Log::error('Error al registrar auditoría: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Registrar login exitoso
     */
    public static function logLogin($usuario)
    {
        return self::log([
            'usuario_id' => $usuario->id,
            'accion' => 'login',
            'tabla' => 'usuarios',
            'registro_id' => $usuario->id,
            'datos_nuevos' => [
                'email' => $usuario->email,
                'nombre_completo' => $usuario->nombre_completo ?? $usuario->nombre . ' ' . $usuario->apellido,
                'resultado' => 'exitoso',
            ],
        ]);
    }

    /**
     * Registrar login fallido
     */
    public static function logLoginFallido($email, $motivo = 'Credenciales incorrectas')
    {
        return self::log([
            'usuario_id' => null,
            'accion' => 'login_fallido',
            'tabla' => 'usuarios',
            'datos_nuevos' => [
                'email' => $email,
                'motivo' => $motivo,
            ],
        ]);
    }

    /**
     * Registrar logout
     */
    public static function logLogout($usuario)
    {
        return self::log([
            'usuario_id' => $usuario->id,
            'accion' => 'logout',
            'tabla' => 'usuarios',
            'registro_id' => $usuario->id,
            'datos_nuevos' => [
                'email' => $usuario->email,
                'nombre_completo' => $usuario->nombre_completo ?? $usuario->nombre . ' ' . $usuario->apellido,
            ],
        ]);
    }

    /**
     * Registrar creación de registro
     */
    public static function logCreate($modelo, $tabla, $datosNuevos)
    {
        return self::log([
            'accion' => 'create',
            'tabla' => $tabla,
            'registro_id' => $modelo->id ?? null,
            'datos_nuevos' => $datosNuevos,
        ]);
    }

    /**
     * Registrar actualización de registro
     */
    public static function logUpdate($modelo, $tabla, $datosAnteriores, $datosNuevos)
    {
        return self::log([
            'accion' => 'update',
            'tabla' => $tabla,
            'registro_id' => $modelo->id ?? null,
            'datos_anteriores' => $datosAnteriores,
            'datos_nuevos' => $datosNuevos,
        ]);
    }

    /**
     * Registrar eliminación de registro
     */
    public static function logDelete($modelo, $tabla, $datosAnteriores)
    {
        return self::log([
            'accion' => 'delete',
            'tabla' => $tabla,
            'registro_id' => $modelo->id ?? null,
            'datos_anteriores' => $datosAnteriores,
        ]);
    }

    /**
     * Registrar asignación de permisos
     */
    public static function logAsignarPermisos($rol, $permisosAnteriores, $permisosNuevos)
    {
        return self::log([
            'accion' => 'asignar_permiso',
            'tabla' => 'roles',
            'registro_id' => $rol->id,
            'datos_anteriores' => ['permisos' => $permisosAnteriores],
            'datos_nuevos' => ['permisos' => $permisosNuevos],
        ]);
    }

    /**
     * Registrar cambio de estado
     */
    public static function logCambiarEstado($modelo, $tabla, $estadoAnterior, $estadoNuevo)
    {
        return self::log([
            'accion' => 'cambiar_estado',
            'tabla' => $tabla,
            'registro_id' => $modelo->id,
            'datos_anteriores' => ['estado' => $estadoAnterior],
            'datos_nuevos' => ['estado' => $estadoNuevo],
        ]);
    }

    /**
     * Registrar cambio de contraseña
     */
    public static function logCambiarContrasena($usuario)
    {
        return self::log([
            'accion' => 'cambiar_contrasena',
            'tabla' => 'usuarios',
            'registro_id' => $usuario->id,
            'datos_nuevos' => [
                'email' => $usuario->email,
                'cambiado_por' => Auth::id() === $usuario->id ? 'mismo_usuario' : 'administrador',
            ],
        ]);
    }

    /**
     * Registrar exportación
     */
    public static function logExportar($tipo, $formato, $filtros = [])
    {
        return self::log([
            'accion' => 'exportar',
            'tabla' => $tipo,
            'datos_nuevos' => [
                'formato' => $formato,
                'filtros' => $filtros,
            ],
        ]);
    }

    /**
     * Registrar importación
     */
    public static function logImportar($tipo, $cantidad, $detalles = [])
    {
        return self::log([
            'accion' => 'importar',
            'tabla' => $tipo,
            'datos_nuevos' => [
                'cantidad' => $cantidad,
                'detalles' => $detalles,
            ],
        ]);
    }
}