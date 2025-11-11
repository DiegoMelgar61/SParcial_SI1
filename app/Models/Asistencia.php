<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Asistencia extends Model
{
    use Auditable;

    protected $table = 'asistencias';
    
    protected $primaryKey = 'id';
    
    public $timestamps = true;

    protected $fillable = [
        'horario_id',
        'estudiante_id',
        'fecha',
        'estado',
        'observaciones',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $casts = [
        'fecha' => 'date',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
    ];

    /**
     * Relación con Horario
     */
    public function horario()
    {
        return $this->belongsTo(Horario::class, 'horario_id');
    }

    /**
     * Relación con Estudiante (Usuario)
     */
    public function estudiante()
    {
        return $this->belongsTo(Usuario::class, 'estudiante_id');
    }
}
