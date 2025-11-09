<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles';
    
    protected $primaryKey = 'id';
    
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'descripcion',
        'esta_activo',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $casts = [
        'esta_activo' => 'boolean',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
    ];

    /**
     * Relación con Usuarios
     */
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'rol_id');
    }

    /**
     * Relación con Permisos (muchos a muchos)
     */
    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'rol_permiso', 'rol_id', 'permiso_id')
                    ->withPivot('fecha_creacion');
    }

    /**
     * Scope para roles activos
     */
    public function scopeActivos($query)
    {
        return $query->where('esta_activo', true);
    }

    /**
     * Verificar si el rol tiene un permiso específico
     */
    public function tienePermiso($permisoNombre)
    {
        return $this->permisos()->where('nombre', $permisoNombre)->exists();
    }

    /**
     * Verificar si el rol tiene alguno de los permisos dados
     */
    public function tieneAlgunPermiso($permisos)
    {
        return $this->permisos()->whereIn('nombre', $permisos)->exists();
    }

    /**
     * Verificar si el rol tiene todos los permisos dados
     */
    public function tieneTodosLosPermisos($permisos)
    {
        return $this->permisos()->whereIn('nombre', $permisos)->count() === count($permisos);
    }
}