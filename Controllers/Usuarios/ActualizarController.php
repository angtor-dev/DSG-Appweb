<?php
requiereAutenticacion();
requierePermiso("usuarios", "actualizar");

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    if (empty($_GET['id'])) {
        $_SESSION['errores'][] = "Se debe especificar un usuario";
        redirigir(LOCAL_DIR."/Usuarios");
    }

    $usuario = Usuario::cargar($_GET['id'],true);

    if (is_null($usuario)) {
        $_SESSION['errores'][] = "El usuario que intenta actulizar no existe";
        redirigir(LOCAL_DIR."/Usuarios");
    }

    $rolObj = new Rol();
    $roles = $rolObj->listar(1);

    require_once "Views/Usuarios/_Actualizar.php";
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    if(isset($_GET['id'])){
        $_POST['id'] = $_GET['id'];
    }
    $usuario = new Usuario();

    //$usuario->setTestingMode(true);

    $usuario->setterArray([
        "id" => $_POST['id'],
        "cedula" => $_POST['cedula'],
        "nombre" => $_POST['nombre'],
        "apellido" => $_POST['apellido'],
        "correo" => $_POST['correo'],
        "idRol" => $_POST['idRol'],
        "clave" => (!empty($_POST['clave'])) ? $_POST['clave'] : null
    ]);


    if (($resp = $usuario->actualizarUsuario(false))['success']) {
        $_SESSION['exitos'][] = $resp['mensaje'];
    }
    else
    {
        if(DEVELOPER_MODE) $_SESSION['errores'][] = $resp['consoleError'];
        $_SESSION['errores'][] = $resp['mensaje'];
    }

    

    redirigir(LOCAL_DIR."/Usuarios");
}
else
{
    http_response_code(405);
    exit;
}