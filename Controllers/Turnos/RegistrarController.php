<?php
cargarPost();
requiereAutenticacion();
requierePermiso(Modulo::TURNOS, Permiso::REGISTRAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    
    require_once "Views/Turnos/_Registrar.php";
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    /**
     * payload
     * 
     * accion
     * accion :"Registrar"
     * domingo :"1"
     * form-nombre :"Probando registrar"
     * horario_entrada :"09:00"
     * horario_salida :"23:00"
     * jueves :"1"
     * lunes :"1"
     * martes :"1"
     * sabado :"1"
     * viernes :"1"
     */

    if(isset($_POST['accion']) && $_POST['accion'] == "Registrar")
    {
        $turnos = new Turno();
        $turnos->setterArray([
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
        $turnos->registrar();

    }

}
else
{
    http_response_code(405);
    exit;
}