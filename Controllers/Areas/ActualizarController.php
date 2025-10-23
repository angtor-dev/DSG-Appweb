<?php
requiereAutenticacion();
requierePermiso(Modulo::AREAS, Permiso::ACTUALIZAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    if (empty($_GET['id'])) {
        $_SESSION['errores'][] = "Se debe especificar un área para actualizar";
        redirigir(LOCAL_DIR."/Areas");
    }

    $area = Area::cargar($_GET['id']);

    if (is_null($area)) {
        $_SESSION['errores'][] = "El área que intenta actulizar no existe";
        redirigir(LOCAL_DIR."/Areas");
    }

    $areas = $area->listar();

    require_once "Views/Areas/_Actualizar.php";
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $area = new Area();
    $area->setterArray([
        "id" => $_POST["id"],
        "nombre" => $_POST["nombre"],
        "idArea" => (!empty($_POST["idArea"])) ? intval($_POST["idArea"]) : null
    ]);

    if ($area->esValido(true) && $area->actualizar()) {
        $_SESSION['exitos'][] = "Área actualizada con exito";
        Bitacora::registrar("Área '".$area->getNombre()."' actualizada");
    }

    redirigir(LOCAL_DIR."/Areas");
}
else
{
    http_response_code(405);
    exit;
}