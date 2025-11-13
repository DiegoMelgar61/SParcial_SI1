<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Rol::withCount('usuarios');

        // Búsqueda por nombre
        if ($request->has('search') && $request->search) {
            $query->where('nombre', 'ILIKE', '%' . $request->search . '%');
        }

        // Filtro por estado
        if ($request->has('estado') && $request->estado !== '') {
            $query->where('esta_activo', $request->estado === 'activo');
        }

        $roles = $query->orderBy('nombre', 'asc')->get();

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'filters' => $request->only(['search', 'estado'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Roles/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:roles,nombre',
            'descripcion' => 'nullable|string|max:500',
        ], [
            'nombre.required' => 'El nombre del rol es obligatorio.',
            'nombre.unique' => 'Ya existe un rol con este nombre.',
            'nombre.max' => 'El nombre no puede tener más de 100 caracteres.',
            'descripcion.max' => 'La descripción no puede tener más de 500 caracteres.',
        ]);

        $validated['esta_activo'] = true;

        Rol::create($validated);

        return redirect()->route('roles.index')
            ->with('success', 'Rol creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Rol $rol)
    {
        $rol->load(['usuarios', 'permisos']);

        return Inertia::render('Roles/Show', [
            'rol' => $rol
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rol $rol)
    {
        return Inertia::render('Roles/Edit', [
            'rol' => $rol
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rol $rol)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:roles,nombre,' . $rol->id,
            'descripcion' => 'nullable|string|max:500',
            'esta_activo' => 'required|boolean',
        ], [
            'nombre.required' => 'El nombre del rol es obligatorio.',
            'nombre.unique' => 'Ya existe otro rol con este nombre.',
            'nombre.max' => 'El nombre no puede tener más de 100 caracteres.',
            'descripcion.max' => 'La descripción no puede tener más de 500 caracteres.',
            'esta_activo.required' => 'Debes especificar si el rol está activo.',
        ]);

        $rol->update($validated);

        return redirect()->route('roles.index')
            ->with('success', 'Rol actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rol $rol)
    {
        try {
            // Verificar si el rol tiene usuarios asignados
            $usuariosAsignados = $rol->usuarios()->count();
            
            if ($usuariosAsignados > 0) {
                return redirect()->route('roles.index')
                    ->with('error', 'No se puede eliminar el rol porque tiene ' . $usuariosAsignados . ' usuario(s) asignado(s).');
            }

            $rol->delete();

            return redirect()->route('roles.index')
                ->with('success', 'Rol eliminado exitosamente.');
                
        } catch (\Exception $e) {
            return redirect()->route('roles.index')
                ->with('error', 'Error al eliminar el rol: ' . $e->getMessage());
        }
    }
}