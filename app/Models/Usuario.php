<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'uuid',
        'rol_id',
        'nombre',
        'apellido',
        'cedula_identidad',
        'email',
        'telefono',
        'contrasena',
        'debe_cambiar_contrasena',
        'email_verificado_en',
        'estado',
        'foto_perfil',
        'ultimo_login',
        'ultimo_login_ip',
        'intentos_fallidos_login',
        'bloqueado_hasta',
        'creado_por',
        'actualizado_por'
    ];

    protected $hidden = [
        'contrasena',
        'token_recordar'
    ];

    protected $casts = [
        'email_verificado_en' => 'datetime',
        'ultimo_login' => 'datetime',
        'debe_cambiar_contrasena' => 'boolean',
        'intentos_fallidos_login' => 'integer',
        'bloqueado_hasta' => 'datetime',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
        'fecha_eliminacion' => 'datetime'
    ];

    const DELETED_AT = 'fecha_eliminacion';
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';


    public function getRememberTokenName()
    {
        return 'token_recordar';
    }

    /**
     * Laravel Auth: Obtener la contraseña para autenticación
     */
    public function getAuthPassword()
    {
        return $this->attributes['contrasena'] ?? $this->attributes['password'] ?? null;
    }

    
    public function getPasswordAttribute()
    {
        return $this->attributes['contrasena'] ?? null;
    }

    // ==================== RELACIONES ====================

    /**
     * Relación: Un usuario pertenece a un rol
     */
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    /**
     * Relación: Un usuario puede tener un perfil de docente
     */
    public function docente()
    {
        return $this->hasOne(Docente::class, 'usuario_id');
    }

    /**
     * Relación: Usuario que creó este registro
     */
    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'creado_por');
    }

    /**
     * Relación: Usuario que actualizó este registro
     */
    public function actualizador()
    {
        return $this->belongsTo(Usuario::class, 'actualizado_por');
    }

    // ==================== SCOPES ====================

    /**
     * Scope: Filtrar por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope: Filtrar por rol
     */
    public function scopePorRol($query, $rolId)
    {
        return $query->where('rol_id', $rolId);
    }

    /**
     * Scope: Buscar por nombre, apellido, email o cédula
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombre', 'ILIKE', "%{$termino}%")
              ->orWhere('apellido', 'ILIKE', "%{$termino}%")
              ->orWhere('email', 'ILIKE', "%{$termino}%")
              ->orWhere('cedula_identidad', 'LIKE', "%{$termino}%");
        });
    }

    /**
     * Scope: Solo usuarios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope: Usuarios bloqueados
     */
    public function scopeBloqueados($query)
    {
        return $query->where('bloqueado_hasta', '>', now());
    }

    // ==================== ACCESSORS ====================

    /**
     * Accessor: Nombre completo
     */
    public function getNombreCompletoAttribute()
    {
        return trim("{$this->nombre} {$this->apellido}");
    }

    /**
     * Accessor: Iniciales
     */
    public function getInicialesAttribute()
    {
        $nombre = substr($this->nombre, 0, 1);
        $apellido = substr($this->apellido, 0, 1);
        return strtoupper($nombre . $apellido);
    }

    // ==================== MÉTODOS ÚTILES ====================

    /**
     * Verificar si el usuario está bloqueado
     */
    public function estaBloqueado()
    {
        return $this->bloqueado_hasta && $this->bloqueado_hasta->isFuture();
    }

    /**
     * Verificar si el usuario está activo
     */
    public function estaActivo()
    {
        return $this->estado === 'activo';
    }

    /**
     * Verificar si el usuario es docente
     */
    public function esDocente()
    {
        return $this->docente()->exists();
    }

    /**
     * Verificar si tiene un rol específico
     */
    public function tieneRol($nombreRol)
    {
        return $this->rol && $this->rol->nombre === $nombreRol;
    }

    /**
     * Incrementar intentos fallidos de login
     */
    public function incrementarIntentosFallidos()
    {
        $this->increment('intentos_fallidos_login');
        
        // Bloquear después de 5 intentos
        if ($this->intentos_fallidos_login >= 5) {
            $this->bloqueado_hasta = now()->addMinutes(30);
            $this->save();
        }
    }

    /**
     * Resetear intentos fallidos
     */
    public function resetearIntentosFallidos()
    {
        $this->update([
            'intentos_fallidos_login' => 0,
            'bloqueado_hasta' => null,
        ]);
    }

    /**
     * Registrar último login
     */
    public function registrarLogin($ip = null)
    {
        $this->update([
            'ultimo_login' => now(),
            'ultimo_login_ip' => $ip,
            'intentos_fallidos_login' => 0,
            'bloqueado_hasta' => null,
        ]);
    }

    /**
     * Cambiar estado del usuario
     */
    public function cambiarEstado($nuevoEstado)
    {
        $estadosValidos = ['activo', 'inactivo', 'pendiente_activacion', 'suspendido'];
        
        if (!in_array($nuevoEstado, $estadosValidos)) {
            throw new \InvalidArgumentException("Estado inválido: {$nuevoEstado}");
        }

        $this->update(['estado' => $nuevoEstado]);
    }

    /**
     * Activar usuario
     */
    public function activar()
    {
        $this->cambiarEstado('activo');
    }

    /**
     * Desactivar usuario
     */
    public function desactivar()
    {
        $this->cambiarEstado('inactivo');
    }

    /**
     * Suspender usuario
     */
    public function suspender()
    {
        $this->cambiarEstado('suspendido');
    }
}