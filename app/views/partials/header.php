<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<div class="dashboard-header">
     <div class="header-inner">
    <h1>🏢 Panel de Empresa</h1>
    <a href="<?= BASE_URL ?>login/salir" class="btn-logout">Cerrar sesión</a>
    </div>
</div>
