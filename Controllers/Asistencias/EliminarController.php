<?php
$_POST = json_decode(file_get_contents("php://input"), true);
requiereAutenticacion();
requierePermiso(Modulo::ASISTENCIAS, Permiso::ELIMINAR);

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if(isset($_POST['action']) and $_POST['action'] == "Eliminar"){
        http_response_code(200);
        $asistencia = new Asistencia;


        $asistencia->mapearFormulario();
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