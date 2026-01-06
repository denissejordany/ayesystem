<?php
require_once dirname(__DIR__) . '/models/Conexion.php';

class LoginModel {
    private $db;

    public function __construct() {
        $this->db = Conexion::getInstance();
    }

    public function verificarCredenciales($email, $password) {
        try {
            $sql = "SELECT 
                        u.ID,
                        u.name,
                        u.email,
                        u.password,
                        r.nombre AS rol_nombre,
                        e.ID AS empresa_ID,
                        e.nombre AS empresa_nombre
                    FROM usuarios u
                    INNER JOIN roles r ON u.ROLES_ID = r.ID
                    LEFT JOIN empresas e ON u.empresa_ID = e.ID
                    WHERE u.email = ?
                    AND u.estado_ID = 1
                    LIMIT 1";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && $usuario['password'] === $password) {
                return $usuario;
            }

            return false;
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error SQL: ' . $e->getMessage()
            ]);
            exit;
        }
    }
}
