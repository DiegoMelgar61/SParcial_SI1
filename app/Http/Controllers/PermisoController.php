<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PermisoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Permiso::query();

        // Filtro por búsqueda
        if ($request->has('search') && $request->search != '') {
            $query->buscar($request->search);
        }

        // Filtro por módulo
        if ($request->has('modulo') && $request->modulo != '') {
            $query->porModulo($request->modulo);
        }

        // Ordenamiento
        $sortField = $request->get('sort', 'modulo');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Paginación
        $permisos = $query->paginate(10)->withQueryString();

        // Obtener módulos únicos para el filtro
        $modulos = Permiso::getModulosUnicos();

        return Inertia::render('Permisos/Index', [
            'permisos' => $permisos,
            'modulos' => $modulos,
            'filters' => [
                'search' => $request->search,
                'modulo' => $request->modulo,
                'sort' => $sortField,
                'direction' => $sortDirection,
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtener módulos existentes para sugerencias
        $modulosSugeridos = Permiso::getModulosUnicos();

        return Inertia::render('Permisos/Create', [
            'modulosSugeridos' => $modulosSugeridos
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:permisos,nombre',
            'slug' => 'nullable|string|max:100|unique:permisos,slug',
            'descripcion' => 'nullable|string|max:255',
            'modulo' => 'required|string|max:50',
        ], [
            'nombre.required' => 'El nombre del permiso es obligatorio',
            'nombre.unique' => 'Ya existe un permiso con este nombre',
            'slug.unique' => 'Ya existe un permiso con este slug',
            'modulo.required' => 'El módulo es obligatorio',
        ]);

        // Si no se proporciona slug, generarlo automáticamente
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['nombre'], '_');
        }

        $permiso = Permiso::create($validated);

        return redirect()
            ->route('permisos.index')
            ->with('success', 'Permiso creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Permiso $permiso)
    {
        // Cargar los roles asociados al permiso
        $permiso->load('roles');

        return Inertia::render('Permisos/Show', [
            'permiso' => $permiso,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permiso $permiso)
    {
        // Obtener módulos existentes para sugerencias
        $modulosSugeridos = Permiso::getModulosUnicos();

        return Inertia::render('Permisos/Edit', [
            'permiso' => $permiso,
            'modulosSugeridos' => $modulosSugeridos
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permiso $permiso)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:permisos,nombre,' . $permiso->id,
            'slug' => 'nullable|string|max:100|unique:permisos,slug,' . $permiso->id,
            'descripcion' => 'nullable|string|max:255',
            'modulo' => 'required|string|max:50',
        ], [
            'nombre.required' => 'El nombre del permiso es obligatorio',
            'nombre.unique' => 'Ya existe un permiso con este nombre',
            'slug.unique' => 'Ya existe un permiso con este slug',
            'modulo.required' => 'El módulo es obligatorio',
        ]);

        // Si se modificó el nombre y no el slug, regenerar slug
        if ($request->nombre != $permiso->nombre && empty($request->slug)) {
            $validated['slug'] = Str::slug($validated['nombre'], '_');
        }

        $permiso->update($validated);

        return redirect()
            ->route('permisos.index')
            ->with('success', 'Permiso actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permiso $permiso)
    {
        // Verificar si el permiso está asignado a algún rol
        if ($permiso->estaAsignado()) {
            return redirect()
                ->route('permisos.index')
                ->with('error', 'No se puede eliminar el permiso porque está asignado a uno o más roles');
        }

        $permiso->delete();

        return redirect()
            ->route('permisos.index')
            ->with('success', 'Permiso eliminado exitosamente');
    }

    /**
     * Obtener permisos agrupados por módulo (para select)
     */
    public function getPorModulo()
    {
        $permisos = Permiso::orderBy('modulo')
            ->orderBy('nombre')
            ->get()
            ->groupBy('modulo');

        return response()->json($permisos);
    }
}