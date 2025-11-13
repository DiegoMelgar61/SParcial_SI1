<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gestiones_academicas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 255);
            $table->integer('anio');
            $table->integer('semestre'); // 1 o 2
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->date('fecha_inicio_inscripciones')->nullable();
            $table->date('fecha_fin_inscripciones')->nullable();
            $table->date('fecha_inicio_clases')->nullable();
            $table->date('fecha_fin_clases')->nullable();
            $table->enum('estado', ['planificacion', 'inscripciones', 'activa', 'finalizada', 'cancelada'])->default('planificacion');
            $table->boolean('esta_activo')->default(true);
            $table->text('observaciones')->nullable();
            $table->foreignId('creado_por')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_actualizacion')->useCurrent()->useCurrentOnUpdate();

            // Índices
            $table->index(['anio', 'semestre']);
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gestiones_academicas');
    }
};
