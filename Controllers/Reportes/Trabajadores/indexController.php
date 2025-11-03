<?php
cargarPost();
requiereAutenticacion();
requierePermiso("reporteasistencias", "consultar");
//requierePermiso("reporteAsistencias", "consultar");
// TODO validar permisos



$departamentos = (new Division())->listar();
if(!empty($_POST)) {
    
    if(isset($_POST['action']) && $_POST['action'] == "consultar") {
        http_response_code(200);
        $asistencias = new Trabajador();
        $reporte = $asistencias->reporte(
            fechaInicio: $_POST['fechaInicio'],
            fechaFin: $_POST['hasta'],
            division: $_POST['departamento']??null,
            turno: $_POST['turno']??null,
            cargo: $_POST['cargo']??null,
            estado: $_POST['activo']??null,
            grupo: $_POST['agrupar']??null);
        echo json_encode($reporte);
        die;
    }
    else{
        http_response_code(405);
        exit;
    }
        
}

renderView("Trabajadores", "Reportes/");