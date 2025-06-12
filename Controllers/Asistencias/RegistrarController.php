<?php
cargarPost();
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
        $asistencia = new Asistencia;

//        $asistencia->setTestingMode(true);

        $asistencia->setterArray(Array(
            "idDepartamento" => $_POST["idDepartamento"],
            "fecha" => $_POST["fecha"],
            "turno" => $_POST["turno"],
            "trabajadores" => $_POST["trabajadores"],
        ));


        $asistencia->registrar(true);
        
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