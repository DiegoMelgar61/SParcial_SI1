<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Docente;
use App\Models\Carrera;

/**
 * Seeder para crear un usuario docente de prueba
 *
 * Crea:
 * - Rol "Docente" si no existe
 * - Usuario con email: docente@ficct.uagrm.edu.bo
 * - Perfil de docente asociado al usuario
 * - Carrera de prueba si no existe ninguna
 */
class DocentePruebaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear o obtener el rol "Docente"
        $rolDocente = Rol::firstOrCreate(
            ['nombre' => 'Docente'],
            [
                'uuid' => (string) Str::uuid(),
                'descripcion' => 'Rol para docentes con acceso a registro de asistencia y consulta de horarios',
                'nivel_acceso' => 2,
                'es_sistema' => true,
                'esta_activo' => true,
            ]
        );

        $this->command->info('✓ Rol Docente creado/verificado');

        // 2. Crear o obtener una carrera de prueba
        $carrera = Carrera::first();

        if (!$carrera) {
            $carrera = Carrera::create([
                'uuid' => (string) Str::uuid(),
                'nombre' => 'Ingeniería de Sistemas',
                'sigla' => 'IS',
                'descripcion' => 'Carrera de Ingeniería de Sistemas',
                'duracion_semestres' => 10,
                'esta_activo' => true,
            ]);
            $this->command->info('✓ Carrera de prueba creada');
        } else {
            $this->command->info('✓ Carrera existente encontrada: ' . $carrera->nombre);
        }

        // 3. Verificar si ya existe el usuario docente
        $usuarioExistente = Usuario::where('email', 'docente@ficct.uagrm.edu.bo')->first();

        if ($usuarioExistente) {
            $this->command->warn('⚠ El usuario docente ya existe');
            return;
        }

        // 4. Crear el usuario docente
        $usuario = Usuario::create([
            'uuid' => (string) Str::uuid(),
            'rol_id' => $rolDocente->id,
            'nombre' => 'Juan Carlos',
            'apellido' => 'Pérez García',
            'cedula_identidad' => '1234567',
            'email' => 'docente@ficct.uagrm.edu.bo',
            'telefono' => '70123456',
            'contrasena' => Hash::make('docente123'),
            'debe_cambiar_contrasena' => false,
            'estado' => 'activo',
        ]);

        $this->command->info('✓ Usuario docente creado');

        // 5. Crear el perfil de docente asociado al usuario
        $docente = Docente::create([
            'usuario_id' => $usuario->id,
            'carrera_id' => $carrera->id,
            'titulo_academico' => 'Magister en Ingeniería de Sistemas',
            'especializacion' => 'Desarrollo de Software',
            'codigo_empleado' => 'DOC-' . date('Y') . '-001',
            'fecha_contratacion' => now()->subYears(5),
            'horas_semanales_max' => 40,
            'tipo_contrato' => 'tiempo_completo',
            'turnos_preferidos' => 'mañana,tarde',
            'esta_activo' => true,
        ]);

        $this->command->info('✓ Perfil de docente creado');

        // Resumen
        $this->command->line('');
        $this->command->line('════════════════════════════════════════════════════════');
        $this->command->info('  USUARIO DOCENTE DE PRUEBA CREADO EXITOSAMENTE');
        $this->command->line('════════════════════════════════════════════════════════');
        $this->command->line('');
        $this->command->line('  Email:     docente@ficct.uagrm.edu.bo');
        $this->command->line('  Contraseña: docente123');
        $this->command->line('  Nombre:    ' . $usuario->nombre . ' ' . $usuario->apellido);
        $this->command->line('  Rol:       ' . $rolDocente->nombre);
        $this->command->line('  Carrera:   ' . $carrera->nombre);
        $this->command->line('');
        $this->command->line('════════════════════════════════════════════════════════');
    }
}
