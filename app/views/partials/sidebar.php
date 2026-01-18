<aside class="sidebar">
    <div class="perfil-empresa">
    <?php
    // Si existe el logo en sesión, lo usamos; si no, mostramos el logo por defecto
    $logo = $_SESSION['empresa_logo'] ?? 'img/perfil_empresa.png';
    ?>
    <img src="<?= BASE_URL . $logo ?>" alt="Perfil Empresa">
    <p><?= htmlspecialchars($_SESSION['empresa_nombre'] ?? 'Empresa') ?></p>
</div>

    <ul>
        <li><a href="<?= BASE_URL ?>dashboard/empresa">🏠 Inicio</a></li>
        <li><a href="<?= BASE_URL ?>empresa/perfil">👤 Perfil Empresa</a></li>
        <li><a href="<?= BASE_URL ?>dashboard/documentos">📄 Documentos</a></li>
        <li><a href="#">📞 Soporte</a></li>
    </ul>
</aside>
