<?php
$_POST = json_decode(file_get_contents("php://input"), true);
requiereAutenticacion();
requierePermiso(Modulo::ASISTENCIAS, Permiso::CONSULTAR);
require_once("Models/Enums/Turno.php");
require_once("Models/Enums/Cargo.php");
require_once("Models/Enums/Justificacion.php");


if(!empty($_POST)){
    $asistenciaObj = new Asistencia();
    $asistenciaObj->mapearFormulario();
    $asistenciaObj->verAsistencias(true);
    die;
}


$departamentos = (new Departamento())->listar();
renderView();
// debug($trabajadores);