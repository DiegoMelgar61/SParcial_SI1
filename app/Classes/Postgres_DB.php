<?php

namespace App\Classes;

use PDO;
use PDOException;

class Postgres_DB
{
    private $host;
    private $port;
    private $db;
    private $user;
    private $password;
    private $conn;

    public function __construct($host, $port, $db_name, $user, $password)
    {
        $this->host = $host;
        $this->port = $port;
        $this->db = $db_name;
        $this->user = $user;
        $this->password = $password;
        $this->conn = null;
    }

    // CREAR CONEXIÓN
    public function create_conection()
    {
        try {
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db}";
            $this->conn = new PDO($dsn, $this->user, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return true;
        } catch (PDOException $e) {
            // Lanza la excepción para que el controlador la maneje
            throw new PDOException("Error al conectarse a la DB: " . $e->getMessage());
        }
    }

    // CERRAR CONEXIÓN
    public function close_conection($commit = false)
    {
        try {
            if ($this->conn) {
                if ($commit) {
                    $this->conn->commit();
                }
                $this->conn = null;
            }
        } catch (PDOException $e) {
            throw new PDOException("Error al cerrar la conexión: " . $e->getMessage());
        }
    }

    // EJECUTAR CONSULTA
    public function execute_query($sql, $params = [])
    {
        if (!$this->conn) {
            throw new PDOException("No hay conexión activa con la base de datos.");
        }

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new PDOException("Error en la consulta SQL: " . $e->getMessage());
        }
    }

    // OBTENER UNA FILA
    public function fetch_one($stmt)
    {
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
    }

    // OBTENER TODAS LAS FILAS
    public function fetch_all($stmt)
    {
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    // RETORNAR LA CONEXIÓN
    public function get_connection()
    {
        return $this->conn;
    }

    // GUARDAR REGISTRO EN BITÁCORA
    public function save_log_bitacora($action, $date, $status, $coment, $user_code)
    {
        
        if (!$this->conn) {
            throw new PDOException("No hay conexión activa con la base de datos.");
        }

        try {
            $sql = "INSERT INTO ex_g32.bitacora (accion, fecha_hora, estado,comentario,codigo_usuario)
                    VALUES (:accion, :fecha_hora, :estado, :comentario, :codigo_usuario)";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindParam(':accion', $action);
            $stmt->bindParam(':fecha_hora', $date);
            $stmt->bindParam(':estado', $status);
            $stmt->bindParam(':comentario',$coment);
            $stmt->bindParam(':codigo_usuario',$user_code);

            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new PDOException("Error al guardar en bitácora: " . $e->getMessage());
        }
    }
}
