<?php
ob_start();
?>

<div class="contenido-principal">
    <h2>Perfil de la Empresa</h2>

    <div class="perfil-empresa-box">
        <!-- Logo de la empresa -->
        <div class="logo-empresa">
            <img src="<?= BASE_URL . ($_SESSION['empresa_logo'] ?? 'img/perfil_empresa.png') ?>" alt="Logo Empresa">
        </div>

        <!-- Información de la empresa -->
        <div class="info-box">
            <p><strong>ID:</strong> <?= htmlspecialchars($empresa['id']) ?></p>
            <p><strong>Nombre:</strong> <?= htmlspecialchars($empresa['nombre']) ?></p>
            <p><strong>RUC:</strong> <?= htmlspecialchars($empresa['ruc']) ?></p>
            <p><strong>Dirección:</strong> <?= htmlspecialchars($empresa['direccion']) ?></p>
            <p><strong>Teléfono:</strong> <?= htmlspecialchars($empresa['telefono']) ?></p>
            <p><strong>Email de contacto:</strong> <?= htmlspecialchars($empresa['email_contacto']) ?></p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
