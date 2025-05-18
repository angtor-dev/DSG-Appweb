<?php
//requiereAutenticacion();
//requierePermiso("reporteAsistencias", "consultar");


require_once 'Models/Enums/Turno.php';

$departamentos = (new Departamento())->listar();

if(isset($_GET['fechaInicio']) && isset($_GET['hasta'])) {
    $fechaInicio = $_GET['fechaInicio'];
    $fechaFin = $_GET['hasta'];
    $idDepartamento = $_GET['departamento'] ?? null;
    $turno = $_GET['turno'] ?? null;
    $grupo = $_GET['agrupar'] ?? null;
    $asistencias = new Asistencia;
    $lista = $asistencias->reporte($fechaInicio, $fechaFin, $idDepartamento, $turno, $grupo);
}


renderView();