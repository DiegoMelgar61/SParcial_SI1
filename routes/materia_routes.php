<?php

#IMPORTAR CLASES Y LIBRERIAS
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Config;
use App\Classes\Postgres_DB;

// ==================== GESTIÓN DE MATERIAS ====================
//ENDPOINT GESTION DE MATERIAS
Route::get('/admin/materias', function () {
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
        $accion = 'VISUALIZAR MATERIAS';
        $fecha = date('Y-m-d H:i:s');
        $estado = 'SUCCESS';
        $comentario = 'Acceso al módulo de gestión de materias.';
        $codigo = Session::get('user_code');
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);

        //OBTENER MATERIAS
        $sql = "SELECT sigla, nombre, semestre, carga_horaria
                FROM ex_g32.materia
                ORDER BY semestre, sigla";
        $stmt = $db->execute_query($sql);
        $materias = $db->fetch_all($stmt);

        // Obtener grupos asignados a cada materia
        foreach ($materias as &$materia) {
            $sql = "SELECT sigla_grupo FROM ex_g32.materia_grupo WHERE sigla_materia = :sigla ORDER BY sigla_grupo";
            $params = [':sigla' => $materia['sigla']];
            $stmt = $db->execute_query($sql, $params);
            $grupos = $db->fetch_all($stmt);
            $materia['grupos'] = array_map(function($g) {
                return $g['sigla_grupo'];
            }, $grupos);
        }

        //DATOS USUARIO
         $user = [
            'nomb_comp' => Session::get('name'),  // Asegúrate de tener este dato en la sesión
            'rol' => Session::get('user_role'),
            'ci' => Session::get('ci'),
            'correo' => Session::get('mail'),
            'tel' => Session::get('tel'),
        ];

        return view('admin_materias', [
            'materias' => $materias,
            'user' => $user
        ]);
    } catch (Exception $e) {
        return redirect('/')->with('error', 'Error al cargar materias.');
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

//ENDPOINT CREAR MATERIA
Route::post('/admin/materias/create', function (Request $request) {
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
    $sigla = $data['sigla'] ?? null;
    $nombre = $data['nombre'] ?? null;
    $semestre = $data['semestre'] ?? null;
    $carga_horaria = $data['carga_horaria'] ?? null;

    //VALIDACIONES
    if (!$sigla || !$nombre || !$semestre || !$carga_horaria) {
        return response()->json([
            'success' => false,
            'message' => 'Todos los campos son obligatorios.'
        ], 400);
    }

    //OBTENER DATOS BITACORA
    $accion = 'CREAR MATERIA';
    $fecha = date('Y-m-d H:i:s');
    $estado = 'ERROR';
    $comentario = 'Crear una nueva materia.';
    $codigo = Session::get('user_code');

    $db = Config::$db;
    try {
        $db->create_conection();

        //VERIFICAR QUE LA MATERIA NO EXISTA
        $sql = "SELECT sigla FROM ex_g32.materia WHERE sigla = :sigla";
        $params = [':sigla' => $sigla];
        $stmt = $db->execute_query($sql, $params);
        $existe = $db->fetch_one($stmt);

        if ($existe) {
            $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);
            return response()->json([
                'success' => false,
                'message' => 'La materia ya existe en el sistema.'
            ], 409);
        }

        //INSERTAR MATERIA
        $sql = "INSERT INTO ex_g32.materia (sigla, nombre, semestre, carga_horaria) 
                VALUES (:sigla, :nombre, :semestre, :carga_horaria)";
        $params = [
            ':sigla' => $sigla,
            ':nombre' => $nombre,
            ':semestre' => $semestre,
            ':carga_horaria' => $carga_horaria
        ];
        $stmt = $db->execute_query($sql, $params);

        $estado = 'SUCCESS';
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);

        return response()->json([
            'success' => true,
            'message' => 'Materia creada exitosamente.'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Ocurrió un error en el proceso.',
            'error' => $e->getMessage()
        ], 500);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

//ENDPOINT ACTUALIZAR MATERIA
Route::post('/admin/materias/update', function (Request $request) {
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
    $sigla = $data['sigla'] ?? null;
    $nombre = $data['nombre'] ?? null;
    $semestre = $data['semestre'] ?? null;
    $carga_horaria = $data['carga_horaria'] ?? null;

    //VALIDACIONES
    if (!$sigla || !$nombre || !$semestre || !$carga_horaria) {
        return response()->json([
            'success' => false,
            'message' => 'Todos los campos son obligatorios.'
        ], 400);
    }

    //OBTENER DATOS BITACORA
    $accion = 'MODIFICAR MATERIA';
    $fecha = date('Y-m-d H:i:s');
    $estado = 'ERROR';
    $comentario = 'Modificar una materia existente.';
    $codigo = Session::get('user_code');

    $db = Config::$db;
    try {
        $db->create_conection();

        //VERIFICAR QUE LA MATERIA EXISTE
        $sql = "SELECT sigla FROM ex_g32.materia WHERE sigla = :sigla";
        $params = [':sigla' => $sigla];
        $stmt = $db->execute_query($sql, $params);
        $materia_existe = $db->fetch_one($stmt);

        if (!$materia_existe) {
            $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);
            return response()->json([
                'success' => false,
                'message' => 'La materia no existe en el sistema.'
            ], 404);
        }

        //ACTUALIZAR MATERIA
        $sql = "UPDATE ex_g32.materia 
                SET nombre = :nombre, semestre = :semestre, carga_horaria = :carga_horaria 
                WHERE sigla = :sigla";
        $params = [
            ':sigla' => $sigla,
            ':nombre' => $nombre,
            ':semestre' => $semestre,
            ':carga_horaria' => $carga_horaria
        ];
        $stmt = $db->execute_query($sql, $params);

        $estado = 'SUCCESS';
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);

        return response()->json([
            'success' => true,
            'message' => 'Materia actualizada exitosamente.'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Ocurrió un error en el proceso.',
            'error' => $e->getMessage()
        ], 500);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

//ENDPOINT ELIMINAR MATERIA
Route::post('/admin/materias/delete', function (Request $request) {
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
    $sigla_eliminar = $data['sigla'] ?? null;

    if (!$sigla_eliminar) {
        return response()->json([
            'success' => false,
            'message' => 'La sigla de la materia es obligatoria.'
        ], 400);
    }

    //OBTENER DATOS BITACORA
    $accion = 'ELIMINAR MATERIA';
    $fecha = date('Y-m-d H:i:s');
    $estado = 'ERROR';
    $comentario = 'Eliminar una materia indicada.';
    $codigo = Session::get('user_code');

    $db = Config::$db;
    try {
        $db->create_conection();

        //VERIFICAR QUE LA MATERIA EXISTE
        $sql = "SELECT sigla FROM ex_g32.materia WHERE sigla = :sigla";
        $params = [':sigla' => $sigla_eliminar];
        $stmt = $db->execute_query($sql, $params);
        $materia = $db->fetch_one($stmt);

        if (!$materia) {
            $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);
            return response()->json([
                'success' => false,
                'message' => 'La materia no está registrada en el sistema.'
            ], 404);
        }

        //ELIMINAR MATERIA
        $sql = "DELETE FROM ex_g32.materia WHERE sigla = :sigla";
        $params = [':sigla' => $sigla_eliminar];
        $stmt = $db->execute_query($sql, $params);

        $estado = 'SUCCESS';
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);

        return response()->json([
            'success' => true,
            'message' => 'Materia eliminada exitosamente.'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Ocurrió un error en el proceso.',
            'error' => $e->getMessage()
        ], 500);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

//ENDPOINT OBTENER TODOS LOS GRUPOS
Route::get('/admin/materias/get-grupos', function (Request $request) {
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

    $db = Config::$db;
    try {
        $db->create_conection();

        $sql = "SELECT sigla FROM ex_g32.grupo ORDER BY sigla";
        $stmt = $db->execute_query($sql);
        $grupos = $db->fetch_all($stmt);

        return response()->json($grupos);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener grupos.',
            'error' => $e->getMessage()
        ], 500);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

//ENDPOINT OBTENER GRUPOS ASIGNADOS A UNA MATERIA
Route::get('/admin/materias/get-grupos-asignados', function (Request $request) {
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

    $siglaMateria = $request->query('sigla_materia');
    if (!$siglaMateria) {
        return response()->json([
            'success' => false,
            'message' => 'La sigla de la materia es obligatoria.'
        ], 400);
    }

    $db = Config::$db;
    try {
        $db->create_conection();

        $sql = "SELECT sigla_grupo FROM ex_g32.materia_grupo WHERE sigla_materia = :sigla ORDER BY sigla_grupo";
        $params = [':sigla' => $siglaMateria];
        $stmt = $db->execute_query($sql, $params);
        $grupos = $db->fetch_all($stmt);

        // Extraer solo las siglas en un array simple
        $siglas = array_map(function($g) {
            return $g['sigla_grupo'];
        }, $grupos);

        return response()->json($siglas);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener grupos asignados.',
            'error' => $e->getMessage()
        ], 500);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

//ENDPOINT ASIGNAR GRUPOS A MATERIA
Route::post('/admin/materias/asignar-grupos', function (Request $request) {
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
    $siglaMateria = $data['sigla_materia'] ?? null;
    $grupos = $data['grupos'] ?? [];

    if (!$siglaMateria) {
        return response()->json([
            'success' => false,
            'message' => 'La sigla de la materia es obligatoria.'
        ], 400);
    }

    //OBTENER DATOS BITACORA
    $accion = 'ASIGNAR GRUPOS A MATERIA';
    $fecha = date('Y-m-d H:i:s');
    $estado = 'ERROR';
    $comentario = 'Asignar grupos a la materia ' . $siglaMateria;
    $codigo = Session::get('user_code');

    $db = Config::$db;
    try {
        $db->create_conection();
        $pdo = $db->get_connection();
        $pdo->beginTransaction();

        //ELIMINAR ASIGNACIONES PREVIAS
        $sql = "DELETE FROM ex_g32.materia_grupo WHERE sigla_materia = :sigla";
        $params = [':sigla' => $siglaMateria];
        $db->execute_query($sql, $params);

        //INSERTAR NUEVAS ASIGNACIONES
        if (!empty($grupos)) {
            foreach ($grupos as $siglaGrupo) {
                $sql = "INSERT INTO ex_g32.materia_grupo (sigla_materia, sigla_grupo) VALUES (:mat, :gru)";
                $params = [':mat' => $siglaMateria, ':gru' => $siglaGrupo];
                $db->execute_query($sql, $params);
            }
        }

        $pdo->commit();
        $estado = 'SUCCESS';
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);

        return response()->json([
            'success' => true,
            'message' => 'Grupos asignados exitosamente.'
        ]);
    } catch (Exception $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);
        return response()->json([
            'success' => false,
            'message' => 'Ocurrió un error al asignar grupos.',
            'error' => $e->getMessage()
        ], 500);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});


// RUTA PRINCIPAL: Vista de gestión de carga horaria docente
Route::get('/admin/carga-horaria', function () {
    // VALIDACIÓN: USUARIO EN SESIÓN
    if (!Session::has('user_code')) {
        return redirect('/login');
    }

    // VALIDACIÓN: USUARIO ADMIN
    if (Session::get('user_role') !== 'admin') {
        return redirect('/')->with('error', 'Acceso denegado.');
    }

    $db = Config::$db;
    try {
        $db->create_conection();

        // Obtener datos del usuario actual
        $sql = "SELECT u.codigo, u.ci, p.nomb_comp, p.tel, p.correo, r.nombre as rol
                FROM ex_g32.usuario u
                INNER JOIN ex_g32.persona p ON p.ci = u.ci
                INNER JOIN ex_g32.rol r ON r.id = u.id_rol
                WHERE u.codigo = :codigo";
        $params = [':codigo' => Session::get('user_code')];
        $stmt = $db->execute_query($sql, $params);
        $user = $db->fetch_one($stmt);

        // Obtener todos los docentes con su carga horaria
        $sqlDocentes = "
            SELECT 
                u.codigo,
                p.ci,
                p.nomb_comp,
                p.correo,
                p.tel,
                COUNT(DISTINCT c.id) as total_clases,
                COALESCE(SUM(CAST(m.carga_horaria AS INTEGER)), 0) as carga_horaria_total
            FROM ex_g32.usuario u
            INNER JOIN ex_g32.persona p ON u.ci = p.ci
            INNER JOIN ex_g32.rol r ON u.id_rol = r.id
            LEFT JOIN ex_g32.clase c ON c.usuario_codigo = u.codigo
            LEFT JOIN ex_g32.materia_grupo mg ON c.id_materia_grupo = mg.id
            LEFT JOIN ex_g32.materia m ON mg.sigla_materia = m.sigla
            WHERE r.nombre = 'docente'
            GROUP BY u.codigo, p.ci, p.nomb_comp, p.correo, p.tel
            ORDER BY p.nomb_comp ASC
        ";
        
        $stmt = $db->execute_query($sqlDocentes, []);
        $docentes = $db->fetch_all($stmt);

        $db->close_conection();

        return view('admin_carga_horaria', [
            'user' => $user,
            'docentes' => $docentes
        ]);

    } catch (Exception $e) {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
        return response('<h1>ERROR:</h1><pre>' . $e->getMessage() . "\n\n" . $e->getTraceAsString() . '</pre>', 500);
    }
});

// ENDPOINT: Obtener detalle de carga horaria por docente
Route::get('/admin/carga-horaria/detalle/{codigo}', function ($codigo) {
    // VALIDACIÓN: USUARIO EN SESIÓN
    if (!Session::has('user_code')) {
        return response()->json(['success' => false, 'message' => 'No autenticado'], 401);
    }

    // VALIDACIÓN: USUARIO ADMIN
    if (Session::get('user_role') !== 'admin') {
        return response()->json(['success' => false, 'message' => 'Acceso denegado'], 403);
    }

    $db = Config::$db;
    try {
        $db->create_conection();

        // Obtener información del docente
        $sqlDocente = "
            SELECT 
                u.codigo,
                p.ci,
                p.nomb_comp,
                p.correo,
                p.tel,
                p.profesion
            FROM ex_g32.usuario u
            INNER JOIN ex_g32.persona p ON u.ci = p.ci
            INNER JOIN ex_g32.rol r ON u.id_rol = r.id
            WHERE u.codigo = :codigo AND r.nombre = 'docente'
        ";
        
        $stmt = $db->execute_query($sqlDocente, [':codigo' => $codigo]);
        $docente = $db->fetch_one($stmt);

        if (!$docente) {
            $db->close_conection();
            return response()->json([
                'success' => false,
                'message' => 'Docente no encontrado'
            ], 404);
        }

        // Obtener materias asignadas con carga horaria
        $sqlMaterias = "
            SELECT 
                m.sigla,
                m.nombre,
                m.semestre,
                m.carga_horaria,
                mg.sigla_grupo,
                COUNT(DISTINCT c.id) as total_clases,
                COUNT(DISTINCT h.id) as total_horarios,
                STRING_AGG(DISTINCT h.dia, ', ') as dias
            FROM ex_g32.clase c
            INNER JOIN ex_g32.materia_grupo mg ON c.id_materia_grupo = mg.id
            INNER JOIN ex_g32.materia m ON mg.sigla_materia = m.sigla
            LEFT JOIN ex_g32.horario h ON c.id_horario = h.id
            WHERE c.usuario_codigo = :codigo
            GROUP BY m.sigla, m.nombre, m.semestre, m.carga_horaria, mg.sigla_grupo
            ORDER BY m.semestre ASC, m.nombre ASC
        ";
        
        $stmt = $db->execute_query($sqlMaterias, [':codigo' => $codigo]);
        $materias = $db->fetch_all($stmt);

        // Calcular horas semanales por horarios
        $sqlHorasSemanal = "
            SELECT 
                COALESCE(
                    SUM(
                        EXTRACT(EPOCH FROM (h.hora_f - h.hora_i)) / 3600
                    ), 0
                ) as horas_semanales
            FROM ex_g32.clase c
            INNER JOIN ex_g32.horario h ON c.id_horario = h.id
            WHERE c.usuario_codigo = :codigo
        ";
        
        $stmt = $db->execute_query($sqlHorasSemanal, [':codigo' => $codigo]);
        $horasData = $db->fetch_one($stmt);
        $horasSemanal = round($horasData['horas_semanales'] ?? 0, 2);

        // Calcular carga horaria total (suma de carga_horaria de materias)
        $cargaTotal = 0;
        foreach ($materias as $materia) {
            $cargaTotal += (int)$materia['carga_horaria']; // Convertir a entero
        }

        $db->close_conection();

        return response()->json([
            'success' => true,
            'docente' => $docente,
            'materias' => $materias,
            'horas_semanales' => $horasSemanal,
            'carga_total' => $cargaTotal,
            'total_materias' => count($materias)
        ]);

    } catch (Exception $e) {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener detalle: ' . $e->getMessage()
        ], 500);
    }
});

// ENDPOINT: Obtener horario completo del docente
Route::get('/admin/carga-horaria/horario/{codigo}', function ($codigo) {
    // VALIDACIÓN: USUARIO EN SESIÓN
    if (!Session::has('user_code')) {
        return response()->json(['success' => false, 'message' => 'No autenticado'], 401);
    }

    // VALIDACIÓN: USUARIO ADMIN
    if (Session::get('user_role') !== 'admin') {
        return response()->json(['success' => false, 'message' => 'Acceso denegado'], 403);
    }

    $db = Config::$db;
    try {
        $db->create_conection();

        // Obtener todas las clases con horarios del docente
        $sqlHorarios = "
            SELECT 
                c.id as clase_id,
                h.dia,
                TO_CHAR(h.hora_i, 'HH24:MI') as hora_inicio,
                TO_CHAR(h.hora_f, 'HH24:MI') as hora_fin,
                m.sigla as materia_sigla,
                m.nombre as materia_nombre,
                mg.sigla_grupo as grupo,
                a.nro as aula,
                a.modulo
            FROM ex_g32.clase c
            INNER JOIN ex_g32.horario h ON c.id_horario = h.id
            INNER JOIN ex_g32.materia_grupo mg ON c.id_materia_grupo = mg.id
            INNER JOIN ex_g32.materia m ON mg.sigla_materia = m.sigla
            INNER JOIN ex_g32.aula a ON c.nro_aula = a.nro
            WHERE c.usuario_codigo = :codigo
            ORDER BY 
                CASE h.dia
                    WHEN 'Lunes' THEN 1
                    WHEN 'Martes' THEN 2
                    WHEN 'Miércoles' THEN 3
                    WHEN 'Miercoles' THEN 3
                    WHEN 'Jueves' THEN 4
                    WHEN 'Viernes' THEN 5
                    WHEN 'Sábado' THEN 6
                    WHEN 'Sabado' THEN 6
                    WHEN 'Domingo' THEN 7
                END,
                h.hora_i ASC
        ";
        
        $stmt = $db->execute_query($sqlHorarios, [':codigo' => $codigo]);
        $horarios = $db->fetch_all($stmt);

        $db->close_conection();

        return response()->json([
            'success' => true,
            'horarios' => $horarios
        ]);

    } catch (Exception $e) {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener horarios: ' . $e->getMessage()
        ], 500);
    }
});
