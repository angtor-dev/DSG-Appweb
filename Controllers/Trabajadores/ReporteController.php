<?php
cargarPost();
requiereAutenticacion();
requierePermiso(Modulo::TRABAJADORES, Permiso::REGISTRAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if(!empty($_GET['cedula'])){
        postTienePermiso(Modulo::TRABAJADORES, Permiso::CONSULTAR);
        $trabajador = Trabajador::cargarPorCedula($_GET["cedula"]);
        require_once "Views/Trabajadores/_Reporte.php";

    }

}  else {
    http_response_code(405);
    exit;
}