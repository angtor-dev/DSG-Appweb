<?php
cargarPost();
requiereAutenticacion();
requierePermiso(Modulo::TRABAJADORES, Permiso::REGISTRAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    postTienePermiso(Modulo::TRABAJADORES, Permiso::REGISTRAR);
    $departamentoObj = new Division();
    $departamentos = $departamentoObj->listar();
    $cargosOptions = Cargo::getCargosOptions();
    $turnosOptions = Turno::getTurnosOptions();
     if(!empty($_GET['cedula'])){
        postTienePermiso("falla", Permiso::REGISTRAR);
        $Trabajador = Trabajador::cargarPorCedula($_GET["cedula"]);

        if($Trabajador instanceof Trabajador and $Trabajador->getEstado() == $Trabajador::TRABAJADOR_ACTIVO){
            echo json_encode([
                "cedula" => $Trabajador->getCedula(),
                ]);
        }
        else{
            echo "{}";
        }
    }
    else{
        require_once "Views/Trabajadores/_Registrar.php";
    }

}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    
    $Trabajador = new Trabajador();
    //$Trabajador->setTestingMode(true);

  

    $Trabajador->setterArray([
        "cedula" => $_POST["cedula"],
        "nombre" => $_POST["nombre"],
        "apellido" => $_POST["apellido"],
        "telefono" => $_POST["telefono"],
        "cargo" => $_POST["cargo"],
        "turno" => $_POST["turno"],
        "idDepartamento" => $_POST["departamento"],
        "fechaIngreso" => $_POST["fecha_ingreso"],
        
    ]);

    if ($Trabajador->registrar()["success"]) {
        $_SESSION['exitos'][] = "Trabajador registrado con exito";
    }

}
else
{
    http_response_code(405);
    exit;
}