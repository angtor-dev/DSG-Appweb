<?php
cargarPost();
requiereAutenticacion();
requierePermiso(Modulo::TRABAJADORES, Permiso::REGISTRAR);

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if($_POST["action"] == "Eliminar"){
        $Trabajador = new Trabajador();
        $Trabajador->mapearFormulario();
        $Trabajador->eliminarTrabajador();
    }


}
else
{
    http_response_code(405);
    exit;
}