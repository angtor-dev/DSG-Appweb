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
        // REGISTRO DE EVALUACIÓN (viene el formulario completo)
        $evaluacion = new Tarea();
        
        if ($evaluacion->evaluar($_POST)) {
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