<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grupo extends Model
{
    use HasFactory, SoftDeletes, Auditable; // ← AGREGADO TRAIT

    protected $table = 'grupos';
    
    protected $primaryKey = 'id';
    
    public $timestamps = true;

    protected $fillable = [
        'gestion_academica_id',
        'carrera_id',
        'sigla',
        'codigo',
        'nombre',
        'semestre',
        'capacidad',
        'cantidad_inscritos',
        'turno',
        'esta_activo',
        'observaciones',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';

    protected $casts = [
        'esta_activo' => 'boolean',
        'semestre' => 'integer',
        'capacidad' => 'integer',
        'cantidad_inscritos' => 'integer',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
        'fecha_eliminacion' => 'datetime',
    ];

    /**
     * Turnos disponibles
     */
    public static function getTurnos()
    {
        return [
            'manana' => 'Mañana',
            'tarde' => 'Tarde',
            'noche' => 'Noche',
        ];
    }

    /**
     * Relación con Gestión Académica
     */
    public function gestionAcademica()
    {
        return $this->belongsTo(GestionAcademica::class, 'gestion_academica_id');
    }

    /**
     * Relación con Carrera
     */
    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'carrera_id');
    }

    /**
     * Relación con Materias (muchos a muchos)
     */
    public function materias()
    {
        return $this->belongsToMany(Materia::class, 'grupo_materia', 'grupo_id', 'materia_id')
                    ->withPivot('horas_asignadas')
                    ->withTimestamps();
    }

    /**
     * Relación con Horarios
     */
    public function horarios()
    {
        return $this->hasMany(Horario::class, 'grupo_id');
    }

    /**
     * Accessor para obtener el turno formateado
     */
    public function getTurnoNombreAttribute()
    {
        $turnos = self::getTurnos();
        return $turnos[$this->turno] ?? $this->turno;
    }

    /**
     * Scope para grupos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('esta_activo', true);
    }

    /**
     * Verificar si el grupo está lleno
     */
    public function estaLleno()
    {
        return $this->cantidad_inscritos >= $this->capacidad;
    }

    /**
     * Obtener cupos disponibles
     */
    public function getCuposDisponiblesAttribute()
    {
        return $this->capacidad - $this->cantidad_inscritos;
    }
}