<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloqueHorario extends Model
{
    use HasFactory;

    protected $table = 'bloques_horarios';
    
    protected $primaryKey = 'id';
    
    public $timestamps = true;

    protected $fillable = [
        'gestion_academica_id',
        'nombre',
        'hora_inicio',
        'hora_fin',
        'duracion_minutos',
        'orden',
        'esta_activo',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $casts = [
        'esta_activo' => 'boolean',
        'duracion_minutos' => 'integer',
        'orden' => 'integer',
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
    ];

    /**
     * Relación con Gestión Académica
     */
    public function gestionAcademica()
    {
        return $this->belongsTo(GestionAcademica::class, 'gestion_academica_id');
    }

    /**
     * Relación con Horarios
     */
    public function horarios()
    {
        return $this->hasMany(Horario::class, 'bloque_horario_id');
    }

    /**
     * Scope para bloques activos
     */
    public function scopeActivos($query)
    {
        return $query->where('esta_activo', true);
    }

    /**
     * Scope ordenado
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('orden')->orderBy('hora_inicio');
    }
}