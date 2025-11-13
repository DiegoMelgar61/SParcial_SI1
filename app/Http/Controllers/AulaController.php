<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AulaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Aula::query();

        // Filtro por tipo
        if ($request->has('tipo') && $request->tipo) {
            $query->where('tipo', $request->tipo);
        }

        // Filtro por piso
        if ($request->has('piso') && $request->piso !== '') {
            $query->where('piso', $request->piso);
        }

        // Búsqueda por código o nombre
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('codigo', 'ILIKE', '%' . $request->search . '%')
                  ->orWhere('nombre', 'ILIKE', '%' . $request->search . '%');
            });
        }

        $aulas = $query->orderBy('codigo', 'asc')->get();

        return Inertia::render('Aulas/Index', [
            'aulas' => $aulas,
            'tiposAula' => Aula::getTiposAula(),
            'filters' => $request->only(['tipo', 'piso', 'search'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Aulas/Create', [
            'tiposAula' => Aula::getTiposAula()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:20|unique:aulas,codigo',
            'nombre' => 'required|string|max:255',
            'piso' => 'required|integer|min:0|max:20',
            'tipo' => 'required|in:teorica,laboratorio,auditorio,sala_computo',
            'capacidad' => 'required|integer|min:1|max:500',
            'tiene_computadoras' => 'required|boolean',
            'cantidad_computadoras' => 'nullable|integer|min:0|max:100',
            'observaciones' => 'nullable|string|max:1000',
        ], [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'Ya existe un aula con este código.',
            'nombre.required' => 'El nombre es obligatorio.',
            'piso.required' => 'El piso es obligatorio.',
            'piso.min' => 'El piso debe ser al menos 0.',
            'piso.max' => 'El piso no puede exceder 20.',
            'tipo.required' => 'El tipo de aula es obligatorio.',
            'tipo.in' => 'El tipo de aula seleccionado no es válido.',
            'capacidad.required' => 'La capacidad es obligatoria.',
            'capacidad.min' => 'La capacidad debe ser al menos 1.',
            'capacidad.max' => 'La capacidad no puede exceder 500.',
            'tiene_computadoras.required' => 'Debes indicar si tiene computadoras.',
            'cantidad_computadoras.max' => 'La cantidad de computadoras no puede exceder 100.',
        ]);

        // Si no tiene computadoras, la cantidad debe ser 0
        if (!$validated['tiene_computadoras']) {
            $validated['cantidad_computadoras'] = 0;
        }

        Aula::create($validated);

        return redirect()->route('aulas.index')
            ->with('success', 'Aula creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Aula $aula)
    {
        $aula->load('horarios');

        return Inertia::render('Aulas/Show', [
            'aula' => $aula
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aula $aula)
    {
        return Inertia::render('Aulas/Edit', [
            'aula' => $aula,
            'tiposAula' => Aula::getTiposAula()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Aula $aula)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:20|unique:aulas,codigo,' . $aula->id,
            'nombre' => 'required|string|max:255',
            'piso' => 'required|integer|min:0|max:20',
            'tipo' => 'required|in:teorica,laboratorio,auditorio,sala_computo',
            'capacidad' => 'required|integer|min:1|max:500',
            'tiene_computadoras' => 'required|boolean',
            'cantidad_computadoras' => 'nullable|integer|min:0|max:100',
            'observaciones' => 'nullable|string|max:1000',
        ], [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'Ya existe otra aula con este código.',
            'nombre.required' => 'El nombre es obligatorio.',
            'piso.required' => 'El piso es obligatorio.',
            'piso.min' => 'El piso debe ser al menos 0.',
            'piso.max' => 'El piso no puede exceder 20.',
            'tipo.required' => 'El tipo de aula es obligatorio.',
            'tipo.in' => 'El tipo de aula seleccionado no es válido.',
            'capacidad.required' => 'La capacidad es obligatoria.',
            'capacidad.min' => 'La capacidad debe ser al menos 1.',
            'capacidad.max' => 'La capacidad no puede exceder 500.',
            'tiene_computadoras.required' => 'Debes indicar si tiene computadoras.',
            'cantidad_computadoras.max' => 'La cantidad de computadoras no puede exceder 100.',
        ]);

        // Si no tiene computadoras, la cantidad debe ser 0
        if (!$validated['tiene_computadoras']) {
            $validated['cantidad_computadoras'] = 0;
        }

        $aula->update($validated);

        return redirect()->route('aulas.index')
            ->with('success', 'Aula actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aula $aula)
    {
        try {
            // Verificar si el aula tiene horarios asignados activos
            $horariosActivos = $aula->horarios()->whereNull('fecha_eliminacion')->count();
            
            if ($horariosActivos > 0) {
                return redirect()->route('aulas.index')
                    ->with('error', 'No se puede eliminar el aula porque tiene ' . $horariosActivos . ' horario(s) asignado(s).');
            }

            // Soft delete
            $aula->delete();

            return redirect()->route('aulas.index')
                ->with('success', 'Aula eliminada exitosamente.');
                
        } catch (\Exception $e) {
            return redirect()->route('aulas.index')
                ->with('error', 'Error al eliminar el aula: ' . $e->getMessage());
        }
    }

    /**
     * Regenerar código QR del aula
     */
    public function regenerarQR(Aula $aula)
    {
        try {
            $aula->regenerarCodigoQR();

            return redirect()->route('aulas.index')
                ->with('success', 'Código QR regenerado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('aulas.index')
                ->with('error', 'Error al regenerar código QR: ' . $e->getMessage());
        }
    }
}