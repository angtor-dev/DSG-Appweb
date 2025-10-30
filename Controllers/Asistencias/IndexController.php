<?php
$_POST = json_decode(file_get_contents("php://input"), true);
requiereAutenticacion();
requierePermiso(Modulo::ASISTENCIAS, Permiso::CONSULTAR);
require_once("Models/Enums/Justificacion.php");


if(!empty($_POST)){
    if(isset($_POST['consultar']) && $_POST['consultar']){
        $asistenciaObj = new Asistencia();
        //$asistenciaObj->mapearFormulario();
        $asistenciaObj->setterArray([
            "idDepartamento" => $_POST['idDepartamento'],
            "fecha" => $_POST['fecha'],
            "turno" => $_POST['turno']
        ]);
        $asistenciaObj->verAsistenciasSemanal(true);
    }
    else if (isset($_POST['consultarDia']) && $_POST['consultarDia']) {
        // phpcs:disable
        if(ASISTENCIAS_SEMANALES) die;// solo para asistencias diarias desactivadas por el momento
        $asistenciaObj = new Asistencia();
        //$asistenciaObj->mapearFormulario();
        $asistenciaObj->setterArray([
            "idDepartamento" => $_POST['idDepartamento'],
            "fecha" => $_POST['fecha'],
            "turno" => $_POST['turno']
        ]);
        $asistenciaObj->verAsistencias(true);
        // phpcs:enable
    }
    die;
}


$departamentos = (new Division())->listar();
renderView();
// debug($trabajadores);