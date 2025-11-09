<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\AulaController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\GestionAcademicaController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\UsuarioController;

// Public routes

Route::get('/', function () {
    return redirect()->route('login');
});

// Auth routes
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');


// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('carreras', CarreraController::class);

    // Materias routes
    Route::resource('materias', MateriaController::class);  

    //Aulas routes
    Route::resource('aulas', AulaController::class);

    //Docentes routes
    Route::resource('docentes', DocenteController::class);

    //Gestion Academica
    Route::resource('gestiones-academicas', GestionAcademicaController::class);

    //Grupos
    Route::resource('grupos', GrupoController::class);

    //Horarios
    Route::resource('horarios', HorarioController::class);

    //Roles
    Route::resource('roles', RolController::class);

    //Permisos
    // Rutas específicas deben ir ANTES del resource para evitar conflictos
    Route::get('permisos/gestion-por-rol', [PermisoController::class, 'gestionPorRol'])->name('permisos.gestion-por-rol');
    Route::post('permisos/roles/{rol}/asignar', [PermisoController::class, 'asignarPermisos'])->name('permisos.asignar');
    Route::resource('permisos', PermisoController::class);

    //Usuario
    Route::resource('usuarios', UsuarioController::class);
    Route::post('usuarios/{usuario}/cambiar-contrasena', [UsuarioController::class, 'cambiarContrasena'])->name('usuarios.cambiar-contrasena');
    Route::post('usuarios/{usuario}/cambiar-estado', [UsuarioController::class, 'cambiarEstado'])->name('usuarios.cambiar-estado');
    Route::post('usuarios/{usuario}/desbloquear', [UsuarioController::class, 'desbloquear'])->name('usuarios.desbloquear');
    Route::post('usuarios/{usuario}/resetear-intentos', [UsuarioController::class, 'resetearIntentos'])->name('usuarios.resetear-intentos');
});