<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Config;

//RUTA PRINCIPAL (INDEX)
Route::get('/', function () 
{
    //VALIDACION: SI EL USUARIO NO ESTA REGISTRADO REDIRIGIR AL LOGIN
    if (!Session::has('user_code'))
    {
        return redirect('/login');
    }
    
    //OBTENER DATOS DEL USUARIO
    $db=Config::$db;
    $db->create_conection();
    $sql="
        SELECT u.codigo,u.ci,p.nomb_comp,p.tel,p.correo,r.nombre as rol
        FROM ex_g32.usuario u
        INNER JOIN ex_g32.persona p ON p.ci=u.ci
        INNER JOIN ex_g32.rol r ON r.id =u.id_rol
        WHERE u.codigo= :codigo
    ";
    $params=[
        ':codigo'=>Session::get('user_code')
    ];

    $stmt=$db->execute_query($sql,$params);
    $user=$db->fetch_one($stmt);
    Session::put('name',$user['nomb_comp']);
    Session::put('mail',$user['correo']);
    Session::put('tel',$user['tel']);
    Session::put('ci',$user['ci']);
    $db->close_conection();

    return view('index',['user'=>$user]);
});

// RUTA PARA VER Y EDITAR PERFIL DEL USUARIO
Route::get('/perfil', function () {
    // VALIDACIÓN: USUARIO EN SESIÓN
    if (!Session::has('user_code')) {
        return redirect('/login');
    }

    $db = Config::$db;
    try {
        $db->create_conection();

        // Obtener datos completos del usuario
        $sql = "SELECT u.codigo, u.ci, p.nomb_comp, p.tel, p.correo, p.profesion, r.nombre as rol
                FROM ex_g32.usuario u
                INNER JOIN ex_g32.persona p ON p.ci = u.ci
                INNER JOIN ex_g32.rol r ON r.id = u.id_rol
                WHERE u.codigo = :codigo";
        
        $params = [':codigo' => Session::get('user_code')];
        $stmt = $db->execute_query($sql, $params);
        $user = $db->fetch_one($stmt);

        $db->close_conection();

        if (!$user) {
            return redirect('/')->with('error', 'Usuario no encontrado.');
        }

        return view('perfil_nuevo', ['user' => $user]);

    } catch (Exception $e) {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
        // Mostrar error para debug
        return response('<h1>ERROR:</h1><pre>' . $e->getMessage() . "\n\n" . $e->getTraceAsString() . '</pre>', 500);
    }
});

// RUTA PARA ACTUALIZAR PERFIL DEL USUARIO
Route::post('/perfil/actualizar', function (Request $request) {
    // VALIDACIÓN: USUARIO EN SESIÓN
    if (!Session::has('user_code')) {
        return response()->json([
            'success' => false,
            'message' => 'Usuario no autenticado.'
        ], 401);
    }

    $data = $request->json()->all();
    $telefono = $data['telefono'] ?? null;
    $correo = $data['correo'] ?? null;
    $passwordActual = $data['password_actual'] ?? null;
    $passwordNueva = $data['password_nueva'] ?? null;

    $db = Config::$db;
    $pdo = null;
    
    try {
        $db->create_conection();
        $pdo = $db->get_connection();

        // Obtener CI y password del usuario
        $sql = "SELECT ci, password_hash FROM ex_g32.usuario WHERE codigo = :codigo";
        $params = [':codigo' => Session::get('user_code')];
        $stmt = $db->execute_query($sql, $params);
        $usuario = $db->fetch_one($stmt);

        if (!$usuario) {
            $db->close_conection();
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado.'
            ], 404);
        }

        $ci = $usuario['ci'];
        
        // Iniciar transacción
        $pdo->beginTransaction();

        // Actualizar datos de persona (teléfono y correo)
        if ($telefono || $correo) {
            $updates = [];
            $updateParams = [':ci' => $ci];

            if ($telefono) {
                $updates[] = "tel = :tel";
                $updateParams[':tel'] = $telefono;
            }

            if ($correo) {
                $updates[] = "correo = :correo";
                $updateParams[':correo'] = $correo;
            }

            if (!empty($updates)) {
                $sqlUpdate = "UPDATE ex_g32.persona SET " . implode(', ', $updates) . " WHERE ci = :ci";
                $db->execute_query($sqlUpdate, $updateParams);
            }
        }

        // Cambiar contraseña si se proporcionó
        if ($passwordActual && $passwordNueva) {
            // Verificar contraseña actual usando password_verify con el hash
            if (!password_verify($passwordActual, $usuario['password_hash'])) {
                $pdo->rollBack();
                $db->close_conection();
                return response()->json([
                    'success' => false,
                    'message' => 'La contraseña actual es incorrecta.'
                ], 400);
            }

            // Actualizar contraseña hasheada
            $passwordHash = password_hash($passwordNueva, PASSWORD_BCRYPT);
            $sqlPassword = "UPDATE ex_g32.usuario SET password_hash = :password WHERE codigo = :codigo";
            $passwordParams = [
                ':password' => $passwordHash,
                ':codigo' => Session::get('user_code')
            ];
            $db->execute_query($sqlPassword, $passwordParams);
        }

        // Confirmar transacción
        $pdo->commit();

        // Actualizar sesión
        if ($telefono) Session::put('tel', $telefono);
        if ($correo) Session::put('mail', $correo);

        // Registrar en bitácora
        $accion = 'ACTUALIZAR PERFIL';
        $fecha = date('Y-m-d H:i:s');
        $estado = 'SUCCESS';
        $comentario = 'Usuario actualizó su perfil personal.';
        $codigo = Session::get('user_code');
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);

        $db->close_conection();

        return response()->json([
            'success' => true,
            'message' => 'Perfil actualizado correctamente.'
        ]);

    } catch (Exception $e) {
        // Rollback si hay transacción activa
        if ($pdo !== null) {
            try {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
            } catch (Exception $rollbackEx) {
                // Ignorar errores de rollback
            }
        }
        
        // Cerrar conexión
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Error al actualizar perfil: ' . $e->getMessage(),
            'error' => $e->getMessage()
        ], 500);
    }
});
