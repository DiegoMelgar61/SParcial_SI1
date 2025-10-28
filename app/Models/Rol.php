<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles';
    
    protected $fillable = ['nombre', 'slug', 'descripcion', 'nivel', 'es_sistema'];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'rol_id');
    }
}