<?php

namespace App\Http\Controllers;

use App\Models\GestionAcademica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class GestionAcademicaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = GestionAcademica::query();

        // Filtro por año
        if ($request->has('anio') && $request->anio) {
            $query->where('anio', $request->anio);
        }

        // Filtro por estado
        if ($request->has('estado') && $request->estado) {
            $query->where('estado', $request->estado);
        }

        // Búsqueda por código o nombre
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('codigo', 'ILIKE', '%' . $request->search . '%')
                  ->orWhere('nombre', 'ILIKE', '%' . $request->search . '%');
            });
        }

        $gestiones = $query->orderBy('anio', 'desc')
                          ->orderBy('semestre', 'desc')
                          ->get();

        // Obtener años disponibles para filtro
        $anios = GestionAcademica::selectRaw('DISTINCT anio')
                                ->orderBy('anio', 'desc')
                                ->pluck('anio');

        return Inertia::render('GestionAcademica/Index', [
            'gestiones' => $gestiones,
            'anios' => $anios,
            'estados' => GestionAcademica::getEstados(),
            'filters' => $request->only(['anio', 'estado', 'search'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('GestionAcademica/Create', [
            'estados' => GestionAcademica::getEstados(),
            'semestres' => GestionAcademica::getSemestres()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:gestiones_academicas,codigo',
            'nombre' => 'required|string|max:255',
            'anio' => 'required|integer|min:2020|max:2100',
            'semestre' => 'required|integer|in:1,2',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'fecha_inicio_inscripciones' => 'nullable|date|before_or_equal:fecha_inicio_clases',
            'fecha_fin_inscripciones' => 'nullable|date|after:fecha_inicio_inscripciones|before_or_equal:fecha_inicio_clases',
            'fecha_inicio_clases' => 'nullable|date|after_or_equal:fecha_inicio',
            'fecha_fin_clases' => 'nullable|date|after:fecha_inicio_clases|before_or_equal:fecha_fin',
            'estado' => 'required|in:planificacion,inscripciones,activa,finalizada,cancelada',
            'observaciones' => 'nullable|string|max:1000',
        ], [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'Ya existe una gestión con este código.',
            'nombre.required' => 'El nombre es obligatorio.',
            'anio.required' => 'El año es obligatorio.',
            'anio.min' => 'El año debe ser mayor o igual a 2020.',
            'anio.max' => 'El año debe ser menor o igual a 2100.',
            'semestre.required' => 'El semestre es obligatorio.',
            'semestre.in' => 'El semestre debe ser 1 o 2.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'fecha_inicio_inscripciones.before_or_equal' => 'Las inscripciones deben iniciar antes o junto con las clases.',
            'fecha_fin_inscripciones.after' => 'La fecha fin de inscripciones debe ser posterior al inicio.',
            'fecha_inicio_clases.after_or_equal' => 'Las clases deben iniciar dentro del período académico.',
            'fecha_fin_clases.after' => 'La fecha fin de clases debe ser posterior al inicio.',
            'fecha_fin_clases.before_or_equal' => 'Las clases deben finalizar dentro del período académico.',
            'estado.required' => 'El estado es obligatorio.',
        ]);

        // Agregar usuario que lo crea
        $validated['creado_por'] = Auth::id();
        $validated['esta_activo'] = true;

        GestionAcademica::create($validated);

        return redirect()->route('gestiones-academicas.index')
            ->with('success', 'Gestión académica creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(GestionAcademica $gestionAcademica)
    {
        $gestionAcademica->load(['grupos.carrera', 'creador']);

        return Inertia::render('GestionAcademica/Show', [
            'gestion' => $gestionAcademica
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GestionAcademica $gestionAcademica)
    {
        return Inertia::render('GestionAcademica/Edit', [
            'gestion' => $gestionAcademica,
            'estados' => GestionAcademica::getEstados(),
            'semestres' => GestionAcademica::getSemestres()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GestionAcademica $gestionAcademica)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:gestiones_academicas,codigo,' . $gestionAcademica->id,
            'nombre' => 'required|string|max:255',
            'anio' => 'required|integer|min:2020|max:2100',
            'semestre' => 'required|integer|in:1,2',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'fecha_inicio_inscripciones' => 'nullable|date|before_or_equal:fecha_inicio_clases',
            'fecha_fin_inscripciones' => 'nullable|date|after:fecha_inicio_inscripciones|before_or_equal:fecha_inicio_clases',
            'fecha_inicio_clases' => 'nullable|date|after_or_equal:fecha_inicio',
            'fecha_fin_clases' => 'nullable|date|after:fecha_inicio_clases|before_or_equal:fecha_fin',
            'estado' => 'required|in:planificacion,inscripciones,activa,finalizada,cancelada',
            'observaciones' => 'nullable|string|max:1000',
        ], [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'Ya existe otra gestión con este código.',
            'nombre.required' => 'El nombre es obligatorio.',
            'anio.required' => 'El año es obligatorio.',
            'anio.min' => 'El año debe ser mayor o igual a 2020.',
            'anio.max' => 'El año debe ser menor o igual a 2100.',
            'semestre.required' => 'El semestre es obligatorio.',
            'semestre.in' => 'El semestre debe ser 1 o 2.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'estado.required' => 'El estado es obligatorio.',
        ]);

        $gestionAcademica->update($validated);

        return redirect()->route('gestiones-academicas.index')
            ->with('success', 'Gestión académica actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GestionAcademica $gestionAcademica)
    {
        try {
            // Verificar si tiene grupos asociados
            $gruposActivos = $gestionAcademica->grupos()->count();
            
            if ($gruposActivos > 0) {
                return redirect()->route('gestiones-academicas.index')
                    ->with('error', 'No se puede eliminar la gestión porque tiene ' . $gruposActivos . ' grupo(s) asociado(s).');
            }

            $gestionAcademica->delete();

            return redirect()->route('gestiones-academicas.index')
                ->with('success', 'Gestión académica eliminada exitosamente.');
                
        } catch (\Exception $e) {
            return redirect()->route('gestiones-academicas.index')
                ->with('error', 'Error al eliminar la gestión académica: ' . $e->getMessage());
        }
    }
}