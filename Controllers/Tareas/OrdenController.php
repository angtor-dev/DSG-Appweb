<?php
requiereAutenticacion();
requierePermiso(Modulo::TAREAS, Permiso::REGISTRAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once "Views/Tareas/_Orden.php";
}

elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $response = ['success' => false];

    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
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
                'tarea' => $tareaCompleta,
                'redirect' => 'Tareas/Orden/' . $idTarea
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
    http_response_code(405);
    exit;
}
