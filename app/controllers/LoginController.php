<?php
require_once __DIR__ . '/../models/LoginModel.php';

class LoginController {
    private $loginModel;

    public function __construct() {
        $this->loginModel = new LoginModel();
    }
    public function index() {
        require_once APP_PATH . 'views/login.php';
    }

public function autenticar() {
    session_start();
    header('Content-Type: application/json; charset=utf-8');

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $usuario = $this->loginModel->verificarCredenciales($email, $password);

        if ($usuario) {
            $_SESSION['usuario_id'] = $usuario['ID'];
            $_SESSION['usuario_nombre'] = $usuario['name'];
            $_SESSION['rol_nombre'] = strtoupper($usuario['rol_nombre']);
            $_SESSION['empresa_id'] = $usuario['empresa_ID'];
            $_SESSION['empresa_nombre'] = $usuario['empresa_nombre'];
            // Cargar logo de la empresa desde la base de datos
require_once APP_PATH . 'models/Empresa.php';
$empresaModel = new Empresa();
$empresa = $empresaModel->obtenerDatosEmpresa($usuario['empresa_ID']);
$_SESSION['empresa_logo'] = $empresa['logo'] ?? 'img/perfil_empresa.png';


            // ✅ Devuelve JSON limpio
            echo json_encode([
                'success' => true,
    'redirect' => ($_SESSION['rol_nombre'] === 'ADMIN')
        ? BASE_URL . 'dashboard/admin'
        : BASE_URL . 'dashboard/empresa'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ]);
        }
    } catch (Exception $e) {
        // ⚠️ Captura errores reales del servidor
        echo json_encode([
            'success' => false,
            'message' => 'Error en el servidor: ' . $e->getMessage()
        ]);
    }

    exit;
}


    public function salir() {
        session_start();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");
        header('Location: ' . BASE_URL . 'login');
        exit;
    }
}
