<?php
cargarPost();
requiereAutenticacion();
requierePermiso(Modulo::TRABAJADORES, Permiso::REGISTRAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    $departamentoObj = new Division();
    $departamentos = $departamentoObj->listar();
     if(!empty($_GET['cedula'])){

        $Trabajador = Trabajador::cargarPorCedula($_GET["cedula"]);

        $mensaje = "";

        if(isset($_SESSION['errores'])){
            http_response_code(423);
            $sms = $_SESSION['errores'][0];
            unset($_SESSION["errores"]);
            exit($sms);
        }
        else if(! ($Trabajador instanceof Trabajador)){
            $mensaje = "El trabajador no se encuentra registrado en el sistema";
            http_response_code(423);
            exit($sms);
        }

        require_once "Views/Trabajadores/_Actualizar.php";

    }

}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if($_POST["action"] == "Actualizar"){
        $Trabajador = new Trabajador();
        //$Trabajador->setTestingMode(true);

        $Trabajador->setterArray([
            "id" => $_POST["idTrabajador"],
            "cedula" => $_POST["cedula"],
            "nombre" => $_POST["nombre"],
            "apellido" => $_POST["apellido"],
            "telefono" => $_POST["telefono"],
            "cargo" => $_POST["cargo"],
            "turno" => $_POST["turno"],
            "idDepartamento" => $_POST["departamento"],
            "fechaIngreso" => $_POST["fecha_ingreso"],
            "cedulaSeleccion" => $_GET["cedula"] // cedula anteriro para comparar
        ]);


        if ($Trabajador->actualizar()["success"]) {
            $_SESSION['exitos'][] = "Trabajador actualizado con exito";
        }
    }


}
else
{
    http_response_code(405);
    exit;
}