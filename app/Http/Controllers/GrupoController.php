<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\GestionAcademica;
use App\Models\Carrera;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GrupoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Grupo::with(['gestionAcademica', 'carrera']);

        // Filtro por gestión académica
        if ($request->has('gestion_academica_id') && $request->gestion_academica_id) {
            $query->where('gestion_academica_id', $request->gestion_academica_id);
        }

        // Filtro por carrera
        if ($request->has('carrera_id') && $request->carrera_id) {
            $query->where('carrera_id', $request->carrera_id);
        }

        // Filtro por turno
        if ($request->has('turno') && $request->turno) {
            $query->where('turno', $request->turno);
        }

        // Búsqueda por código, sigla o nombre
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('codigo', 'ILIKE', '%' . $request->search . '%')
                  ->orWhere('sigla', 'ILIKE', '%' . $request->search . '%')
                  ->orWhere('nombre', 'ILIKE', '%' . $request->search . '%');
            });
        }

        $grupos = $query->orderBy('semestre', 'asc')
                       ->orderBy('codigo', 'asc')
                       ->get();

        $gestiones = GestionAcademica::activa()
                                    ->orderBy('anio', 'desc')
                                    ->orderBy('semestre', 'desc')
                                    ->get();
        
        $carreras = Carrera::orderBy('nombre', 'asc')->get();

        return Inertia::render('Grupos/Index', [
            'grupos' => $grupos,
            'gestiones' => $gestiones,
            'carreras' => $carreras,
            'turnos' => Grupo::getTurnos(),
            'filters' => $request->only(['gestion_academica_id', 'carrera_id', 'turno', 'search'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $gestiones = GestionAcademica::activa()
                                    ->orderBy('anio', 'desc')
                                    ->orderBy('semestre', 'desc')
                                    ->get();
        
        $carreras = Carrera::orderBy('nombre', 'asc')->get();

        return Inertia::render('Grupos/Create', [
            'gestiones' => $gestiones,
            'carreras' => $carreras,
            'turnos' => Grupo::getTurnos()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'gestion_academica_id' => 'required|exists:gestiones_academicas,id',
            'carrera_id' => 'required|exists:carreras,id',
            'sigla' => 'required|string|max:20',
            'codigo' => 'required|string|max:50|unique:grupos,codigo',
            'nombre' => 'required|string|max:255',
            'semestre' => 'required|integer|min:1|max:10',
            'capacidad' => 'required|integer|min:1|max:100',
            'turno' => 'required|in:manana,tarde,noche',
            'observaciones' => 'nullable|string|max:1000',
        ], [
            'gestion_academica_id.required' => 'Debes seleccionar una gestión académica.',
            'gestion_academica_id.exists' => 'La gestión académica seleccionada no existe.',
            'carrera_id.required' => 'Debes seleccionar una carrera.',
            'carrera_id.exists' => 'La carrera seleccionada no existe.',
            'sigla.required' => 'La sigla es obligatoria.',
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'Ya existe un grupo con este código.',
            'nombre.required' => 'El nombre es obligatorio.',
            'semestre.required' => 'El semestre es obligatorio.',
            'semestre.min' => 'El semestre debe ser al menos 1.',
            'semestre.max' => 'El semestre no puede ser mayor a 10.',
            'capacidad.required' => 'La capacidad es obligatoria.',
            'capacidad.min' => 'La capacidad debe ser al menos 1.',
            'capacidad.max' => 'La capacidad no puede ser mayor a 100.',
            'turno.required' => 'El turno es obligatorio.',
            'turno.in' => 'El turno seleccionado no es válido.',
        ]);

        // Inicializar cantidad de inscritos en 0
        $validated['cantidad_inscritos'] = 0;
        $validated['esta_activo'] = true;

        Grupo::create($validated);

        return redirect()->route('grupos.index')
            ->with('success', 'Grupo creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Grupo $grupo)
    {
        $grupo->load(['gestionAcademica', 'carrera', 'materias', 'horarios.materia', 'horarios.docente', 'horarios.aula']);

        return Inertia::render('Grupos/Show', [
            'grupo' => $grupo
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Grupo $grupo)
    {
        $grupo->load('gestionAcademica', 'carrera');
        
        $gestiones = GestionAcademica::activa()
                                    ->orderBy('anio', 'desc')
                                    ->orderBy('semestre', 'desc')
                                    ->get();
        
        $carreras = Carrera::orderBy('nombre', 'asc')->get();

        return Inertia::render('Grupos/Edit', [
            'grupo' => $grupo,
            'gestiones' => $gestiones,
            'carreras' => $carreras,
            'turnos' => Grupo::getTurnos()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Grupo $grupo)
    {
        $validated = $request->validate([
            'gestion_academica_id' => 'required|exists:gestiones_academicas,id',
            'carrera_id' => 'required|exists:carreras,id',
            'sigla' => 'required|string|max:20',
            'codigo' => 'required|string|max:50|unique:grupos,codigo,' . $grupo->id,
            'nombre' => 'required|string|max:255',
            'semestre' => 'required|integer|min:1|max:10',
            'capacidad' => 'required|integer|min:1|max:100',
            'turno' => 'required|in:manana,tarde,noche',
            'observaciones' => 'nullable|string|max:1000',
        ], [
            'gestion_academica_id.required' => 'Debes seleccionar una gestión académica.',
            'gestion_academica_id.exists' => 'La gestión académica seleccionada no existe.',
            'carrera_id.required' => 'Debes seleccionar una carrera.',
            'carrera_id.exists' => 'La carrera seleccionada no existe.',
            'sigla.required' => 'La sigla es obligatoria.',
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'Ya existe otro grupo con este código.',
            'nombre.required' => 'El nombre es obligatorio.',
            'semestre.required' => 'El semestre es obligatorio.',
            'semestre.min' => 'El semestre debe ser al menos 1.',
            'semestre.max' => 'El semestre no puede ser mayor a 10.',
            'capacidad.required' => 'La capacidad es obligatoria.',
            'capacidad.min' => 'La capacidad debe ser al menos 1.',
            'capacidad.max' => 'La capacidad no puede ser mayor a 100.',
            'turno.required' => 'El turno es obligatorio.',
            'turno.in' => 'El turno seleccionado no es válido.',
        ]);

        $grupo->update($validated);

        return redirect()->route('grupos.index')
            ->with('success', 'Grupo actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Grupo $grupo)
    {
        try {
            // Verificar si el grupo tiene horarios asignados
            $horariosActivos = $grupo->horarios()->count();
            
            if ($horariosActivos > 0) {
                return redirect()->route('grupos.index')
                    ->with('error', 'No se puede eliminar el grupo porque tiene ' . $horariosActivos . ' horario(s) asignado(s).');
            }

            // Soft delete
            $grupo->delete();

            return redirect()->route('grupos.index')
                ->with('success', 'Grupo eliminado exitosamente.');
                
        } catch (\Exception $e) {
            return redirect()->route('grupos.index')
                ->with('error', 'Error al eliminar el grupo: ' . $e->getMessage());
        }
    }
}