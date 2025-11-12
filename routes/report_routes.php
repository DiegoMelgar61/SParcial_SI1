<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Config;
use App\Classes\Postgres_DB;

Route::get('/reportes', function () {
    // VALIDAR SESIÓN
    if (!Session::has('user_code')) {
        return redirect('/login');
    }

    // VALIDAR ROL ADMIN
    if (Session::get('user_role') != 'admin') {
        return redirect('/');
    }

    try {
        $db = Config::$db;
        $db->create_conection();

        // Total de usuarios
        $sql = "SELECT COUNT(*) AS cant_user FROM ex_g32.usuario;";
        $stmt = $db->execute_query($sql);
        $cant_usuarios = $db->fetch_one($stmt)['cant_user'] ?? 0;

        // Total de docentes
        $sql = "SELECT COUNT(u.codigo) AS cant_docente
                FROM ex_g32.usuario u
                INNER JOIN ex_g32.rol r ON r.id = u.id_rol
                WHERE LOWER(r.nombre) = 'docente';";
        $stmt = $db->execute_query($sql);
        $cant_docente = $db->fetch_one($stmt)['cant_docente'] ?? 0;

        // Total de clases
        $sql = "SELECT COUNT(*) AS total_clases FROM ex_g32.clase;";
        $stmt = $db->execute_query($sql);
        $cant_clases = $db->fetch_one($stmt)['total_clases'] ?? 0;

        // Total de asistencias registradas
        $sql = "SELECT COUNT(*) AS total_asist FROM ex_g32.asistencia;";
        $stmt = $db->execute_query($sql);
        $cant_asist = $db->fetch_one($stmt)['total_asist'] ?? 0;

        // Total de licencias
        $sql = "SELECT COUNT(*) AS total_lic FROM ex_g32.licencia;";
        $stmt = $db->execute_query($sql);
        $cant_lic = $db->fetch_one($stmt)['total_lic'] ?? 0;

        // Gestión actual
        $sql = "SELECT nombre
                FROM ex_g32.gestion
                WHERE CURRENT_DATE BETWEEN fecha_i AND fecha_f;";
        $stmt = $db->execute_query($sql);
        $gestion = $db->fetch_one($stmt);
        $gestion_actual = $gestion['nombre'] ?? 'Sin gestión activa';

        // Registrar en bitácora
        $accion = 'ACCESO A MÓDULO REPORTES';
        $fecha = date('Y-m-d H:i:s');
        $estado = 'SUCCESS';
        $comentario = 'El usuario accedió al módulo de reportes.';
        $codigo = Session::get('user_code');
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);

        // Usuario actual
        $user = [
            'nomb_comp' => Session::get('name'),
            'rol' => Session::get('user_role'),
            'ci' => Session::get('ci'),
            'correo' => Session::get('mail'),
            'tel' => Session::get('tel'),
        ];

        // Renderizar vista
        return view('reportes', [
            'cant_usuarios'   => $cant_usuarios,
            'cant_docente'    => $cant_docente,
            'cant_clases'     => $cant_clases,
            'cant_asist'      => $cant_asist,
            'cant_lic'        => $cant_lic,
            'gestion_actual'  => $gestion_actual,
            'user'            => $user,
        ]);
    } catch (Exception $e) {
        return redirect('/')->with('error', 'Error en la base de datos: ' . $e->getMessage());
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});
