<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Empresa</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/dashboard_empresa.css">
</head>
<body>

    <!-- 🔥 FLASH MESSAGE -->
    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="flash-message <?= $_SESSION['flash']['tipo']; ?>">
            <?= $_SESSION['flash']['mensaje']; ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <?php include __DIR__ . '/header.php'; ?>

    <div class="dashboard-container">
        <?php include __DIR__ . '/sidebar.php'; ?>
        <main class="dashboard-content">
            <div class="contenido-principal-wrapper">
                <?php if (isset($content)) echo $content; ?>
            </div>
        </main>
    </div>

    <?php include __DIR__ . '/footer.php'; ?>

</body>
</html>
