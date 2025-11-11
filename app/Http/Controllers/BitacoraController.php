<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BitacoraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Bitacora::with('usuario.rol')
            ->orderBy('fecha_creacion', 'desc');

        // Búsqueda
        if ($request->has('buscar') && $request->buscar) {
            $query->buscar($request->buscar);
        }

        // Filtro por acción
        if ($request->has('accion') && $request->accion) {
            $query->porAccion($request->accion);
        }

        // Filtro por tabla
        if ($request->has('tabla') && $request->tabla) {
            $query->porTabla($request->tabla);
        }

        // Filtro por usuario
        if ($request->has('usuario_id') && $request->usuario_id) {
            $query->porUsuario($request->usuario_id);
        }

        // Filtro por fecha
        if ($request->has('fecha_desde') && $request->fecha_desde) {
            $query->whereDate('fecha_creacion', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta') && $request->fecha_hasta) {
            $query->whereDate('fecha_creacion', '<=', $request->fecha_hasta);
        }

        $bitacora = $query->paginate(20)->withQueryString();

        // Obtener acciones únicas para el filtro
        $acciones = Bitacora::distinct()->pluck('accion')->sort()->values();

        // Obtener tablas únicas para el filtro
        $tablas = Bitacora::distinct()->whereNotNull('tabla')->pluck('tabla')->sort()->values();

        return Inertia::render('Bitacora/Index', [
            'bitacora' => $bitacora,
            'filters' => $request->only(['buscar', 'accion', 'tabla', 'usuario_id', 'fecha_desde', 'fecha_hasta']),
            'acciones' => $acciones,
            'tablas' => $tablas,
        ]);
    }
}
