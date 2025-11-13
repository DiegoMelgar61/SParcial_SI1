<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Usuario::with('rol');

        // Filtro por búsqueda
        if ($request->has('search') && $request->search != '') {
            $query->buscar($request->search);
        }

        // Filtro por rol
        if ($request->has('rol') && $request->rol != '') {
            $query->porRol($request->rol);
        }

        // Filtro por estado
        if ($request->has('estado') && $request->estado != '') {
            $query->porEstado($request->estado);
        }

        // Ordenamiento
        $sortField = $request->get('sort', 'nombre');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Paginación
        $usuarios = $query->paginate(10)->withQueryString();

        // Obtener roles para filtro
        $roles = Rol::orderBy('nombre')->get();

        // Estados disponibles
        $estados = [
            ['value' => 'activo', 'label' => 'Activo'],
            ['value' => 'inactivo', 'label' => 'Inactivo'],
            ['value' => 'pendiente_activacion', 'label' => 'Pendiente Activación'],
            ['value' => 'suspendido', 'label' => 'Suspendido'],
        ];

        return Inertia::render('Usuario/Index', [
            'usuarios' => $usuarios,
            'roles' => $roles,
            'estados' => $estados,
            'filters' => [
                'search' => $request->search,
                'rol' => $request->rol,
                'estado' => $request->estado,
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
        $roles = Rol::orderBy('nombre')->get();

        return Inertia::render('Usuario/Create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rol_id' => 'required|exists:roles,id',
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'cedula_identidad' => 'nullable|string|max:20|unique:usuarios,cedula_identidad',
            'email' => 'required|email|max:255|unique:usuarios,email',
            'telefono' => 'nullable|string|max:20',
            'contrasena' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'debe_cambiar_contrasena' => 'boolean',
            'estado' => 'required|in:activo,inactivo,pendiente_activacion,suspendido',
        ], [
            'rol_id.required' => 'Debe seleccionar un rol',
            'rol_id.exists' => 'El rol seleccionado no existe',
            'nombre.required' => 'El nombre es obligatorio',
            'apellido.required' => 'El apellido es obligatorio',
            'cedula_identidad.unique' => 'Ya existe un usuario con esta cédula de identidad',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'Debe ingresar un email válido',
            'email.unique' => 'Ya existe un usuario con este email',
            'contrasena.required' => 'La contraseña es obligatoria',
            'contrasena.confirmed' => 'Las contraseñas no coinciden',
            'estado.required' => 'Debe seleccionar un estado',
        ]);

        // Encriptar contraseña
        $validated['contrasena'] = Hash::make($validated['contrasena']);

        // Establecer valores por defecto
        $validated['debe_cambiar_contrasena'] = $request->debe_cambiar_contrasena ?? true;
        $validated['creado_por'] = auth()->id();

        $usuario = Usuario::create($validated);

        return redirect('/usuarios')
            ->with('success', 'Usuario creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Usuario $usuario)
    {
        $usuario->load(['rol', 'docente', 'creador', 'actualizador']);

        return Inertia::render('Usuario/Show', [
            'usuario' => $usuario,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Usuario $usuario)
    {
        $usuario->load('rol');
        $roles = Rol::orderBy('nombre')->get();

        return Inertia::render('Usuario/Edit', [
            'usuario' => $usuario,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Usuario $usuario)
    {
        $validated = $request->validate([
            'rol_id' => 'required|exists:roles,id',
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'estado' => 'required|in:activo,inactivo,pendiente_activacion,suspendido',
        ], [
            'rol_id.required' => 'Debe seleccionar un rol',
            'rol_id.exists' => 'El rol seleccionado no existe',
            'nombre.required' => 'El nombre es obligatorio',
            'apellido.required' => 'El apellido es obligatorio',
            'estado.required' => 'Debe seleccionar un estado',
        ]);

        $validated['actualizado_por'] = auth()->id();

        $usuario->update($validated);

        return redirect('/usuarios')
            ->with('success', 'Usuario actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Usuario $usuario)
    {
        // Verificar que no sea el usuario actual
        if ($usuario->id === auth()->id()) {
            return redirect('/usuarios')
                ->with('error', 'No puede eliminar su propio usuario');
        }

        // Verificar si es docente con horarios asignados
        if ($usuario->esDocente()) {
            $docente = $usuario->docente;
            if ($docente->horarios()->exists()) {
                return redirect('/usuarios')
                    ->with('error', 'No se puede eliminar el usuario porque tiene horarios asignados como docente');
            }
        }

        $usuario->delete();

        return redirect('/usuarios')
            ->with('success', 'Usuario eliminado exitosamente');
    }

    /**
     * Cambiar estado del usuario
     */
    public function cambiarEstado(Request $request, Usuario $usuario)
    {
        $validated = $request->validate([
            'estado' => 'required|in:activo,inactivo,pendiente_activacion,suspendido',
        ]);

        $usuario->cambiarEstado($validated['estado']);

        return redirect('/usuarios')
            ->with('success', 'Estado del usuario actualizado exitosamente');
    }

    /**
     * Cambiar contraseña del usuario
     */
    public function cambiarContrasena(Request $request, Usuario $usuario)
    {
        $validated = $request->validate([
            'contrasena' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'debe_cambiar_contrasena' => 'boolean',
        ], [
            'contrasena.required' => 'La contraseña es obligatoria',
            'contrasena.confirmed' => 'Las contraseñas no coinciden',
        ]);

        $usuario->update([
            'contrasena' => Hash::make($validated['contrasena']),
            'debe_cambiar_contrasena' => $validated['debe_cambiar_contrasena'] ?? false,
            'actualizado_por' => auth()->id(),
        ]);

        return redirect('/usuarios')
            ->with('success', 'Contraseña actualizada exitosamente');
    }

    /**
     * Desbloquear usuario
     */
    public function desbloquear(Usuario $usuario)
    {
        if (!$usuario->estaBloqueado()) {
            return redirect('/usuarios')
                ->with('info', 'El usuario no está bloqueado');
        }

        $usuario->resetearIntentosFallidos();

        return redirect('/usuarios')
            ->with('success', 'Usuario desbloqueado exitosamente');
    }

    /**
     * Resetear intentos fallidos
     */
    public function resetearIntentos(Usuario $usuario)
    {
        $usuario->resetearIntentosFallidos();

        return redirect('/usuarios')
            ->with('success', 'Intentos fallidos reseteados exitosamente');
    }
}