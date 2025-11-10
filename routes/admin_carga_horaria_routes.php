<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Config;

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
