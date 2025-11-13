<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Docente;
use App\Models\Aula;
use App\Models\BloqueHorario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HorarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Horario::with(['grupo.carrera', 'grupo.gestionAcademica', 'materia', 'docente.usuario', 'aula', 'bloqueHorario']);

        // Filtros dinámicos
        if ($request->filled('grupo_id')) {
            $query->where('grupo_id', $request->grupo_id);
        }

        if ($request->filled('docente_id')) {
            $query->where('docente_id', $request->docente_id);
        }

        if ($request->filled('aula_id')) {
            $query->where('aula_id', $request->aula_id);
        }

        if ($request->filled('dia')) {
            $query->where('dia', $request->dia);
        }

        $horarios = $query->orderBy('dia')
                         ->get()
                         ->sortBy(fn($h) => $h->bloqueHorario ? $h->bloqueHorario->orden : 999);

        $grupos = Grupo::with(['carrera', 'gestionAcademica'])
                      ->orderBy('codigo')
                      ->get();

        // 🔹 Nueva forma de traer docentes ordenados por usuario
        $docentes = Docente::with('usuario')
            ->join('usuarios', 'docentes.usuario_id', '=', 'usuarios.id')
            ->orderBy('usuarios.apellido', 'asc')
            ->orderBy('usuarios.nombre', 'asc')
            ->select('docentes.*')
            ->get();

        $aulas = Aula::orderBy('nombre')->get();

        return Inertia::render('Horarios/Index', [
            'horarios' => $horarios->values(),
            'grupos' => $grupos,
            'docentes' => $docentes,
            'aulas' => $aulas,
            'diasSemana' => Horario::getDiasSemana(),
            'filters' => $request->only(['grupo_id', 'docente_id', 'aula_id', 'dia'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $grupos = Grupo::with(['carrera', 'gestionAcademica'])
                      ->activos()
                      ->orderBy('codigo')
                      ->get();
        
        $materias = Materia::orderBy('nombre')->get();

        // 🔹 Docentes ordenados correctamente por apellido y nombre
        $docentes = Docente::with('usuario')
            ->join('usuarios', 'docentes.usuario_id', '=', 'usuarios.id')
            ->orderBy('usuarios.apellido', 'asc')
            ->orderBy('usuarios.nombre', 'asc')
            ->select('docentes.*')
            ->get();

        $aulas = Aula::orderBy('nombre')->get();

        $bloquesHorarios = BloqueHorario::activos()
                                       ->ordenado()
                                       ->get();

        return Inertia::render('Horarios/Create', [
            'grupos' => $grupos,
            'materias' => $materias,
            'docentes' => $docentes,
            'aulas' => $aulas,
            'bloquesHorarios' => $bloquesHorarios,
            'diasSemana' => Horario::getDiasSemana()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Horario $horario)
    {
        $horario->load(['grupo', 'materia', 'docente.usuario', 'aula', 'bloqueHorario']);
        
        $grupos = Grupo::with(['carrera', 'gestionAcademica'])
                      ->activos()
                      ->orderBy('codigo')
                      ->get();
        
        $materias = Materia::orderBy('nombre')->get();

        // 🔹 Docentes ordenados correctamente por usuario
        $docentes = Docente::with('usuario')
            ->join('usuarios', 'docentes.usuario_id', '=', 'usuarios.id')
            ->orderBy('usuarios.apellido', 'asc')
            ->orderBy('usuarios.nombre', 'asc')
            ->select('docentes.*')
            ->get();

        $aulas = Aula::orderBy('nombre')->get();
        
        $bloquesHorarios = BloqueHorario::activos()
                                       ->ordenado()
                                       ->get();

        return Inertia::render('Horarios/Edit', [
            'horario' => $horario,
            'grupos' => $grupos,
            'materias' => $materias,
            'docentes' => $docentes,
            'aulas' => $aulas,
            'bloquesHorarios' => $bloquesHorarios,
            'diasSemana' => Horario::getDiasSemana()
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Horario $horario)
    {
        $validated = $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
            'materia_id' => 'required|exists:materias,id',
            'docente_id' => 'required|exists:docentes,id',
            'aula_id' => 'required|exists:aulas,id',
            'bloque_horario_id' => 'required|exists:bloques_horarios,id',
            'dia' => 'required|in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
            'observaciones' => 'nullable|string|max:1000',
        ], [
            'grupo_id.required' => 'Debes seleccionar un grupo.',
            'grupo_id.exists' => 'El grupo seleccionado no existe.',
            'materia_id.required' => 'Debes seleccionar una materia.',
            'materia_id.exists' => 'La materia seleccionada no existe.',
            'docente_id.required' => 'Debes seleccionar un docente.',
            'docente_id.exists' => 'El docente seleccionado no existe.',
            'aula_id.required' => 'Debes seleccionar un aula.',
            'aula_id.exists' => 'El aula seleccionada no existe.',
            'bloque_horario_id.required' => 'Debes seleccionar un bloque horario.',
            'bloque_horario_id.exists' => 'El bloque horario seleccionado no existe.',
            'dia.required' => 'Debes seleccionar un día de la semana.',
            'dia.in' => 'El día seleccionado no es válido.',
        ]);

        // Verificar cruce de docente (excluyendo el horario actual)
        if (Horario::verificarCruceDocente($validated['docente_id'], $validated['dia'], $validated['bloque_horario_id'], $horario->id)) {
            return back()->withErrors([
                'bloque_horario_id' => 'El docente ya tiene un horario asignado en este día y bloque horario.'
            ])->withInput();
        }

        // Verificar cruce de aula (excluyendo el horario actual)
        if (Horario::verificarCruceAula($validated['aula_id'], $validated['dia'], $validated['bloque_horario_id'], $horario->id)) {
            return back()->withErrors([
                'bloque_horario_id' => 'El aula ya está ocupada en este día y bloque horario.'
            ])->withInput();
        }

        // Verificar cruce de grupo (excluyendo el horario actual)
        if (Horario::verificarCruceGrupo($validated['grupo_id'], $validated['dia'], $validated['bloque_horario_id'], $horario->id)) {
            return back()->withErrors([
                'bloque_horario_id' => 'El grupo ya tiene una clase asignada en este día y bloque horario.'
            ])->withInput();
        }

        // Obtener gestión académica del grupo
        $grupo = Grupo::find($validated['grupo_id']);
        $validated['gestion_academica_id'] = $grupo->gestion_academica_id;
        $validated['actualizado_por'] = Auth::id();

        $horario->update($validated);

        return redirect()->route('horarios.index')
            ->with('success', 'Horario actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Horario $horario)
    {
        try {
            // Soft delete
            $horario->delete();

            return redirect()->route('horarios.index')
                ->with('success', 'Horario eliminado exitosamente.');
                
        } catch (\Exception $e) {
            return redirect()->route('horarios.index')
                ->with('error', 'Error al eliminar el horario: ' . $e->getMessage());
        }
    }
}