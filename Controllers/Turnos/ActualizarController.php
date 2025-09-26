<?php
cargarPost();
requiereAutenticacion();
requierePermiso(Modulo::TURNOS, Permiso::ACTUALIZAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    $turnoObj = new Turno();
    $turnoObj->set_codigo(urldecode($_GET['id']));

    $turnoObj = $turnoObj->obtenerPorId();
    if(is_array($turnoObj) and isset($turnoObj["success"]) and $turnoObj["success"] == false){

        http_response_code(400);
        echo $turnoObj["error"];

        die;
    }

    
    
    require_once "Views/Turnos/_Actualizar.php";
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{


    if(isset($_POST['accion']) && $_POST['accion'] == "Actualizar")
    {
        $turnos = new Turno();
        $turnos->setterArray([
            "codigo" => $_POST['id'] ?? "",
            "nombre" => $_POST['form-nombre'],
            "horario_entrada" => $_POST['horario_entrada'],
            "horario_salida" => $_POST['horario_salida'],
            "lunes" => $_POST['lunes'] ?? "0",
            "martes" => $_POST['martes'] ?? "0",
            "miercoles" => $_POST['miercoles'] ?? "0",
            "jueves" => $_POST['jueves'] ?? "0",
            "viernes" => $_POST['viernes'] ?? "0",
            "sabado" => $_POST['sabado'] ?? "0",
            "domingo" => $_POST['domingo'] ?? "0"
        ]);


        //$turnos->setTestingMode(true);
        $turnos->actualizar();

    }

}
else
{
    http_response_code(405);
    exit;
}