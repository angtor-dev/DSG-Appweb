<?php
cargarPost();
requiereAutenticacion();
requierePermiso(Modulo::TRABAJADORES, Permiso::REGISTRAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    $departamentoObj = new Departamento();
    $departamentos = $departamentoObj->listar();
     if(!empty($_GET['cedula'])){

        $Trabajador = Trabajador::cargarPorCedula($_GET["cedula"]);

        $mensaje = "";

        if(isset($_SESSION['errores'])){
            $mensaje = $_SESSION['errores'][0];
        }
        else if(! ($Trabajador instanceof Trabajador)){
            $mensaje = "El trabajador no se encuentra registrado en el sistema";
        }

        require_once "Views/Trabajadores/_Actualizar.php";

    }

}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if($_POST["action"] == "Actualizar"){
        $Trabajador = new Trabajador();
        $_POST["cedulaSeleccion"] = $_GET["cedula"];
        $Trabajador->mapearFormulario();
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