<?php
requiereAutenticacion();
requierePermiso("usuarios", "actualizar");

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    if (empty($_GET['id'])) {
        $_SESSION['errores'][] = "Se debe especificar un usuario";
        redirigir(LOCAL_DIR."/Usuarios");
    }

    $usuario = Usuario::cargar($_GET['id']);

    if (is_null($usuario)) {
        $_SESSION['errores'][] = "El usuario que intenta actulizar no existe";
        redirigir(LOCAL_DIR."/Usuarios");
    }

    $roles = Rol::listar(1);

    require_once "Views/Usuarios/_Actualizar.php";
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    $usuario = new Usuario();
    $usuario->mapearFormulario();

    if ($usuario->esValido() && $usuario->actualizar()) {
        $_SESSION['exitos'][] = "Usuario actualizado con exito";
        Bitacora::registrar("Usuario '".$usuario->getCorreo()."' actualizado");
    }

    redirigir(LOCAL_DIR."/Usuarios");
}
else
{
    http_response_code(405);
    exit;
}