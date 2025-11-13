<?php

namespace App\Http\Controllers;

use App\Models\AsistenciaDocente;
use App\Models\Docente;
use App\Models\Horario;
use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Carbon\Carbon;

/**
 * Controlador para gestionar las asistencias de los docentes
 *
 * Maneja el registro de asistencia diaria de docentes,
 * consulta de historial, estadísticas y materias asignadas
 */
class AsistenciaDocenteController extends Controller
{
    /**
     * Mostrar lista de asistencias del docente autenticado
     */
    public function index(Request $request)
    {
        try {
            // Obtener el usuario autenticado
            $usuario = Auth::user();

            // Obtener el perfil de docente asociado
            $docente = Docente::where('usuario_id', $usuario->id)->first();

            if (!$docente) {
                return redirect()->route('dashboard')
                    ->with('error', 'No se encontró un perfil de docente asociado a su usuario.');
            }

            // Obtener filtros de la request
            $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
            $fechaFin = $request->input('fecha_fin', now()->endOfMonth()->format('Y-m-d'));
            $estado = $request->input('estado', null);

            // Query base de asistencias
            $query = AsistenciaDocente::query()
                ->where('docente_id', $docente->id)
                ->with(['horario.materia', 'horario.grupo', 'horario.aula'])
                ->entreFechas($fechaInicio, $fechaFin);

            // Aplicar filtro de estado si existe
            if ($estado && $estado !== 'todos') {
                $query->where('estado', $estado);
            }

            // Obtener asistencias paginadas
            $asistencias = $query->orderBy('fecha', 'desc')
                ->orderBy('hora_registro', 'desc')
                ->paginate(15)
                ->withQueryString();

            // Calcular estadísticas
            $estadisticas = $this->obtenerEstadisticas($docente->id, $fechaInicio, $fechaFin);

            // Obtener lista de estados disponibles
            $estados = AsistenciaDocente::getEstados();

            return Inertia::render('AsistenciaDocente/Index', [
                'asistencias' => $asistencias,
                'estadisticas' => $estadisticas,
                'filtros' => [
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin,
                    'estado' => $estado,
                ],
                'estados' => $estados,
                'docente' => $docente,
            ]);

        } catch (\Exception $e) {
            return redirect()->route('dashboard')
                ->with('error', 'Error al cargar las asistencias: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario para registrar nueva asistencia
     */
    public function create()
    {
        try {
            // Obtener el usuario autenticado
            $usuario = Auth::user();

            // Obtener el perfil de docente asociado
            $docente = Docente::where('usuario_id', $usuario->id)->first();

            if (!$docente) {
                return redirect()->route('dashboard')
                    ->with('error', 'No se encontró un perfil de docente asociado a su usuario.');
            }

            // Obtener horarios del día de hoy para el docente
            $horariosHoy = $this->obtenerHorariosHoy($docente->id);

            // Obtener asistencias ya registradas hoy
            $asistenciasRegistradas = AsistenciaDocente::where('docente_id', $docente->id)
                ->whereDate('fecha', today())
                ->pluck('horario_id')
                ->toArray();

            // Filtrar horarios que aún no tienen asistencia registrada
            $horariosDisponibles = $horariosHoy->filter(function ($horario) use ($asistenciasRegistradas) {
                return !in_array($horario->id, $asistenciasRegistradas);
            });

            // Obtener tipos de ausencia
            $tiposAusencia = AsistenciaDocente::getTiposAusencia();

            // Obtener estados disponibles
            $estados = $this->getEstadosDisponibles();

            return Inertia::render('AsistenciaDocente/Create', [
                'horarios' => $horariosDisponibles->values(),
                'asistenciasRegistradas' => $asistenciasRegistradas,
                'tiposAusencia' => $tiposAusencia,
                'estados' => $estados,
                'docente' => $docente,
            ]);

        } catch (\Exception $e) {
            return redirect()->route('asistencia-docente.index')
                ->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Guardar nueva asistencia
     */
    public function store(Request $request)
    {
        try {
            // Obtener el usuario autenticado
            $usuario = Auth::user();

            // Obtener el perfil de docente asociado
            $docente = Docente::where('usuario_id', $usuario->id)->first();

            if (!$docente) {
                return back()->with('error', 'No se encontró un perfil de docente asociado a su usuario.');
            }

            // Validar datos
            $validated = $request->validate([
                'horario_id' => 'required|exists:horarios,id',
                'estado' => 'required|in:presente,ausente,licencia,justificado',
                'tipo_ausencia' => 'required_unless:estado,presente|in:ninguna,enfermedad,personal,oficial,duelo,otra',
                'observaciones' => 'nullable|string|max:1000',
                'documento_respaldo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'latitud' => 'nullable|numeric|between:-90,90',
                'longitud' => 'nullable|numeric|between:-180,180',
            ], [
                'horario_id.required' => 'Debe seleccionar un horario.',
                'horario_id.exists' => 'El horario seleccionado no es válido.',
                'estado.required' => 'Debe seleccionar un estado de asistencia.',
                'estado.in' => 'El estado seleccionado no es válido.',
                'tipo_ausencia.required_unless' => 'Debe especificar el tipo de ausencia.',
                'tipo_ausencia.in' => 'El tipo de ausencia seleccionado no es válido.',
                'documento_respaldo.file' => 'El documento debe ser un archivo válido.',
                'documento_respaldo.mimes' => 'El documento debe ser PDF, JPG, JPEG o PNG.',
                'documento_respaldo.max' => 'El documento no puede superar los 2MB.',
                'observaciones.max' => 'Las observaciones no pueden superar los 1000 caracteres.',
            ]);

            // Verificar que el horario pertenece al docente
            $horario = Horario::where('id', $validated['horario_id'])
                ->where('docente_id', $docente->id)
                ->first();

            if (!$horario) {
                return back()->with('error', 'El horario seleccionado no está asignado a su perfil de docente.');
            }

            // Verificar que no existe asistencia duplicada para hoy
            $yaRegistrado = AsistenciaDocente::where('docente_id', $docente->id)
                ->where('horario_id', $validated['horario_id'])
                ->whereDate('fecha', today())
                ->exists();

            if ($yaRegistrado) {
                return back()->with('error', 'Ya registró su asistencia para este horario hoy.');
            }

            // Procesar archivo adjunto si existe
            $rutaDocumento = null;
            if ($request->hasFile('documento_respaldo')) {
                $archivo = $request->file('documento_respaldo');
                $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
                $rutaDocumento = $archivo->storeAs('asistencias/documentos', $nombreArchivo, 'public');
            }

            // Si el estado es presente, el tipo de ausencia debe ser 'ninguna'
            if ($validated['estado'] === AsistenciaDocente::ESTADO_PRESENTE) {
                $validated['tipo_ausencia'] = AsistenciaDocente::TIPO_NINGUNA;
            }

            // Crear el registro de asistencia
            AsistenciaDocente::create([
                'docente_id' => $docente->id,
                'horario_id' => $validated['horario_id'],
                'fecha' => today(),
                'hora_registro' => now(),
                'estado' => $validated['estado'],
                'tipo_ausencia' => $validated['tipo_ausencia'] ?? AsistenciaDocente::TIPO_NINGUNA,
                'observaciones' => $validated['observaciones'],
                'documento_respaldo' => $rutaDocumento,
                'latitud' => $validated['latitud'] ?? null,
                'longitud' => $validated['longitud'] ?? null,
                'ip_registro' => $request->ip(),
                'registrado_por' => $usuario->id,
            ]);

            return redirect()->route('asistencia-docente.index')
                ->with('success', 'Asistencia registrada exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al registrar la asistencia: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalle de una asistencia específica
     */
    public function show($id)
    {
        try {
            // Obtener el usuario autenticado
            $usuario = Auth::user();

            // Obtener el perfil de docente asociado
            $docente = Docente::where('usuario_id', $usuario->id)->first();

            if (!$docente) {
                return redirect()->route('dashboard')
                    ->with('error', 'No se encontró un perfil de docente asociado a su usuario.');
            }

            // Buscar la asistencia por UUID o ID
            $asistencia = AsistenciaDocente::where(function($query) use ($id) {
                    $query->where('uuid', $id)
                          ->orWhere('id', $id);
                })
                ->where('docente_id', $docente->id)
                ->with(['horario.materia', 'horario.grupo', 'horario.aula', 'registradoPor'])
                ->firstOrFail();

            return Inertia::render('AsistenciaDocente/Show', [
                'asistencia' => $asistencia,
            ]);

        } catch (\Exception $e) {
            return redirect()->route('asistencia-docente.index')
                ->with('error', 'Error al cargar el detalle de la asistencia: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar materias asignadas al docente
     */
    public function misMaterias()
    {
        try {
            // Obtener el usuario autenticado
            $usuario = Auth::user();

            // Obtener el perfil de docente asociado
            $docente = Docente::where('usuario_id', $usuario->id)
                ->with(['usuario', 'carrera'])
                ->first();

            if (!$docente) {
                return redirect()->route('dashboard')
                    ->with('error', 'No se encontró un perfil de docente asociado a su usuario.');
            }

            // Obtener horarios activos del docente con sus relaciones
            $horarios = Horario::where('docente_id', $docente->id)
                ->with(['materia', 'grupo', 'aula'])
                ->get();

            // Agrupar horarios por materia
            $materias = $horarios->groupBy('materia_id')->map(function ($horariosMateria) {
                $materia = $horariosMateria->first()->materia;
                $grupos = $horariosMateria->pluck('grupo')->unique('id');
                $horasSemanales = $horariosMateria->sum(function ($horario) {
                    $horaInicio = Carbon::parse($horario->hora_inicio);
                    $horaFin = Carbon::parse($horario->hora_fin);
                    return $horaInicio->diffInHours($horaFin);
                });

                return [
                    'id' => $materia->id,
                    'nombre' => $materia->nombre,
                    'sigla' => $materia->sigla ?? $materia->codigo ?? 'N/A',
                    'horas_semanales' => $horasSemanales,
                    'grupos' => $grupos->values(),
                    'horarios' => $horariosMateria->map(function ($horario) {
                        return [
                            'id' => $horario->id,
                            'dia_semana' => $horario->dia_semana,
                            'hora_inicio' => $horario->hora_inicio,
                            'hora_fin' => $horario->hora_fin,
                            'aula' => $horario->aula ? [
                                'nombre' => $horario->aula->nombre,
                                'edificio' => $horario->aula->edificio ?? 'N/A',
                            ] : null,
                        ];
                    })->values(),
                ];
            })->values();

            return Inertia::render('AsistenciaDocente/MisMaterias', [
                'materias' => $materias,
                'docente' => [
                    'nombre_completo' => $docente->usuario->nombre . ' ' . $docente->usuario->apellido,
                    'titulo_academico' => $docente->titulo_academico,
                    'especializacion' => $docente->especializacion,
                    'carrera' => $docente->carrera ? $docente->carrera->nombre : 'N/A',
                ],
            ]);

        } catch (\Exception $e) {
            return redirect()->route('dashboard')
                ->with('error', 'Error al cargar las materias: ' . $e->getMessage());
        }
    }

    /**
     * Método privado: Obtener horarios del docente para el día de hoy
     */
    private function obtenerHorariosHoy($docenteId)
    {
        $diaSemana = now()->locale('es')->dayName; // Lunes, Martes, etc.

        return Horario::where('docente_id', $docenteId)
            ->where('dia_semana', $diaSemana)
            ->with(['materia', 'grupo', 'aula'])
            ->get();
    }

    /**
     * Método privado: Obtener estadísticas de asistencia del docente
     */
    private function obtenerEstadisticas($docenteId, $fechaInicio, $fechaFin)
    {
        $totalRegistros = AsistenciaDocente::where('docente_id', $docenteId)
            ->entreFechas($fechaInicio, $fechaFin)
            ->count();

        $totalPresentes = AsistenciaDocente::where('docente_id', $docenteId)
            ->entreFechas($fechaInicio, $fechaFin)
            ->where('estado', AsistenciaDocente::ESTADO_PRESENTE)
            ->count();

        $totalAusentes = AsistenciaDocente::where('docente_id', $docenteId)
            ->entreFechas($fechaInicio, $fechaFin)
            ->where('estado', AsistenciaDocente::ESTADO_AUSENTE)
            ->count();

        $totalLicencias = AsistenciaDocente::where('docente_id', $docenteId)
            ->entreFechas($fechaInicio, $fechaFin)
            ->where('estado', AsistenciaDocente::ESTADO_LICENCIA)
            ->count();

        $totalJustificados = AsistenciaDocente::where('docente_id', $docenteId)
            ->entreFechas($fechaInicio, $fechaFin)
            ->where('estado', AsistenciaDocente::ESTADO_JUSTIFICADO)
            ->count();

        $porcentajeAsistencia = $totalRegistros > 0
            ? round(($totalPresentes / $totalRegistros) * 100, 2)
            : 0;

        return [
            'total_registros' => $totalRegistros,
            'total_presentes' => $totalPresentes,
            'total_ausentes' => $totalAusentes,
            'total_licencias' => $totalLicencias,
            'total_justificados' => $totalJustificados,
            'porcentaje_asistencia' => $porcentajeAsistencia,
        ];
    }

    /**
     * Método privado: Obtener estados disponibles
     */
    private function getEstadosDisponibles()
    {
        return [
            [
                'value' => AsistenciaDocente::ESTADO_PRESENTE,
                'label' => 'Presente',
                'color' => 'green',
            ],
            [
                'value' => AsistenciaDocente::ESTADO_AUSENTE,
                'label' => 'Ausente',
                'color' => 'red',
            ],
            [
                'value' => AsistenciaDocente::ESTADO_LICENCIA,
                'label' => 'Licencia',
                'color' => 'yellow',
            ],
            [
                'value' => AsistenciaDocente::ESTADO_JUSTIFICADO,
                'label' => 'Justificado',
                'color' => 'blue',
            ],
        ];
    }

    /**
     * Método privado: Obtener tipos de ausencia
     */
    private function getTiposAusencia()
    {
        return AsistenciaDocente::getTiposAusencia();
    }
}