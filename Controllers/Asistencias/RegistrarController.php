<?php
cargarPost();
requiereAutenticacion();
requierePermiso(Modulo::ASISTENCIAS, Permiso::REGISTRAR);

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if(isset($_POST['action']) and $_POST['action'] == "Registrar"){
        postTienePermiso(Modulo::ASISTENCIAS, Permiso::REGISTRAR);
        $asistencia = new Asistencia;

       //$asistencia->setTestingMode(true);

        $asistencia->setterArray(Array(
            "idDepartamento" => $_POST["idDepartamento"],
            "fecha" => $_POST["fecha"],
            "turno" => $_POST["turno"],
            "trabajadores" => $_POST["trabajadores"],
        ));


        $asistencia->registrarSemanal(true);
        
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