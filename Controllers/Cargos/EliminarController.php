<?php
cargarPost();
requiereAutenticacion();
requierePermiso(Modulo::TURNOS, Permiso::ELIMINAR);
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    /**
     * payload
     * 
     * accion
     * accion :"Registrar"
     * id : "1"
     */

    if(isset($_POST['accion']) && $_POST['accion'] == "Eliminar")
    {
        $turnos = new Cargo();
        $turnos->setterArray([
            "id" => $_POST['id']??""
        ]);

        //$turnos->setTestingMode(true);
        $turnos->eliminarCargo();

    }

}
else
{
    http_response_code(405);
    exit;
}