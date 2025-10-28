<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Docente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'docentes';
    
    protected $primaryKey = 'id';
    
    public $timestamps = true;

    protected $fillable = [
        'usuario_id',
        'carrera_id',
        'titulo_academico',
        'especializacion',
        'codigo_empleado',
        'fecha_contratacion',
        'horas_semanales_max',
        'horas_semanales_actuales',
        'tipo_contrato',
        'turnos_preferidos',
        'observaciones',
        'esta_activo',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';

    protected $casts = [
        'esta_activo' => 'boolean',
        'horas_semanales_max' => 'integer',
        'horas_semanales_actuales' => 'integer',
        'fecha_contratacion' => 'date',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
        'fecha_eliminacion' => 'datetime',
    ];

    /**
     * Tipos de contrato disponibles
     */
    public static function getTiposContrato()
    {
        return [
            'tiempo_completo' => 'Tiempo Completo',
            'medio_tiempo' => 'Medio Tiempo',
            'por_horas' => 'Por Horas',
            'invitado' => 'Docente Invitado',
        ];
    }

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
     * Relación con Usuario
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Relación con Carrera
     */
    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'carrera_id');
    }

    /**
     * Relación con Grupos (materias que dicta)
     */
    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'docente_id');
    }

    /**
     * Accessor para obtener el nombre completo del docente
     */
    public function getNombreCompletoAttribute()
    {
        return $this->usuario ? $this->usuario->nombre . ' ' . $this->usuario->apellido : '';
    }

    /**
     * Accessor para obtener el tipo de contrato formateado
     */
    public function getTipoContratoNombreAttribute()
    {
        $tipos = self::getTiposContrato();
        return $tipos[$this->tipo_contrato] ?? $this->tipo_contrato;
    }

    /**
     * Scope para docentes activos
     */
    public function scopeActivos($query)
    {
        return $query->where('esta_activo', true);
    }

    /**
     * Mutator: convierte array PHP a literal de arreglo de PostgreSQL (text[])
     */
    public function setTurnosPreferidosAttribute($value)
    {
        if (is_array($value)) {
            $escaped = array_map(function ($v) {
                $v = (string) $v;
                $v = str_replace(['\\', '"'], ['\\\\', '\\"'], $v);
                return '"' . $v . '"';
            }, $value);
            $this->attributes['turnos_preferidos'] = '{' . implode(',', $escaped) . '}';
        } elseif (is_string($value) && str_starts_with($value, '{') && str_ends_with($value, '}')) {
            $this->attributes['turnos_preferidos'] = $value;
        } else {
            $this->attributes['turnos_preferidos'] = null;
        }
    }

    /**
     * Accessor: convierte literal de arreglo PostgreSQL a array PHP
     */
    public function getTurnosPreferidosAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }
        if ($value === null) {
            return [];
        }
        $trimmed = trim($value, '{}');
        if ($trimmed === '') {
            return [];
        }
        // Separar por comas ignorando comas dentro de comillas
        $parts = preg_split('/,(?=(?:[^\"]*\"[^\"]*\")*[^\"]*$)/', $trimmed);
        $result = array_map(function ($item) {
            $item = trim($item);
            if (strlen($item) >= 2 && $item[0] === '"' && substr($item, -1) === '"') {
                $item = substr($item, 1, -1);
            }
            $item = str_replace(['\\"', '\\\\'], ['"', '\\'], $item);
            return $item;
        }, $parts);
        return $result;
    }
}
