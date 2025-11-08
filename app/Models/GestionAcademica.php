<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GestionAcademica extends Model
{
    use HasFactory;

    protected $table = 'gestiones_academicas';
    
    protected $primaryKey = 'id';
    
    public $timestamps = true;

    protected $fillable = [
        'codigo',
        'nombre',
        'anio',
        'semestre',
        'fecha_inicio',
        'fecha_fin',
        'fecha_inicio_inscripciones',
        'fecha_fin_inscripciones',
        'fecha_inicio_clases',
        'fecha_fin_clases',
        'estado',
        'esta_activo',
        'observaciones',
        'creado_por',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $casts = [
        'esta_activo' => 'boolean',
        'anio' => 'integer',
        'semestre' => 'integer',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_inicio_inscripciones' => 'date',
        'fecha_fin_inscripciones' => 'date',
        'fecha_inicio_clases' => 'date',
        'fecha_fin_clases' => 'date',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
    ];

    /**
     * Estados disponibles
     */
    public static function getEstados()
    {
        return [
            'planificacion' => 'En Planificación',
            'inscripciones' => 'Inscripciones Abiertas',
            'activa' => 'Activa',
            'finalizada' => 'Finalizada',
            'cancelada' => 'Cancelada',
        ];
    }

    /**
     * Semestres disponibles
     */
    public static function getSemestres()
    {
        return [
            1 => 'Primer Semestre',
            2 => 'Segundo Semestre',
        ];
    }

    /**
     * Relación con Usuario que lo creó
     */
    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'creado_por');
    }

    /**
     * Relación con Grupos
     */
    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'gestion_academica_id');
    }

    /**
     * Scope para gestión activa
     */
    public function scopeActiva($query)
    {
        return $query->where('esta_activo', true);
    }

    /**
     * Scope para gestión en curso
     */
    public function scopeEnCurso($query)
    {
        return $query->where('estado', 'activa')
                    ->where('esta_activo', true);
    }

    /**
     * Accessor para obtener el nombre del estado
     */
    public function getEstadoNombreAttribute()
    {
        $estados = self::getEstados();
        return $estados[$this->estado] ?? $this->estado;
    }

    /**
     * Accessor para obtener el nombre del semestre
     */
    public function getSemestreNombreAttribute()
    {
        $semestres = self::getSemestres();
        return $semestres[$this->semestre] ?? "Semestre {$this->semestre}";
    }
}