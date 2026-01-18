<?php
require_once __DIR__ . '/../models/Documento.php';

class DocumentosController
{
    public function documentos()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['usuario_nombre']) || $_SESSION['rol_nombre'] !== 'EMPRESA') {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $empresa_id = $_SESSION['empresa_id'] ?? null;
        if (!$empresa_id) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $documentoModel = new Documento();
         $porPagina = 6; 
    $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
    $offset = ($pagina - 1) * $porPagina;

    $total = $documentoModel->contarDocumentos($empresa_id);
    $documentos = $documentoModel->getByEmpresaPaginado($empresa_id, $porPagina, $offset);

    $totalPaginas = ceil($total / $porPagina);

        require APP_PATH . 'views/partials/empresa/documentos_empresa.php';
    }

    public function obtenerTiposDocumento()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['empresa_id'])) {
            http_response_code(403);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $documentoModel = new Documento();
        $tipos = $documentoModel->getTiposDocumento();

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($tipos, JSON_UNESCAPED_UNICODE);
        exit;
    }

public function guardarDocumento()
{
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (empty($_SESSION['empresa_id'])) {
        header('Location: ' . BASE_URL . 'login');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . BASE_URL . 'dashboard/documentos?alerta=error');
        exit;
    }

    $documentoModel = new Documento();

    $id_empresa = $_SESSION['empresa_id'];
    $id_tipo_doc = $_POST['id_tipo_doc'] ?? null;
    $fecha_emision = $_POST['fecha_emision'] ?? null;
    $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? null;
    $observaciones = $_POST['observaciones'] ?? '';

    if (!isset($_FILES['archivo_pdf']) || $_FILES['archivo_pdf']['error'] !== UPLOAD_ERR_OK) {
        header('Location: ' . BASE_URL . 'dashboard/documentos?alerta=error');
        exit;
    }

    $archivo = $_FILES['archivo_pdf'];
    $nombre_original = $archivo['name'];

    $nombreArchivo = time() . "_" . basename($archivo['name']);
    $rutaDestino = __DIR__ . '/../../public/uploads/documentos/' . $nombreArchivo;

    if (!file_exists(dirname($rutaDestino))) {
        mkdir(dirname($rutaDestino), 0777, true);
    }

    if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
        header('Location: ' . BASE_URL . 'dashboard/documentos?alerta=error');
        exit;
    }

    if (!$fecha_emision) {
        $fecha_emision = date("Y-m-d");
    }

    $rutaRelativa = 'uploads/documentos/' . $nombreArchivo;

    $documentoModel->guardarDocumento(
        $id_empresa,
        $id_tipo_doc,
        $nombre_original,
        $rutaRelativa,
        $fecha_emision,
        $fecha_vencimiento,
        $observaciones
    );

    header('Location: ' . BASE_URL . 'dashboard/documentos?alerta=exito');
    exit;
}


public function obtenerDocumentoPorId()
{
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['empresa_id'])) {
        http_response_code(403);
        echo json_encode(['error' => 'No autorizado']);
        exit;
    }

    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Falta el ID']);
        exit;
    }

    $documentoModel = new Documento();
    $doc = $documentoModel->getById($_GET['id']);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($doc, JSON_UNESCAPED_UNICODE);
    exit;
}
public function actualizarDocumento()
{
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (empty($_SESSION['empresa_id'])) {
        header('Location: ' . BASE_URL . 'login');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $documentoModel = new Documento();

        $id_documento = $_POST['id_documento'] ?? null;
        $id_tipo_doc = $_POST['id_tipo_doc'] ?? null;
        $fecha_emision = $_POST['fecha_emision'] ?? null;
        $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? null;
        $observaciones = $_POST['observaciones'] ?? '';

        if (!$id_documento) {
            header('Location: ' . BASE_URL . 'documentos/documentos?alerta=error');
            exit;
        }

        // 1️⃣ Obtener datos actuales del documento
        $documentoActual = $documentoModel->getById($id_documento);

        // Nombre original → por defecto el actual
        $nombre_original = $documentoActual['nombre_original'];

        // Ruta actual → por si no se reemplaza el archivo
        $rutaRelativa = $documentoActual['ruta_archivo'];

        // 2️⃣ Verificamos si se subió un nuevo archivo
        if (isset($_FILES['archivo_pdf']) && $_FILES['archivo_pdf']['error'] === UPLOAD_ERR_OK) {

            $archivo = $_FILES['archivo_pdf'];

            // Nuevo nombre original (visible en la tabla)
            $nombre_original = $archivo['name'];

            // Nombre físico único
            $nombreArchivo = time() . "_" . basename($archivo['name']);
            $rutaDestino = __DIR__ . '/../../public/uploads/documentos/' . $nombreArchivo;

            // Crear carpeta si no existe
            if (!file_exists(dirname($rutaDestino))) {
                mkdir(dirname($rutaDestino), 0777, true);
            }

            // Guardamos archivo en servidor
            if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {

                // Nueva ruta relativa (guardada en la BD)
                $rutaRelativa = 'uploads/documentos/' . $nombreArchivo;
            }
        }

        // 3️⃣ Actualizar en base de datos
        $documentoModel->actualizarDocumento(
            $id_documento,
            $id_tipo_doc,
            $nombre_original,
            $fecha_emision,
            $fecha_vencimiento,
            $observaciones,
            $rutaRelativa
        );

        header('Location: ' . BASE_URL . 'documentos/documentos?alerta=editado');
        exit;
    }
}
public function eliminar($id)
{
    $documentoModel = new Documento();
    $doc = $documentoModel->getById($id);

    if ($doc && file_exists(__DIR__ . "/../../" . $doc['ruta_archivo'])) {
        unlink(__DIR__ . "/../../" . $doc['ruta_archivo']);
    }

    $documentoModel->eliminarDocumento($id);

    $_SESSION['flash'] = [
        "tipo" => "flash-exito",
        "mensaje" => "📄 Documento eliminado correctamente"
    ];

    header("Location: " . BASE_URL . "dashboard/documentos");
    exit;
}


}
