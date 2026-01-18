<?php
require_once dirname(__DIR__) . '/../config/config.php';

class Conexion {
    private static $instance = null;
    private $conn;

    private function __construct() {
        

        try {
            $this->conn = new PDO(
                "mysql:host=" . DB_HOST .";port=3306;dbname=" . DB_NAME,
                DB_USER,
                DB_PASS
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("SET NAMES utf8mb4");
        } catch (PDOException $e) {
            die('Error de conexión a la base de datos: ' . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Conexion();
        }
        return self::$instance->conn;
    }
}
