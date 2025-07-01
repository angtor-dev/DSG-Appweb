<?php
$_POST = json_decode(file_get_contents("php://input"), true);
requiereAutenticacion();
requierePermiso(Modulo::ASISTENCIAS, Permiso::CONSULTAR);
require_once("Models/Enums/Justificacion.php");


if(!empty($_POST)){
    $asistenciaObj = new Asistencia();
    //$asistenciaObj->mapearFormulario();
    $asistenciaObj->setterArray([
        "idDepartamento" => $_POST['idDepartamento'],
        "fecha" => $_POST['fecha'],
        "turno" => $_POST['turno']
    ]);
    $asistenciaObj->verAsistencias(true);
    die;
}


$departamentos = (new Division())->listar();
renderView();
// debug($trabajadores);