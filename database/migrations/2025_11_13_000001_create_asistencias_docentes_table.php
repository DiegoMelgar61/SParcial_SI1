<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para crear la tabla de asistencias de docentes
 *
 * Esta tabla registra la asistencia diaria de los docentes a sus clases,
 * incluyendo estados (presente, ausente, licencia, justificado),
 * tipos de ausencia y documentación de respaldo.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asistencias_docentes', function (Blueprint $table) {
            // Identificadores
            $table->id();
            $table->uuid('uuid')->unique();

            // Relaciones
            $table->foreignId('docente_id')
                  ->constrained('docentes')
                  ->onDelete('cascade')
                  ->comment('ID del docente que registra asistencia');

            $table->foreignId('horario_id')
                  ->constrained('horarios')
                  ->onDelete('cascade')
                  ->comment('ID del horario de clase');

            // Datos de asistencia
            $table->date('fecha')->comment('Fecha de la asistencia');
            $table->timestamp('hora_registro')->comment('Hora exacta del registro');

            // Estado de asistencia (enum simulado con string)
            $table->enum('estado', ['presente', 'ausente', 'licencia', 'justificado'])
                  ->default('presente')
                  ->comment('Estado de asistencia del docente');

            // Tipo de ausencia (enum simulado con string)
            $table->enum('tipo_ausencia', ['ninguna', 'enfermedad', 'personal', 'oficial', 'duelo', 'otra'])
                  ->default('ninguna')
                  ->comment('Tipo de ausencia cuando no está presente');

            // Información adicional
            $table->text('observaciones')->nullable()->comment('Observaciones adicionales');
            $table->string('documento_respaldo')->nullable()->comment('Path al archivo de respaldo');

            // Datos de geolocalización
            $table->decimal('latitud', 10, 8)->nullable()->comment('Latitud de registro GPS');
            $table->decimal('longitud', 11, 8)->nullable()->comment('Longitud de registro GPS');

            // Metadatos
            $table->string('ip_registro', 45)->nullable()->comment('IP desde donde se registró');
            $table->foreignId('registrado_por')
                  ->nullable()
                  ->constrained('usuarios')
                  ->onDelete('set null')
                  ->comment('Usuario que realizó el registro');

            // Timestamps y soft deletes
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('fecha_eliminacion')->nullable();

            // Índices para mejorar el rendimiento de consultas
            $table->index(['docente_id', 'fecha'], 'idx_docente_fecha');
            $table->index(['horario_id', 'fecha'], 'idx_horario_fecha');
            $table->index('estado', 'idx_estado');
            $table->index('fecha', 'idx_fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias_docentes');
    }
};