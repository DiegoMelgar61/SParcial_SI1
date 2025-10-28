<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Materia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'materias';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'carrera_id',
        'codigo',
        'nombre',
        'nombre_corto',
        'creditos',
        'horas_teoricas',
        'horas_practicas',
        'prerequisitos',
        'esta_activo',
    ];

    // Nombres personalizados de timestamps
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';

    protected $casts = [
        'esta_activo' => 'boolean',
        'creditos' => 'integer',
        'horas_teoricas' => 'integer',
        'horas_practicas' => 'integer',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
        'fecha_eliminacion' => 'datetime',
    ];

    /**
     * Relación con Carrera
     */
    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'carrera_id');
    }
}
