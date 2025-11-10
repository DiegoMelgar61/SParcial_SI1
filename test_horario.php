<?php
/**
 * Script de prueba para verificar el endpoint de horarios
 * Ejecutar desde: php test_horario.php
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Config.php';

use App\Config;

echo "🔍 Probando consulta de horarios generados...\n\n";

// Inicializar Config
Config::load();

$db = Config::$db;
$db->create_conection();

// Probar con gestión 1
$gestionId = 1;

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

try {
    $params = [':gestion_id' => $gestionId];
    $stmt = $db->execute_query($sql, $params);
    $clases = $db->fetch_all($stmt);
    
    echo "✅ Gestión ID: $gestionId\n";
    echo "📊 Total clases encontradas: " . count($clases) . "\n\n";
    
    if (count($clases) > 0) {
        echo "Primeras 3 clases:\n";
        echo str_repeat("-", 80) . "\n";
        
        foreach (array_slice($clases, 0, 3) as $clase) {
            echo "Día: {$clase['dia']}\n";
            echo "Horario: {$clase['hora_i']} - {$clase['hora_f']}\n";
            echo "Aula: {$clase['nro_aula']} ({$clase['tipo_aula']})\n";
            echo "Materia: {$clase['nombre_materia']} ({$clase['sigla_materia']})\n";
            echo "Grupo: {$clase['sigla_grupo']}\n";
            echo "Docente: {$clase['docente']}\n";
            echo str_repeat("-", 80) . "\n";
        }
    } else {
        echo "⚠️ No hay clases generadas para la gestión $gestionId\n";
        
        // Verificar si hay clases en general
        $sqlCount = "SELECT COUNT(*) as total FROM ex_g32.clase";
        $stmtCount = $db->execute_query($sqlCount);
        $result = $db->fetch_one($stmtCount);
        echo "ℹ️ Total clases en la tabla: {$result['total']}\n";
        
        // Verificar gestiones disponibles
        $sqlGestiones = "SELECT DISTINCT id_gestion FROM ex_g32.clase ORDER BY id_gestion";
        $stmtGestiones = $db->execute_query($sqlGestiones);
        $gestiones = $db->fetch_all($stmtGestiones);
        echo "📋 Gestiones con clases: ";
        echo implode(", ", array_column($gestiones, 'id_gestion')) . "\n";
    }
    
    // Verificar tipos de aula
    echo "\n🔍 Verificando tipos de aula en la BD:\n";
    $sqlTipos = "SELECT DISTINCT tipo FROM ex_g32.aula ORDER BY tipo";
    $stmtTipos = $db->execute_query($sqlTipos);
    $tipos = $db->fetch_all($stmtTipos);
    foreach ($tipos as $tipo) {
        echo "  - '{$tipo['tipo']}'\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

$db->close_conection();
echo "\n✅ Prueba completada\n";
