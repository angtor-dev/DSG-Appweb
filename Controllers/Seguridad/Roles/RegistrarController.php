<?php
requiereAutenticacion();
requierePermiso("roles", "registrar");

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    renderView();
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    $rol = new Rol();
    $rol->mapearFormulario();
    
    if (!$rol->esValido()) {
        redirigir(LOCAL_DIR."/Seguridad/Roles/Registrar");
    }

    if ($rol->registrar()) {
        $_SESSION['exitos'][] = "Rol registrado con exito";
        Bitacora::registrar("Rol '".$rol->getNombre()."' registrado");
    }

    redirigir(LOCAL_DIR."/Seguridad/Roles");
}
else
{
    http_response_code(405);
    exit;
}