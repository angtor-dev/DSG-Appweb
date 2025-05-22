<?php
cargarPost();
requiereAutenticacion();
requierePermiso(Modulo::TRABAJADORES, Permiso::REGISTRAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    $departamentoObj = new Departamento();
    $departamentos = $departamentoObj->listar();
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

    if ($Trabajador->registrar()["success"]) {
        $_SESSION['exitos'][] = "Trabajador registrado con exito";
    }

}
else
{
    http_response_code(405);
    exit;
}