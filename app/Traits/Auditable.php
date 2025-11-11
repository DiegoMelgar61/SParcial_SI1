<?php

namespace App\Traits;

use App\Services\AuditoriaService;

trait Auditable
{
    /**
     * Boot del trait
     */
    protected static function bootAuditable()
    {
        // Registrar cuando se crea un registro
        static::created(function ($modelo) {
            $datosNuevos = $modelo->getAuditableAttributes();
            
            AuditoriaService::logCreate(
                $modelo,
                $modelo->getTable(),
                $datosNuevos
            );
        });

        // Registrar cuando se actualiza un registro
        static::updated(function ($modelo) {
            $datosAnteriores = [];
            $datosNuevos = [];

            // Obtener solo los campos que cambiaron
            foreach ($modelo->getDirty() as $campo => $valorNuevo) {
                // Excluir campos sensibles
                if (!in_array($campo, $modelo->getHiddenAudit())) {
                    $datosAnteriores[$campo] = $modelo->getOriginal($campo);
                    $datosNuevos[$campo] = $valorNuevo;
                }
            }

            // Solo registrar si hubo cambios
            if (!empty($datosNuevos)) {
                AuditoriaService::logUpdate(
                    $modelo,
                    $modelo->getTable(),
                    $datosAnteriores,
                    $datosNuevos
                );
            }
        });

        // Registrar cuando se elimina un registro
        static::deleted(function ($modelo) {
            $datosAnteriores = $modelo->getAuditableAttributes();
            
            AuditoriaService::logDelete(
                $modelo,
                $modelo->getTable(),
                $datosAnteriores
            );
        });

        // Registrar cuando se restaura un registro (soft delete)
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($modelo) {
                AuditoriaService::log([
                    'accion' => 'restore',
                    'tabla' => $modelo->getTable(),
                    'registro_id' => $modelo->id,
                    'datos_nuevos' => $modelo->getAuditableAttributes(),
                ]);
            });
        }
    }

    /**
     * Obtener atributos auditables del modelo
     */
    protected function getAuditableAttributes()
    {
        $attributes = $this->getAttributes();
        
        // Excluir campos ocultos y timestamps
        $exclude = array_merge(
            $this->getHiddenAudit(),
            ['fecha_creacion', 'fecha_actualizacion', 'fecha_eliminacion', 'created_at', 'updated_at', 'deleted_at']
        );

        return array_diff_key($attributes, array_flip($exclude));
    }

    /**
     * Campos que NO deben auditarse
     * Los modelos pueden sobrescribir este método
     */
    protected function getHiddenAudit()
    {
        return array_merge(
            $this->hidden ?? [],
            ['contrasena', 'password', 'token_recordar', 'remember_token']
        );
    }
}