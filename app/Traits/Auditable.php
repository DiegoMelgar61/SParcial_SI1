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
            // Si hay una transacción activa, ejecutar después del commit
            // Si no hay transacción, ejecutar inmediatamente
            if (\Illuminate\Support\Facades\DB::transactionLevel() > 0) {
                \Illuminate\Support\Facades\DB::afterCommit(function () use ($modelo) {
                    try {
                        $datosNuevos = $modelo->getAuditableAttributes();
                        AuditoriaService::logCreate(
                            $modelo,
                            $modelo->getTable(),
                            $datosNuevos
                        );
                    } catch (\Exception $e) {
                        \Log::error('Error en auditoría created: ' . $e->getMessage());
                    }
                });
            } else {
                // No hay transacción activa, ejecutar inmediatamente
                try {
                    $datosNuevos = $modelo->getAuditableAttributes();
                    AuditoriaService::logCreate(
                        $modelo,
                        $modelo->getTable(),
                        $datosNuevos
                    );
                } catch (\Exception $e) {
                    \Log::error('Error en auditoría created: ' . $e->getMessage());
                }
            }
        });

        // Registrar cuando se actualiza un registro
        static::updated(function ($modelo) {
            // Si hay una transacción activa, ejecutar después del commit
            // Si no hay transacción, ejecutar inmediatamente
            if (\Illuminate\Support\Facades\DB::transactionLevel() > 0) {
                \Illuminate\Support\Facades\DB::afterCommit(function () use ($modelo) {
                    try {
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
                    } catch (\Exception $e) {
                        \Log::error('Error en auditoría updated: ' . $e->getMessage());
                    }
                });
            } else {
                // No hay transacción activa, ejecutar inmediatamente
                try {
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
                } catch (\Exception $e) {
                    \Log::error('Error en auditoría updated: ' . $e->getMessage());
                }
            }
        });

        // Registrar cuando se elimina un registro
        static::deleted(function ($modelo) {
            // Si hay una transacción activa, ejecutar después del commit
            // Si no hay transacción, ejecutar inmediatamente
            if (\Illuminate\Support\Facades\DB::transactionLevel() > 0) {
                \Illuminate\Support\Facades\DB::afterCommit(function () use ($modelo) {
                    try {
                        $datosAnteriores = $modelo->getAuditableAttributes();
                        AuditoriaService::logDelete(
                            $modelo,
                            $modelo->getTable(),
                            $datosAnteriores
                        );
                    } catch (\Exception $e) {
                        \Log::error('Error en auditoría deleted: ' . $e->getMessage());
                    }
                });
            } else {
                // No hay transacción activa, ejecutar inmediatamente
                try {
                    $datosAnteriores = $modelo->getAuditableAttributes();
                    AuditoriaService::logDelete(
                        $modelo,
                        $modelo->getTable(),
                        $datosAnteriores
                    );
                } catch (\Exception $e) {
                    \Log::error('Error en auditoría deleted: ' . $e->getMessage());
                }
            }
        });

        // Registrar cuando se restaura un registro (soft delete)
        // Solo si el modelo usa SoftDeletes
        if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive(static::class))) {
            static::restored(function ($modelo) {
                // Si hay una transacción activa, ejecutar después del commit
                // Si no hay transacción, ejecutar inmediatamente
                if (\Illuminate\Support\Facades\DB::transactionLevel() > 0) {
                    \Illuminate\Support\Facades\DB::afterCommit(function () use ($modelo) {
                        try {
                            AuditoriaService::log([
                                'accion' => 'restore',
                                'tabla' => $modelo->getTable(),
                                'registro_id' => $modelo->id,
                                'datos_nuevos' => $modelo->getAuditableAttributes(),
                            ]);
                        } catch (\Exception $e) {
                            \Log::error('Error en auditoría restored: ' . $e->getMessage());
                        }
                    });
                } else {
                    // No hay transacción activa, ejecutar inmediatamente
                    try {
                        AuditoriaService::log([
                            'accion' => 'restore',
                            'tabla' => $modelo->getTable(),
                            'registro_id' => $modelo->id,
                            'datos_nuevos' => $modelo->getAuditableAttributes(),
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Error en auditoría restored: ' . $e->getMessage());
                    }
                }
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