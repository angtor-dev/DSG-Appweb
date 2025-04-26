<?php
requiereAutenticacion();
requierePermiso(Modulo::TRABAJADORES, Permiso::REGISTRAR);



if ($_SERVER['REQUEST_METHOD'] === 'GET')
{


     $departamentos = Departamento::listar();
     if(!empty($_GET['cedula'])){
        require_once "Models/Trabajador.php";
        $Trabajador = Trabajador::cargarPorCedula($_GET["cedula"]);

        if($Trabajador instanceof Trabajador){
            echo json_encode([
                "cedula" => $Trabajador->getCedula(),
                ]);
        }
        else{
            echo "{}";
        }
    }
    else{
        require_once "Views/Trabajadores/_Registrar.php";
    }

}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{

    $Trabajador = new Trabajador();
    $Trabajador->mapearFormulario();

    if ($Trabajador->esValido() && $Trabajador->registrar()) {
        $_SESSION['exitos'][] = "Trabajador registrado con exito";
        Bitacora::registrar("Trabajador '".$Trabajador->getNombreCompleto()."' registrado");
    }

    redirigir(LOCAL_DIR."/Trabajadores");
    // $usuario = new Usuario();
    // $usuario->mapearFormulario();

    // if ($usuario->esValido() && $usuario->registrar()) {
    //     $_SESSION['exitos'][] = "Usuario registrado con exito";
    //     Bitacora::registrar("Usuario '".$usuario->getCorreo()."' registrado");
    // }

    // redirigir(LOCAL_DIR."/Usuarios");
}
else
{
    http_response_code(405);
    exit;
}