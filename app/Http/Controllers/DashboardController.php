<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\Materia;
use App\Models\Grupo;
use App\Models\Aula;
use App\Models\GestionAcademica;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_docentes' => Docente::count(),
            'total_materias' => Materia::count(),
            'total_grupos' => Grupo::count(),
            'total_aulas' => Aula::count(),
            'total_semestres' => GestionAcademica::count(),
            'semestre_activo' => GestionAcademica::activa()->first(),
        ];

        return Inertia::render('Dashboard/Index', [
            'stats' => $stats,
        ]);
    }
}