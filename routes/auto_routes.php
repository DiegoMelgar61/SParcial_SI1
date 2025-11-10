<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Config;

// ==================== CONSULTA DE HORARIOS DE AULAS ====================

// Endpoint para obtener el horario de un aula específica
Route::get('/auto/aulas/horario', function (Request $request) {
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');

    //VALIDACION:USUARIO EN SESION
    if (!Session::has('user_code')) {
        return response()->json([
            'success' => false,
            'message' => 'Usuario no Autenticado.'
        ], 401);
    }

    $aulaNro = $request->query('aula_nro');
    if (!$aulaNro) {
        return response()->json([
            'success' => false,
            'message' => 'El número del aula es obligatorio.'
        ], 400);
    }

    $db = Config::$db;
    try {
        $db->create_conection();

        // Verificar que el aula existe
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

        // Obtener los horarios asignados al aula
        // Estructura real según la BD:
        // CLASE tiene: id_horario (FK), nro_aula (FK), id_materia_grupo (FK)
        // HORARIO tiene: id (PK), dia, hora_i, hora_f (TIMESTAMP)
        // La relación es: CLASE -> HORARIO (no HORARIO -> CLASE)
        
        $sql = "SELECT 
                    h.id as horario_id,
                    h.dia,
                    TO_CHAR(h.hora_i, 'HH24:MI') as hora_i,
                    TO_CHAR(h.hora_f, 'HH24:MI') as hora_f,
                    c.id as clase_id,
                    c.fecha_creacion,
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
        
        $params = [':nro_aula' => $aulaNro];
        $stmt = $db->execute_query($sql, $params);
        $horarios = $db->fetch_all($stmt);

        // Registrar en bitácora
        $accion = 'CONSULTAR HORARIO AULA';
        $fecha = date('Y-m-d H:i:s');
        $estado = 'SUCCESS';
        $comentario = 'Consulta de horario del aula Nro: ' . $aulaNro;
        $codigo = Session::get('user_code');
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);

        return response()->json([
            'success' => true,
            'aula' => $aula,
            'horarios' => $horarios
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener el horario del aula.',
            'error' => $e->getMessage()
        ], 500);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

// Endpoint para obtener disponibilidad de aulas en un horario específico
Route::get('/auto/aulas/disponibilidad', function (Request $request) {
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');

    //VALIDACION:USUARIO EN SESION
    if (!Session::has('user_code')) {
        return response()->json([
            'success' => false,
            'message' => 'Usuario no Autenticado.'
        ], 401);
    }

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

/*
================================================================================
GENERACIÓN AUTOMÁTICA DE HORARIOS - DESHABILITADA TEMPORALMENTE
================================================================================
Motivo: Funcionalidad en desarrollo/pruebas
TODO: Revisar, probar y habilitar cuando esté completamente funcional
================================================================================
*/

// Generar horario automático (DESHABILITADO)
/*
Route::post('/auto/generar-horario/generar', function (Request $request) {
    if (!Session::has('user_code')) {
        return response()->json(['success' => false, 'message' => 'No autenticado'], 401);
    }

    $gestionId = $request->input('gestion_id');
    $materiaSigla = $request->input('materia_sigla');
    $grupoSigla = $request->input('grupo_sigla');
    $docenteCodigo = $request->input('docente_codigo');
    $horasSemanales = $request->input('horas_semanales');
    $cargaHoraria = $request->input('carga_horaria');

    if (!$gestionId || !$materiaSigla || !$grupoSigla || !$docenteCodigo || !$horasSemanales) {
        return response()->json(['success' => false, 'message' => 'Datos incompletos'], 400);
    }

    $db = Config::$db;
    try {
        $db->create_conection();
        $pdo = $db->get_connection();
        $pdo->beginTransaction();

        // 1. Verificar o crear materia_grupo
        $sqlCheckMG = "SELECT id FROM ex_g32.materia_grupo 
                      WHERE sigla_materia = :sigla_materia AND sigla_grupo = :sigla_grupo";
        $stmtCheckMG = $db->execute_query($sqlCheckMG, [
            ':sigla_materia' => $materiaSigla,
            ':sigla_grupo' => $grupoSigla
        ]);
        $materiaGrupo = $db->fetch_one($stmtCheckMG);

        if (!$materiaGrupo) {
            // Crear materia_grupo
            $sqlInsertMG = "INSERT INTO ex_g32.materia_grupo (sigla_materia, sigla_grupo) 
                           VALUES (:sigla_materia, :sigla_grupo) RETURNING id";
            $stmtInsertMG = $db->execute_query($sqlInsertMG, [
                ':sigla_materia' => $materiaSigla,
                ':sigla_grupo' => $grupoSigla
            ]);
            $materiaGrupo = $db->fetch_one($stmtInsertMG);
        }
        
        $materiaGrupoId = $materiaGrupo['id'];

        // 1.5 Verificar si ya existen clases para esta materia-grupo en esta gestión
        $sqlCheckClases = "SELECT COUNT(*) as total FROM ex_g32.clase 
                          WHERE id_materia_grupo = :materia_grupo_id 
                          AND id_gestion = :gestion_id";
        $stmtCheckClases = $db->execute_query($sqlCheckClases, [
            ':materia_grupo_id' => $materiaGrupoId,
            ':gestion_id' => $gestionId
        ]);
        $checkClases = $db->fetch_one($stmtCheckClases);

        if ($checkClases['total'] > 0) {
            $pdo->rollBack();
            return response()->json([
                'success' => false,
                'message' => "Ya existen {$checkClases['total']} clases generadas para {$materiaSigla}-{$grupoSigla} en esta gestión. Elimínelas primero si desea regenerar."
            ], 400);
        }

        // 2. Obtener horarios disponibles que sumen las horas semanales necesarias
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
        $todosHorarios = $db->fetch_all($stmtHorarios);

        // 3. Buscar combinación de horarios que sumen las horas necesarias
        $combinacionHorarios = encontrarCombinacionHorarios($todosHorarios, $horasSemanales);

        if (empty($combinacionHorarios)) {
            $pdo->rollBack();
            return response()->json([
                'success' => false,
                'message' => 'No se encontró combinación de horarios que sumen ' . $horasSemanales . ' horas semanales'
            ], 400);
        }

        // 4. Obtener aulas disponibles (priorizar laboratorios si carga_horaria > 135)
        $tipoAulaPreferido = $cargaHoraria > 135 ? 'laboratorio' : 'teorica';
        $sqlAulas = "SELECT nro, tipo FROM ex_g32.aula 
                    ORDER BY CASE WHEN tipo = :tipo_preferido THEN 0 ELSE 1 END, nro";
        $stmtAulas = $db->execute_query($sqlAulas, [':tipo_preferido' => $tipoAulaPreferido]);
        $aulas = $db->fetch_all($stmtAulas);

        // 5. Asignar clases - MEJORADO: No repetir aulas, distribuir días
        $clasesGeneradas = 0;
        $errores = [];
        $aulasUsadas = []; // Para evitar repetir aulas

        foreach ($combinacionHorarios as $horario) {
            $aulaAsignada = null;
            
            // Buscar aula disponible para este horario (que no se haya usado antes)
            foreach ($aulas as $aula) {
                // Saltar si esta aula ya fue usada para esta materia-grupo
                if (in_array($aula['nro'], $aulasUsadas)) {
                    continue;
                }

                // Verificar que el aula no esté ocupada en este horario y gestión
                $sqlCheck = "SELECT COUNT(*) as total
                            FROM ex_g32.clase
                            WHERE nro_aula = :nro_aula
                            AND id_horario = :horario_id
                            AND id_gestion = :gestion_id";
                $stmtCheck = $db->execute_query($sqlCheck, [
                    ':nro_aula' => $aula['nro'],
                    ':horario_id' => $horario['id'],
                    ':gestion_id' => $gestionId
                ]);
                $check = $db->fetch_one($stmtCheck);

                if ($check['total'] == 0) {
                    $aulaAsignada = $aula['nro'];
                    $aulasUsadas[] = $aula['nro']; // Marcar como usada
                    break;
                }
            }

            if ($aulaAsignada) {
                // Insertar clase
                $sqlInsert = "INSERT INTO ex_g32.clase 
                             (usuario_codigo, id_horario, nro_aula, id_materia_grupo, id_gestion, fecha_creacion)
                             VALUES (:usuario_codigo, :id_horario, :nro_aula, :id_materia_grupo, :id_gestion, NOW())";
                $db->execute_query($sqlInsert, [
                    ':usuario_codigo' => $docenteCodigo,
                    ':id_horario' => $horario['id'],
                    ':nro_aula' => $aulaAsignada,
                    ':id_materia_grupo' => $materiaGrupoId,
                    ':id_gestion' => $gestionId
                ]);
                $clasesGeneradas++;
            } else {
                $errores[] = "No hay aulas disponibles para horario: {$horario['dia']} {$horario['hora_i']}-{$horario['hora_f']}";
            }
        }

        $pdo->commit();

        // Registrar en bitácora
        $accion = 'GENERAR HORARIO AUTOMÁTICO';
        $fecha = date('Y-m-d H:i:s');
        $estado = 'SUCCESS';
        $comentario = "Generación automática: {$clasesGeneradas} clases para {$materiaSigla}-{$grupoSigla}";
        $codigo = Session::get('user_code');
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);

        return response()->json([
            'success' => true,
            'message' => 'Horario generado exitosamente',
            'resultado' => [
                'clases_generadas' => $clasesGeneradas,
                'horas_semanales' => $horasSemanales,
                'errores' => $errores,
                'advertencias' => []
            ]
        ]);

    } catch (Exception $e) {
        if (isset($pdo)) {
            $pdo->rollBack();
        }
        return response()->json([
            'success' => false,
            'message' => 'Error al generar horario',
            'error' => $e->getMessage()
        ], 500);
    } finally {
        if (isset($db) && $db !== null) {
            $db->close_conection();
        }
    }
});

// Función auxiliar para encontrar combinación de horarios
// MEJORADO: Distribuye días, evita repeticiones
function encontrarCombinacionHorarios($horarios, $horasObjetivo) {
    $tolerancia = 0.25; // 15 minutos de tolerancia
    
    // Agrupar horarios por duración para búsqueda más eficiente
    $horariosPorDuracion = [];
    foreach ($horarios as $h) {
        $duracion = round($h['duracion_horas'] * 4) / 4; // Redondear a 0.25
        if (!isset($horariosPorDuracion[$duracion])) {
            $horariosPorDuracion[$duracion] = [];
        }
        $horariosPorDuracion[$duracion][] = $h;
    }
    
    // Caso 1: Un solo horario que coincida
    foreach ($horarios as $h) {
        if (abs($h['duracion_horas'] - $horasObjetivo) <= $tolerancia) {
            return [$h];
        }
    }
    
    // Caso 2: Dos horarios que sumen (PRIORIZANDO DIFERENTES DÍAS)
    for ($i = 0; $i < count($horarios); $i++) {
        for ($j = $i + 1; $j < count($horarios); $j++) {
            $suma = $horarios[$i]['duracion_horas'] + $horarios[$j]['duracion_horas'];
            
            if (abs($suma - $horasObjetivo) <= $tolerancia) {
                // IMPORTANTE: Verificar que NO sean el mismo día
                if ($horarios[$i]['dia'] !== $horarios[$j]['dia']) {
                    // Preferir días no consecutivos para mejor distribución
                    return [$horarios[$i], $horarios[$j]];
                }
            }
        }
    }
    
    // Caso 3: Tres horarios que sumen (DIFERENTES DÍAS)
    for ($i = 0; $i < count($horarios); $i++) {
        for ($j = $i + 1; $j < count($horarios); $j++) {
            for ($k = $j + 1; $k < count($horarios); $k++) {
                $suma = $horarios[$i]['duracion_horas'] + 
                       $horarios[$j]['duracion_horas'] + 
                       $horarios[$k]['duracion_horas'];
                
                if (abs($suma - $horasObjetivo) <= $tolerancia) {
                    // Verificar que TODOS sean días diferentes
                    $dias = [$horarios[$i]['dia'], $horarios[$j]['dia'], $horarios[$k]['dia']];
                    if (count($dias) === count(array_unique($dias))) {
                        return [$horarios[$i], $horarios[$j], $horarios[$k]];
                    }
                }
            }
        }
    }
    
    // Caso 4: Cuatro horarios (para casos especiales)
    for ($i = 0; $i < count($horarios); $i++) {
        for ($j = $i + 1; $j < count($horarios); $j++) {
            for ($k = $j + 1; $k < count($horarios); $k++) {
                for ($l = $k + 1; $l < count($horarios); $l++) {
                    $suma = $horarios[$i]['duracion_horas'] + 
                           $horarios[$j]['duracion_horas'] + 
                           $horarios[$k]['duracion_horas'] +
                           $horarios[$l]['duracion_horas'];
                    
                    if (abs($suma - $horasObjetivo) <= $tolerancia) {
                        $dias = [
                            $horarios[$i]['dia'], 
                            $horarios[$j]['dia'], 
                            $horarios[$k]['dia'],
                            $horarios[$l]['dia']
                        ];
                        if (count($dias) === count(array_unique($dias))) {
                            return [$horarios[$i], $horarios[$j], $horarios[$k], $horarios[$l]];
                        }
                    }
                }
            }
        }
    }
    
    return [];
}
*/
// FIN GENERACIÓN AUTOMÁTICA DESHABILITADA

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

// Asignar clase manualmente con validación de horas semanales
Route::post('/auto/generar-horario/asignar-clase', function (Request $request) {
    if (!Session::has('user_code')) {
        return response()->json(['success' => false, 'message' => 'No autenticado'], 401);
    }

    $docenteCodigo = $request->input('docente_codigo');
    $horarioIds = $request->input('horario_ids'); // Array de hasta 4 IDs (algunos pueden ser null)
    $siglaMateriaInput = $request->input('sigla_materia');
    $siglaGrupoInput = $request->input('sigla_grupo');
    $gestionId = $request->input('gestion_id');

    if (!$docenteCodigo || !$horarioIds || !$siglaMateriaInput || !$siglaGrupoInput || !$gestionId) {
        return response()->json(['success' => false, 'message' => 'Datos incompletos'], 400);
    }

    // Filtrar horarios no nulos
    $horariosSeleccionados = array_filter($horarioIds, function($id) {
        return $id !== null && $id !== '' && $id !== 'null';
    });

    if (empty($horariosSeleccionados)) {
        return response()->json(['success' => false, 'message' => 'Debe seleccionar al menos un horario'], 400);
    }

    $db = Config::$db;
    try {
        $db->create_conection();
        $pdo = $db->get_connection();
        $pdo->beginTransaction();

        // Obtener información de la materia
        $sqlMateria = "SELECT sigla, nombre, carga_horaria FROM ex_g32.materia WHERE sigla = :sigla";
        $stmtMateria = $db->execute_query($sqlMateria, [':sigla' => $siglaMateriaInput]);
        $materia = $db->fetch_one($stmtMateria);

        if (!$materia) {
            $pdo->rollBack();
            return response()->json(['success' => false, 'message' => 'Materia no encontrada'], 404);
        }

        // Obtener o crear materia_grupo
        $sqlMateriaGrupo = "SELECT id FROM ex_g32.materia_grupo 
                           WHERE sigla_materia = :sigla_materia AND sigla_grupo = :sigla_grupo";
        $paramsMG = [':sigla_materia' => $siglaMateriaInput, ':sigla_grupo' => $siglaGrupoInput];
        $stmtMG = $db->execute_query($sqlMateriaGrupo, $paramsMG);
        $materiaGrupo = $db->fetch_one($stmtMG);

        if (!$materiaGrupo) {
            // Crear materia_grupo si no existe
            $sqlInsertMG = "INSERT INTO ex_g32.materia_grupo (sigla_materia, sigla_grupo) 
                           VALUES (:sigla_materia, :sigla_grupo) RETURNING id";
            $stmtInsertMG = $db->execute_query($sqlInsertMG, $paramsMG);
            $materiaGrupo = $db->fetch_one($stmtInsertMG);
        }

        $materiaGrupoId = $materiaGrupo['id'];

        // Verificar si ya existen clases para esta materia-grupo en esta gestión
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

        // Calcular horas totales de los horarios seleccionados
        $sqlHorarios = "SELECT id, dia, 
                       TO_CHAR(hora_i, 'HH24:MI') as hora_i,
                       TO_CHAR(hora_f, 'HH24:MI') as hora_f,
                       EXTRACT(EPOCH FROM (hora_f - hora_i)) / 3600 as duracion_horas
                       FROM ex_g32.horario
                       WHERE id IN (" . implode(',', array_fill(0, count($horariosSeleccionados), '?')) . ")";
        
        $stmtHorarios = $pdo->prepare($sqlHorarios);
        $stmtHorarios->execute(array_values($horariosSeleccionados));
        $horarios = $stmtHorarios->fetchAll(PDO::FETCH_ASSOC);

        error_log("📅 Horarios seleccionados: " . json_encode($horarios, JSON_PRETTY_PRINT));

        $horasSemanales = 0;
        $diasUsados = [];
        foreach ($horarios as $h) {
            $horasSemanales += floatval($h['duracion_horas']);
            $diasUsados[] = $h['dia'];
        }

        // Verificar que todos los horarios sean en días diferentes
        if (count($diasUsados) !== count(array_unique($diasUsados))) {
            $pdo->rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Todos los horarios deben ser en días diferentes'
            ], 400);
        }

        // VALIDACIÓN DE HORAS SEMANALES SEGÚN REGLAS DE NEGOCIO
        $cargaHoraria = intval($materia['carga_horaria']);
        $nombreMateria = strtoupper($materia['nombre']);
        $esModalidadGraduacion = strpos($nombreMateria, 'MODALIDAD DE GRADUACION') !== false || 
                                 strpos($nombreMateria, 'MODALIDAD GRADUACION') !== false;

        $horasRequeridas = 0;
        $tolerancia = 0.25; // 15 minutos
        $mensajeError = '';

        if ($esModalidadGraduacion) {
            // Modalidad de graduación: entre 5 y 7 horas
            if ($horasSemanales < 5 || $horasSemanales > 7) {
                $mensajeError = "Materias de 'MODALIDAD DE GRADUACION' deben tener entre 5 y 7 horas semanales. Actual: {$horasSemanales} hrs";
            }
        } else {
            // Validaciones normales según carga horaria
            if ($cargaHoraria == 135) {
                $horasRequeridas = 4.5;
                $mensajeError = "Materias con 135 hrs de carga deben tener exactamente 4.5 horas semanales. Actual: {$horasSemanales} hrs";
            } elseif ($cargaHoraria > 135) {
                $horasRequeridas = 6;
                $mensajeError = "Materias con más de 135 hrs de carga deben tener exactamente 6 horas semanales. Actual: {$horasSemanales} hrs";
            } elseif ($cargaHoraria == 90) {
                $horasRequeridas = 3;
                $mensajeError = "Materias con 90 hrs de carga deben tener exactamente 3 horas semanales. Actual: {$horasSemanales} hrs";
            }

            // Verificar si las horas están dentro de la tolerancia
            if ($horasRequeridas > 0 && abs($horasSemanales - $horasRequeridas) > $tolerancia) {
                $pdo->rollBack();
                return response()->json([
                    'success' => false,
                    'message' => $mensajeError
                ], 400);
            }
        }

        // Si pasó las validaciones, proceder a asignar las clases
        $aulasDisponibles = [];
        $sqlAulas = "SELECT nro, tipo FROM ex_g32.aula ORDER BY nro";
        $stmtAulas = $db->execute_query($sqlAulas);
        $todasAulas = $db->fetch_all($stmtAulas);

        $aulasUsadas = [];
        $clasesCreadas = 0;

        foreach ($horarios as $horario) {
            $asignado = false;
            
            error_log("🔍 Buscando aula para horario: {$horario['dia']} ID={$horario['id']}");
            
            foreach ($todasAulas as $aula) {
                // Saltar aulas ya usadas para esta materia-grupo
                if (in_array($aula['nro'], $aulasUsadas)) {
                    error_log("  ⏭ Aula {$aula['nro']} ya usada, saltando...");
                    continue;
                }

                // Verificar disponibilidad del aula en este horario
                $sqlCheck = "SELECT COUNT(*) as total FROM ex_g32.clase
                            WHERE nro_aula = :nro_aula 
                            AND id_horario = :id_horario 
                            AND id_gestion = :id_gestion";
                
                $stmtCheck = $db->execute_query($sqlCheck, [
                    ':nro_aula' => $aula['nro'],
                    ':id_horario' => $horario['id'],
                    ':id_gestion' => $gestionId
                ]);
                $check = $db->fetch_one($stmtCheck);

                error_log("  🏫 Aula {$aula['nro']}: {$check['total']} clases existentes");

                if ($check['total'] == 0) {
                    // Aula disponible, asignar clase
                    error_log("  ✅ Asignando aula {$aula['nro']} para horario {$horario['dia']}");
                    
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

                    $aulasUsadas[] = $aula['nro'];
                    $clasesCreadas++;
                    $asignado = true;
                    break;
                } else {
                    error_log("  ❌ Aula {$aula['nro']} ocupada");
                }
            }

            if (!$asignado) {
                error_log("❌ No se encontró aula disponible para horario {$horario['dia']} ID={$horario['id']}");
                error_log("📊 Total de aulas verificadas: " . count($todasAulas));
                error_log("📊 Aulas ya usadas: " . implode(', ', $aulasUsadas));
                
                $pdo->rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "No hay aulas disponibles para el horario {$horario['dia']}"
                ], 400);
            }
        }

        $pdo->commit();

        // Registrar en bitácora
        $accion = 'ASIGNAR CLASE MANUAL';
        $fecha = date('Y-m-d H:i:s');
        $estado = 'SUCCESS';
        $comentario = "Clases asignadas manualmente: {$clasesCreadas} para {$siglaMateriaInput}-{$siglaGrupoInput}, {$horasSemanales} hrs/semana";
        $codigo = Session::get('user_code');
        $db->save_log_bitacora($accion, $fecha, $estado, $comentario, $codigo);

        return response()->json([
            'success' => true,
            'message' => "Se asignaron {$clasesCreadas} clases exitosamente ({$horasSemanales} hrs/semana)",
            'clases_creadas' => $clasesCreadas,
            'horas_semanales' => $horasSemanales
        ]);

    } catch (Exception $e) {
        if (isset($pdo)) {
            $pdo->rollBack();
        }
        error_log("❌ Error asignación manual: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error al asignar clase',
            'error' => $e->getMessage()
        ], 500);
    } finally {
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
