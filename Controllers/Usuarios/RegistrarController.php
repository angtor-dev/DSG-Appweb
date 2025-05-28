<?php
requiereAutenticacion();
requierePermiso("usuarios", "registrar");

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    $departamentoObj = new Departamento();
    $departamentos = $departamentoObj->listar();
     if(!empty($_GET['cedula'])){
        $Trabajador = Trabajador::cargarPorCedula($_GET["cedula"]);

        if($Trabajador instanceof Trabajador) {
            echo json_encode([
                "cedula" => $Trabajador->getCedula(),
                "nombre" => $Trabajador->getNombreCompleto(),
                "departamento" => $Trabajador->departamento->getNombre(),
                "usuario" => ( Usuario::cargarPorCedula($_GET['cedula']) )?1:null
            ]);
        } else {
            echo "{}";
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
    $usuario->mapearFormulario();

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