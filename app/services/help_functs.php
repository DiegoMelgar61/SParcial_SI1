<?php

use App\Classes\Postgres_DB;
use App\Config;
use PhpOffice\PhpSpreadsheet\IOFactory; // Solo si querés soportar XLSX (opcional)

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
