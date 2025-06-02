<?php
requiereAutenticacion();
requierePermiso(Modulo::MEDIDAS, Permiso::REGISTRAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    $medidaObj = new Medida();
    $medidas = $medidaObj->listar();
    
    require_once "Views/Medidas/_Registrar.php";
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $medida = new Medida();
    $medida->mapearFormulario();

    if ($medida->esValido() && $medida->registrar()) {
        $_SESSION['exitos'][] = "Medida registrada con exito";
        Bitacora::registrar("Medida '".$medida->getUnidad()."' registrada");
    }

    redirigir(LOCAL_DIR."/Medidas");
}
else
{
    http_response_code(405);
    exit;
}