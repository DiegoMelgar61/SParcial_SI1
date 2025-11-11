<?php

#IMPORTAR CLASES Y LIBRERIAS
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Config;
use App\Classes\Postgres_DB;

//MODULO DOCENCIA
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

// CU23: REGISTRAR ASISTENCIA POR QR O FORMULARIO
Route::post('/docen/asistencia/marcar', function (Request $request) {
    
    #VALIDACION: USUARIO EN SESION
    if (!Session::has('user_code')) {
        return redirect('/login');
    }

    #VALIDACION: USUARIO DOCENTE O ADMIN
    $rol = Session::get('user_role');
    if ($rol !== 'docente' && $rol !== 'admin') {
        return redirect('/');
    }

    #RECUPERAR DATOS DEL USUARIO
    $user = [
        'codigo' => Session::get('user_code'),
        'nombre' => Session::get('name'),
        'rol'    => $rol,
    ];

    //REGISTRO DE BITACORA
    $accion = 'ACCESO A MÓDULO ADMIN';
    $fecha_bit = date('Y-m-d H:i:s');
    $estado_bit = 'SUCCESS';
    $comentario = 'Acceso al módulo de docencia.';
    $codigo = Session::get('user_code');
    

    #CONEXION A LA BD
    $db = Config::$db;
    try 
    {
        $db->create_conection();
    } 
    catch (Exception $e) 
    {
        $db->save_log_bitacora($accion, $fecha_bit, $estado_bit, $comentario, $codigo);
        return response()->json([
            'success' => false,
            'message' => 'Error al conectar con la base de datos: ' . $e->getMessage()
        ]);
    }

    #OBTENER DATOS DESDE EL FORMULARIO O QR
    $id_clase = $request->input('id_clase');
    $fecha = $request->input('fecha', date('Y-m-d'));
    $estado = $request->input('estado', 'Presente');
    $metodo_r = $request->input('metodo_r', 'Formulario');
    $observacion = $request->input('observacion', null);

    if (!$id_clase) 
    {
        $db->save_log_bitacora($accion, $fecha_bit, $estado_bit, $comentario, $codigo);
        return response()->json([
            'success' => false,
            'message' => 'Falta el identificador de clase.'
        ]);
    }

    try 
    {
        
        // VALIDAR SI YA EXISTE ASISTENCIA PARA ESA CLASE Y FECHA
        $sql_check = "
            SELECT COUNT(*) AS total
            FROM ex_g32.asistencia 
            WHERE id_clase = :id_clase 
              AND fecha = :fecha
        ";
        $params_check = [
            ':id_clase' => $id_clase,
            ':fecha' => $fecha
        ];
        $stmt = $db->execute_query($sql_check, $params_check);
        $exists = $db->fetch_one($stmt)['total'];

        if ($exists > 0) {
            $db->save_log_bitacora($accion, $fecha_bit, $estado_bit, $comentario, $codigo);
            return response()->json([
                'success' => false,
                'message' => 'Ya se registró asistencia para esta clase hoy.'
            ]);
        }

        
        // INSERTAR NUEVO REGISTRO
        $sql_insert = "
            INSERT INTO ex_g32.asistencia (fecha, estado, metodo_r, observacion, id_clase)
            VALUES (:fecha, :estado, :metodo_r, :observacion, :id_clase)
        ";

        $params_insert = [
            ':fecha' => $fecha,
            ':estado' => $estado,
            ':metodo_r' => $metodo_r,
            ':observacion' => $observacion,
            ':id_clase' => $id_clase
        ];

        $db->execute_query($sql_insert, $params_insert);

        

        
        // RESPUESTA EXITOSA
        return response()->json([
            'success' => true,
            'message' => 'Asistencia registrada correctamente.',
            'data' => [
                'id_clase' => $id_clase,
                'estado' => $estado,
                'fecha' => $fecha,
                'metodo' => $metodo_r
            ]
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => 500,
            'message' => 'Error al registrar asistencia: ' . $e->getMessage()
        ]);
    }
});


//SECCION DE ASISTENCIA
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
            SELECT c.id as id_clase,m.sigla sigla_materia,m.nombre as nombre_materia,mg.sigla_grupo as grupo,h.dia,h.hora_i as hora_inicio,h.hora_f as hora_final
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
            SELECT c.id as id_clase,m.sigla sigla_materia,m.nombre as nombre_materia,mg.sigla_grupo as grupo,h.dia,h.hora_i as hora_inicio,h.hora_f as hora_final,a.estado,a.fecha
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

// =====================================================================
// SISTEMA DE GESTIÓN DE LICENCIAS PARA DOCENTES
// =====================================================================
// REGLAS DE NEGOCIO:
// 1. Un docente puede solicitar máximo 7 días de licencia por mes
// 2. Puede crear licencias de 1 a 7 días
// 3. Solo puede editar/eliminar licencias durante la primera hora después de crearlas
// 4. La fecha final se calcula automáticamente según los días seleccionados
// =====================================================================

/**
 * GET /docente/licencias
 * Vista principal del sistema de licencias
 * Muestra botón para crear nueva licencia y tabla con últimas 5 licencias
 */
Route::get('/docencia/licencia', function () {
    // VALIDACIÓN: USUARIO EN SESIÓN
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
        $db->create_conection();

        // REGISTRO DE BITÁCORA
        $accion = 'ACCESO A GESTIÓN DE LICENCIAS';
        $fecha = date('Y-m-d H:i:s');
        $estado = 'SUCCESS';
        $comentario = 'Acceso al módulo de licencias para docentes.';
        $codigo = Session::get('user_code');
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);

        // RECUPERAR DATOS DEL USUARIO
        $user = [
            'nomb_comp' => Session::get('name'),
            'rol' => Session::get('user_role'),
            'ci' => Session::get('ci'),
            'correo' => Session::get('mail'),
            'tel' => Session::get('tel'),
            'codigo' => Session::get('user_code')
        ];

        return view('docente_licencia', ['user' => $user]);

    } catch (Exception $e) {
        return redirect('/')->with('error', 'Error al conectar: ' . $e->getMessage());
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

/**
 * GET /docente/licencias/listar
 * Obtiene las últimas 5 licencias del docente
 * Retorna JSON con información completa y si pueden editarse/eliminarse
 */
Route::get('/docente/licencias/listar', function () {
    // VALIDACIÓN: USUARIO EN SESIÓN
    if (!Session::has('user_code')) {
        return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
    }
        // VALIDACION: USUARIO ADMIN O DOCENTE
    if (Session::get('user_role') != 'docente' and Session::get('user_role') != 'admin') 
    {
        return redirect('/');
    }

    try {
        $db = Config::$db;
        $db->create_conection();

        $codigo_usuario = Session::get('user_code');

        // CONSULTAR ÚLTIMAS 5 LICENCIAS
        $sql = "SELECT 
                    nro,
                    descripcion,
                    fecha_hora,
                    codigo_usuario,
                    fecha_i,
                    fecha_f,
                    -- Calcular si puede editar/eliminar (1 hora = 3600 segundos)
                    CASE 
                        WHEN EXTRACT(EPOCH FROM (NOW() - fecha_hora)) <= 3600 THEN true 
                        ELSE false 
                    END as puede_modificar,
                    -- Calcular días de la licencia
                    (fecha_f - fecha_i + 1) as dias_licencia
                FROM ex_g32.licencia
                WHERE codigo_usuario = ?
                ORDER BY fecha_hora DESC
                LIMIT 5";
        
        $stmt = $db->execute_query($sql, [$codigo_usuario]);
        $licencias = $db->fetch_all($stmt);

        // Formatear fechas para mejor visualización
        foreach ($licencias as &$licencia) {
            $licencia['fecha_hora_formato'] = date('d/m/Y H:i', strtotime($licencia['fecha_hora']));
            $licencia['fecha_i_formato'] = date('d/m/Y', strtotime($licencia['fecha_i']));
            $licencia['fecha_f_formato'] = date('d/m/Y', strtotime($licencia['fecha_f']));
            $licencia['puede_modificar'] = $licencia['puede_modificar'] === 't' || $licencia['puede_modificar'] === true;
        }

        return response()->json([
            'success' => true,
            'licencias' => $licencias
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener licencias: ' . $e->getMessage()
        ], 500);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

/**
 * GET /docente/licencias/dias-disponibles
 * Calcula cuántos días de licencia tiene disponibles el docente este mes
 * Retorna JSON con días usados y días disponibles (máximo 7 por mes)
 */
Route::get('/docente/licencias/dias-disponibles', function () {
    // VALIDACIÓN: USUARIO EN SESIÓN
    if (!Session::has('user_code')) {
        return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
    }

    try {
        $db = Config::$db;
        $db->create_conection();

        $codigo_usuario = Session::get('user_code');

        // CALCULAR DÍAS USADOS EN EL MES ACTUAL
        $sql = "SELECT 
                    COALESCE(SUM(fecha_f - fecha_i + 1), 0) as dias_usados
                FROM ex_g32.licencia
                WHERE codigo_usuario = ?
                AND EXTRACT(MONTH FROM fecha_i) = EXTRACT(MONTH FROM CURRENT_DATE)
                AND EXTRACT(YEAR FROM fecha_i) = EXTRACT(YEAR FROM CURRENT_DATE)";
        
        $stmt = $db->execute_query($sql, [$codigo_usuario]);
        $resultado = $db->fetch_one($stmt);
        
        // Asegurar que dias_usados sea un número entero válido
        $dias_usados = isset($resultado['dias_usados']) ? intval($resultado['dias_usados']) : 0;
        $dias_disponibles = 7 - $dias_usados;
        
        // Asegurar que no sea negativo
        if ($dias_disponibles < 0) {
            $dias_disponibles = 0;
        }

        // Log para debugging
        error_log("DEBUG Licencias - Usuario: $codigo_usuario, Días usados: $dias_usados, Disponibles: $dias_disponibles");

        return response()->json([
            'success' => true,
            'dias_usados' => $dias_usados,
            'dias_disponibles' => $dias_disponibles,
            'limite_mensual' => 7
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al calcular días disponibles: ' . $e->getMessage()
        ], 500);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

/**
 * POST /docente/licencias/crear
 * Crea una nueva solicitud de licencia
 * Validaciones:
 * - Descripción requerida
 * - Fecha inicio requerida y no puede ser anterior a hoy
 * - Días debe estar entre 1 y días disponibles
 * - No exceder 7 días totales por mes
 */
Route::post('/docente/licencias/crear', function (Request $request) {
    // VALIDACIÓN: USUARIO EN SESIÓN
    if (!Session::has('user_code')) {
        return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
    }
        // VALIDACION: USUARIO ADMIN O DOCENTE
    if (Session::get('user_role') != 'docente' and Session::get('user_role') != 'admin') 
    {
        return redirect('/');
    }

    try {
        $db = Config::$db;
        $db->create_conection();

        // OBTENER DATOS DEL REQUEST
        $descripcion = trim($request->input('descripcion'));
        $fecha_inicio = $request->input('fecha_inicio');
        $dias = intval($request->input('dias'));
        $codigo_usuario = Session::get('user_code');

        // DEBUG: Log de los datos recibidos
        error_log("=== CREAR LICENCIA ===");
        error_log("Descripcion: " . ($descripcion ?? 'NULL'));
        error_log("Fecha inicio: " . ($fecha_inicio ?? 'NULL'));
        error_log("Dias: " . $dias);
        error_log("Codigo usuario: " . ($codigo_usuario ?? 'NULL'));

        // VALIDACIÓN: CAMPOS REQUERIDOS
        if (empty($descripcion)) {
            return response()->json([
                'success' => false,
                'message' => 'La descripción es obligatoria'
            ], 400);
        }

        if (empty($fecha_inicio)) {
            return response()->json([
                'success' => false,
                'message' => 'La fecha de inicio es obligatoria'
            ], 400);
        }

        // VALIDACIÓN: FECHA INICIO NO PUEDE SER ANTERIOR A HOY
        $hoy = date('Y-m-d');
        if ($fecha_inicio < $hoy) {
            return response()->json([
                'success' => false,
                'message' => 'La fecha de inicio no puede ser anterior a hoy'
            ], 400);
        }

        // VALIDACIÓN: DÍAS DEBE SER ENTRE 1 Y 7
        if ($dias < 1 || $dias > 7) {
            return response()->json([
                'success' => false,
                'message' => 'Los días deben estar entre 1 y 7'
            ], 400);
        }

        // CALCULAR DÍAS DISPONIBLES ESTE MES
        // Usar CAST para convertir el parámetro a DATE correctamente
        $sql_dias = "SELECT 
                        COALESCE(SUM(fecha_f - fecha_i + 1), 0) as dias_usados
                    FROM ex_g32.licencia
                    WHERE codigo_usuario = ?
                    AND EXTRACT(MONTH FROM fecha_i) = EXTRACT(MONTH FROM CAST(? AS DATE))
                    AND EXTRACT(YEAR FROM fecha_i) = EXTRACT(YEAR FROM CAST(? AS DATE))";
        
        $stmt = $db->execute_query($sql_dias, [$codigo_usuario, $fecha_inicio, $fecha_inicio]);
        $resultado = $db->fetch_one($stmt);
        $dias_usados = intval($resultado['dias_usados']);
        $dias_disponibles = 7 - $dias_usados;

        // VALIDACIÓN: NO EXCEDER LÍMITE MENSUAL
        if ($dias > $dias_disponibles) {
            return response()->json([
                'success' => false,
                'message' => "Solo tienes $dias_disponibles días disponibles este mes"
            ], 400);
        }

        // CALCULAR FECHA FINAL (fecha_inicio + dias - 1)
        $fecha_fin = date('Y-m-d', strtotime($fecha_inicio . " + " . ($dias - 1) . " days"));

        // DEBUG: Log antes del INSERT
        error_log("Fecha fin calculada: " . $fecha_fin);
        error_log("Parámetros para INSERT: [" . $descripcion . ", " . $codigo_usuario . ", " . $fecha_inicio . ", " . $fecha_fin . "]");

        // INSERTAR NUEVA LICENCIA
        // Usar placeholders ? en lugar de $1, $2 para compatibilidad con PDO
        $sql_insert = "INSERT INTO ex_g32.licencia 
                        (descripcion, codigo_usuario, fecha_i, fecha_f)
                        VALUES (?, ?, ?, ?)
                        RETURNING nro, fecha_hora";
        
        error_log("SQL: " . $sql_insert);
        
        $stmt = $db->execute_query($sql_insert, [
            $descripcion,
            $codigo_usuario,
            $fecha_inicio,
            $fecha_fin
        ]);
        
        error_log("Query ejecutada exitosamente");
        
        $licencia_nueva = $db->fetch_one($stmt);

        // REGISTRO DE BITÁCORA
        $accion = 'CREAR LICENCIA';
        $fecha = date('Y-m-d H:i:s');
        $estado = 'SUCCESS';
        $comentario = "Licencia creada: $dias días desde $fecha_inicio hasta $fecha_fin";
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo_usuario);

        return response()->json([
            'success' => true,
            'message' => 'Licencia creada exitosamente',
            'licencia' => [
                'nro' => $licencia_nueva['nro'],
                'descripcion' => $descripcion,
                'fecha_hora' => $licencia_nueva['fecha_hora'],
                'fecha_i' => $fecha_inicio,
                'fecha_f' => $fecha_fin,
                'dias' => $dias
            ]
        ]);

    } catch (Exception $e) {
        // REGISTRO DE ERROR EN BITÁCORA
        if (isset($db) && $db !== null) {
            $db->save_log_bitacora(
                'CREAR LICENCIA',
                date('Y-m-d H:i:s'),
                'ERROR',
                'Error al crear licencia: ' . $e->getMessage(),
                Session::get('user_code')
            );
        }

        return response()->json([
            'success' => false,
            'message' => 'Error al crear licencia: ' . $e->getMessage()
        ], 500);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

/**
 * PUT /docente/licencias/editar/{nro}
 * Edita una licencia existente
 * Validaciones:
 * - Solo puede editarse durante la primera hora después de crearla
 * - Mismas validaciones que crear
 * - Debe ser del usuario actual
 */
Route::put('/docente/licencias/editar/{nro}', function (Request $request, $nro) {
    // VALIDACIÓN: USUARIO EN SESIÓN
    if (!Session::has('user_code')) {
        return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
    }
    // VALIDACION: USUARIO ADMIN O DOCENTE
    if (Session::get('user_role') != 'docente' and Session::get('user_role') != 'admin') 
    {
        return redirect('/');
    }
    try {
        $db = Config::$db;
        $db->create_conection();

        $codigo_usuario = Session::get('user_code');

        // VERIFICAR QUE LA LICENCIA EXISTA Y SEA DEL USUARIO
        $sql_verificar = "SELECT 
                            nro,
                            fecha_hora,
                            fecha_i,
                            fecha_f,
                            EXTRACT(EPOCH FROM (NOW() - fecha_hora)) as segundos_transcurridos
                        FROM ex_g32.licencia
                        WHERE nro = ? AND codigo_usuario = ?";
        
        $stmt = $db->execute_query($sql_verificar, [$nro, $codigo_usuario]);
        $licencia_actual = $db->fetch_one($stmt);

        if (!$licencia_actual) {
            return response()->json([
                'success' => false,
                'message' => 'Licencia no encontrada o no autorizado'
            ], 404);
        }

        // VALIDACIÓN: SOLO PUEDE EDITAR EN LA PRIMERA HORA
        if ($licencia_actual['segundos_transcurridos'] > 3600) {
            return response()->json([
                'success' => false,
                'message' => 'Solo puedes editar licencias durante la primera hora después de crearlas'
            ], 403);
        }

        // OBTENER NUEVOS DATOS
        $descripcion = trim($request->input('descripcion'));
        $fecha_inicio = $request->input('fecha_inicio');
        $dias = intval($request->input('dias'));

        // VALIDACIONES BÁSICAS
        if (empty($descripcion)) {
            return response()->json(['success' => false, 'message' => 'La descripción es obligatoria'], 400);
        }

        if (empty($fecha_inicio)) {
            return response()->json(['success' => false, 'message' => 'La fecha de inicio es obligatoria'], 400);
        }

        $hoy = date('Y-m-d');
        if ($fecha_inicio < $hoy) {
            return response()->json(['success' => false, 'message' => 'La fecha de inicio no puede ser anterior a hoy'], 400);
        }

        if ($dias < 1 || $dias > 7) {
            return response()->json(['success' => false, 'message' => 'Los días deben estar entre 1 y 7'], 400);
        }

        // CALCULAR DÍAS DISPONIBLES (EXCLUYENDO ESTA LICENCIA)
        $dias_actuales = (strtotime($licencia_actual['fecha_f']) - strtotime($licencia_actual['fecha_i'])) / 86400 + 1;
        
        // Usar CAST para convertir el parámetro a DATE correctamente
        $sql_dias = "SELECT 
                        COALESCE(SUM(fecha_f - fecha_i + 1), 0) as dias_usados
                    FROM ex_g32.licencia
                    WHERE codigo_usuario = ?
                    AND nro != ?
                    AND EXTRACT(MONTH FROM fecha_i) = EXTRACT(MONTH FROM CAST(? AS DATE))
                    AND EXTRACT(YEAR FROM fecha_i) = EXTRACT(YEAR FROM CAST(? AS DATE))";
        
        $stmt = $db->execute_query($sql_dias, [$codigo_usuario, $nro, $fecha_inicio, $fecha_inicio]);
        $resultado = $db->fetch_one($stmt);
        $dias_usados = intval($resultado['dias_usados']);
        $dias_disponibles = 7 - $dias_usados;

        if ($dias > $dias_disponibles) {
            return response()->json([
                'success' => false,
                'message' => "Solo tienes $dias_disponibles días disponibles este mes"
            ], 400);
        }

        // CALCULAR NUEVA FECHA FINAL
        $fecha_fin = date('Y-m-d', strtotime($fecha_inicio . " + " . ($dias - 1) . " days"));

        // ACTUALIZAR LICENCIA
        $sql_update = "UPDATE ex_g32.licencia
                        SET descripcion = ?,
                            fecha_i = ?,
                            fecha_f = ?
                        WHERE nro = ?";
        
        $db->execute_query($sql_update, [$descripcion, $fecha_inicio, $fecha_fin, $nro]);

        // REGISTRO DE BITÁCORA
        $db->save_log_bitacora(
            'EDITAR LICENCIA',
            date('Y-m-d H:i:s'),
            'SUCCESS',
            "Licencia #$nro editada: $dias días desde $fecha_inicio hasta $fecha_fin",
            $codigo_usuario
        );

        return response()->json([
            'success' => true,
            'message' => 'Licencia actualizada exitosamente'
        ]);

    } catch (Exception $e) {
        if (isset($db) && $db !== null) {
            $db->save_log_bitacora(
                'EDITAR LICENCIA',
                date('Y-m-d H:i:s'),
                'ERROR',
                'Error al editar licencia: ' . $e->getMessage(),
                Session::get('user_code')
            );
        }

        return response()->json([
            'success' => false,
            'message' => 'Error al editar licencia: ' . $e->getMessage()
        ], 500);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

/**
 * DELETE /docente/licencias/eliminar/{nro}
 * Elimina una licencia
 * Validaciones:
 * - Solo puede eliminarse durante la primera hora después de crearla
 * - Debe ser del usuario actual
 */
Route::delete('/docente/licencias/eliminar/{nro}', function ($nro) {
    // VALIDACIÓN: USUARIO EN SESIÓN
    if (!Session::has('user_code')) {
        return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
    }
        // VALIDACION: USUARIO ADMIN O DOCENTE
    if (Session::get('user_role') != 'docente' and Session::get('user_role') != 'admin') 
    {
        return redirect('/');
    }
    try {
        $db = Config::$db;
        $db->create_conection();

        $codigo_usuario = Session::get('user_code');

        // VERIFICAR QUE LA LICENCIA EXISTA Y SEA DEL USUARIO
        $sql_verificar = "SELECT 
                            nro,
                            fecha_hora,
                            descripcion,
                            EXTRACT(EPOCH FROM (NOW() - fecha_hora)) as segundos_transcurridos
                        FROM ex_g32.licencia
                        WHERE nro = ? AND codigo_usuario = ?";
        
        $stmt = $db->execute_query($sql_verificar, [$nro, $codigo_usuario]);
        $licencia = $db->fetch_one($stmt);

        if (!$licencia) {
            return response()->json([
                'success' => false,
                'message' => 'Licencia no encontrada o no autorizado'
            ], 404);
        }

        // VALIDACIÓN: SOLO PUEDE ELIMINAR EN LA PRIMERA HORA
        if ($licencia['segundos_transcurridos'] > 3600) {
            return response()->json([
                'success' => false,
                'message' => 'Solo puedes eliminar licencias durante la primera hora después de crearlas'
            ], 403);
        }

        // ELIMINAR LICENCIA
        $sql_delete = "DELETE FROM ex_g32.licencia WHERE nro = ?";
        $db->execute_query($sql_delete, [$nro]);

        // REGISTRO DE BITÁCORA
        $db->save_log_bitacora(
            'ELIMINAR LICENCIA',
            date('Y-m-d H:i:s'),
            'SUCCESS',
            "Licencia #$nro eliminada: " . $licencia['descripcion'],
            $codigo_usuario
        );

        return response()->json([
            'success' => true,
            'message' => 'Licencia eliminada exitosamente'
        ]);

    } catch (Exception $e) {
        if (isset($db) && $db !== null) {
            $db->save_log_bitacora(
                'ELIMINAR LICENCIA',
                date('Y-m-d H:i:s'),
                'ERROR',
                'Error al eliminar licencia: ' . $e->getMessage(),
                Session::get('user_code')
            );
        }

        return response()->json([
            'success' => false,
            'message' => 'Error al eliminar licencia: ' . $e->getMessage()
        ], 500);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});