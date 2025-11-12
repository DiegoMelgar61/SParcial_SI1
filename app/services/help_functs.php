<?php

use App\Classes\Postgres_DB;
use App\Config;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function importar_usuarios($file, $extension)
{
    // CREAR CONEXIÓN A LA DB
    $db = Config::$db;
    $db->create_conection();

    $importedUsers = [];

    try {
        //PROCESAR ARCHIVO SEGÚN EXTENSIÓN
        if ($extension === 'csv') {
            $data = [];
            $fileHandle = fopen($file->getPathname(), 'r');

            while (($row = fgetcsv($fileHandle, 1000, ',')) !== false) {
                $data[] = $row;
            }

            fclose($fileHandle);
        } 
        elseif ($extension === 'xlsx') {
            // Requiere: composer require phpoffice/phpspreadsheet
            $reader = IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($file->getPathname());
            $data = $spreadsheet->getActiveSheet()->toArray();
        } 
        else {
            throw new \Exception("Formato de archivo no soportado.");
        }

        //SALTAR LA PRIMERA FILA [ENCABEZADOS]
        unset($data[0]);

        //RECORRER FILAS
        foreach ($data as $row) {
            //EVITAR FILAS VACIAS
            if (empty($row[0]) || strtoupper($row[0]) === 'CI') {
                continue;
            }

            try {
                // VALIDAR SI YA EXISTE EL CI EN LA TABLA PERSONA
                $sql = "SELECT ci FROM ex_g32.persona WHERE ci = :ci";
                $stmt = $db->execute_query($sql, [':ci' => $row[0]]);
                $existingUser = $db->fetch_one($stmt);

                if ($existingUser) {
                    throw new \Exception("El CI {$row[0]} ya existe.");
                }

                // INSERTAR EN TABLA PERSONA
                $sql = "
                    INSERT INTO ex_g32.persona (ci, nomb_comp, fecha_n, correo, tel, profesion, tipo) 
                    VALUES (:ci, :nomb_comp, :fecha_n, :correo, :tel, :profesion, :tipo)
                ";
                $params = [
                    ':ci' => $row[0],
                    ':nomb_comp' => $row[1],
                    ':fecha_n' => $row[2],
                    ':correo' => $row[3],
                    ':tel' => $row[4],
                    ':profesion' => $row[5],
                    ':tipo' => strtolower($row[6])
                ];
                $db->execute_query($sql, $params);

                // DETERMINAR ROL SEGÚN EL TIPO
                $rol_id = 0;
                if (strtolower($row[6]) == 'docente') $rol_id = 1;
                elseif (strtolower($row[6]) == 'admin') $rol_id = 2;
                else throw new \Exception("El rol ingresado no existe.");

                // INSERTAR EN TABLA USUARIO (CON HASH DE CONTRASEÑA)
                $sql = "
                    INSERT INTO ex_g32.usuario (password_hash, ci, id_rol) 
                    VALUES (:password_hash, :ci, :id_rol)
                ";
                $params = [
                    ':password_hash' => password_hash($row[7], PASSWORD_DEFAULT),
                    ':ci' => $row[0],
                    ':id_rol' => $rol_id
                ];
                $db->execute_query($sql, $params);

                // RESULTADO ÉXITOSO
                $importedUsers[] = [
                    'success' => true,
                    'message' => "✅ Usuario {$row[1]} importado correctamente."
                ];

            } catch (\Exception $e) {
                $importedUsers[] = [
                    'success' => false,
                    'message' => "Error en {$row[1]}: " . $e->getMessage()
                ];
            }
        }

    } catch (\Exception $e) {
        $importedUsers[] = [
            'success' => false,
            'message' => "Error general: " . $e->getMessage()
        ];
    } finally {
        $db->close_conection();
    }

    return $importedUsers;
}


function generar_reporte_pdf($tipo)
{
    $db = Config::$db;
    $db->create_conection();

    try {
        if ($tipo === 'asistencia') {
            $sql = "SELECT a.fecha, p.nomb_comp AS docente, m.nombre AS materia, g.sigla AS grupo, a.estado, a.metodo_r
                    FROM ex_g32.asistencia a
                    INNER JOIN ex_g32.clase c ON c.id = a.id_clase
                    INNER JOIN ex_g32.usuario u ON u.codigo = c.usuario_codigo
                    INNER JOIN ex_g32.persona p ON p.ci = u.ci
                    INNER JOIN ex_g32.materia_grupo mg ON mg.id = c.id_materia_grupo
                    INNER JOIN ex_g32.materia m ON m.sigla = mg.sigla_materia
                    INNER JOIN ex_g32.grupo g ON g.sigla = mg.sigla_grupo
                    ORDER BY a.fecha DESC;";
            $columnas = ['Fecha', 'Docente', 'Materia', 'Grupo', 'Estado', 'Método'];
        } elseif ($tipo === 'licencia') {
            $sql = "SELECT p.nomb_comp AS docente, l.descripcion, l.fecha_i, l.fecha_f, l.fecha_hora
                    FROM ex_g32.licencia l
                    INNER JOIN ex_g32.usuario u ON u.codigo = l.codigo_usuario
                    INNER JOIN ex_g32.persona p ON p.ci = u.ci
                    ORDER BY l.fecha_hora DESC;";
            $columnas = ['Docente', 'Descripción', 'Inicio', 'Fin', 'Fecha Registro'];
        } else {
            throw new Exception("Tipo de reporte no válido.");
        }

        $stmt = $db->execute_query($sql);
        $data = $db->fetch_all($stmt);

        $titulo = strtoupper("REPORTE DE $tipo");
        $fecha = date('d/m/Y H:i');

        // ============================
        // ESTRUCTURA HTML CON ESTILO
        // ============================
        $html = "
        <html>
        <head>
        <style>
            body {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 12px;
                color: #333;
            }
            h1 {
                color: #1E3A8A;
                text-align: center;
                margin-bottom: 5px;
                font-size: 18px;
            }
            p.fecha {
                text-align: center;
                font-size: 11px;
                color: #555;
                margin-bottom: 20px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }
            th, td {
                border: 1px solid #888;
                padding: 6px 8px;
                text-align: left;
            }
            th {
                background-color: #1E3A8A;
                color: white;
                text-align: center;
                font-weight: bold;
            }
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                text-align: center;
                font-size: 10px;
                color: #666;
            }
        </style>
        </head>
        <body>
            <h1>$titulo</h1>
            <p class='fecha'>Generado el $fecha</p>
            <table>
                <thead>
                    <tr>";

        foreach ($columnas as $col) {
            $html .= "<th>$col</th>";
        }

        $html .= "
                    </tr>
                </thead>
                <tbody>";

        foreach ($data as $r) {
            $html .= "<tr>";
            foreach ($r as $valor) {
                $html .= "<td>" . htmlspecialchars($valor) . "</td>";
            }
            $html .= "</tr>";
        }

        $html .= "
                </tbody>
            </table>
            <footer>
                Sistema de Gestión Académica INF342 — UAGRM · Generado automáticamente
            </footer>
        </body>
        </html>";

        // ============================
        // GENERAR PDF
        // ============================
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Crear carpeta si no existe
        $dir = storage_path("public/files/reportes");
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $fileName = strtoupper($tipo) . "_REPORTE_" . date('Ymd_His') . ".pdf";
        $filePath = "$dir/$fileName";

        file_put_contents($filePath, $dompdf->output());

        return $filePath;

    } catch (Exception $e) {
        throw new Exception("Error al generar PDF: " . $e->getMessage());
    } finally {
        $db->close_conection();
    }
}


function generar_reporte_excel($tipo)
{
    $db = Config::$db;
    $db->create_conection();

    try {
        if ($tipo === 'asistencia') {
            $sql = "SELECT a.fecha, p.nomb_comp AS docente, m.nombre AS materia, g.sigla AS grupo, a.estado, a.metodo_r
                    FROM ex_g32.asistencia a
                    INNER JOIN ex_g32.clase c ON c.id = a.id_clase
                    INNER JOIN ex_g32.usuario u ON u.codigo = c.usuario_codigo
                    INNER JOIN ex_g32.persona p ON p.ci = u.ci
                    INNER JOIN ex_g32.materia_grupo mg ON mg.id = c.id_materia_grupo
                    INNER JOIN ex_g32.materia m ON m.sigla = mg.sigla_materia
                    INNER JOIN ex_g32.grupo g ON g.sigla = mg.sigla_grupo
                    ORDER BY a.fecha DESC;";
            $headers = ['Fecha', 'Docente', 'Materia', 'Grupo', 'Estado', 'Método'];
        } elseif ($tipo === 'licencia') {
            $sql = "SELECT p.nomb_comp AS docente, l.descripcion, l.fecha_i, l.fecha_f, l.fecha_hora
                    FROM ex_g32.licencia l
                    INNER JOIN ex_g32.usuario u ON u.codigo = l.codigo_usuario
                    INNER JOIN ex_g32.persona p ON p.ci = u.ci
                    ORDER BY l.fecha_hora DESC;";
            $headers = ['Docente', 'Descripción', 'Inicio', 'Fin', 'Fecha Registro'];
        } else {
            throw new Exception("Tipo de reporte no válido.");
        }

        $stmt = $db->execute_query($sql);
        $data = $db->fetch_all($stmt);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Estilos básicos
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '1E3A8A']], // Azul institucional
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
        ];

        $cellStyle = [
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
            'alignment' => ['vertical' => 'center'],
        ];

        // Título general
        $titulo = strtoupper("REPORTE DE $tipo");
        $sheet->mergeCells('A1:' . chr(64 + count($headers)) . '1');
        $sheet->setCellValue('A1', $titulo);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E3A8A']],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Fecha
        $sheet->mergeCells('A2:' . chr(64 + count($headers)) . '2');
        $sheet->setCellValue('A2', 'Generado el ' . date('d/m/Y H:i'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

        // Encabezados
        $sheet->fromArray($headers, null, 'A4');
        $sheet->getStyle('A4:' . chr(64 + count($headers)) . '4')->applyFromArray($headerStyle);
        $sheet->getRowDimension(4)->setRowHeight(20);

        // Datos
        $row = 5;
        foreach ($data as $r) {
            $sheet->fromArray(array_values($r), null, "A{$row}");
            $sheet->getStyle("A{$row}:" . chr(64 + count($headers)) . "{$row}")->applyFromArray($cellStyle);
            $row++;
        }

        // Ajustar ancho de columnas automáticamente
        foreach (range('A', chr(64 + count($headers))) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Fondo gris claro alternado
        for ($i = 5; $i < $row; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle("A{$i}:" . chr(64 + count($headers)) . "{$i}")
                      ->getFill()->setFillType('solid')->getStartColor()->setRGB('F9FAFB');
            }
        }

        $fileName = strtoupper($tipo) . "_REPORTE_" . date('Ymd_His') . ".xlsx";
        $dir = storage_path('app/public/reportes');
        if (!is_dir($dir)) mkdir($dir, 0775, true);
        $filePath = "$dir/$fileName";

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return $filePath;

    } catch (Exception $e) {
        throw new Exception("Error al generar Excel: " . $e->getMessage());
    } finally {
        $db->close_conection();
    }
}
