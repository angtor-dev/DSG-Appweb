<?php
requiereAutenticacion();
requierePermiso(Modulo::TAREAS, Permiso::CONSULTAR);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    unset($_SESSION['errores']);
    
    $evaluacion = new tarea();
    $response = ['success' => false];

    if ($evaluacion->registrarEval($_POST)) {
        $response = [
            'success' => true,
            'message' => "Evaluación registrada con éxito"
        ];
        Bitacora::registrar("Evaluación registrada para asignación: " . $_POST['idAsignacion']);
    } else {
        $response = [
            'success' => false,
            'errors' => $_SESSION['errores'] ?? ['Error desconocido al registrar la evaluación'],
            'message' => 'Error al registrar la evaluación'
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

require_once 'Views/Tareas/_Evaluar.php';
