<?php
requiereAutenticacion();
requierePermiso(Modulo::TAREAS, Permiso::REGISTRAR);



if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Cargar datos necesarios para el formulario

    $trabajadorObj = new Trabajador(); // mi vista te sigue cuando cambias de archivo 
    $trabajadores = $trabajadorObj->listar(0);

    $departamentoObj = new Departamento();
    $departamentos = $departamentoObj->listar();
    
    $areaObj = new Area();
    $areas = $areaObj->listar();
    
    require_once "Views/Tareas/_Registrar.php";
}


elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Limpiar errores previos de sesión si existen
    unset($_SESSION['errores']);
    
    $tarea = new Tarea();
    $response = ['success' => false];

    
    if ($tarea->registrar($_POST)) {
        $tareaCompleta = Tarea::obtenerPorId($tarea->id);
        $response = [
            'success' => true,
            'message' => "Tarea registrada con éxito",
            'data' => [
                'id' => $tarea->id,
                'tarea' => $tareaCompleta, // Incluir todos los datos de la tarea
                'redirect' => 'Tareas/Orden/' . $tarea->id // Opcional: ruta para obtener la orden
            ]
        ];
        Bitacora::registrar("Tarea registrada: " . $tarea->descripcion);
    } else {
        // Si hay errores en la sesión, los pasamos a la respuesta
        $response = [
            'success' => false,
            'errors' => $_SESSION['errores'] ?? ['Error desconocido al registrar la tarea'],
            'message' => 'Error al registrar la tarea'
        ];
    }

    // Devolver respuesta JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

else {
    http_response_code(405);
    exit;
}