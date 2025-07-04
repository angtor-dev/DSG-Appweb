<?php
requiereAutenticacion();
requierePermiso(Modulo::TAREAS, Permiso::REGISTRAR);



if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Cargar datos necesarios para el formulario

    $trabajadorObj = new Trabajador(); // mi vista te sigue cuando cambias de archivo 
    $trabajadores = $trabajadorObj->listar(0);

    $departamentoObj = new Division();
    $departamentos = $departamentoObj->listar();

    $turnosOptions = Turno::getTurnosOptions();
    
    $areaObj = new Area();
    $areas = $areaObj->listar();
    
    require_once "Views/Tareas/_Registrar.php";
}

elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    unset($_SESSION['errores']);
    
    $tarea = new Tarea();
    $response = ['success' => false];

    // Mapeo de datos en el controlador
  $datos = [
    'idArea' => (int)$_POST['idArea'],
    'idDepartamento' => (int)$_POST['idDepartamento'],
    'descripcion' => trim($_POST['descripcion']),
    'turno' => $_POST['turno'],
    'fecha_inicio' => $_POST['fecha_inicio'],
    'idSupervisor' => (int)($_POST['supervisor'] ?? 0),
    'personalAsignado' => isset($_POST['personal']) ? (array)$_POST['personal'] : null,
    'materiales' => isset($_POST['materiales']) ? 
        (is_string($_POST['materiales'])) ? json_decode($_POST['materiales'], true) : (array)$_POST['materiales'] 
        : null
];

    $tarea->setterArray($datos);

    if ($tarea->registrar()) {
        $tareaCompleta = Tarea::obtenerPorId($tarea->getId());
        $response = [
            'success' => true,
            'message' => "Tarea registrada con éxito",
            'data' => [
                'id' => $tarea->getId(),
                'tarea' => $tareaCompleta,
                'redirect' => 'Tareas/Orden/' . $tarea->getId()
            ]
        ];
        Bitacora::registrar("Tarea registrada: " . $tarea->getDescripcion());
    } else {
        $response = [
            'success' => false,
            'errors' => $_SESSION['errores'] ?? ['Error desconocido al registrar la tarea'],
            'message' => 'Error al registrar la tarea'
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

else {
    http_response_code(405);
    exit;
}