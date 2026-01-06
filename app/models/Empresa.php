<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/Conexion.php';

class Empresa {
    private $conn;

    public function __construct() {
        // Obtener la conexión existente
        $this->conn = Conexion::getInstance();
    }

    // Obtener los datos de la empresa por ID
    public function obtenerDatosEmpresa($empresa_id) {
        $query = "SELECT id, nombre, ruc, direccion, telefono, email_contacto ,logo
                  FROM empresas 
                  WHERE id = :empresa_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
