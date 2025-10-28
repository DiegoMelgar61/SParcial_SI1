<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'usuarios';
    protected $primary = 'id';
    
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

    // Método para especificar el nombre personalizado de la columna remember_token
    public function getRememberTokenName()
    {
        return 'token_recordar';
    }

    public function getAuthPassword()
    {
        return $this->attributes['contrasena'] ?? $this->attributes['password'] ?? null;
    }

    // Accessor para que Laravel pueda acceder al campo password usando contrasena
    public function getPasswordAttribute()
    {
        return $this->attributes['contrasena'] ?? null;
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }
}