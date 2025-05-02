<?php
requiereAutenticacion();
requierePermiso("usuarios", "registrar");

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{

    // $departamentos = Departamento::listar();
    if(!empty($_GET['cedula'])){
        require_once "Models/Trabajador.php";
        $Trabajador = Trabajador::cargarPorCedula($_GET["cedula"]);

        if($Trabajador instanceof Trabajador){
            echo json_encode([
                "id" => $Trabajador->getCedula(),
                "nombre" => $Trabajador->getNombreCompleto(),
                "departamento" => $Trabajador->departamento->getNombre()
                ]);
        }
        else{
            echo "{}";
        }







    }
    else{
        require_once "Views/Asistencias/_registrar.php";
    }
    
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