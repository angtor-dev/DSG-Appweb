<?php
requiereAutenticacion();
requierePermiso(Modulo::MEDIDAS, Permiso::ACTUALIZAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    if (empty($_GET['id'])) {
        $_SESSION['errores'][] = "Se debe especificar una medida para actualizar";
        redirigir(LOCAL_DIR."/Medidas");
    }

    $medida = Medida::cargar($_GET['id']);

    if (is_null($medida)) {
        $_SESSION['errores'][] = "La medida que intenta actulizar no existe";
        redirigir(LOCAL_DIR."/Medidas");
    }

    $medidas = $medida->listar();

    require_once "Views/Medidas/_Actualizar.php";
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $medida = new Medida();
    $medida->mapearFormulario();

    if ($medida->esValido() && $medida->actualizar()) {
        $_SESSION['exitos'][] = "Medida actualizada con exito";
        Bitacora::registrar("Medida '".$medida->getUnidad()."' actualizada");
    }

    redirigir(LOCAL_DIR."/Medidas");
}
else
{
    http_response_code(405);
    exit;
}