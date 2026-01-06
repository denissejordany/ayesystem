<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/config.php';

// 1) Soporte legacy: ?controller=X&action=Y  (mantener compatibilidad)
if (isset($_GET['controller'])) {
    $controller = $_GET['controller'];
    $action = $_GET['action'] ?? 'index';
    $controllerName = ucfirst($controller) . 'Controller';
    $method = $action;
} else {
    // 2) Soporte "url=controller/action" (tu sistema original)
    $url = isset($_GET['url']) ? $_GET['url'] : 'login';
    $url = explode('/', $url);

    $controllerName = ucfirst($url[0]) . 'Controller';
    $method = $url[1] ?? 'index';
}

$controllerPath = __DIR__ . '/../app/controllers/' . $controllerName . '.php';

if (file_exists($controllerPath)) {
    require_once $controllerPath;
    $controller = new $controllerName();

   if (method_exists($controller, $method)) {
    $param = $url[2] ?? null;  // ← capturamos el ID si existe

    if ($param !== null) {
        $controller->$method($param);
    } else {
        $controller->$method();
    }
}
    } else {
        echo "Método no encontrado.";
    }
