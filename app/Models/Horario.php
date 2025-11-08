<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Horario extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'horarios';
    
    protected $primaryKey = 'id';
    
    public $timestamps = true;

    protected $fillable = [
        'gestion_academica_id',
        'grupo_id',
        'materia_id',
        'docente_id',
        'aula_id',
        'bloque_horario_id',
        'dia',
        'esta_publicado',
        'esta_confirmado',
        'observaciones',
        'creado_por',
        'actualizado_por',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';

    protected $casts = [
        'esta_publicado' => 'boolean',
        'esta_confirmado' => 'boolean',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
        'fecha_eliminacion' => 'datetime',
    ];

    /**
     * Días de la semana disponibles
     */
    public static function getDiasSemana()
    {
        return [
            'lunes' => 'Lunes',
            'martes' => 'Martes',
            'miercoles' => 'Miércoles',
            'jueves' => 'Jueves',
            'viernes' => 'Viernes',
            'sabado' => 'Sábado',
            'domingo' => 'Domingo',
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
     * Relación con Grupo
     */
    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }

    /**
     * Relación con Materia
     */
    public function materia()
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }

    /**
     * Relación con Docente
     */
    public function docente()
    {
        return $this->belongsTo(Docente::class, 'docente_id');
    }

    /**
     * Relación con Aula
     */
    public function aula()
    {
        return $this->belongsTo(Aula::class, 'aula_id');
    }

    /**
     * Relación con Bloque Horario
     */
    public function bloqueHorario()
    {
        return $this->belongsTo(BloqueHorario::class, 'bloque_horario_id');
    }

    /**
     * Accessor para obtener el día formateado
     */
    public function getDiaNombreAttribute()
    {
        $dias = self::getDiasSemana();
        return $dias[$this->dia] ?? $this->dia;
    }

    /**
     * Accessor para obtener hora_inicio desde bloque_horario
     */
    public function getHoraInicioAttribute()
    {
        if ($this->bloqueHorario) {
            return substr($this->bloqueHorario->hora_inicio, 0, 5);
        }
        return null;
    }

    /**
     * Accessor para obtener hora_fin desde bloque_horario
     */
    public function getHoraFinAttribute()
    {
        if ($this->bloqueHorario) {
            return substr($this->bloqueHorario->hora_fin, 0, 5);
        }
        return null;
    }

    /**
     * Verificar si hay cruce con otro horario del mismo docente
     */
    public static function verificarCruceDocente($docente_id, $dia, $bloque_horario_id, $horario_id = null)
    {
        $query = self::where('docente_id', $docente_id)
                    ->where('dia', $dia)
                    ->where('bloque_horario_id', $bloque_horario_id);

        if ($horario_id) {
            $query->where('id', '!=', $horario_id);
        }

        return $query->exists();
    }

    /**
     * Verificar si hay cruce con otro horario en la misma aula
     */
    public static function verificarCruceAula($aula_id, $dia, $bloque_horario_id, $horario_id = null)
    {
        $query = self::where('aula_id', $aula_id)
                    ->where('dia', $dia)
                    ->where('bloque_horario_id', $bloque_horario_id);

        if ($horario_id) {
            $query->where('id', '!=', $horario_id);
        }

        return $query->exists();
    }

    /**
     * Verificar si hay cruce con otro horario del mismo grupo
     */
    public static function verificarCruceGrupo($grupo_id, $dia, $bloque_horario_id, $horario_id = null)
    {
        $query = self::where('grupo_id', $grupo_id)
                    ->where('dia', $dia)
                    ->where('bloque_horario_id', $bloque_horario_id);

        if ($horario_id) {
            $query->where('id', '!=', $horario_id);
        }

        return $query->exists();
    }

    /**
     * Scope para horarios de un grupo específico
     */
    public function scopePorGrupo($query, $grupo_id)
    {
        return $query->where('grupo_id', $grupo_id);
    }

    /**
     * Scope para horarios de un docente específico
     */
    public function scopePorDocente($query, $docente_id)
    {
        return $query->where('docente_id', $docente_id);
    }

    /**
     * Scope para horarios en un aula específica
     */
    public function scopePorAula($query, $aula_id)
    {
        return $query->where('aula_id', $aula_id);
    }
}