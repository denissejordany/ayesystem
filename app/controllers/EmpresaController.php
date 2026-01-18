<?php
require_once APP_PATH . 'models/Empresa.php';

class EmpresaController {
    private $empresaModel;

    public function __construct() {
        $this->empresaModel = new Empresa();
    }

    // Mostrar perfil de la empresa
    public function perfil() {
        session_start();
        if (!isset($_SESSION['empresa_id'])) {
            header("Location: " . BASE_URL . "login");
            exit;
        }

        $empresa_id = $_SESSION['empresa_id'];
        $empresa = $this->empresaModel->obtenerDatosEmpresa($empresa_id);

        if (!$empresa) {
            echo "No se encontraron los datos de la empresa.";
            exit;
        }
 $_SESSION['empresa_nombre'] = $empresa['nombre'];
    $_SESSION['empresa_logo'] = $empresa['logo'] ?? 'img/perfil_empresa.png'; // ruta por defecto si no hay logo

        require APP_PATH . 'views/partials/empresa/perfil_empresa.php';
    }
    
  public function documentos() {
        session_start();
        if (!isset($_SESSION['empresa_id'])) {
            header("Location: " . BASE_URL . "login");
            exit;
        }

        $empresa_id = $_SESSION['empresa_id'];
        $documentoModel = new Documento();
        $documentos = $documentoModel->getByEmpresa($empresa_id);

        require APP_PATH . 'views/empresa/documentos_empresa.php';
    }

}
?>
