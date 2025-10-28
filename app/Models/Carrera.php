<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Carrera extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'carreras';

    protected $primaryKey = 'id';

    public $timestamps = true;  // Cambiado a true para usar timestamps personalizados

    protected $fillable = [
        'codigo',
        'nombre',
        'nombre_corto',
        'descripcion',
        'duracion_semestres',
        'esta_activo',
    ];

    // Especificar los nombres personalizados de las columnas de timestamps
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';

    protected $casts = [
        'esta_activo' => 'boolean',
        'duracion_semestres' => 'integer',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
        'fecha_eliminacion' => 'datetime',
    ];

    /**
     * Relación con Materias (solo activas)
     */
    public function materias()
    {
        return $this->hasMany(Materia::class, 'carrera_id');
    }
}