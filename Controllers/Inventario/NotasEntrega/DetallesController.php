<?php
requiereAutenticacion();
requierePermiso(Modulo::NOTASENTREGA, Permiso::CONSULTAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'] ?? null;
    if (empty($id)) {
        $_SESSION['errores'][] = "Se debe especificar una nota de entrega para ver sus detalles";
        redirigir(LOCAL_DIR."/Inventario/NotasEntrega");
    }

    $entrada = Entrada::cargarConDetalles($id);

    if (is_null($entrada)) {
        $_SESSION['errores'][] = "La nota de entrega que intenta ver no existe";
        redirigir(LOCAL_DIR."/Inventario/NotasEntrega");
    }

    require_once "Views/Inventario/NotasEntrega/_Detalles.php";
} else {
    http_response_code(405);
}