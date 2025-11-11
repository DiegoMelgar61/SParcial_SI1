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

    // VALIDACION: USUARIO ADMIN
    if (Session::get('user_role') != 'docente' and Session::get('user_role') != 'admin') 
    {
        return redirect('/');
    }

    try {
        $db = Config::$db;
        $db->create_conection(); // Crea la conexión

        //CONTAR USUARIOS
        $sql = "SELECT COUNT(*) as cant_user FROM ex_g32.usuario";
        $stmt = $db->execute_query($sql);
        $cant_usuarios = $db->fetch_one($stmt);

        //CONTAR DOCENTES
        $sql = "SELECT COUNT(U.codigo) as cant_docent
                FROM ex_g32.usuario u
                INNER JOIN ex_g32.rol r ON r.id = u.id_rol 
                WHERE r.nombre = 'docente'";
        $stmt = $db->execute_query($sql);
        $cant_docente = $db->fetch_one($stmt);


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