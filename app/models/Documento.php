<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/Conexion.php';

class Documento  
{
    private $db;

    public function __construct()
    {
        $this->db = Conexion::getInstance();
    }

 public function getByEmpresaPaginado($empresa_id, $limit, $offset)
{
    $sql = "SELECT d.ID,
                   d.nombre_original,
                   d.fecha_emision,
                   d.fecha_vencimiento,
                   d.observaciones,
                   d.ruta_archivo,
                   d.tipo_ID,
                   t.nombre AS tipo_nombre,
                   e.Nombre AS estado_nombre
            FROM documentos d
            LEFT JOIN tipo_documento t ON d.tipo_ID = t.ID
            LEFT JOIN estado_doc e ON d.id_estado_doc = e.id_estado_doc
            WHERE d.empresa_ID = :empresa_id
            ORDER BY d.ID DESC
            LIMIT :limit OFFSET :offset";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function contarDocumentos($empresa_id)
{
    $sql = "SELECT COUNT(*) as total FROM documentos WHERE empresa_ID = :empresa_id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':empresa_id', $empresa_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

public function getTiposDocumento()
{
    $sql = "SELECT ID AS id_tipo_doc, nombre AS tipo_nombre, Req_vencimiento AS req_vencimiento
            FROM tipo_documento
            WHERE activo = 1";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function guardarDocumento($empresa_id, $tipo_id, $nombre_original, $ruta_archivo, $fecha_emision, $fecha_vencimiento, $observaciones) {
    $sql = "INSERT INTO documentos 
            (empresa_ID, tipo_ID, nombre_original, ruta_archivo, fecha_emision, fecha_vencimiento, observaciones, id_estado_doc)
            VALUES (:empresa_ID, :tipo_ID, :nombre_original, :ruta_archivo, :fecha_emision, :fecha_vencimiento, :observaciones, 1)";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':empresa_ID', $empresa_id);
    $stmt->bindParam(':tipo_ID', $tipo_id);
    $stmt->bindParam(':nombre_original', $nombre_original);
    $stmt->bindParam(':ruta_archivo', $ruta_archivo);
    $stmt->bindParam(':fecha_emision', $fecha_emision);
    $stmt->bindParam(':fecha_vencimiento', $fecha_vencimiento);
    $stmt->bindParam(':observaciones', $observaciones);
    $stmt->execute();
}
public function getById($id)
{
    $sql = "SELECT d.*,
                   t.Req_vencimiento,
                   t.nombre AS tipo_nombre
            FROM documentos d
            INNER JOIN tipo_documento t ON d.tipo_ID = t.ID
            WHERE d.ID = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function actualizarDocumento($id, $tipo_id, $nombre_original, $fecha_emision, $fecha_vencimiento, $observaciones, $ruta_archivo = null)
{
    $sql = "UPDATE documentos 
            SET tipo_ID = :tipo_ID,
                nombre_original = :nombre_original,
                fecha_emision = :fecha_emision,
                fecha_vencimiento = :fecha_vencimiento,
                observaciones = :observaciones";

    if ($ruta_archivo) {
        $sql .= ", ruta_archivo = :ruta_archivo";
    }

    $sql .= " WHERE ID = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':tipo_ID', $tipo_id);
    $stmt->bindParam(':nombre_original', $nombre_original);
    $stmt->bindParam(':fecha_emision', $fecha_emision);
    $stmt->bindParam(':fecha_vencimiento', $fecha_vencimiento);
    $stmt->bindParam(':observaciones', $observaciones);
    if ($ruta_archivo) $stmt->bindParam(':ruta_archivo', $ruta_archivo);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
}
public function eliminarDocumento($id)
{
    $sql = "DELETE FROM documentos WHERE ID = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    return true;
}


}
