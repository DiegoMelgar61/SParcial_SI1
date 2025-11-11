<?php

#IMPORTAR CLASES Y LIBRERIAS
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Config;
use App\Classes\Postgres_DB;

//MODULO DOCENNCIA
Route::get('/docen/mod-doc', function () {
    // VALIDACION: USUARIO EN SESION
    if (!Session::has('user_code')) {
        return redirect('/login');
    }

    // VALIDACION: USUARIO ADMIN O DOCENTE
    if (Session::get('user_role') != 'docente' and Session::get('user_role') != 'admin') 
    {
        return redirect('/');
    }

    try {
        $db = Config::$db;
        $db->create_conection(); // Crea la conexión

        //REGISTRO DE BITACORA
        $accion = 'ACCESO A MÓDULO ADMIN';
        $fecha = date('Y-m-d H:i:s');
        $estado = 'SUCCESS';
        $comentario = 'Acceso al módulo de docencia.';
        $codigo = Session::get('user_code');
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);

        //RECUPERAR DATOS DEL USUARIO
        $user = [
            'nomb_comp' => Session::get('name'),  // Asegúrate de tener este dato en la sesión
            'rol' => Session::get('user_role'),
            'ci' => Session::get('ci'),
            'correo' => Session::get('mail'),
            'tel' => Session::get('tel'),
        ];

        //ENVIAR RESULTADOS A LA VISTA
        return view('mod_docencia', [
            'user' => $user
        ]);
    } catch (Exception $e) {
        // Manejo de excepciones: Si la conexión falla, redirige al login
        return redirect('/')->with('error', 'Error al conectar con la base de datos: ' . $e->getMessage());
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

Route::get('/docen/asistencia', function () {
    // VALIDACION: USUARIO EN SESION
    if (!Session::has('user_code')) {
        return redirect('/login');
    }

    // VALIDACION: USUARIO ADMIN O DOCENTE
    if (Session::get('user_role') != 'docente' and Session::get('user_role') != 'admin') 
    {
        return redirect('/');
    }

    try {
        $db = Config::$db;
        $db->create_conection(); // Crea la conexión

        //OBTENER DATOS DE LA DB

        //MATERIAS PROXIMAS A MARCAR ASISTENCIA
        $sql="
            SELECT m.sigla sigla_materia,m.nombre as nombre_materia,mg.sigla_grupo as grupo,h.dia,h.hora_i as hora_inicio,h.hora_f as hora_final
            FROM ex_g32.usuario u
            INNER JOIN ex_g32.clase c ON c.usuario_codigo = u.codigo 
            INNER JOIN ex_g32.materia_grupo mg ON mg.id =c.id_materia_grupo 
            INNER JOIN ex_g32.materia m ON m.sigla =mg.sigla_materia
            INNER JOIN ex_g32.horario h ON h.id =c.id_horario  
            WHERE u.codigo = :codigo
        ";

        $params=[
            ':codigo'=>Session::get('user_code')
        ];

        $stmt=$db->execute_query($sql,$params);
        $prox_asist=$db->fetch_all($stmt);


        $sql="
            SELECT m.sigla sigla_materia,m.nombre as nombre_materia,mg.sigla_grupo as grupo,h.dia,h.hora_i as hora_inicio,h.hora_f as hora_final
            FROM ex_g32.usuario u
            INNER JOIN ex_g32.clase c ON c.usuario_codigo = u.codigo 
            INNER JOIN ex_g32.materia_grupo mg ON mg.id =c.id_materia_grupo 
            INNER JOIN ex_g32.materia m ON m.sigla =mg.sigla_materia
            INNER JOIN ex_g32.horario h ON h.id =c.id_horario 
            INNER JOIN ex_g32.asistencia a ON c.id =a.id_clase 
            WHERE u.codigo = :codigo
        ";

        $stmt=$db->execute_query($sql,$params);
        $asistencias=$db->fetch_all($stmt);

        //REGISTRO DE BITACORA
        $accion = 'ACCESO A MÓDULO ADMIN';
        $fecha = date('Y-m-d H:i:s');
        $estado = 'SUCCESS';
        $comentario = 'Acceso al módulo de docencia.';
        $codigo = Session::get('user_code');
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);

        //RECUPERAR DATOS DEL USUARIO
        $user = [
            'nomb_comp' => Session::get('name'),  // Asegúrate de tener este dato en la sesión
            'rol' => Session::get('user_role'),
            'ci' => Session::get('ci'),
            'correo' => Session::get('mail'),
            'tel' => Session::get('tel'),
        ];

        //ENVIAR RESULTADOS A LA VISTA
        return view('docen_asist', [
            'user' => $user,
            'prox_asist'=>$prox_asist,
            'asistencias'=>$asistencias
        ]);

    } 
    catch (Exception $e) 
    {
        // Manejo de excepciones: Si la conexión falla, redirige al login
        return redirect('/')->with('error', 'Error al conectar con la base de datos: ' . $e->getMessage());
    } 
    finally 
    {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});