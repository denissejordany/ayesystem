<?php
require_once __DIR__ . '/../models/Documento.php';


class DashboardController {

    public function admin() {
        session_start();
       

        if (!isset($_SESSION['usuario_nombre']) || $_SESSION['rol_nombre'] !== 'ADMIN') {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $usuario_nombre = $_SESSION['usuario_nombre'];
        $rol_nombre = $_SESSION['rol_nombre'];

        require_once __DIR__ . '/../views/partials/admin/dashboard_admin.php';
    }

    public function empresa() {
        session_start();

        if (!isset($_SESSION['usuario_nombre']) || $_SESSION['rol_nombre'] !== 'EMPRESA') {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $usuario_nombre = $_SESSION['usuario_nombre'];
        $empresa_nombre = $_SESSION['empresa_nombre'];

        require_once __DIR__ . '/../views/partials/empresa/dashboard_empresa.php';
    }
    


public function documentos()
{
    // simple forward al nuevo controller de Documentos para mantener compatibilidad
    require_once __DIR__ . '/DocumentosController.php';
    $docController = new DocumentosController();
    // Llamamos al método documentos() del otro controller
    return $docController->documentos();
}


}
