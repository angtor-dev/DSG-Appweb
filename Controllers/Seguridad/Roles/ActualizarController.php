<?php
requiereAutenticacion();
requierePermiso("roles", "actualizar");

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    if (empty($_GET['id'])) {
        $_SESSION['errores'][] = "Se debe especificar un rol";
        redirigir(LOCAL_DIR."/Seguridad/Roles");
    }

    $rol = Rol::cargar($_GET['id']);

    if (is_null($rol)) {
        $_SESSION['errores'][] = "El rol que intenta actulizar no existe";
        redirigir(LOCAL_DIR."/Seguridad/Roles");
    }

    $roles = Rol::listar();

    renderView();
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $rol = new Rol();
    $rol->mapearFormulario();
    
    if (!$rol->esValido()) {
        renderView();
    }

    if ($rol->actualizar()) {
        $_SESSION['exitos'][] = "Rol actualizado con exito";
        Bitacora::registrar("Rol '".$rol->getNombre()."' actualizado");
    }

    redirigir(LOCAL_DIR."/Seguridad/Roles");
}
else
{
    http_response_code(405);
    exit;
}