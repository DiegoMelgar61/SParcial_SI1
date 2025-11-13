<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Modelo para gestionar las asistencias de los docentes
 *
 * Registra la asistencia diaria de los docentes a sus clases programadas,
 * incluyendo estados (presente, ausente, licencia, justificado),
 * tipos de ausencia y documentación de respaldo.
 */
class AsistenciaDocente extends Model
{
    use SoftDeletes;

    /**
     * Nombre de la tabla en la base de datos
     */
    protected $table = 'asistencias_docentes';

    /**
     * Constantes para los nombres de las columnas de timestamps
     */
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';

    /**
     * Estados válidos de asistencia
     */
    const ESTADO_PRESENTE = 'presente';
    const ESTADO_AUSENTE = 'ausente';
    const ESTADO_LICENCIA = 'licencia';
    const ESTADO_JUSTIFICADO = 'justificado';

    /**
     * Tipos de ausencia
     */
    const TIPO_NINGUNA = 'ninguna';
    const TIPO_ENFERMEDAD = 'enfermedad';
    const TIPO_PERSONAL = 'personal';
    const TIPO_OFICIAL = 'oficial';
    const TIPO_DUELO = 'duelo';
    const TIPO_OTRA = 'otra';

    /**
     * Atributos que son asignables en masa
     */
    protected $fillable = [
        'uuid',
        'docente_id',
        'horario_id',
        'fecha',
        'hora_registro',
        'estado',
        'tipo_ausencia',
        'observaciones',
        'documento_respaldo',
        'latitud',
        'longitud',
        'ip_registro',
        'registrado_por',
    ];

    /**
     * Conversión de atributos a tipos nativos
     */
    protected $casts = [
        'fecha' => 'date',
        'hora_registro' => 'datetime',
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
        'fecha_eliminacion' => 'datetime',
    ];

    /**
     * Boot del modelo para auto-generar UUID
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            if (empty($model->hora_registro)) {
                $model->hora_registro = now();
            }
        });
    }

    /**
     * Relación: Asistencia pertenece a un docente
     */
    public function docente()
    {
        return $this->belongsTo(Docente::class, 'docente_id');
    }

    /**
     * Relación: Asistencia pertenece a un horario
     */
    public function horario()
    {
        return $this->belongsTo(Horario::class, 'horario_id');
    }

    /**
     * Relación: Asistencia registrada por un usuario
     */
    public function registradoPor()
    {
        return $this->belongsTo(Usuario::class, 'registrado_por');
    }

    /**
     * Scope: Filtrar asistencias de hoy
     */
    public function scopeHoy($query)
    {
        return $query->whereDate('fecha', today());
    }

    /**
     * Scope: Filtrar por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope: Filtrar por docente
     */
    public function scopePorDocente($query, $docenteId)
    {
        return $query->where('docente_id', $docenteId);
    }

    /**
     * Scope: Filtrar entre fechas
     */
    public function scopeEntreFechas($query, $inicio, $fin)
    {
        return $query->whereBetween('fecha', [$inicio, $fin]);
    }

    /**
     * Scope: Filtrar por mes y año
     */
    public function scopePorMes($query, $mes, $anio)
    {
        return $query->whereMonth('fecha', $mes)
                     ->whereYear('fecha', $anio);
    }

    /**
     * Verificar si la asistencia es de hoy
     */
    public function esDeHoy()
    {
        return $this->fecha->isToday();
    }

    /**
     * Verificar si el docente está presente
     */
    public function estaPresente()
    {
        return $this->estado === self::ESTADO_PRESENTE;
    }

    /**
     * Verificar si tiene licencia
     */
    public function tieneLicencia()
    {
        return $this->estado === self::ESTADO_LICENCIA;
    }

    /**
     * Verificar si está ausente
     */
    public function estaAusente()
    {
        return $this->estado === self::ESTADO_AUSENTE;
    }

    /**
     * Verificar si está justificado
     */
    public function estaJustificado()
    {
        return $this->estado === self::ESTADO_JUSTIFICADO;
    }

    /**
     * Obtener badge HTML del estado con colores
     */
    public function getEstadoBadgeAttribute()
    {
        $badges = [
            self::ESTADO_PRESENTE => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Presente</span>',
            self::ESTADO_AUSENTE => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Ausente</span>',
            self::ESTADO_LICENCIA => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Licencia</span>',
            self::ESTADO_JUSTIFICADO => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Justificado</span>',
        ];

        return $badges[$this->estado] ?? '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Desconocido</span>';
    }

    /**
     * Obtener clase de color del badge según el estado
     */
    public function getEstadoColorAttribute()
    {
        $colores = [
            self::ESTADO_PRESENTE => 'green',
            self::ESTADO_AUSENTE => 'red',
            self::ESTADO_LICENCIA => 'yellow',
            self::ESTADO_JUSTIFICADO => 'blue',
        ];

        return $colores[$this->estado] ?? 'gray';
    }

    /**
     * Obtener el nombre legible del estado
     */
    public function getEstadoNombreAttribute()
    {
        $nombres = [
            self::ESTADO_PRESENTE => 'Presente',
            self::ESTADO_AUSENTE => 'Ausente',
            self::ESTADO_LICENCIA => 'Licencia',
            self::ESTADO_JUSTIFICADO => 'Justificado',
        ];

        return $nombres[$this->estado] ?? 'Desconocido';
    }

    /**
     * Obtener el nombre legible del tipo de ausencia
     */
    public function getTipoAusenciaNombreAttribute()
    {
        $nombres = [
            self::TIPO_NINGUNA => 'Ninguna',
            self::TIPO_ENFERMEDAD => 'Enfermedad',
            self::TIPO_PERSONAL => 'Personal',
            self::TIPO_OFICIAL => 'Oficial',
            self::TIPO_DUELO => 'Duelo',
            self::TIPO_OTRA => 'Otra',
        ];

        return $nombres[$this->tipo_ausencia] ?? 'Ninguna';
    }

    /**
     * Obtener todos los estados disponibles
     */
    public static function getEstados()
    {
        return [
            self::ESTADO_PRESENTE => 'Presente',
            self::ESTADO_AUSENTE => 'Ausente',
            self::ESTADO_LICENCIA => 'Licencia',
            self::ESTADO_JUSTIFICADO => 'Justificado',
        ];
    }

    /**
     * Obtener todos los tipos de ausencia disponibles
     */
    public static function getTiposAusencia()
    {
        return [
            self::TIPO_NINGUNA => 'Ninguna',
            self::TIPO_ENFERMEDAD => 'Enfermedad',
            self::TIPO_PERSONAL => 'Personal',
            self::TIPO_OFICIAL => 'Oficial',
            self::TIPO_DUELO => 'Duelo',
            self::TIPO_OTRA => 'Otra',
        ];
    }

    /**
     * Verificar si tiene documento de respaldo
     */
    public function tieneDocumento()
    {
        return !empty($this->documento_respaldo);
    }

    /**
     * Obtener la URL del documento de respaldo
     */
    public function getDocumentoUrlAttribute()
    {
        if ($this->documento_respaldo) {
            return asset('storage/' . $this->documento_respaldo);
        }
        return null;
    }
}