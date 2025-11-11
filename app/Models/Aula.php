<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Traits\Auditable;

class Aula extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'aulas';
    
    protected $primaryKey = 'id';
    
    public $timestamps = true;

    protected $fillable = [
        'codigo',
        'nombre',
        'piso',
        'tipo',
        'capacidad',
        'codigo_qr',
        'fecha_regeneracion_qr',
        'tiene_computadoras',
        'cantidad_computadoras',
        'observaciones',
        'esta_activo',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';

    protected $casts = [
        'tiene_computadoras' => 'boolean',
        'esta_activo' => 'boolean',
        'piso' => 'integer',
        'capacidad' => 'integer',
        'cantidad_computadoras' => 'integer',
        'fecha_regeneracion_qr' => 'datetime',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
        'fecha_eliminacion' => 'datetime',
    ];

    /**
     * Tipos de aula disponibles
     */
    public static function getTiposAula()
    {
        return [
            'teorica' => 'Aula Teórica',
            'laboratorio' => 'Laboratorio',
            'auditorio' => 'Auditorio',
            'sala_computo' => 'Sala de Cómputo',
        ];
    }

    /**
     * Boot method para generar código QR automáticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($aula) {
            if (empty($aula->codigo_qr)) {
                $aula->codigo_qr = Str::uuid();
                $aula->fecha_regeneracion_qr = now();
            }
        });
    }

    /**
     * Regenerar código QR
     */
    public function regenerarCodigoQR()
    {
        $this->codigo_qr = Str::uuid();
        $this->fecha_regeneracion_qr = now();
        $this->save();
    }

    /**
     * Relación con Horarios (si existe)
     */
    public function horarios()
    {
        return $this->hasMany(Horario::class, 'aula_id');
    }

    /**
     * Accessor para obtener el nombre del tipo
     */
    public function getTipoNombreAttribute()
    {
        $tipos = self::getTiposAula();
        return $tipos[$this->tipo] ?? $this->tipo;
    }
}