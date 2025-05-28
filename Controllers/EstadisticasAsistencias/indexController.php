<?php
cargarPost();

//requiereAutenticacion();
//requierePermiso("estadisticasasistencias", "consultar");
// TODO validar permisos modificar bd

if(!empty($_POST['fechaIn']) && !empty($_POST['fechaOut'])) {

    $asistencias = new Asistencia();

    $asistencias->mapearFormulario();
    $asistencias->reporteEstadistica(true);
    die;
    
}



$departamentos = (new Departamento())->listar();


renderView("Estadisticas/asistencias", "");