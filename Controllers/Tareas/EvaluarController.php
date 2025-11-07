<?php
requiereAutenticacion();
requierePermiso(Modulo::TAREAS, Permiso::CONSULTAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once 'Views/Tareas/_Evaluar.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    unset($_SESSION['errores']);
    header('Content-Type: application/json');
    $response = ['success' => false];

    // DIFERENCIACIÓN ENTRE CONSULTA Y REGISTRO
    if (isset($_POST['id']) && !isset($_POST['idTarea'])) {
        // CONSULTA POR ID (solo viene el ID)
        if (!is_numeric($_POST['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de tarea inválido'
            ]);
            exit;
        }

        $idTarea = intval($_POST['id']);
        $tareaCompleta = Tarea::obtenerPorId($idTarea);

        if ($tareaCompleta) {
            echo json_encode([
                'success' => true,
                'data' => [
                    'id' => $idTarea,
                    'tarea' => $tareaCompleta
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se encontró la tarea con ese ID'
            ]);
        }
        exit;
    } 
   else {
    $evaluacion = new Tarea();
    
    // Mapeo de datos en el controlador
    $datosEvaluacion = [
        'id' => (int)$_POST['idTarea'],
        'evaluacion' => [
            'ponderacion' => $_POST['ponderacion'] ?? '',
            'comentarios' => $_POST['comentarios'] ?? '',
            'aprobacion' => isset($_POST['aprobacion']) ? 1 : 0
        ],
        'materiales' => isset($_POST['materiales']) ? 
            (is_string($_POST['materiales']) ? json_decode($_POST['materiales'], true) : (array)$_POST['materiales']) 
            : null
    ];

    $evaluacion->setterArray($datosEvaluacion);
    
    if ($evaluacion->evaluar()) {
        $response = [
            'success' => true,
            'message' => "Evaluación registrada con éxito"
        ];
        Bitacora::registrar("Evaluación registrada para tarea: " . $_POST['idTarea']);
    } else {
        $response = [
            'success' => false,
            'errors' => $_SESSION['errores'] ?? ['Error desconocido al registrar la evaluación'],
            'message' => 'Error al registrar la evaluación'
        ];
    }
    
    echo json_encode($response);
    exit;
}
}