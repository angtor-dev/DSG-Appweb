<?php
requiereAutenticacion();
requierePermiso(Modulo::TAREAS, Permiso::REGISTRAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Cargar datos necesarios para el formulario
    $departamentoObj = new Departamento();
    $departamentos = $departamentoObj->listar();
    
    $areaObj = new Area();
    $areas = $areaObj->listar();
    
    require_once "Views/Tareas/_Registrar.php";
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {


    // RegistrarController.php
    $tarea = new Tarea();

    if ($tarea->registrar($_POST)) {
        $_SESSION['exitos'][] = "Tarea registrada con éxito";
        Bitacora::registrar("Tarea registrada: " . $tarea->descripcion);
    } else {
        // Los errores ya fueron agregados a $_SESSION por el modelo
    }

    redirigir(LOCAL_DIR."/Tareas");


}
else {
    http_response_code(405);
    exit;
}