<?php
$_POST = json_decode(file_get_contents("php://input"), true);
requiereAutenticacion();
requierePermiso(Modulo::ASISTENCIAS, Permiso::REGISTRAR);

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
        require_once "Models/Enums/Turno.php";
        
        $departamentos = (new Departamento())->listar();
        
        require_once "Views/Asistencias/_registrar.php";
    }
    
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if(isset($_POST['action']) and $_POST['action'] == "Registrar"){
        http_response_code(200);
        $asistencia = new Asistencia;


        $asistencia->mapearFormulario();
        $asistencia->registrar(true);
        
    }
    else if (isset($_POST['action']) and $_POST['action'] == "Eliminar") {
        http_response_code(200);
        $asistencia = new Asistencia;
        $asistencia->mapearFormulario();
        $asistencia->eliminar();
        
    }
    else{
        http_response_code(404);
    }
}
else
{
    http_response_code(405);
    exit;
}