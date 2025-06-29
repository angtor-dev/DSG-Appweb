<?php
requiereAutenticacion();
requierePermiso("usuarios", "registrar");

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
     if(!empty($_GET['cedula'])){
        $usuario = Usuario::cargarPorCedula($_GET['cedula']);


        if($usuario instanceof Usuario and (!isset($_GET['id']) or $_GET['id'] != $usuario->id) ) {
            echo json_encode([
                "userFound" => true,
                "cedula" => $usuario->getCedula(),
                "nombre" => $usuario->getNombreCompleto(),
                "usuario" => ( $usuario )?1:null
            ]);
        } else {
            echo json_encode([
                "userFound" => false
            ]);
        }
    } else {
        $rolObj = new Rol();
        $roles = $rolObj->listar(1);

        require_once "Views/Usuarios/_Registrar.php";
    }
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $usuario = new Usuario();
    //$usuario->setTestingMode(true);
    
    $usuario->setterArray([
        "cedula" => $_POST['cedula'],
        "nombre" => $_POST['nombre'],
        "apellido" => $_POST['apellido'],
        "correo" => $_POST['correo'],
        "idRol" => $_POST['idRol'],
        "clave" => $_POST['clave'],

    ]);


    if(($resp = $usuario->registrar(false))['success']) {
        $_SESSION['exitos'][] = $resp['mensaje'];
    }
    else{
        if(isset($resp['consoleError'])) $_SESSION['errores'][] = $resp['consoleError'];
        $_SESSION['errores'][] = $resp['mensaje'];
    }

    redirigir(LOCAL_DIR."/Usuarios");
}
else
{
    http_response_code(405);
    exit;
}