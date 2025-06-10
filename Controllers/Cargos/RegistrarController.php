<?php
cargarPost();
requiereAutenticacion();
requierePermiso(Modulo::CARGOS, Permiso::REGISTRAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    
    require_once "Views/Cargos/_Registrar.php";
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if(isset($_POST['accion']) && $_POST['accion'] == "Registrar")
    {
        $cargo = new Cargo();
        $cargo->setterArray([
            "nombre" => $_POST['form-nombre-cargo'],
            "nivel" => $_POST['form-nivel-cargo']
        ]);


        //$cargo->setTestingMode(true);
        $cargo->registrar();

    }
}
else
{
    http_response_code(405);
    exit;
}