<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Config;
use App\Classes\Postgres_DB;

require_once app_path('/services/help_functs.php');

//ENDPOINT GESTOR DE USUARIOS: ELIMINAR
Route::post('/admin/users/delete', function (Request $request) {
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');

    //VALIDACION:USUARIO EN SESION
    if (!Session::has('user_code')) {
        return response()->json([
            'success' => false,
            'message' => 'Usuario no Autenticado.'
        ]);
    }

    //VALIDACION:USUARIO ADMIN
    if (Session::get('user_role') !== 'admin') {
        return response()->json([
            'success' => false,
            'message' => 'El Usuario no es Administrador.'
        ]);
    }

    //OBTENER DATOS
    $data = $request->json()->all();
    $codigo_eliminar = $data['id'];

    //OBTENER DATOS BITACORA
    $accion = 'ELIMINAR USUARIO';
    $fecha = date('Y-m-d H:i:s');
    $estado = 'ERROR';
    $comentario = 'Eliminar un usuario indicado.';
    $codigo = Session::get('user_code');

    $db = Config::$db;
    try {
        $db->create_conection();

        $sql = "  SELECT ci
                FROM ex_g32.usuario
                WHERE codigo= :codigo";
        $params = [':codigo' => $codigo_eliminar];

        $stmt = $db->execute_query($sql, $params);
        $ci = $db->fetch_one($stmt);

        if ($ci == null) {
            $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);
            return response()->json([
                'success' => false,
                'message' => 'El usuario no esta Registrado en el Sistema.'
            ]);
        }
        $sql = "  DELETE FROM ex_g32.persona
                WHERE ci= :ci";
        $params = [':ci' => $ci['ci']];
        $stmt = $db->execute_query($sql, $params);

        $estado = 'SUCCESS';
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);
        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado Exitosamente'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Ocurrio un error en el proceso.',
            'error' => $e->getMessage()
        ], 500);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});
//ENDPOINT GESTION DE USUARIO
Route::get('/admin/users', function () {
    //VALIDACION: USUARIO EN SESION
    if (!Session::has('user_code')) {
        return redirect('/login');
    }

    //VALIDACION: USUARIO ADMIN
    if (Session::get('user_role') != 'admin') {
        return redirect('/');
    }

    $db = Config::$db;
    try {
        $db->create_conection();

        //REGISTRO DE BITACORA
        $accion = 'GESTION DE USUARIOS';
        $fecha = date('Y-m-d H:i:s');
        $estado = 'ERROR';
        $comentario = 'Consultar Usuarios Registrados.';
        $codigo = Session::get('user_code');

        //OBTENER BITACORA
        $sql = "  SELECT u.codigo,u.ci,p.nomb_comp,p.tel,p.correo,r.nombre as rol
                FROM ex_g32.usuario u
                INNER JOIN ex_g32.persona p ON p.ci=u.ci
                INNER JOIN ex_g32.rol r ON r.id =u.id_rol";
        $stmt = $db->execute_query($sql);
        $usuarios = $db->fetch_all($stmt);

        $estado = 'SUCCESS';
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);

        //RECUPERAR DATOS DEL USUARIO
        $user = [
            'nomb_comp' => Session::get('name'),  // Asegúrate de tener este dato en la sesión
            'rol' => Session::get('user_role'),
            'ci' => Session::get('ci'),
            'correo' => Session::get('mail'),
            'tel' => Session::get('tel'),
        ];

        return view('admin_users', ['usuarios' => $usuarios, 'user' => $user]);
    } catch (Exception $e) {
        return redirect('/admin')->with('error', 'Error al consultar usuarios: ' . $e->getMessage());
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

//RUTA ENCARGADA DE LA BITACORA
Route::get('/admin/bitacora', function () {
    //VALIDACION: USUARIO EN SESION
    if (!Session::has('user_code')) {
        return redirect('/login');
    }

    //VALIDACION: USUARIO ADMIN
    if (Session::get('user_role') != 'admin') {
        return redirect('/');
    }

    $db = Config::$db;
    try {
        $db->create_conection();

        //REGISTRO DE BITACORA
        $accion = 'CONSULTAR BITACORA';
        $fecha = date('Y-m-d H:i:s');
        $estado = 'ERROR';
        $comentario = 'Consultar Historial de acciones.';
        $codigo = Session::get('user_code');

        //OBTENER BITACORA
        $sql = "  SELECT *
                FROM ex_g32.bitacora
                ORDER BY fecha_hora DESC
                LIMIT 30";
        $stmt = $db->execute_query($sql);
        $bitacora = $db->fetch_all($stmt);

        $estado = 'SUCCESS';
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);

        //RECUPERAR DATOS DEL USUARIO
        $user = [
            'nomb_comp' => Session::get('name'),  // Asegúrate de tener este dato en la sesión
            'rol' => Session::get('user_role'),
            'ci' => Session::get('ci'),
            'correo' => Session::get('mail'),
            'tel' => Session::get('tel'),
        ];

        return view('admin_bitacora', ['bitacora' => $bitacora, 'user' => $user]);
    } catch (Exception $e) {
        return redirect('/admin')->with('error', 'Error al consultar la bitácora: ' . $e->getMessage());
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

//ENDPOINT GESTOR DE USUARIOS: CREAR
Route::post('/admin/users/store', function (Request $request) {
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');


    //VALIDACION:USUARIO EN SESION
    if (!Session::has('user_code')) {
        return response()->json([
            'success' => false,
            'message' => 'Usuario no Autenticado.'
        ]);
    }

    //VALIDACION:USUARIO ADMIN
    if (Session::get('user_role') !== 'admin') {
        return response()->json([
            'success' => false,
            'message' => 'El Usuario no es Administrador.'
        ]);
    }

    //OBTENER DATOS
    $data = $request->json()->all();
    $ci = $data['ci'];
    $nomb_comp = $data['nomb_comp'];
    $fecha_n = $data['fecha_nac'];
    $correo = $data['correo'];
    $tel = $data['tel'];
    $rol = $data['rol'];
    $password = $data['password'];

    //OBTENER DATOS BITACORA
    $accion = 'CREAR USUARIO';
    $fecha = date('Y-m-d H:i:s');
    $estado = 'ERROR';
    $comentario = 'Registrar un usuario .';
    $codigo = Session::get('user_code');

    $db = Config::$db;

    try {

        $db->create_conection();

        // VALIDAR SI YA EXISTE EL CI EN LA TABLA PERSONA
        $sql = "SELECT ci FROM ex_g32.persona WHERE ci = :ci";
        $stmt = $db->execute_query($sql, [':ci' => $ci]);
        $existingUser = $db->fetch_one($stmt);

        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'El usuario ya esta Registrado en el Sistema.'
            ]);
        }


        // INSERTAR EN TABLA PERSONA
        $sql = "
                    INSERT INTO ex_g32.persona (ci, nomb_comp, fecha_n, correo, tel, profesion, tipo) 
                    VALUES (:ci, :nomb_comp, :fecha_n, :correo, :tel, :profesion, :tipo)
                ";
        $params = [
            ':ci' => $ci,
            ':nomb_comp' => $nomb_comp,
            ':fecha_n' => $fecha_n,
            ':correo' => $correo,
            ':tel' => $tel,
            ':profesion' => $rol,
            ':tipo' => strtolower($rol)
        ];
        $db->execute_query($sql, $params);

        // DETERMINAR ROL SEGÚN EL TIPO

        $rol_id = 0;
        if (strtolower($rol) == 'docente')
            $rol_id = 1;
        elseif (strtolower($rol) == 'admin')
            $rol_id = 2;
        else
            return response()->json([
                'success' => false,
                'message' => 'El rol no esta registrado en el Sistema.'
            ]);


        // INSERTAR EN TABLA USUARIO (CON HASH DE CONTRASEÑA)
        $sql = "
                    INSERT INTO ex_g32.usuario (password_hash, ci, id_rol) 
                    VALUES (:password_hash, :ci, :id_rol)
                ";
        $params = [
            ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
            ':ci' => $ci,
            ':id_rol' => $rol_id
        ];
        error_log(print_r($request->json()->all(), true));
        $db->execute_query($sql, $params);


        $estado = 'SUCCESS';
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);
        return response()->json([
            'success' => true,
            'message' => 'Usuario creado Exitosamente'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Ocurrio un error en el proceso.',
            'error' => $e->getMessage()
        ], 500);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});
//ENDPOINT GESTOR DE USUARIOS: Actualizar
Route::post('/admin/users/update', function (Request $request) {
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');


    //VALIDACION:USUARIO EN SESION
    if (!Session::has('user_code')) {
        return response()->json([
            'success' => false,
            'message' => 'Usuario no Autenticado.'
        ]);
    }

    //VALIDACION:USUARIO ADMIN
    if (Session::get('user_role') !== 'admin') {
        return response()->json([
            'success' => false,
            'message' => 'El Usuario no es Administrador.'
        ]);
    }

    //OBTENER DATOS
    $data = $request->json()->all();
    $ci = $data['ci'];
    $nomb_comp = $data['nomb_comp'];
    $fecha_n = $data['fecha_nac'];
    $correo = $data['correo'];
    $tel = $data['tel'];
    $rol = $data['rol'];
    $password = $data['password'];

    //OBTENER DATOS BITACORA
    $accion = 'CREAR USUARIO';
    $fecha = date('Y-m-d H:i:s');
    $estado = 'ERROR';
    $comentario = 'Registrar un usuario .';
    $codigo = Session::get('user_code');

    $db = Config::$db;

    try {

        $db->create_conection();

        // VALIDAR SI YA EXISTE EL CI EN LA TABLA PERSONA
        $sql = "SELECT ci FROM ex_g32.persona WHERE ci = :ci";
        $stmt = $db->execute_query($sql, [':ci' => $ci]);
        $existingUser = $db->fetch_one($stmt);

        if (!$existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'El usuario no esta Registrado en el Sistema.'
            ]);
        }


        // INSERTAR EN TABLA PERSONA
        $sql = "
                    UPDATE ex_g32.persona SET nomb_comp=:nomb_comp, correo=:correo, tel=:tel, tipo=:tipo
                     WHERE ci=:ci
                ";
        $params = [
            ':ci' => $ci,
            ':nomb_comp' => $nomb_comp,
            ':correo' => $correo,
            ':tel' => $tel,
            ':tipo' => strtolower($rol)
        ];
        $db->execute_query($sql, $params);

        // DETERMINAR ROL SEGÚN EL TIPO

        $rol_id = 0;
        if (strtolower($rol) == 'docente')
            $rol_id = 1;
        elseif (strtolower($rol) == 'admin')
            $rol_id = 2;
        else
            return response()->json([
                'success' => false,
                'message' => 'El rol no esta registrado en el Sistema.'
            ]);


        // INSERTAR EN TABLA USUARIO (CON HASH DE CONTRASEÑA)
        if ($password != '') {
            $sql = "
                    UPDATE ex_g32.usuario SET password_hash=:password_hash
                     WHERE ci=:ci
                ";
            $params = [
                ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
                ':ci' => $ci
            ];
        }

        $sql = "
                    UPDATE ex_g32.usuario SET id_rol=:id_rol
                     WHERE ci=:ci
                ";
        $params = [
            'id_rol' => $rol_id,
            'ci' => $ci
        ];
error_log(print_r($request->json()->all(), true));

        $db->execute_query($sql, params: $params);
        error_log(print_r($request->json()->all(), true));

        $estado = 'SUCCESS';
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);
        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado Exitosamente'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Ocurrio un error en el proceso.',
            'error' => $e->getMessage()
        ], 500);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});
//RUTA GESTORA DEL MODULO DE ADMINISTRADORES
Route::get('/admin/mod-adm', function () {
    // VALIDACION: USUARIO EN SESION
    if (!Session::has('user_code')) {
        return redirect('/login');
    }

    // VALIDACION: USUARIO ADMIN
    if (Session::get('user_role') != 'admin') {
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

        //SELECCIONAR GESTION ACTUAL
        $sql = "  SELECT nombre
                FROM ex_g32.gestion  
                WHERE CURRENT_DATE BETWEEN fecha_i AND fecha_f;";
        $stmt = $db->execute_query($sql);
        $gestion_actual = $db->fetch_one($stmt);

        //REGISTRO DE BITACORA
        $accion = 'ACCESO A MÓDULO ADMIN';
        $fecha = date('Y-m-d H:i:s');
        $estado = 'SUCCESS';
        $comentario = 'Acceso al módulo de administración.';
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
        return view('mod_admin', [
            'cant_usuarios' => $cant_usuarios['cant_user'],
            'cant_docente' => $cant_docente['cant_docent'],
            'gestion_actual' => $gestion_actual['nombre'],
            'user' => $user,
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


//RUTA GESTORA DEL MODULO DE IMPORTACION DE USUARIOS
Route::match(['get', 'post'], '/admin/import-users', function (Request $request) {
    //EVITAR ERRORES CORS
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');

    if (!Session::has('user_code')) {
        return redirect('/login');
    }

    if ($request->isMethod('get')) {
        return view('import_user');
    }

    if (!$request->hasFile('archivo')) {
        return response()->json([
            'success' => false,
            'message' => 'No se ha enviado ningun archivo.'
        ], 400);
    }

    $file = $request->file('archivo');

    //VERIFICAMOS EL TIPO DE ARCHIVO (.xlsx/.csv)
    $allowed_extensions = ['csv', 'xlsx'];
    $extension = $file->getClientOriginalExtension();

    if (!in_array($extension, $allowed_extensions)) {
        return response()->json([
            'success' => false,
            'message' => 'Formato de archivo no válido. Solo se permiten archivos .csv o .xlsx.'
        ], 400);
    }

    //REGISTRAR BITACORA
    $accion = 'IMPORTAR USUARIOS';
    $fecha = date('Y-m-d H:i:s');
    $estado = 'ERROR';
    $comentario = 'Inicio del proceso de importación de usuarios.';
    $codigo = Session::get('user_code');
    $db = Config::$db;

    //PROCESAR EL ARCHIVO
    try {

        $result = importar_usuarios($file, $extension);

        $db->create_conection();

        $estado = 'SUCCESS';
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);
        return response()->json([
            'success' => true,
            'message' => 'Usuarios cargados exitosamente',
            'data' => $result
        ]);
    } catch (\Exception $e) {
        $estado = 'ERROR';
        $comentario = 'Error durante la carga del archivo: ' . $e->getMessage();
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);
        return response()->json([
            'success' => false,
            'message' => 'Ocurrio un error al cargar los usuarios.',
            'error' => $e->getMessage()
        ]);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }

});




//ENDPOINT ADMINISTRAR ROLES Y PERMISOS

//GET PERMISOS
Route::get('/admin/permisos', function () {
    //VALIDACION: USUARIO EN SESION
    if (!Session::has('user_code')) {
        return redirect('/login');
    }

    //VALIDACION: USUARIO ADMIN
    if (Session::get('user_role') != 'admin') {
        return redirect('/');
    }

    $db = Config::$db;
    try {
        $db->create_conection();

        //REGISTRO DE BITACORA
        $accion = 'GESTION DE PERMISOS';
        $fecha = date('Y-m-d H:i:s');
        $estado = 'ERROR';
        $comentario = 'Consultar permisos registrados.';
        $codigo = Session::get('user_code');

        //OBTENER BITACORA
        $sql = "  SELECT id, nombre, descripcion
                FROM ex_g32.permisos
                ORDER BY nombre";
        $stmt = $db->execute_query($sql);
        $permisos = $db->fetch_all($stmt);

        $estado = 'SUCCESS';
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);

        //RECUPERAR DATOS DEL USUARIO
        $user = [
            'nomb_comp' => Session::get('name'),  // Asegúrate de tener este dato en la sesión
            'rol' => Session::get('user_role'),
            'ci' => Session::get('ci'),
            'correo' => Session::get('mail'),
            'tel' => Session::get('tel'),
        ];

        return view('admin_permisos', ['permisos' => $permisos, 'user' => $user]);
    } catch (Exception $e) {
        return redirect('/admin')->with('error', 'Error al consultar usuarios: ' . $e->getMessage());
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

// CREAR PERMISOS
Route::post('/admin/permisos/create', function (Request $request) {


    //VALIDACION:USUARIO EN SESION
    if (!Session::has('user_code')) {
        return response()->json([
            'success' => false,
            'message' => 'Usuario no Autenticado.'
        ]);
    }

    //VALIDACION:USUARIO ADMIN
    if (Session::get('user_role') !== 'admin') {
        return response()->json([
            'success' => false,
            'message' => 'El Usuario no es Administrador.'
        ]);
    }

    //OBTENER DATOS
    $data = $request->json()->all();
    $nombre_p = $data['nombre'];
    $descripcion_p = $data['descripcion'];



    //OBTENER DATOS BITACORA
    $accion = 'CREAR PERMISO';
    $fecha = date('Y-m-d H:i:s');
    $estado = 'ERROR';
    $comentario = 'Creacion de permisos.';
    $codigo = Session::get('user_code');

    $db = Config::$db;
    try {
        $db->create_conection();

        $sql = "  SELECT nombre
                FROM ex_g32.permisos
                WHERE nombre= :nombre";
        $params = [':nombre' => $nombre_p];

        $stmt = $db->execute_query($sql, $params);
        $name = $db->fetch_one($stmt);

        if ($name != null) {
            $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);
            return response()->json([
                'success' => false,
                'message' => 'El permiso ya existe en el sistema.'
            ]);
        }

        $sql = " INSERT INTO ex_g32.permisos (nombre, descripcion)
                VALUES (:nombre, :descripcion)";
        $params = [':nombre' => $nombre_p, ':descripcion' => $descripcion_p];
        $stmt = $db->execute_query($sql, $params);

        $estado = 'SUCCESS';
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);
        return response()->json([
            'success' => true,
            'message' => 'Permiso creado exitosamente'
        ]);
    } catch (Exception $e) {

        return response()->json([
            'success' => false,
            'message' => 'Ocurrio un error en el proceso.',
            'error' => $e->getMessage()
        ], 500);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

//ENDPOINT ELIMINAR PERMISO

Route::post('/admin/permisos/delete', function (Request $request) {
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');

    //VALIDACION:USUARIO EN SESION
    if (!Session::has('user_code')) {
        return response()->json([
            'success' => false,
            'message' => 'Usuario no Autenticado.'
        ]);
    }

    //VALIDACION:USUARIO ADMIN
    if (Session::get('user_role') !== 'admin') {
        return response()->json([
            'success' => false,
            'message' => 'El Usuario no es Administrador.'
        ]);
    }

    //OBTENER DATOS
    $data = $request->json()->all();
    $id_eliminar = $data['id'];

    //OBTENER DATOS BITACORA
    $accion = 'ELIMINAR PERMISO';
    $fecha = date('Y-m-d H:i:s');
    $estado = 'ERROR';
    $comentario = 'Eliminar un permiso indicado.';
    $codigo = Session::get('user_code');

    $db = Config::$db;
    try {
        $db->create_conection();

        $sql = "  SELECT id
                FROM ex_g32.permisos
                WHERE id= :id";
        $params = [':id' => $id_eliminar];

        $stmt = $db->execute_query($sql, $params);
        $id = $db->fetch_one($stmt);

        if ($id == null) {
            $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);
            return response()->json([
                'success' => false,
                'message' => 'El permiso no esta Registrado en el Sistema.'
            ]);
        }
        $sql = "  DELETE FROM ex_g32.permisos
                WHERE id= :id";
        $params = [':id' => $id_eliminar];

        $stmt = $db->execute_query($sql, $params);
        $estado = 'SUCCESS';
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);
        return response()->json([
            'success' => true,
            'message' => 'Permiso eliminado Exitosamente'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Ocurrio un error en el proceso.',
            'error' => $e->getMessage()
        ], 500);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

//ENDPOINT ACTUALIZAR PERMISO

Route::post('/admin/permisos/update', function (Request $request) {
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');

    //VALIDACION:USUARIO EN SESION
    if (!Session::has('user_code')) {
        return response()->json([
            'success' => false,
            'message' => 'Usuario no Autenticado.'
        ]);
    }

    //VALIDACION:USUARIO ADMIN
    if (Session::get('user_role') !== 'admin') {
        return response()->json([
            'success' => false,
            'message' => 'El Usuario no es Administrador.'
        ]);
    }

    //OBTENER DATOS
    $data = $request->json()->all();
    $id_mod = $data['id'];
    $nombre_p = $data['nombre'];
    $descripcion_p = $data['descripcion'];

    //OBTENER DATOS BITACORA
    $accion = 'MOFICIAR PERMISO';
    $fecha = date('Y-m-d H:i:s');
    $estado = 'ERROR';
    $comentario = 'Modifica un permiso indicado.';
    $codigo = Session::get('user_code');

    $db = Config::$db;
    try {
        $db->create_conection();

        $sql = "  SELECT id
                FROM ex_g32.permisos
                WHERE id= :id";
        $params = [':id' => $id_mod];

        $stmt = $db->execute_query($sql, $params);
        $id = $db->fetch_one($stmt);

        if ($id == null) {
            $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);
            return response()->json([
                'success' => false,
                'message' => 'El permiso no esta Registrado en el Sistema.'
            ]);
        }
        $sql = "  UPDATE ex_g32.permisos
                SET nombre= :nombre, descripcion= :descripcion
                WHERE id= :id";
        $params = [':id' => $id_mod, ':nombre' => $nombre_p, ':descripcion' => $descripcion_p];

        $stmt = $db->execute_query($sql, $params);
        $estado = 'SUCCESS';
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);
        return response()->json([
            'success' => true,
            'message' => 'Permiso actualizado Exitosamente'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Ocurrio un error en el proceso.',
            'error' => $e->getMessage()
        ], 500);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

//ENDPOINT ROLES GET

//GET roles
Route::get('/admin/roles', function () {
    //VALIDACION: USUARIO EN SESION
    if (!Session::has('user_code')) {
        return redirect('/login');
    }

    //VALIDACION: USUARIO ADMIN
    if (Session::get('user_role') != 'admin') {
        return redirect('/');
    }

    $db = Config::$db;
    try {
        $db->create_conection();

        //REGISTRO DE BITACORA
        $accion = 'GESTION DE ROLES';
        $fecha = date('Y-m-d H:i:s');
        $estado = 'ERROR';
        $comentario = 'Consultar roles registrados.';
        $codigo = Session::get('user_code');

        //OBTENER ROLES
        $sql = "  SELECT id, nombre, descripcion
                FROM ex_g32.rol
                ORDER BY nombre";
        $stmt = $db->execute_query($sql);
        $roles = $db->fetch_all($stmt);


        //RECUPERAR DATOS DEL USUARIO
        $user = [
            'nomb_comp' => Session::get('name'),  // Asegúrate de tener este dato en la sesión
            'rol' => Session::get('user_role'),
            'ci' => Session::get('ci'),
            'correo' => Session::get('mail'),
            'tel' => Session::get('tel'),
        ];

        // SEGUNDO: PARA CADA ROL, OBTENER SUS PERMISOS
        foreach ($roles as &$rol) {
            $sql_permisos = "SELECT p.id, p.nombre 
                            FROM ex_g32.permisos p, ex_g32.rol_permiso rp 
                            WHERE rp.id_rol = :id AND rp.id_permiso = p.id";

            $params = [':id' => $rol['id']];
            $stmt_permisos = $db->execute_query($sql_permisos, $params);
            $rol['permisos'] = $db->fetch_all($stmt_permisos);
        }
        $estado = 'SUCCESS';
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);


        return view('admin_roles', ['roles' => $roles, 'user' => $user]);
    } catch (Exception $e) {
        return redirect('/admin')->with('error', 'Error al consultar roles: ' . $e->getMessage());
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

// Compatibility endpoints used by the frontend scripts
// Listar todos los permisos (JSON)
Route::get('/admin/permisos/listar', function () {
    if (!Session::has('user_code')) {
        return response()->json([], 401);
    }
    $db = Config::$db;
    try {


        $db->create_conection();
        $sql = "SELECT id, nombre, descripcion FROM ex_g32.permisos ORDER BY nombre";
        $stmt = $db->execute_query($sql);
        $permisos = $db->fetch_all($stmt);

        return response()->json($permisos);
    } catch (Exception $e) {
        return response()->json([], 500);
    } finally {
        if (isset($db) && $db !== null)
            $db->close_conection();
    }
});

// Obtener permisos asignados a un rol (compatibilidad con /admin/roles/get-permisos?role_id=)
Route::get('/admin/roles/get-permisos', function (Request $request) {
    if (!Session::has('user_code')) {
        return response()->json([], 401);
    }
    $roleId = $request->query('role_id');
    if (!$roleId)
        return response()->json([], 400);

    $db = Config::$db;
    try {


        $db->create_conection();
        $sql = "SELECT id_permiso FROM ex_g32.rol_permiso WHERE id_rol = :rol";
        $params = [':rol' => $roleId];
        $stmt = $db->execute_query($sql, $params);
        $rows = $db->fetch_all($stmt);
        $ids = array_map(function ($r) {
            return $r['id_permiso'];
        }, $rows);
        return response()->json($ids);
    } catch (Exception $e) {
        return response()->json([], 500);
    } finally {
        if (isset($db) && $db !== null)
            $db->close_conection();
    }
});

// Crear rol (compatibilidad POST)
Route::post('/admin/roles/create', function (Request $request) {
    if (!Session::has('user_code')) {
        return response()->json(['success' => false, 'message' => 'Usuario no autenticado'], 401);
    }
    if (Session::get('user_role') !== 'admin') {
        return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
    }

    $data = $request->json()->all();
    $nombre = $data['nombre'] ?? '';
    $descripcion = $data['descripcion'] ?? '';
    $permisos = $data['permisos'] ?? [];

    $db = Config::$db;
    try {

        //REGISTRO DE BITACORA
        $accion = 'CREAR ROL';
        $fecha = date('Y-m-d H:i:s');
        $estado = 'ERROR';
        $comentario = 'Crea un nuevo rol.';
        $codigo = Session::get('user_code');

        $db->create_conection();
        $pdo = $db->get_connection();
        $pdo->beginTransaction();

        $sql = "INSERT INTO ex_g32.rol (nombre, descripcion) VALUES (:nombre, :descripcion) RETURNING id";
        $params = [':nombre' => $nombre, ':descripcion' => $descripcion];
        $stmt = $db->execute_query($sql, $params);
        $row = $db->fetch_one($stmt);
        $newId = $row['id'] ?? null;

        if ($newId && !empty($permisos)) {
            foreach ($permisos as $pid) {
                $sql2 = "INSERT INTO ex_g32.rol_permiso (id_rol, id_permiso) VALUES (:rol, :perm)";
                $db->execute_query($sql2, [':rol' => $newId, ':perm' => $pid]);
            }
        }

        $pdo->commit();
        $estado = 'SUCCESS';
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);
        return response()->json(['success' => true, 'message' => 'Rol creado', 'id' => $newId]);
    } catch (Exception $e) {
        if (isset($pdo) && $pdo->inTransaction())
            $pdo->rollBack();
        return response()->json(['success' => false, 'message' => 'Error al crear rol', 'error' => $e->getMessage()], 500);
    } finally {
        if (isset($db) && $db !== null)
            $db->close_conection();
    }
});

// Actualizar rol (compatibilidad POST)
Route::post('/admin/roles/update', function (Request $request) {
    if (!Session::has('user_code')) {
        return response()->json(['success' => false, 'message' => 'Usuario no autenticado'], 401);
    }
    if (Session::get('user_role') !== 'admin') {
        return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
    }

    //REGISTRO DE BITACORA
    $accion = 'ACTUALIZACION DE ROLES';
    $fecha = date('Y-m-d H:i:s');
    $estado = 'ERROR';
    $comentario = 'Actualizar roles registrados.';
    $codigo = Session::get('user_code');


    $data = $request->json()->all();
    $id = $data['id'] ?? null;
    $nombre = $data['nombre'] ?? '';
    $descripcion = $data['descripcion'] ?? '';
    $permisos = $data['permisos'] ?? [];

    if (!$id)
        return response()->json(['success' => false, 'message' => 'id requerido'], 400);

    $db = Config::$db;
    try {
        $db->create_conection();
        $pdo = $db->get_connection();
        $pdo->beginTransaction();

        $sql = "UPDATE ex_g32.rol SET nombre = :nombre, descripcion = :descripcion WHERE id = :id";
        $db->execute_query($sql, [':nombre' => $nombre, ':descripcion' => $descripcion, ':id' => $id]);

        // reemplazar permisos
        $db->execute_query("DELETE FROM ex_g32.rol_permiso WHERE id_rol = :id", [':id' => $id]);
        if (!empty($permisos)) {
            foreach ($permisos as $pid) {
                $db->execute_query("INSERT INTO ex_g32.rol_permiso (id_rol, id_permiso) VALUES (:rol, :perm)", [':rol' => $id, ':perm' => $pid]);
            }
        }

        $pdo->commit();
        $estado = 'SUCCESS';
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);
        return response()->json(['success' => true, 'message' => 'Rol actualizado']);
    } catch (Exception $e) {
        if (isset($pdo) && $pdo->inTransaction())
            $pdo->rollBack();
        return response()->json(['success' => false, 'message' => 'Error al actualizar rol', 'error' => $e->getMessage()], 500);
    } finally {
        if (isset($db) && $db !== null)
            $db->close_conection();
    }
});

// Eliminar rol (compatibilidad POST)
Route::post('/admin/roles/delete', function (Request $request) {
    if (!Session::has('user_code')) {
        return response()->json(['success' => false, 'message' => 'Usuario no autenticado'], 401);
    }
    if (Session::get('user_role') !== 'admin') {
        return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
    }

    //REGISTRO DE BITACORA
    $accion = 'ELIMINAR ROL';
    $fecha = date('Y-m-d H:i:s');
    $estado = 'ERROR';
    $comentario = 'Eliminando rol.';
    $codigo = Session::get('user_code');


    $data = $request->json()->all();
    $id = $data['id'] ?? null;
    if (!$id)
        return response()->json(['success' => false, 'message' => 'id requerido'], 400);

    $db = Config::$db;
    try {
        $db->create_conection();
        $pdo = $db->get_connection();
        $pdo->beginTransaction();

        $db->execute_query("DELETE FROM ex_g32.rol_permiso WHERE id_rol = :id", [':id' => $id]);
        $db->execute_query("DELETE FROM ex_g32.rol WHERE id = :id", [':id' => $id]);

        $pdo->commit();
        $estado = 'SUCCESS';
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);
        return response()->json(['success' => true, 'message' => 'Rol eliminado']);
    } catch (Exception $e) {
        if (isset($pdo) && $pdo->inTransaction())
            $pdo->rollBack();
        return response()->json(['success' => false, 'message' => 'Error al eliminar rol', 'error' => $e->getMessage()], 500);
    } finally {
        if (isset($db) && $db !== null)
            $db->close_conection();
    }
});

