<?php
requiereAutenticacion();
requierePermiso("usuarios", "registrar");

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    $roles = Rol::listar(1);

    require_once "Views/Usuarios/_Registrar.php";
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $usuario = new Usuario();
    $usuario->mapearFormulario();

    if ($usuario->esValido() && $usuario->registrar()) {
        $_SESSION['exitos'][] = "Usuario registrado con exito";
        Bitacora::registrar("Usuario '".$usuario->getCorreo()."' registrado");
    }

    redirigir(LOCAL_DIR."/Usuarios");
}
else
{
    http_response_code(405);
    exit;
}