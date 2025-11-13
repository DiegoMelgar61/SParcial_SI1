<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\Usuario;
use App\Models\Carrera;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DocenteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Docente::with(['usuario', 'carrera']);

        // Filtro por carrera
        if ($request->has('carrera_id') && $request->carrera_id) {
            $query->where('carrera_id', $request->carrera_id);
        }

        // Filtro por tipo de contrato
        if ($request->has('tipo_contrato') && $request->tipo_contrato) {
            $query->where('tipo_contrato', $request->tipo_contrato);
        }

        // Búsqueda por nombre, código o email
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('codigo_empleado', 'ILIKE', '%' . $request->search . '%')
                  ->orWhereHas('usuario', function($uq) use ($request) {
                      $uq->where('nombre', 'ILIKE', '%' . $request->search . '%')
                         ->orWhere('apellido', 'ILIKE', '%' . $request->search . '%')
                         ->orWhere('email', 'ILIKE', '%' . $request->search . '%');
                  });
            });
        }

        $docentes = $query->orderBy('codigo_empleado', 'asc')->get();
        $carreras = Carrera::orderBy('nombre', 'asc')->get();

        return Inertia::render('Docentes/Index', [
            'docentes' => $docentes,
            'carreras' => $carreras,
            'tiposContrato' => Docente::getTiposContrato(),
            'filters' => $request->only(['carrera_id', 'tipo_contrato', 'search'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtener solo usuarios que NO sean docentes ya
        $usuariosDisponibles = Usuario::whereNotIn('id', function($query) {
            $query->select('usuario_id')->from('docentes')->whereNull('fecha_eliminacion');
        })->orderBy('nombre', 'asc')->get();

        $carreras = Carrera::orderBy('nombre', 'asc')->get();

        return Inertia::render('Docentes/Create', [
            'usuarios' => $usuariosDisponibles,
            'carreras' => $carreras,
            'tiposContrato' => Docente::getTiposContrato(),
            'turnos' => Docente::getTurnos()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id|unique:docentes,usuario_id,NULL,id,fecha_eliminacion,NULL',
            'carrera_id' => 'required|exists:carreras,id',
            'codigo_empleado' => 'required|string|max:50|unique:docentes,codigo_empleado',
            'titulo_academico' => 'required|string|max:255',
            'especializacion' => 'nullable|string|max:255',
            'tipo_contrato' => 'required|in:tiempo_completo,medio_tiempo,por_horas,invitado',
            'fecha_contratacion' => 'required|date',
            'horas_semanales_max' => 'required|integer|min:1|max:48',
            'turnos_preferidos' => 'required|array|min:1',
            'turnos_preferidos.*' => 'in:manana,tarde,noche',
            'observaciones' => 'nullable|string|max:1000',
        ], [
            'usuario_id.required' => 'Debes seleccionar un usuario.',
            'usuario_id.exists' => 'El usuario seleccionado no existe.',
            'usuario_id.unique' => 'Este usuario ya está registrado como docente.',
            'carrera_id.required' => 'Debes seleccionar una carrera.',
            'carrera_id.exists' => 'La carrera seleccionada no existe.',
            'codigo_empleado.required' => 'El código de empleado es obligatorio.',
            'codigo_empleado.unique' => 'Ya existe un docente con este código.',
            'titulo_academico.required' => 'El título académico es obligatorio.',
            'tipo_contrato.required' => 'El tipo de contrato es obligatorio.',
            'tipo_contrato.in' => 'El tipo de contrato seleccionado no es válido.',
            'fecha_contratacion.required' => 'La fecha de contratación es obligatoria.',
            'horas_semanales_max.required' => 'Las horas semanales máximas son obligatorias.',
            'horas_semanales_max.min' => 'Las horas semanales deben ser al menos 1.',
            'horas_semanales_max.max' => 'Las horas semanales no pueden exceder 48.',
            'turnos_preferidos.required' => 'Debes seleccionar al menos un turno preferido.',
            'turnos_preferidos.min' => 'Debes seleccionar al menos un turno preferido.',
        ]);

        // Inicializar horas actuales en 0
        $validated['horas_semanales_actuales'] = 0;

        Docente::create($validated);

        return redirect()->route('docentes.index')
            ->with('success', 'Docente registrado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Docente $docente)
    {
        $docente->load(['usuario', 'carrera', 'grupos.materia']);

        return Inertia::render('Docentes/Show', [
            'docente' => $docente
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Docente $docente)
    {
        $docente->load('usuario');
        $carreras = Carrera::orderBy('nombre', 'asc')->get();

        return Inertia::render('Docentes/Edit', [
            'docente' => $docente,
            'carreras' => $carreras,
            'tiposContrato' => Docente::getTiposContrato(),
            'turnos' => Docente::getTurnos()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Docente $docente)
    {
        $validated = $request->validate([
            'carrera_id' => 'required|exists:carreras,id',
            'codigo_empleado' => 'required|string|max:50|unique:docentes,codigo_empleado,' . $docente->id,
            'titulo_academico' => 'required|string|max:255',
            'especializacion' => 'nullable|string|max:255',
            'tipo_contrato' => 'required|in:tiempo_completo,medio_tiempo,por_horas,invitado',
            'fecha_contratacion' => 'required|date',
            'horas_semanales_max' => 'required|integer|min:1|max:48',
            'turnos_preferidos' => 'required|array|min:1',
            'turnos_preferidos.*' => 'in:manana,tarde,noche',
            'observaciones' => 'nullable|string|max:1000',
        ], [
            'carrera_id.required' => 'Debes seleccionar una carrera.',
            'carrera_id.exists' => 'La carrera seleccionada no existe.',
            'codigo_empleado.required' => 'El código de empleado es obligatorio.',
            'codigo_empleado.unique' => 'Ya existe otro docente con este código.',
            'titulo_academico.required' => 'El título académico es obligatorio.',
            'tipo_contrato.required' => 'El tipo de contrato es obligatorio.',
            'tipo_contrato.in' => 'El tipo de contrato seleccionado no es válido.',
            'fecha_contratacion.required' => 'La fecha de contratación es obligatoria.',
            'horas_semanales_max.required' => 'Las horas semanales máximas son obligatorias.',
            'horas_semanales_max.min' => 'Las horas semanales deben ser al menos 1.',
            'horas_semanales_max.max' => 'Las horas semanales no pueden exceder 48.',
            'turnos_preferidos.required' => 'Debes seleccionar al menos un turno preferido.',
            'turnos_preferidos.min' => 'Debes seleccionar al menos un turno preferido.',
        ]);

        $docente->update($validated);

        return redirect()->route('docentes.index')
            ->with('success', 'Docente actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Docente $docente)
    {
        try {
            // Verificar si el docente tiene grupos asignados activos
            $gruposActivos = $docente->grupos()->whereNull('fecha_eliminacion')->count();
            
            if ($gruposActivos > 0) {
                return redirect()->route('docentes.index')
                    ->with('error', 'No se puede eliminar el docente porque tiene ' . $gruposActivos . ' grupo(s) asignado(s).');
            }

            // Soft delete
            $docente->delete();

            return redirect()->route('docentes.index')
                ->with('success', 'Docente eliminado exitosamente.');
                
        } catch (\Exception $e) {
            return redirect()->route('docentes.index')
                ->with('error', 'Error al eliminar el docente: ' . $e->getMessage());
        }
    }
}