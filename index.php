<?php
if (file_exists("user_config.php"))
    require_once "user_config.php";

require_once "vendor/autoload.php";

session_start();
sincronizarPermisosEnSesion();
cargarNotificacionesEnSesion();

$defaultController = "Index";
$defaultPath = "Home/";

// Carpeta relativa actual del proyecto
$relativePath = substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], "index.php"));
// Elimina la ruta del proyecto, los parametro y el ultimo "/"
$requestUri = substr($_SERVER['REQUEST_URI'], strlen($relativePath));
$requestUri = substr($requestUri, 0, strpos($requestUri, '?') === false ? strlen($requestUri) : strpos($requestUri, '?'));
$requestUri = rtrim($requestUri, "/");
// Divide la ruta en partes
$uriParts = explode('/', $requestUri);

$controllerName = count($uriParts) > 1 ? $uriParts[count($uriParts) - 1] : $defaultController;
$controllerPath = empty($requestUri) ? $defaultPath :
    (substr($requestUri, 0, strpos($requestUri, $controllerName)) ?: $requestUri."/");
$controllerNameCopy = $controllerName;
$controllerPathCopy = $controllerPath;

if (is_file("Controllers/".$controllerPath.$controllerName."Controller.php")) {
    require_once "Controllers/".ucfirst($controllerPath).ucfirst($controllerName)."Controller.php";
    exit();
} else {
    $controllerPath .= $controllerName."/";
    $controllerName = "Index";
    if (is_file("Controllers/".ucfirst($controllerPath).$controllerName."Controller.php")) {
        require_once "Controllers/".ucfirst($controllerPath).$controllerName."Controller.php";
        exit();
    }
}

http_response_code(404);
require_once "Views/Home/404.php";
// TODO ayudas , reportes, estadisticas
// TODO entender bien que verga es un perfil para nuestros usuarios
// TODO diagrama de carriles agregar de nuevo en modoficar asistencias que se guarde inconclusa