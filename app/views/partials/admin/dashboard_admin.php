<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Administrador</title>
<link rel="stylesheet" href="<?= BASE_URL ?>css/dashboard_admin.css">

</head>
<body>
    <div class="dashboard-header">
        <h1>👑 Panel del Administrador</h1>
        <a href="<?= BASE_URL ?>login/salir" class="btn-logout">Cerrar sesión</a>
    </div>

    <div class="dashboard-container">
        <aside class="sidebar">
            <ul>
                <li><a href="#">🏠 Inicio</a></li>
                <li><a href="#">📄 Documentos</a></li>
                <li><a href="#">👥 Usuarios</a></li>
                <li><a href="#">⚙️ Configuración</a></li>
            </ul>
        </aside>

        <main class="dashboard-content">
            <h2>Bienvenido, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></h2>
            <p>Has iniciado sesión como <strong><?= htmlspecialchars($_SESSION['rol_nombre']) ?></strong>.</p>
        </main>
    </div>
</body>

</html>
