<?php
$_POST = json_decode(file_get_contents("php://input"), true);

requiereAutenticacion();
requierePermiso("reporteasistencias", "consultar");
//requierePermiso("reporteAsistencias", "consultar");
// TODO validar permisos



$departamentos = (new Division())->listar();
if(!empty($_POST)) {
    
    if(isset($_POST['action']) && $_POST['action'] == "consultar") {
        http_response_code(200);
        $asistencias = new Asistencia;
        $asistencias->reporte(
            $_POST['fechaInicio'],
            $_POST['hasta'],
            $_POST['departamento']??null,
            $_POST['turno']??null,
            $_POST['agrupar']??null,
            true);
        die;
    }
    else{
        http_response_code(405);
        exit;
    }
        
}

// if(isset($_GET['fechaInicio']) && isset($_GET['hasta'])) {
//     $fechaInicio = $_GET['fechaInicio'];
//     $fechaFin = $_GET['hasta'];
//     $idDepartamento = $_GET['departamento'] ?? null;
//     $turno = $_GET['turno'] ?? null;
//     $grupo = $_GET['agrupar'] ?? null;
//     $asistencias = new Asistencia;
//     $lista = $asistencias->reporte($fechaInicio, $fechaFin, $idDepartamento, $turno, $grupo);
//     $lista = $lista['data'];
// }


renderView();