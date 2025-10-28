<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CarreraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $carreras = Carrera::orderBy('nombre', 'asc')->get();

        return Inertia::render('Carreras/Index', [
            'carreras' => $carreras
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Carreras/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:carreras,nombre',
            'descripcion' => 'nullable|string|max:500',
        ], [
            'nombre.required' => 'El nombre de la carrera es obligatorio.',
            'nombre.unique' => 'Ya existe una carrera con este nombre.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres.',
        ]);

        Carrera::create($validated);

        return redirect()->route('carreras.index')
            ->with('success', 'Carrera creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Carrera $carrera)
    {
        // Cargar las materias relacionadas con la carrera
        $carrera->load('materias');

        return Inertia::render('Carreras/Show', [
            'carrera' => $carrera
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Carrera $carrera)
    {
        return Inertia::render('Carreras/Edit', [
            'carrera' => $carrera
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Carrera $carrera)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:carreras,nombre,' . $carrera->id,
            'descripcion' => 'nullable|string|max:500',
        ], [
            'nombre.required' => 'El nombre de la carrera es obligatorio.',
            'nombre.unique' => 'Ya existe otra carrera con este nombre.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres.',
        ]);

        $carrera->update($validated);

        return redirect()->route('carreras.index')
            ->with('success', 'Carrera actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Carrera $carrera)
    {
        try {
            // Verificar si la carrera tiene materias asociadas activas.
            // Con SoftDeletes en Materia, el alcance global ya excluye las eliminadas,
            // por lo que un simple count() devuelve solo las activas.
            $materiasActivas = $carrera->materias()->count();
            
            if ($materiasActivas > 0) {
                return redirect()->route('carreras.index')
                    ->with('error', 'No se puede eliminar la carrera porque tiene ' . $materiasActivas . ' materia(s) activa(s) asociada(s).');
            }

            // Soft delete
            $carrera->delete();

            return redirect()->route('carreras.index')
                ->with('success', 'Carrera eliminada exitosamente.');
                
        } catch (\Exception $e) {
            return redirect()->route('carreras.index')
                ->with('error', 'Error al eliminar la carrera: ' . $e->getMessage());
        }
    }
}
