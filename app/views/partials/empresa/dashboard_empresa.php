<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?><?php
// Capturamos el contenido dinámico
ob_start();
?>
<div class="contenido-principal">
    <h1>Bienvenido, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></h1>
    <h2>Empresa: <?= htmlspecialchars($_SESSION['empresa_nombre']) ?></h2>
    <p>Selecciona una opción del menú lateral para comenzar.</p>
</div>

<?php
$content = ob_get_clean();

// Llamamos al layout
require __DIR__ . '/../layout.php';
