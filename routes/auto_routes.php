<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Config;


Route::get('/auto/aulas/horario', function (Request $request) {
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');

    // VALIDACIÓN: Verificar que el usuario esté autenticado
    if (!Session::has('user_code')) {
        return response()->json([
            'success' => false,
            'message' => 'Usuario no Autenticado.'
        ], 401);
    }

    // VALIDACIÓN: Parámetros obligatorios
    $aulaNro = $request->query('aula_nro');
    $gestionId = $request->query('gestion_id');
    
    if (!$aulaNro) {
        return response()->json([
            'success' => false,
            'message' => 'El número del aula es obligatorio.'
        ], 400);
    }
    
    if (!$gestionId) {
        return response()->json([
            'success' => false,
            'message' => 'La gestión es obligatoria.'
        ], 400);
    }

    $db = Config::$db;
    try {
        $db->create_conection();

        // PASO 1: Verificar que el aula existe en la base de datos
        $sql = "SELECT nro, capacidad, modulo, tipo FROM ex_g32.aula WHERE nro = :nro";
        $params = [':nro' => $aulaNro];
        $stmt = $db->execute_query($sql, $params);
        $aula = $db->fetch_one($stmt);

        if (!$aula) {
            return response()->json([
                'success' => false,
                'message' => 'El aula no existe.'
            ], 404);
        }

        // PASO 1.5: Obtener información de la gestión
        $sqlGestion = "SELECT id, nombre FROM ex_g32.gestion WHERE id = :id";
        $paramsGestion = [':id' => $gestionId];
        $stmtGestion = $db->execute_query($sqlGestion, $paramsGestion);
        $gestion = $db->fetch_one($stmtGestion);

        if (!$gestion) {
            return response()->json([
                'success' => false,
                'message' => 'La gestión no existe.'
            ], 404);
        }

        // PASO 2: Obtener todas las clases asignadas al aula EN ESA GESTIÓN
        // Estructura de relaciones:
        // CLASE -> HORARIO (día, hora_i, hora_f)
        // CLASE -> MATERIA_GRUPO (sigla_materia, sigla_grupo)
        // CLASE -> GESTION (id_gestion)
        // MATERIA_GRUPO -> MATERIA (nombre, semestre, carga_horaria)
        
        $sql = "SELECT 
                    h.id as horario_id,
                    h.dia,
                    TO_CHAR(h.hora_i, 'HH24:MI') as hora_i,
                    TO_CHAR(h.hora_f, 'HH24:MI') as hora_f,
                    c.id as clase_id,
                    c.fecha_creacion,
                    c.id_gestion,
                    mg.id as materia_grupo_id,
                    mg.sigla_materia,
                    mg.sigla_grupo,
                    m.nombre as nombre_materia,
                    m.semestre,
                    m.carga_horaria
                FROM ex_g32.clase c
                INNER JOIN ex_g32.horario h ON c.id_horario = h.id
                LEFT JOIN ex_g32.materia_grupo mg ON c.id_materia_grupo = mg.id
                LEFT JOIN ex_g32.materia m ON mg.sigla_materia = m.sigla
                WHERE c.nro_aula = :nro_aula 
                  AND c.id_gestion = :id_gestion
                ORDER BY 
                    CASE h.dia
                        WHEN 'Lun' THEN 1
                        WHEN 'Mar' THEN 2
                        WHEN 'Mie' THEN 3
                        WHEN 'Jue' THEN 4
                        WHEN 'Vie' THEN 5
                        WHEN 'Sab' THEN 6
                        ELSE 7
                    END,
                    h.hora_i";
        
        $params = [
            ':nro_aula' => $aulaNro,
            ':id_gestion' => $gestionId
        ];
        $stmt = $db->execute_query($sql, $params);
        $horarios = $db->fetch_all($stmt);

        // PASO 3: Registrar consulta en bitácora del sistema
        $accion = 'CONSULTAR HORARIO AULA';
        $fecha = date('Y-m-d H:i:s');
        $estado = 'SUCCESS';
        $comentario = "Consulta de horario del aula Nro: {$aulaNro} - Gestión: {$gestion['nombre']}";
        $codigo = Session::get('user_code');
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);

        // RESPUESTA EXITOSA
        return response()->json([
            'success' => true,
            'aula' => $aula,
            'gestion' => $gestion,
            'horarios' => $horarios
        ]);

    } catch (Exception $e) {
        // MANEJO DE ERRORES
        error_log("❌ Error en /auto/aulas/horario: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener horario del aula',
            'error' => $e->getMessage()
        ], 500);
    } finally {
        // CERRAR CONEXIÓN A BD
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});



Route::get('/auto/aulas/disponibilidad', function (Request $request) {
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');

    // VALIDACIÓN: Usuario autenticado
    if (!Session::has('user_code')) {
        return response()->json([
            'success' => false,
            'message' => 'Usuario no Autenticado.'
        ], 401);
    }

    // VALIDACIÓN: Parámetros requeridos
    $dia = $request->query('dia');
    $horaInicio = $request->query('hora_inicio');
    $horaFin = $request->query('hora_fin');

    if (!$dia || !$horaInicio || !$horaFin) {
        return response()->json([
            'success' => false,
            'message' => 'Debe especificar día, hora inicio y hora fin.'
        ], 400);
    }

    $db = Config::$db;
    try {
        $db->create_conection();

        // Obtener todas las aulas
        $sql = "SELECT nro, capacidad, modulo, tipo FROM ex_g32.aula ORDER BY nro";
        $stmt = $db->execute_query($sql);
        $todasAulas = $db->fetch_all($stmt);

        // Obtener aulas ocupadas en ese horario
        // Recordar: CLASE tiene nro_aula y id_horario
        $sql = "SELECT DISTINCT c.nro_aula
                FROM ex_g32.clase c
                INNER JOIN ex_g32.horario h ON c.id_horario = h.id
                WHERE h.dia = :dia
                AND (
                    (h.hora_i < :hora_fin::time AND h.hora_f > :hora_inicio::time)
                )";
        
        $params = [
            ':dia' => $dia,
            ':hora_inicio' => $horaInicio,
            ':hora_fin' => $horaFin
        ];
        $stmt = $db->execute_query($sql, $params);
        $aulasOcupadas = $db->fetch_all($stmt);
        
        $nrosOcupados = array_map(function($a) {
            return $a['nro_aula'];
        }, $aulasOcupadas);

        // Clasificar aulas
        $aulasDisponibles = [];
        $aulasNoDisponibles = [];

        foreach ($todasAulas as $aula) {
            if (in_array($aula['nro'], $nrosOcupados)) {
                $aulasNoDisponibles[] = $aula;
            } else {
                $aulasDisponibles[] = $aula;
            }
        }

        // Registrar en bitácora
        $accion = 'CONSULTAR DISPONIBILIDAD AULAS';
        $fecha = date('Y-m-d H:i:s');
        $estado = 'SUCCESS';
        $comentario = "Consulta de disponibilidad: $dia $horaInicio-$horaFin";
        $codigo = Session::get('user_code');
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);

        return response()->json([
            'success' => true,
            'disponibles' => $aulasDisponibles,
            'ocupadas' => $aulasNoDisponibles
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al consultar disponibilidad.',
            'error' => $e->getMessage()
        ], 500);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

// Endpoint para verificar conflictos de horario en un aula específica
// Verifica que un aula NO tenga dos horarios en el mismo día y rango de horas
Route::get('/auto/aulas/verificar-conflicto', function (Request $request) {
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');

    //VALIDACION:USUARIO EN SESION
    if (!Session::has('user_code')) {
        return response()->json([
            'success' => false,
            'message' => 'Usuario no Autenticado.'
        ], 401);
    }

    $aulaNro = $request->query('aula_nro');
    $dia = $request->query('dia');
    $horaInicio = $request->query('hora_inicio');
    $horaFin = $request->query('hora_fin');
    $horarioIdExcluir = $request->query('horario_id'); // Para excluir en edición

    if (!$aulaNro || !$dia || !$horaInicio || !$horaFin) {
        return response()->json([
            'success' => false,
            'message' => 'Debe especificar aula, día, hora inicio y hora fin.'
        ], 400);
    }

    $db = Config::$db;
    try {
        $db->create_conection();

        // Buscar horarios que se traslapen en el mismo día y aula
        // Dos rangos de tiempo se traslapan si:
        // (inicio1 < fin2) AND (fin1 > inicio2)
        // Estructura: CLASE -> HORARIO (clase.id_horario = horario.id)
        $sql = "SELECT 
                    h.id as horario_id,
                    h.dia,
                    TO_CHAR(h.hora_i, 'HH24:MI') as hora_i,
                    TO_CHAR(h.hora_f, 'HH24:MI') as hora_f,
                    c.id as clase_id,
                    mg.sigla_materia,
                    mg.sigla_grupo,
                    m.nombre as nombre_materia
                FROM ex_g32.clase c
                INNER JOIN ex_g32.horario h ON c.id_horario = h.id
                LEFT JOIN ex_g32.materia_grupo mg ON c.id_materia_grupo = mg.id
                LEFT JOIN ex_g32.materia m ON mg.sigla_materia = m.sigla
                WHERE c.nro_aula = :nro_aula
                AND h.dia = :dia
                AND (h.hora_i < :hora_fin::time AND h.hora_f > :hora_inicio::time)";
        
        $params = [
            ':nro_aula' => $aulaNro,
            ':dia' => $dia,
            ':hora_inicio' => $horaInicio,
            ':hora_fin' => $horaFin
        ];

        // Si estamos editando un horario, excluir ese registro
        if ($horarioIdExcluir) {
            $sql .= " AND h.id != :horario_id";
            $params[':horario_id'] = $horarioIdExcluir;
        }

        $stmt = $db->execute_query($sql, $params);
        $conflictos = $db->fetch_all($stmt);

        $hayConflicto = count($conflictos) > 0;

        return response()->json([
            'success' => true,
            'hay_conflicto' => $hayConflicto,
            'conflictos' => $conflictos,
            'mensaje' => $hayConflicto 
                ? 'El aula ya está ocupada en ese horario.' 
                : 'El aula está disponible en ese horario.'
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al verificar conflictos.',
            'error' => $e->getMessage()
        ], 500);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

// ==================== GENERACIÓN AUTOMÁTICA DE HORARIOS ====================

// Vista principal de generación de horarios
Route::get('/auto/generar-horario', function () {
    if (!Session::has('user_code')) {
        return redirect('/login');
    }

    $user = [
        'nomb_comp' => Session::get('name'),
        'correo' => Session::get('mail'),
        'tel' => Session::get('tel'),
        'ci' => Session::get('ci'),
        'rol' => Session::get('user_role')
    ];

    $db = Config::$db;
    try {
        $db->create_conection();

        // Obtener gestiones existentes
        $sqlGestiones = "SELECT DISTINCT g.id, g.nombre, g.fecha_i, g.fecha_f
                        FROM ex_g32.gestion g
                        ORDER BY g.fecha_i DESC";
        $stmtGestiones = $db->execute_query($sqlGestiones, []);
        $gestiones = $db->fetch_all($stmtGestiones);

        // Registrar en bitácora
        $accion = 'ACCEDER GENERADOR HORARIO';
        $fecha = date('Y-m-d H:i:s');
        $estado = 'SUCCESS';
        $comentario = 'Acceso al módulo de generación de horarios';
        $codigo = Session::get('user_code');
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);

        return view('auto_generar_horario', [
            'user' => $user,
            'gestiones' => $gestiones
        ]);

    } catch (Exception $e) {
        // Log del error
        error_log("Error en /auto/generar-horario: " . $e->getMessage());
        
        // Retornar con gestiones vacías en caso de error
        return view('auto_generar_horario', [
            'user' => $user,
            'gestiones' => []
        ]);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

// Obtener datos para generación (materias, grupos, docentes)
Route::get('/auto/generar-horario/datos', function (Request $request) {
    if (!Session::has('user_code')) {
        return response()->json(['success' => false, 'message' => 'No autenticado'], 401);
    }

    $gestionId = $request->query('gestion_id');
    if (!$gestionId) {
        return response()->json(['success' => false, 'message' => 'Gestión no especificada'], 400);
    }

    $db = Config::$db;
    try {
        $db->create_conection();

        // Obtener todas las materias con su carga horaria
        $sqlMaterias = "SELECT sigla, nombre, semestre, CAST(carga_horaria AS INTEGER) as carga_horaria
                       FROM ex_g32.materia
                       ORDER BY semestre, nombre";
        $stmtMaterias = $db->execute_query($sqlMaterias, []);
        $materias = $db->fetch_all($stmtMaterias);

        // Obtener todos los grupos
        $sqlGrupos = "SELECT sigla FROM ex_g32.grupo ORDER BY sigla";
        $stmtGrupos = $db->execute_query($sqlGrupos, []);
        $grupos = $db->fetch_all($stmtGrupos);

        // Obtener docentes disponibles
        $sqlDocentes = "SELECT u.codigo, p.nomb_comp, p.ci
                       FROM ex_g32.usuario u
                       INNER JOIN ex_g32.persona p ON u.ci = p.ci
                       INNER JOIN ex_g32.rol r ON u.id_rol = r.id
                       WHERE r.nombre = 'docente'
                       ORDER BY p.nomb_comp";
        $stmtDocentes = $db->execute_query($sqlDocentes, []);
        $docentes = $db->fetch_all($stmtDocentes);

        // Obtener aulas disponibles
        $sqlAulas = "SELECT nro, capacidad, modulo, tipo
                    FROM ex_g32.aula
                    ORDER BY tipo, modulo, nro";
        $stmtAulas = $db->execute_query($sqlAulas, []);
        $aulas = $db->fetch_all($stmtAulas);

        // Obtener horarios base (bloques horarios disponibles)
        $sqlHorarios = "SELECT id, dia, 
                              TO_CHAR(hora_i, 'HH24:MI') as hora_i,
                              TO_CHAR(hora_f, 'HH24:MI') as hora_f,
                              EXTRACT(EPOCH FROM (hora_f - hora_i)) / 3600 as duracion_horas
                       FROM ex_g32.horario
                       ORDER BY 
                           CASE dia
                               WHEN 'Lun' THEN 1
                               WHEN 'Mar' THEN 2
                               WHEN 'Mie' THEN 3
                               WHEN 'Jue' THEN 4
                               WHEN 'Vie' THEN 5
                               WHEN 'Sab' THEN 6
                               ELSE 7
                           END,
                           hora_i";
        $stmtHorarios = $db->execute_query($sqlHorarios, []);
        $horarios = $db->fetch_all($stmtHorarios);

        // Obtener materia_grupo existentes
        $sqlMateriaGrupo = "SELECT id, sigla_materia, sigla_grupo 
                           FROM ex_g32.materia_grupo 
                           ORDER BY sigla_materia, sigla_grupo";
        $stmtMateriaGrupo = $db->execute_query($sqlMateriaGrupo, []);
        $materiaGrupos = $db->fetch_all($stmtMateriaGrupo);

        return response()->json([
            'success' => true,
            'materias' => $materias,
            'grupos' => $grupos,
            'docentes' => $docentes,
            'aulas' => $aulas,
            'horarios' => $horarios,
            'materia_grupos' => $materiaGrupos
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener datos',
            'error' => $e->getMessage()
        ], 500);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});



// Verificar disponibilidad de aula en horario y gestión específica
Route::post('/auto/generar-horario/verificar-disponibilidad', function (Request $request) {
    if (!Session::has('user_code')) {
        return response()->json(['success' => false, 'message' => 'No autenticado'], 401);
    }

    $aulaNro = $request->input('aula_nro');
    $horarioId = $request->input('horario_id');
    $gestionId = $request->input('gestion_id');

    if (!$aulaNro || !$horarioId || !$gestionId) {
        return response()->json(['success' => false, 'message' => 'Datos incompletos'], 400);
    }

    $db = Config::$db;
    try {
        $db->create_conection();

        // Verificar si el aula está ocupada en ese horario y gestión
        $sql = "SELECT COUNT(*) as total
                FROM ex_g32.clase c
                INNER JOIN ex_g32.gestion g ON c.id_gestion = g.id
                WHERE c.nro_aula = :nro_aula
                AND c.id_horario = :horario_id
                AND c.id_gestion = :gestion_id";
        
        $params = [
            ':nro_aula' => $aulaNro,
            ':horario_id' => $horarioId,
            ':gestion_id' => $gestionId
        ];
        
        $stmt = $db->execute_query($sql, $params);
        $resultado = $db->fetch_one($stmt);

        $disponible = $resultado['total'] == 0;

        return response()->json([
            'success' => true,
            'disponible' => $disponible,
            'mensaje' => $disponible ? 'Aula disponible' : 'Aula ocupada en ese horario'
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al verificar disponibilidad',
            'error' => $e->getMessage()
        ], 500);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

// ================================================================================
// ENDPOINT: ASIGNAR CLASE MANUALMENTE
// ================================================================================

// ================================================================================
Route::post('/auto/generar-horario/asignar-clase', function (Request $request) {
    
    // ============================================================================
    // PASO 1: VALIDACIÓN DE AUTENTICACIÓN
    // ============================================================================
    if (!Session::has('user_code')) {
        return response()->json(['success' => false, 'message' => 'No autenticado'], 401);
    }

    // ============================================================================
    // PASO 2: CAPTURA Y VALIDACIÓN DE PARÁMETROS DE ENTRADA
    // ============================================================================
    $docenteCodigo = $request->input('docente_codigo');
    $horarioIds = $request->input('horario_ids'); // Array: puede tener nulls
    $siglaMateriaInput = $request->input('sigla_materia');
    $siglaGrupoInput = $request->input('sigla_grupo');
    $gestionId = $request->input('gestion_id');

    // Validar que todos los campos requeridos estén presentes
    if (!$docenteCodigo || !$horarioIds || !$siglaMateriaInput || !$siglaGrupoInput || !$gestionId) {
        return response()->json(['success' => false, 'message' => 'Datos incompletos'], 400);
    }

    // ============================================================================
    // PASO 3: FILTRAR HORARIOS VÁLIDOS (eliminar nulls, vacíos, "null" string)
    // ============================================================================
    $horariosSeleccionados = array_filter($horarioIds, function($id) {
        return $id !== null && $id !== '' && $id !== 'null';
    });

    // Validar que al menos haya un horario seleccionado
    if (empty($horariosSeleccionados)) {
        return response()->json(['success' => false, 'message' => 'Debe seleccionar al menos un horario'], 400);
    }

    // ============================================================================
    // PASO 4: INICIAR TRANSACCIÓN DE BASE DE DATOS
    // ============================================================================
    $db = Config::$db;
    try {
        $db->create_conection();
        $pdo = $db->get_connection();
        $pdo->beginTransaction(); // Iniciar transacción para rollback en caso de error

        // ========================================================================
        // PASO 5: OBTENER INFORMACIÓN DE LA MATERIA
        // ========================================================================
        // Consulta: Obtener sigla, nombre y carga_horaria de la materia
        $sqlMateria = "SELECT sigla, nombre, carga_horaria FROM ex_g32.materia WHERE sigla = :sigla";
        $stmtMateria = $db->execute_query($sqlMateria, [':sigla' => $siglaMateriaInput]);
        $materia = $db->fetch_one($stmtMateria);

        // Si no existe la materia, abortar transacción
        if (!$materia) {
            $pdo->rollBack();
            return response()->json(['success' => false, 'message' => 'Materia no encontrada'], 404);
        }

        // ========================================================================
        // PASO 6: OBTENER O CREAR MATERIA_GRUPO
        // ========================================================================
        // La tabla materia_grupo vincula una materia con un grupo específico
        // Ej: INF342-SA, INF342-SB, etc.
        
        $sqlMateriaGrupo = "SELECT id FROM ex_g32.materia_grupo 
                           WHERE sigla_materia = :sigla_materia AND sigla_grupo = :sigla_grupo";
        $paramsMG = [':sigla_materia' => $siglaMateriaInput, ':sigla_grupo' => $siglaGrupoInput];
        $stmtMG = $db->execute_query($sqlMateriaGrupo, $paramsMG);
        $materiaGrupo = $db->fetch_one($stmtMG);

        // Si no existe, crear el registro materia_grupo
        if (!$materiaGrupo) {
            $sqlInsertMG = "INSERT INTO ex_g32.materia_grupo (sigla_materia, sigla_grupo) 
                           VALUES (:sigla_materia, :sigla_grupo) RETURNING id";
            $stmtInsertMG = $db->execute_query($sqlInsertMG, $paramsMG);
            $materiaGrupo = $db->fetch_one($stmtInsertMG);
        }

        $materiaGrupoId = $materiaGrupo['id'];

        // ========================================================================
        // PASO 7: VERIFICAR QUE NO EXISTAN CLASES PREVIAS
        // ========================================================================
        // No se puede asignar la misma materia-grupo dos veces en la misma gestión
        
        $sqlExistentes = "SELECT COUNT(*) as total FROM ex_g32.clase 
                         WHERE id_materia_grupo = :id_mg AND id_gestion = :id_gestion";
        $stmtExistentes = $db->execute_query($sqlExistentes, [
            ':id_mg' => $materiaGrupoId,
            ':id_gestion' => $gestionId
        ]);
        $existentes = $db->fetch_one($stmtExistentes);

        if ($existentes['total'] > 0) {
            $pdo->rollBack();
            return response()->json([
                'success' => false,
                'message' => "Ya existen {$existentes['total']} clases generadas para {$siglaMateriaInput}-{$siglaGrupoInput} en esta gestión"
            ], 400);
        }

        // ========================================================================
        // PASO 8: OBTENER DETALLES DE LOS HORARIOS SELECCIONADOS
        // ========================================================================
        // Consulta: Para cada horario seleccionado, obtener:
        // - id: ID del horario
        // - dia: Día de la semana (Lunes, Martes, etc.)
        // - hora_i: Hora inicio (formato HH24:MI)
        // - hora_f: Hora fin (formato HH24:MI)
        // - duracion_horas: Duración en horas (decimal)
        
        $sqlHorarios = "SELECT id, dia, 
                       TO_CHAR(hora_i, 'HH24:MI') as hora_i,
                       TO_CHAR(hora_f, 'HH24:MI') as hora_f,
                       EXTRACT(EPOCH FROM (hora_f - hora_i)) / 3600 as duracion_horas
                       FROM ex_g32.horario
                       WHERE id IN (" . implode(',', array_fill(0, count($horariosSeleccionados), '?')) . ")";
        
        $stmtHorarios = $pdo->prepare($sqlHorarios);
        $stmtHorarios->execute(array_values($horariosSeleccionados));
        $horarios = $stmtHorarios->fetchAll(PDO::FETCH_ASSOC);

        // Log de depuración: mostrar horarios obtenidos
        error_log("📅 Horarios seleccionados: " . json_encode($horarios, JSON_PRETTY_PRINT));

        // ========================================================================
        // PASO 8.5: VALIDAR QUE EL DOCENTE NO TENGA CONFLICTOS DE HORARIO
        // ========================================================================
        // Verificar que el docente no tenga otras clases asignadas en los
        // mismos horarios (mismo día + solapamiento de horas) en la misma gestión
        
        foreach ($horarios as $horario) {
            $sqlCheckDocenteConflicto = "SELECT 
                    c.id,
                    mg.sigla_materia,
                    mg.sigla_grupo,
                    h.dia,
                    TO_CHAR(h.hora_i, 'HH24:MI') as hora_i,
                    TO_CHAR(h.hora_f, 'HH24:MI') as hora_f,
                    m.nombre as nombre_materia,
                    c.nro_aula
                FROM ex_g32.clase c
                INNER JOIN ex_g32.horario h ON c.id_horario = h.id
                INNER JOIN ex_g32.materia_grupo mg ON c.id_materia_grupo = mg.id
                INNER JOIN ex_g32.materia m ON mg.sigla_materia = m.sigla
                WHERE c.usuario_codigo = :usuario_codigo
                AND c.id_gestion = :id_gestion
                AND h.dia = :dia
                AND (h.hora_i < :hora_f::time AND h.hora_f > :hora_i::time)";
            
            $stmtCheckDocente = $db->execute_query($sqlCheckDocenteConflicto, [
                ':usuario_codigo' => $docenteCodigo,
                ':id_gestion' => $gestionId,
                ':dia' => $horario['dia'],
                ':hora_i' => $horario['hora_i'],
                ':hora_f' => $horario['hora_f']
            ]);
            
            $conflictosDocente = $db->fetch_all($stmtCheckDocente);
            
            if (!empty($conflictosDocente)) {
                $pdo->rollBack();
                
                // Preparar mensaje con detalles del conflicto
                $conflicto = $conflictosDocente[0];
                $mensaje = "El docente ya tiene clase asignada en este horario: " .
                          "{$conflicto['dia']} {$conflicto['hora_i']}-{$conflicto['hora_f']} " .
                          "({$conflicto['sigla_materia']}-{$conflicto['sigla_grupo']} - " .
                          "{$conflicto['nombre_materia']}, Aula {$conflicto['nro_aula']})";
                
                error_log("❌ Conflicto de horario para docente {$docenteCodigo}: " . $mensaje);
                
                return response()->json([
                    'success' => false,
                    'message' => $mensaje,
                    'conflictos' => $conflictosDocente
                ], 400);
            }
        }
        
        error_log("✅ Docente {$docenteCodigo} no tiene conflictos de horario");

        // ========================================================================
        // PASO 9: CALCULAR HORAS SEMANALES Y VALIDAR DÍAS
        // ========================================================================
        $horasSemanales = 0;
        $diasUsados = [];
        
        foreach ($horarios as $h) {
            $horasSemanales += floatval($h['duracion_horas']);
            $diasUsados[] = $h['dia'];
        }

        // ========================================================================
        // VALIDACIÓN DE DÍAS SEGÚN CARGA HORARIA
        // ========================================================================
        // REGLA 1: Materias de 135 horas → TODOS los horarios en días DIFERENTES
        //          Ejemplo: Si es 135 hrs NO puede tener Lunes 07:00-08:30 Y Lunes 10:00-11:30
        //
        // REGLA 2: Otras cargas horarias → Permite MÁXIMO 2 horarios el mismo día
        //          Ejemplo: Si es 90 hrs SÍ puede tener Lunes 07:00-08:30 Y Lunes 10:00-11:30
        //                   pero NO puede tener 3 horarios el mismo Lunes
        
        $cargaHoraria = intval($materia['carga_horaria']);
        
        if ($cargaHoraria == 135) {
            // Para materias de 135 hrs: días ÚNICOS (no repetir ningún día)
            if (count($diasUsados) !== count(array_unique($diasUsados))) {
                $pdo->rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Materias de 135 horas deben tener todos los horarios en días diferentes'
                ], 400);
            }
        } else {
            // Para otras cargas: máximo 2 horarios por día
            $contadorDias = array_count_values($diasUsados);
            foreach ($contadorDias as $dia => $cantidad) {
                if ($cantidad > 2) {
                    $pdo->rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "No se puede asignar más de 2 horarios el mismo día. Día '{$dia}' tiene {$cantidad} horarios"
                    ], 400);
                }
            }
        }

        // ========================================================================
        // PASO 10: VALIDACIÓN DE HORAS SEMANALES SEGÚN REGLAS DE NEGOCIO
        // ========================================================================
        // Las horas semanales asignadas deben cumplir con las reglas según la
        // carga horaria total de la materia:
        //
        // REGLAS:
        // 1. Carga Horaria = 135 hrs → Debe tener 4.5 hrs/semana
        // 2. Carga Horaria > 135 hrs → Debe tener 6 hrs/semana (laboratorios)
        // 3. Carga Horaria = 90 hrs  → Debe tener 3 hrs/semana
        // 4. Modalidad de Graduación → Entre 5 y 7 hrs/semana (flexible)
        //
        // TOLERANCIA: ±15 minutos (0.25 horas) para compensar redondeos
        // ========================================================================
        
        $cargaHoraria = intval($materia['carga_horaria']);
        $nombreMateria = strtoupper($materia['nombre']);
        
        // Detectar si es una materia de "Modalidad de Graduación"
        $esModalidadGraduacion = strpos($nombreMateria, 'MODALIDAD DE GRADUACION') !== false || 
                                 strpos($nombreMateria, 'MODALIDAD GRADUACION') !== false;

        $horasRequeridas = 0;
        $tolerancia = 0.25; // 15 minutos de tolerancia
        $mensajeError = '';

        if ($esModalidadGraduacion) {
            // ====================================================================
            // CASO ESPECIAL: Modalidad de Graduación
            // ====================================================================
            // Permite mayor flexibilidad: entre 5 y 7 horas semanales
            
            if ($horasSemanales < 5 || $horasSemanales > 7) {
                $pdo->rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Materias de 'MODALIDAD DE GRADUACION' deben tener entre 5 y 7 horas semanales. Actual: {$horasSemanales} hrs"
                ], 400);
            }
        } else {
            // ====================================================================
            // CASOS NORMALES: Validación según carga horaria
            // ====================================================================
            
            if ($cargaHoraria == 135) {
                // Materias de 135 horas → 4.5 hrs/semana
                $horasRequeridas = 4.5;
                $mensajeError = "Materias con 135 hrs de carga deben tener exactamente 4.5 horas semanales. Actual: {$horasSemanales} hrs";
                
            } elseif ($cargaHoraria > 135) {
                // Materias con más de 135 horas (laboratorios) → 6 hrs/semana
                $horasRequeridas = 6;
                $mensajeError = "Materias con más de 135 hrs de carga deben tener exactamente 6 horas semanales. Actual: {$horasSemanales} hrs";
                
            } elseif ($cargaHoraria == 90) {
                // Materias de 90 horas → 3 hrs/semana
                $horasRequeridas = 3;
                $mensajeError = "Materias con 90 hrs de carga deben tener exactamente 3 horas semanales. Actual: {$horasSemanales} hrs";
            }

            // Verificar si las horas están dentro de la tolerancia permitida
            // Ejemplo: Si requiere 4.5 hrs, acepta entre 4.25 y 4.75 hrs
            if ($horasRequeridas > 0 && abs($horasSemanales - $horasRequeridas) > $tolerancia) {
                $pdo->rollBack();
                return response()->json([
                    'success' => false,
                    'message' => $mensajeError
                ], 400);
            }
        }

        // ========================================================================
        // PASO 11: OBTENER TODAS LAS AULAS DISPONIBLES
        // ========================================================================
        // Obtener listado completo de aulas ordenadas por número
        // Se necesitarán aulas para asignar cada horario seleccionado
        
        $sqlAulas = "SELECT nro, tipo FROM ex_g32.aula ORDER BY nro";
        $stmtAulas = $db->execute_query($sqlAulas);
        $todasAulas = $db->fetch_all($stmtAulas);

        $aulaPrincipal = null;  // Aula preferida para esta materia-grupo
        $clasesCreadas = 0;     // Contador de clases creadas exitosamente

        // ========================================================================
        // PASO 12: ASIGNAR AULA PARA CADA HORARIO SELECCIONADO
        // ========================================================================
        // ESTRATEGIA DE ASIGNACIÓN:
        // 1. PRIORIZAR la misma aula para toda la materia-grupo (mejor experiencia)
        // 2. Solo cambiar de aula si hay CONFLICTO de horario (solapamiento)
        // 3. Validar solapamiento por DÍA y RANGO DE HORAS (no solo por ID)
        //
        // Ejemplo:
        // - Aula 10: Miércoles 11:30-13:00 (ocupada)
        // - Nuevo: Miércoles 10:45-13:00
        // - RESULTADO: NO se puede usar Aula 10 porque se solapa, buscar otra
        // ========================================================================
        
        foreach ($horarios as $horario) {
            $asignado = false; // Flag para controlar si se asignó aula para este horario
            
            // Log de depuración
            error_log("🔍 Buscando aula para horario: {$horario['dia']} {$horario['hora_i']}-{$horario['hora_f']} (ID={$horario['id']})");
            
            // ====================================================================
            // ESTRATEGIA 1: Intentar usar el aula principal (si ya se asignó una)
            // ====================================================================
            if ($aulaPrincipal !== null) {
                error_log("  🎯 Intentando usar aula principal: {$aulaPrincipal}");
                
                // ================================================================
                // VALIDACIÓN DE DISPONIBILIDAD DEL AULA PRINCIPAL
                // ================================================================
                // Verifica si el aula está disponible en este horario específico
                // 
                // IMPORTANTE: La validación incluye 3 condiciones críticas:
                // 1. Mismo aula (nro_aula)
                // 2. Misma gestión (id_gestion) ← CLAVE: Si es otra gestión, SÍ puede usar el aula
                // 3. Mismo día con solapamiento de horas
                //
                // Ejemplo:
                // - Gestión 1/2025: Aula 10, Lunes 10:00-12:00 (ocupada)
                // - Gestión 2/2025: Aula 10, Lunes 10:00-12:00 (DISPONIBLE)
                // ✅ Diferentes gestiones = NO hay conflicto
                
                $sqlCheckSolapamiento = "SELECT COUNT(*) as total 
                                        FROM ex_g32.clase c
                                        INNER JOIN ex_g32.horario h ON c.id_horario = h.id
                                        WHERE c.nro_aula = :nro_aula 
                                        AND c.id_gestion = :id_gestion
                                        AND h.dia = :dia
                                        AND (
                                            (h.hora_i < :hora_f::time AND h.hora_f > :hora_i::time)
                                        )";
                
                $stmtCheckSolapamiento = $db->execute_query($sqlCheckSolapamiento, [
                    ':nro_aula' => $aulaPrincipal,
                    ':id_gestion' => $gestionId,
                    ':dia' => $horario['dia'],
                    ':hora_i' => $horario['hora_i'],
                    ':hora_f' => $horario['hora_f']
                ]);
                $checkSolapamiento = $db->fetch_one($stmtCheckSolapamiento);
                
                error_log("  🏫 Aula principal {$aulaPrincipal}: {$checkSolapamiento['total']} conflictos de horario");
                
                if ($checkSolapamiento['total'] == 0) {
                    // Aula principal disponible, usarla
                    error_log("  ✅ Usando aula principal {$aulaPrincipal} para horario {$horario['dia']} {$horario['hora_i']}-{$horario['hora_f']}");
                    
                    $sqlInsert = "INSERT INTO ex_g32.clase 
                                 (usuario_codigo, id_horario, nro_aula, id_materia_grupo, id_gestion, fecha_creacion)
                                 VALUES (:usuario_codigo, :id_horario, :nro_aula, :id_materia_grupo, :id_gestion, NOW())";
                    
                    $db->execute_query($sqlInsert, [
                        ':usuario_codigo' => $docenteCodigo,
                        ':id_horario' => $horario['id'],
                        ':nro_aula' => $aulaPrincipal,
                        ':id_materia_grupo' => $materiaGrupoId,
                        ':id_gestion' => $gestionId
                    ]);
                    
                    $clasesCreadas++;
                    $asignado = true;
                    continue; // Pasar al siguiente horario
                } else {
                    error_log("  ⚠️  Aula principal {$aulaPrincipal} tiene conflicto, buscando aula alternativa...");
                }
            }
            
            // ====================================================================
            // ESTRATEGIA 2: Buscar aula disponible (primera vez o por conflicto)
            // ====================================================================
            foreach ($todasAulas as $aula) {
                
                // ================================================================
                // VALIDACIÓN DE DISPONIBILIDAD CON SOLAPAMIENTO Y GESTIÓN
                // ================================================================
                // Verifica disponibilidad considerando 3 factores:
                // 
                // 1. AULA: Número de aula específica
                // 2. GESTIÓN: Solo conflicto si es la MISMA gestión académica
                //    - Gestión 1/2025 vs Gestión 2/2025 = NO conflicto 
                //    - Gestión 1/2025 vs Gestión 1/2025 = SÍ puede haber conflicto 
                // 3. HORARIO: Mismo día + solapamiento de horas
                //
                // Lógica de solapamiento:
                // Dos horarios se solapan SI:
                // (hora_inicio_nuevo < hora_fin_existente) Y (hora_fin_nuevo > hora_inicio_existente)
                //
                // Ejemplos:
                //  Aula 10, Gestión 1/2025, Lunes 10:00-12:00 (ocupada)
                //    Aula 10, Gestión 2/2025, Lunes 10:00-12:00 (DISPONIBLE - diferente gestión)
                //
                //  Aula 10, Gestión 1/2025, Lunes 10:00-12:00 (ocupada)
                //    Aula 10, Gestión 1/2025, Lunes 11:00-13:00 (NO DISPONIBLE - mismo día, se solapa)
                //
                //  Aula 10, Gestión 1/2025, Lunes 10:00-12:00 (ocupada)
                //    Aula 10, Gestión 1/2025, Martes 10:00-12:00 (DISPONIBLE - diferente día)
                
                $sqlCheckSolapamiento = "SELECT COUNT(*) as total 
                                        FROM ex_g32.clase c
                                        INNER JOIN ex_g32.horario h ON c.id_horario = h.id
                                        WHERE c.nro_aula = :nro_aula 
                                        AND c.id_gestion = :id_gestion
                                        AND h.dia = :dia
                                        AND (
                                            (h.hora_i < :hora_f::time AND h.hora_f > :hora_i::time)
                                        )";
                
                $stmtCheckSolapamiento = $db->execute_query($sqlCheckSolapamiento, [
                    ':nro_aula' => $aula['nro'],
                    ':id_gestion' => $gestionId,
                    ':dia' => $horario['dia'],
                    ':hora_i' => $horario['hora_i'],
                    ':hora_f' => $horario['hora_f']
                ]);
                $checkSolapamiento = $db->fetch_one($stmtCheckSolapamiento);

                error_log("  🏫 Aula {$aula['nro']}: {$checkSolapamiento['total']} conflictos en gestión {$gestionId}, {$horario['dia']} {$horario['hora_i']}-{$horario['hora_f']}");

                // ================================================================
                // ASIGNACIÓN: Si el aula está libre en esta gestión, asignarla
                // ================================================================
                // total = 0 significa que NO hay conflictos en esta gestión específica
                // Puede haber clases en otras gestiones en el mismo horario, pero NO importa
                
                if ($checkSolapamiento['total'] == 0) {
                    error_log("  ✅ Asignando aula {$aula['nro']} para horario {$horario['dia']} {$horario['hora_i']}-{$horario['hora_f']} en gestión {$gestionId}");
                    
                    // Insertar registro de clase en la base de datos
                    $sqlInsert = "INSERT INTO ex_g32.clase 
                                 (usuario_codigo, id_horario, nro_aula, id_materia_grupo, id_gestion, fecha_creacion)
                                 VALUES (:usuario_codigo, :id_horario, :nro_aula, :id_materia_grupo, :id_gestion, NOW())";
                    
                    $db->execute_query($sqlInsert, [
                        ':usuario_codigo' => $docenteCodigo,
                        ':id_horario' => $horario['id'],
                        ':nro_aula' => $aula['nro'],
                        ':id_materia_grupo' => $materiaGrupoId,
                        ':id_gestion' => $gestionId
                    ]);

                    // Si es la primera asignación, establecer como aula principal
                    if ($aulaPrincipal === null) {
                        $aulaPrincipal = $aula['nro'];
                        error_log("  🎯 Aula {$aula['nro']} establecida como aula principal para {$siglaMateriaInput}-{$siglaGrupoInput}");
                    }
                    
                    $clasesCreadas++;
                    $asignado = true;
                    
                    break; // Salir del loop de aulas, pasar al siguiente horario
                } else {
                    error_log("  ❌ Aula {$aula['nro']} tiene conflicto de horario en gestión {$gestionId}");
                }
            }

            // ====================================================================
            // VALIDACIÓN FINAL: Si no se encontró aula disponible, abortar todo
            // ====================================================================
            // Si llegamos aquí, significa que NINGUNA aula está disponible
            // en esta gestión para este horario específico
            
            if (!$asignado) {
                error_log("❌ No se encontró aula disponible para horario {$horario['dia']} {$horario['hora_i']}-{$horario['hora_f']} (ID={$horario['id']}) en gestión {$gestionId}");
                error_log("📊 Total de aulas verificadas: " . count($todasAulas));
                error_log("📊 Todas las aulas tienen conflictos en la gestión actual");
                
                $pdo->rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "No hay aulas disponibles para el horario {$horario['dia']} {$horario['hora_i']}-{$horario['hora_f']} en esta gestión. Todas las aulas están ocupadas en ese horario."
                ], 400);
            }
        }

        // ========================================================================
        // PASO 13: CONFIRMAR TRANSACCIÓN Y REGISTRAR EN BITÁCORA
        // ========================================================================
        // Si llegamos aquí, todas las clases se crearon exitosamente
        // Confirmar cambios en la base de datos
        
        $pdo->commit();

        // Registrar acción en bitácora para auditoría
        // Formato: "Asignación manual de X clases para [Materia] - Grupo Y en Aula Z"
        
        $accion = 'ASIGNAR CLASE MANUAL';
        $fecha = date('Y-m-d H:i:s');
        $estado = 'SUCCESS';
        $comentario = "Clases asignadas manualmente: {$clasesCreadas} para {$siglaMateriaInput}-{$siglaGrupoInput}, {$horasSemanales} hrs/semana, Aula principal: {$aulaPrincipal}";
        $codigo = Session::get('user_code');
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);

        // ========================================================================
        // PASO 14: RESPUESTA EXITOSA
        // ========================================================================
        // Devolver confirmación con detalles de la asignación
        
        return response()->json([
            'success' => true,
            'message' => "Se asignaron {$clasesCreadas} clases exitosamente ({$horasSemanales} hrs/semana) en Aula {$aulaPrincipal}",
            'data' => [
                'clases_creadas' => $clasesCreadas,
                'materia' => $materia['nombre'],
                'sigla_materia' => $siglaMateriaInput,
                'sigla_grupo' => $siglaGrupoInput,
                'docente_codigo' => $docenteCodigo,
                'aula_principal' => $aulaPrincipal,
                'horas_semanales' => $horasSemanales,
                'carga_horaria' => $cargaHoraria
            ]
        ], 201);

    } catch (Exception $e) {
        // ========================================================================
        // MANEJO DE ERRORES GENERALES
        // ========================================================================
        // Si ocurre cualquier error no controlado, revertir todos los cambios
        
        if (isset($pdo)) {
            $pdo->rollBack();
        }
        
        error_log("❌ ERROR CRÍTICO en asignación manual: " . $e->getMessage());
        error_log("📍 Línea: " . $e->getLine() . " | Archivo: " . $e->getFile());
        
        return response()->json([
            'success' => false,
            'message' => 'Error al asignar clase',
            'error' => $e->getMessage()
        ], 500);
        
    } finally {
        // ========================================================================
        // LIMPIEZA: Cerrar conexión a la base de datos
        // ========================================================================
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

// Obtener horario generado para una gestión
Route::get('/auto/generar-horario/ver/{gestion_id}', function ($gestionId) {
    if (!Session::has('user_code')) {
        return response()->json(['success' => false, 'message' => 'No autenticado'], 401);
    }

    $db = Config::$db;
    try {
        $db->create_conection();

        // Log para depuración
        error_log("📊 Ver horario - Gestión ID: " . $gestionId);

        $sql = "SELECT 
                    c.id,
                    h.dia,
                    TO_CHAR(h.hora_i, 'HH24:MI') as hora_i,
                    TO_CHAR(h.hora_f, 'HH24:MI') as hora_f,
                    c.nro_aula,
                    a.tipo as tipo_aula,
                    a.modulo,
                    mg.sigla_materia,
                    mg.sigla_grupo,
                    m.nombre as nombre_materia,
                    m.semestre,
                    p.nomb_comp as docente
                FROM ex_g32.clase c
                INNER JOIN ex_g32.horario h ON c.id_horario = h.id
                INNER JOIN ex_g32.aula a ON c.nro_aula = a.nro
                INNER JOIN ex_g32.materia_grupo mg ON c.id_materia_grupo = mg.id
                INNER JOIN ex_g32.materia m ON mg.sigla_materia = m.sigla
                INNER JOIN ex_g32.usuario u ON c.usuario_codigo = u.codigo
                INNER JOIN ex_g32.persona p ON u.ci = p.ci
                WHERE c.id_gestion = :gestion_id
                ORDER BY 
                    CASE h.dia
                        WHEN 'Lun' THEN 1
                        WHEN 'Mar' THEN 2
                        WHEN 'Mie' THEN 3
                        WHEN 'Jue' THEN 4
                        WHEN 'Vie' THEN 5
                        WHEN 'Sab' THEN 6
                        ELSE 7
                    END,
                    h.hora_i,
                    c.nro_aula";
        
        $params = [':gestion_id' => $gestionId];
        $stmt = $db->execute_query($sql, $params);
        $clases = $db->fetch_all($stmt);

        error_log("✅ Clases encontradas: " . count($clases));

        return response()->json([
            'success' => true,
            'clases' => $clases ?: [],
            'total' => count($clases)
        ]);

    } catch (Exception $e) {
        error_log("❌ Error al obtener horario: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener horario',
            'error' => $e->getMessage()
        ], 500);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

