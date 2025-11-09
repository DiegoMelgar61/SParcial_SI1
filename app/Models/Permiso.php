<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla en la base de datos
     */
    protected $table = 'permisos';

    /**
     * La clave primaria de la tabla
     */
    protected $primaryKey = 'id';

    /**
     * Indica si el modelo debe usar timestamps
     */
    public $timestamps = true;

    /**
     * Nombres personalizados de las columnas de timestamps
     */
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    /**
     * Los atributos que se pueden asignar masivamente
     */
    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'modulo',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos
     */
    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
    ];

    /**
     * Relación muchos a muchos con Rol
     * Un permiso puede estar asignado a muchos roles
     */
    public function roles()
    {
        return $this->belongsToMany(
            Rol::class,
            'rol_permiso',      // Tabla pivot
            'permiso_id',       // Foreign key en tabla pivot para este modelo
            'rol_id'            // Foreign key en tabla pivot para el modelo relacionado
        )->withPivot('fecha_creacion');
    }

    /**
     * Scope para filtrar permisos por módulo
     */
    public function scopePorModulo($query, $modulo)
    {
        return $query->where('modulo', $modulo);
    }

    /**
     * Scope para buscar permisos por nombre o slug
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where('nombre', 'LIKE', "%{$termino}%")
                     ->orWhere('slug', 'LIKE', "%{$termino}%")
                     ->orWhere('descripcion', 'LIKE', "%{$termino}%");
    }

    /**
     * Obtener todos los módulos únicos
     */
    public static function getModulosUnicos()
    {
        return self::distinct()
                   ->pluck('modulo')
                   ->filter()
                   ->sort()
                   ->values();
    }

    /**
     * Verificar si el permiso está asignado a algún rol
     */
    public function estaAsignado()
    {
        return $this->roles()->exists();
    }

    /**
     * Accessor para obtener el nombre formateado
     */
    public function getNombreFormateadoAttribute()
    {
        return ucfirst($this->nombre);
    }
}