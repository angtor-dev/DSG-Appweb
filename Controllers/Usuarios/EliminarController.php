<?php
requiereAutenticacion();
requierePermiso("usuarios", "eliminar");

$usuario = Usuario::cargar($_GET['id']);

if (empty($usuario)) {
    $_SESSION['errores'][] = "El usuario que intenta eliminar no existe";
    redirigir(LOCAL_DIR."/Usuarios");
}

if ($usuario->eliminar(1)) {
    $_SESSION['exitos'][] = "Usuario eliminado con exito";
    Bitacora::registrar("Usuario '".$usuario->getCorreo()."' eliminado");
}

redirigir(LOCAL_DIR."/Usuarios");