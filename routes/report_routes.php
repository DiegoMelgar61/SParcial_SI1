<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Config;
use App\Classes\Postgres_DB;

// ===============================
// 1. MÓDULO PRINCIPAL DE REPORTES
// ===============================
Route::get('/reportes', function () {
    if (!Session::has('user_code')) {
        return redirect('/login');
    }

    if (Session::get('user_role') != 'admin') {
        return redirect('/');
    }

    $user = [
        'nomb_comp' => Session::get('name'),
        'rol'       => Session::get('user_role'),
        'ci'        => Session::get('ci'),
        'correo'    => Session::get('mail'),
        'tel'       => Session::get('tel'),
    ];

    try {
        $db = Config::$db;
        $db->create_conection();
        $accion = 'ACCESO A MÓDULO REPORTES';
        $fecha = date('Y-m-d H:i:s');
        $db->save_log_bitacora($accion, $fecha, 'SUCCESS', 'El usuario accedió al módulo de reportes.', Session::get('user_code'));
    } catch (Exception $e) {
        return redirect('/')->with('error', 'Error al registrar acceso: ' . $e->getMessage());
    } finally {
        if (isset($db)) $db->close_conection();
    }

    return view('reportes', ['user' => $user]);
});


// ===================================
// 2. VISTA WEB: REPORTE DE ASISTENCIA
// ===================================
Route::get('/reportes/asistencia/ver', function () {
    if (!Session::has('user_code')) return redirect('/login');
    if (Session::get('user_role') != 'admin') return redirect('/');

    $user = [
        'nomb_comp' => Session::get('name'),
        'rol'       => Session::get('user_role'),
        'ci'        => Session::get('ci'),
        'correo'    => Session::get('mail'),
        'tel'       => Session::get('tel'),
    ];

    try {
        $db = Config::$db;
        $db->create_conection();

        $sql = "SELECT a.id, a.fecha, a.estado, a.metodo_r, p.nomb_comp AS docente, m.nombre AS materia, g.sigla AS grupo
                FROM ex_g32.asistencia a
                INNER JOIN ex_g32.clase c ON c.id = a.id_clase
                INNER JOIN ex_g32.usuario u ON u.codigo = c.usuario_codigo
                INNER JOIN ex_g32.persona p ON p.ci = u.ci
                INNER JOIN ex_g32.materia_grupo mg ON mg.id = c.id_materia_grupo
                INNER JOIN ex_g32.materia m ON m.sigla = mg.sigla_materia
                INNER JOIN ex_g32.grupo g ON g.sigla = mg.sigla_grupo
                ORDER BY a.fecha DESC;";
        $stmt = $db->execute_query($sql);
        $asistencias = $db->fetch_all($stmt);
    } catch (Exception $e) {
        $asistencias = [];
    } finally {
        if (isset($db)) $db->close_conection();
    }

    return view('reportes_asistencias', ['user' => $user, 'asistencias' => $asistencias]);
});


// =================================
// 3. VISTA WEB: REPORTE DE LICENCIAS
// =================================
Route::get('/reportes/licencia/ver', function () {
    if (!Session::has('user_code')) return redirect('/login');
    if (Session::get('user_role') != 'admin') return redirect('/');

    $user = [
        'nomb_comp' => Session::get('name'),
        'rol'       => Session::get('user_role'),
        'ci'        => Session::get('ci'),
        'correo'    => Session::get('mail'),
        'tel'       => Session::get('tel'),
    ];

    try {
        $db = Config::$db;
        $db->create_conection();

        $sql = "SELECT l.nro, p.nomb_comp AS docente, l.descripcion, l.fecha_i, l.fecha_f, l.fecha_hora
                FROM ex_g32.licencia l
                INNER JOIN ex_g32.usuario u ON u.codigo = l.codigo_usuario
                INNER JOIN ex_g32.persona p ON p.ci = u.ci
                ORDER BY l.fecha_hora DESC;";
        $stmt = $db->execute_query($sql);
        $licencias = $db->fetch_all($stmt);
    } catch (Exception $e) {
        $licencias = [];
    } finally {
        if (isset($db)) $db->close_conection();
    }

    return view('reportes_licencias', ['user' => $user, 'licencias' => $licencias]);
});


// =============================
// 4. EXPORTAR REPORTES (PDF/EXCEL)
// =============================
Route::get('/api/reportes/{tipo}/{formato}', function ($tipo, $formato) {
    if (!Session::has('user_code')) return redirect('/login');
    if (Session::get('user_role') != 'admin') return redirect('/');

    $formatos = ['pdf', 'excel'];
    if (!in_array($formato, $formatos)) {
        return response('Formato inválido.', 400);
    }

    try {
        if ($formato === 'pdf') {
            $filePath = generar_reporte_pdf($tipo);
        } else {
            $filePath = generar_reporte_excel($tipo);
        }

        return response()->download($filePath);

    } catch (Exception $e) {
        return response('Error al generar reporte: ' . $e->getMessage(), 500);
    }
});
