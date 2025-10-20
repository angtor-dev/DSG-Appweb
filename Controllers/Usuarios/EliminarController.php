<?php
requiereAutenticacion();
requierePermiso("usuarios", "eliminar");

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    if(isset($_GET['id'])){
        $_POST['id'] = $_GET['id'];
    }
    $usuario = new Usuario();


    $usuario->setterArray(
        [
            "id" => $_POST['id']
        ]
    );

    if (($resp = $usuario->eliminarUsuario(false))['success']) {
        echo json_encode($resp);
        $_SESSION['exitos'][] = $resp['mensaje'];
    }
    else
    {
        echo json_encode($resp);
    }

    

    //redirigir(LOCAL_DIR."/Usuarios");
}
else
{
    http_response_code(405);
    exit;
}

// $usuario = Usuario::cargar($_GET['id']);

// if (empty($usuario)) {
//     $_SESSION['errores'][] = "El usuario que intenta eliminar no existe";
//     redirigir(LOCAL_DIR."/Usuarios");
// }

// if ($usuario->id == $_SESSION['usuario']->id) {
//     $_SESSION['errores'][] = "No puedes eliminar tu propio usuario";
//     redirigir(LOCAL_DIR."/Usuarios");
// }

// if ($usuario->eliminar(1)) {
//     $_SESSION['exitos'][] = "Usuario eliminado con exito";
//     Bitacora::registrar("Usuario '".$usuario->getCorreo()."' eliminado");
// }

//redirigir(LOCAL_DIR."/Usuarios");