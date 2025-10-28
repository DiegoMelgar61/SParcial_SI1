<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use App\Models\Carrera;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MateriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Materia::with('carrera');

        // Filtro por carrera
        if ($request->has('carrera_id') && $request->carrera_id) {
            $query->where('carrera_id', $request->carrera_id);
        }

        // Búsqueda por nombre o código
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'ILIKE', '%' . $request->search . '%')
                  ->orWhere('codigo', 'ILIKE', '%' . $request->search . '%');
            });
        }

        $materias = $query->orderBy('nombre', 'asc')->get();
        $carreras = Carrera::orderBy('nombre', 'asc')->get();

        return Inertia::render('Materias/Index', [
            'materias' => $materias,
            'carreras' => $carreras,
            'filters' => $request->only(['carrera_id', 'search'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $carreras = Carrera::orderBy('nombre', 'asc')->get();

        return Inertia::render('Materias/Create', [
            'carreras' => $carreras
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'carrera_id' => 'required|exists:carreras,id',
            'sigla' => 'nullable|string|max:20',
            'codigo' => 'required|string|max:20|unique:materias,codigo',
            'nombre' => 'required|string|max:255',
            'nombre_corto' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string|max:1000',
            'semestre' => 'required|integer|min:1|max:12',
            'horas_semanales' => 'required|integer|min:1|max:40',
            'creditos' => 'required|integer|min:1|max:10',
            'es_electiva' => 'required|boolean',
            'requiere_laboratorio' => 'required|boolean',
        ], [
            'carrera_id.required' => 'Debes seleccionar una carrera.',
            'carrera_id.exists' => 'La carrera seleccionada no existe.',
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'Ya existe una materia con este código.',
            'nombre.required' => 'El nombre es obligatorio.',
            'creditos.required' => 'Los créditos son obligatorios.',
            'creditos.min' => 'Los créditos deben ser al menos 1.',
            'creditos.max' => 'Los créditos no pueden exceder 10.',
            'horas_semanales.required' => 'Las horas semanales son obligatorias.',
            'horas_semanales.min' => 'Las horas semanales deben ser al menos 1.',
            'horas_semanales.max' => 'Las horas semanales no pueden exceder 40.',
            'semestre.required' => 'El semestre es obligatorio.',
            'semestre.min' => 'El semestre debe ser al menos 1.',
            'semestre.max' => 'El semestre no puede exceder 12.',
            'es_electiva.required' => 'Debes indicar si la materia es electiva.',
            'requiere_laboratorio.required' => 'Debes indicar si requiere laboratorio.',
        ]);

        Materia::create($validated);

        return redirect()->route('materias.index')
            ->with('success', 'Materia creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Materia $materia)
    {
        $materia->load('carrera', 'grupos');

        return Inertia::render('Materias/Show', [
            'materia' => $materia
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Materia $materia)
    {
        $carreras = Carrera::orderBy('nombre', 'asc')->get();

        return Inertia::render('Materias/Edit', [
            'materia' => $materia,
            'carreras' => $carreras
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Materia $materia)
    {
        $validated = $request->validate([
            'carrera_id' => 'required|exists:carreras,id',
            'sigla' => 'nullable|string|max:20',
            'codigo' => 'required|string|max:20|unique:materias,codigo,' . $materia->id,
            'nombre' => 'required|string|max:255',
            'nombre_corto' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string|max:1000',
            'semestre' => 'required|integer|min:1|max:12',
            'horas_semanales' => 'required|integer|min:1|max:40',
            'creditos' => 'required|integer|min:1|max:10',
            'es_electiva' => 'required|boolean',
            'requiere_laboratorio' => 'required|boolean',
        ], [
            'carrera_id.required' => 'Debes seleccionar una carrera.',
            'carrera_id.exists' => 'La carrera seleccionada no existe.',
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'Ya existe otra materia con este código.',
            'nombre.required' => 'El nombre es obligatorio.',
            'creditos.required' => 'Los créditos son obligatorios.',
            'creditos.min' => 'Los créditos deben ser al menos 1.',
            'creditos.max' => 'Los créditos no pueden exceder 10.',
            'horas_semanales.required' => 'Las horas semanales son obligatorias.',
            'horas_semanales.min' => 'Las horas semanales deben ser al menos 1.',
            'horas_semanales.max' => 'Las horas semanales no pueden exceder 40.',
            'semestre.required' => 'El semestre es obligatorio.',
            'semestre.min' => 'El semestre debe ser al menos 1.',
            'semestre.max' => 'El semestre no puede exceder 12.',
            'es_electiva.required' => 'Debes indicar si la materia es electiva.',
            'requiere_laboratorio.required' => 'Debes indicar si requiere laboratorio.',
        ]);

        $materia->update($validated);

        return redirect()->route('materias.index')
            ->with('success', 'Materia actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Materia $materia)
    {
        try {
            // Verificar si la materia tiene grupos asociados activos
            $gruposActivos = $materia->grupos()->whereNull('fecha_eliminacion')->count();
            
            if ($gruposActivos > 0) {
                return redirect()->route('materias.index')
                    ->with('error', 'No se puede eliminar la materia porque tiene ' . $gruposActivos . ' grupo(s) activo(s) asociado(s).');
            }

            // Soft delete
            $materia->delete();

            return redirect()->route('materias.index')
                ->with('success', 'Materia eliminada exitosamente.');
                
        } catch (\Exception $e) {
            return redirect()->route('materias.index')
                ->with('error', 'Error al eliminar la materia: ' . $e->getMessage());
        }
    }
}