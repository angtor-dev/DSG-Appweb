<?php
$_POST = json_decode(file_get_contents("php://input"), true);
requiereAutenticacion();
requierePermiso(Modulo::ASISTENCIAS, Permiso::ELIMINAR);

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if(isset($_POST['action']) and $_POST['action'] == "Eliminar"){
        http_response_code(200);
        $asistencia = new Asistencia;


        //$asistencia->setTestingMode(true);
        $asistencia->setterArray(Array(
            "fecha" => $_POST["fecha"],
            "turno" => $_POST["turno"],
            "idDepartamento" => $_POST["idDepartamento"],
        ));


        //$asistencia->mapearFormulario();
        $asistencia->eliminarFechaAsistencia(true);
        
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