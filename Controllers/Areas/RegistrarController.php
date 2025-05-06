<?php
requiereAutenticacion();
requierePermiso(Modulo::AREAS, Permiso::REGISTRAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    $areaObj = new Area();
    $areas = $areaObj->listar();
    
    require_once "Views/Areas/_Registrar.php";
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    // $area = new Area();
    // $area->mapearFormulario();

    // if ($area->esValido() && $area->registrar()) {
    //     $_SESSION['exitos'][] = "Área registrada con exito";
    //     Bitacora::registrar("Área '".$area->getNombre()."' registrada");
    // }

    redirigir(LOCAL_DIR."/Areas");
}
else
{
    http_response_code(405);
    exit;
}