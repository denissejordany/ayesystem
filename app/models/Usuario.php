<?php
// app/models/Usuario.php
require_once __DIR__ . '/Conexion.php';

class Usuario {
    private $conn;

    public function __construct() {
        $this->conn = Conexion::getInstance();
    }

    public function obtenerPorEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function verificarUsuario($email, $password) {
        $user = $this->obtenerPorEmail($email);
        if (!$user) {
            return false;
        }

        if (!isset($user['password'])) {
            return false; // asegúrate de tener columna password
        }

        $stored = $user['password'];

        // Detección simple de hash: los hashes de password_hash suelen comenzar con $2y$ o $argon2
        $looksLikeHash = (is_string($stored) && (strpos($stored, '$2y$') === 0 || strpos($stored, '$2a$') === 0 || strpos($stored, '$argon2') === 0 || strlen($stored) >= 60));

        if ($looksLikeHash) {
            // Si está hasheada, usamos password_verify
            if (password_verify($password, $stored)) {
                return $user;
            } else {
                return false;
            }
        } else {
            // Si no parece hash, compara en texto plano (compatibilidad)
            if ($password === $stored) {
                return $user;
            } else {
                return false;
            }
        }
    }
}
