<?php
cargarPost();

//requiereAutenticacion();
//requierePermiso("estadisticasasistencias", "consultar");
// TODO validar permisos modificar bd

if(!empty($_POST['fechaIn']) && !empty($_POST['fechaOut'])) {

    $asistencias = new Asistencia();

    $asistencias->setterArray([
        "fechaIn" => $_POST["fechaIn"],
        "fechaOut" => $_POST["fechaOut"],
        "idTrabajador" => $_POST["idTrabajador"],
        "idDepartamento" => $_POST["idDepartamento"]
    ]);
    $asistencias->reporteEstadistica(true);
    die;
    
}



$departamentos = (new Division())->listar();


renderView("Estadisticas/asistencias", "");