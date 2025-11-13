<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\AsistenciaDocenteController;

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

    // Rutas de Asistencia Docente
    Route::prefix('asistencia-docente')->name('asistencia-docente.')->group(function () {
        Route::get('/', [AsistenciaDocenteController::class, 'index'])->name('index');
        Route::get('/crear', [AsistenciaDocenteController::class, 'create'])->name('create');
        Route::post('/', [AsistenciaDocenteController::class, 'store'])->name('store');
        Route::get('/mis-materias', [AsistenciaDocenteController::class, 'misMaterias'])->name('mis-materias');
        Route::get('/{id}', [AsistenciaDocenteController::class, 'show'])->name('show');
    });
});