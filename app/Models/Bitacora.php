<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAuditoria extends Model
{
    protected $table = 'logs_auditoria';
    protected $primaryKey = 'id';
    public $timestamps = false; // Solo tiene fecha_creacion

    protected $fillable = [
        'usuario_id',
        'accion',
        'tabla',
        'registro_id',
        'datos_anteriores',
        'datos_nuevos',
        'ip_address',
        'user_agent',
        'fecha_creacion',
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
        'fecha_creacion' => 'datetime',
    ];

    /**
     * Relación: Un log pertenece a un usuario
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Scope: Filtrar por usuario
     */
    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    /**
     * Scope: Filtrar por acción
     */
    public function scopePorAccion($query, $accion)
    {
        return $query->where('accion', $accion);
    }

    /**
     * Scope: Filtrar por tabla
     */
    public function scopePorTabla($query, $tabla)
    {
        return $query->where('tabla', $tabla);
    }

    /**
     * Scope: Filtrar por rango de fechas
     */
    public function scopeEntreFechas($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha_creacion', [$desde, $hasta]);
    }

    /**
     * Scope: Buscar en todos los campos
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('accion', 'ILIKE', "%{$termino}%")
              ->orWhere('tabla', 'ILIKE', "%{$termino}%")
              ->orWhere('ip_address', 'LIKE', "%{$termino}%")
              ->orWhereHas('usuario', function ($qu) use ($termino) {
                  $qu->where('nombre', 'ILIKE', "%{$termino}%")
                     ->orWhere('apellido', 'ILIKE', "%{$termino}%");
              });
        });
    }

    /**
     * Accessor: Descripción legible de la acción
     */
    public function getDescripcionAttribute()
    {
        $acciones = [
            'login' => 'Inició sesión',
            'logout' => 'Cerró sesión',
            'login_fallido' => 'Intento de inicio de sesión fallido',
            'create' => 'Creó un registro',
            'update' => 'Actualizó un registro',
            'delete' => 'Eliminó un registro',
            'restore' => 'Restauró un registro',
            'asignar_permiso' => 'Asignó permisos',
            'cambiar_estado' => 'Cambió el estado',
            'cambiar_contrasena' => 'Cambió la contraseña',
            'exportar' => 'Exportó datos',
            'importar' => 'Importó datos',
        ];

        return $acciones[$this->accion] ?? ucfirst($this->accion);
    }

    /**
     * Accessor: Nombre de la tabla en español
     */
    public function getTablaFormateadaAttribute()
    {
        $tablas = [
            'usuarios' => 'Usuarios',
            'roles' => 'Roles',
            'permisos' => 'Permisos',
            'docentes' => 'Docentes',
            'carreras' => 'Carreras',
            'materias' => 'Materias',
            'grupos' => 'Grupos',
            'aulas' => 'Aulas',
            'horarios' => 'Horarios',
            'asistencias' => 'Asistencias',
        ];

        return $tablas[$this->tabla] ?? ucfirst($this->tabla);
    }

    /**
     * Obtener cambios en formato legible
     */
    public function getCambiosFormateados()
    {
        if (!$this->datos_anteriores || !$this->datos_nuevos) {
            return null;
        }

        $cambios = [];
        foreach ($this->datos_nuevos as $campo => $valorNuevo) {
            $valorAnterior = $this->datos_anteriores[$campo] ?? null;
            
            if ($valorAnterior != $valorNuevo) {
                $cambios[] = [
                    'campo' => $campo,
                    'anterior' => $valorAnterior,
                    'nuevo' => $valorNuevo,
                ];
            }
        }

        return $cambios;
    }

    /**
     * Obtener todos los tipos de acciones disponibles
     */
    public static function getTiposAcciones()
    {
        return [
            ['value' => 'login', 'label' => 'Login'],
            ['value' => 'logout', 'label' => 'Logout'],
            ['value' => 'login_fallido', 'label' => 'Login Fallido'],
            ['value' => 'create', 'label' => 'Crear'],
            ['value' => 'update', 'label' => 'Actualizar'],
            ['value' => 'delete', 'label' => 'Eliminar'],
            ['value' => 'restore', 'label' => 'Restaurar'],
            ['value' => 'asignar_permiso', 'label' => 'Asignar Permiso'],
            ['value' => 'cambiar_estado', 'label' => 'Cambiar Estado'],
            ['value' => 'cambiar_contrasena', 'label' => 'Cambiar Contraseña'],
            ['value' => 'exportar', 'label' => 'Exportar'],
            ['value' => 'importar', 'label' => 'Importar'],
        ];
    }

    /**
     * Obtener todas las tablas auditadas
     */
    public static function getTablasAuditadas()
    {
        return [
            ['value' => 'usuarios', 'label' => 'Usuarios'],
            ['value' => 'roles', 'label' => 'Roles'],
            ['value' => 'permisos', 'label' => 'Permisos'],
            ['value' => 'docentes', 'label' => 'Docentes'],
            ['value' => 'carreras', 'label' => 'Carreras'],
            ['value' => 'materias', 'label' => 'Materias'],
            ['value' => 'grupos', 'label' => 'Grupos'],
            ['value' => 'aulas', 'label' => 'Aulas'],
            ['value' => 'horarios', 'label' => 'Horarios'],
            ['value' => 'asistencias', 'label' => 'Asistencias'],
        ];
    }
}