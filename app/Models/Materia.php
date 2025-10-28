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
        'sigla',
        'codigo',
        'nombre',
        'nombre_corto',
        'descripcion',
        'semestre',
        'horas_semanales',
        'creditos',
        'es_electiva',
        'requiere_laboratorio',
        'esta_activo',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';

    protected $casts = [
        'es_electiva' => 'boolean',
        'requiere_laboratorio' => 'boolean',
        'esta_activo' => 'boolean',
        'creditos' => 'integer',
        'horas_semanales' => 'integer',
        'semestre' => 'integer',
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

    /**
     * Relación con Grupos
     */
    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'materia_id');
    }
}