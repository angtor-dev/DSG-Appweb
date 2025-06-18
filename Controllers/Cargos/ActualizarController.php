<?php
cargarPost();
requiereAutenticacion();
requierePermiso(Modulo::CARGOS, Permiso::ACTUALIZAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{   
    $cargo = new Cargo();
    $cargo->set_id($_GET['id']);
    $cargo = $cargo->obtenerPorId();
    
    require_once "Views/Cargos/_Actualizar.php";
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if(isset($_POST['accion']) && $_POST['accion'] == "Actualizar")
    {
        $cargo = new Cargo();
        $cargo->setterArray([
            "id" => $_POST['form-id'],
            "nombre" => $_POST['form-nombre-cargo'],
            "nivel" => $_POST['form-nivel-cargo']
        ]);


        //$cargo->setTestingMode(true);
        $cargo->actualizar();

    }

}
else
{
    http_response_code(405);
    exit;
}