<?php
requiereAutenticacion();
requierePermiso(Modulo::TAREAS, Permiso::ELIMINAR);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        if (!isset($_POST['id'])) {
            throw new Exception('ID de tarea no proporcionado');
        }

        $idTarea = (int)$_POST['id'];
        $tarea = Tarea::cargar($idTarea);
        
        if (!$tarea) {
            throw new Exception('Tarea no encontrada');
        }

        // Verificar si la tarea ya está cancelada
        if ($tarea->getEstado() === 'cancelado') {
            throw new Exception('La tarea ya está cancelada');
        }

        // Cancelar la tarea
        $tarea->cancelar();
        
        // Registrar en bitácora
        Bitacora::registrar("Tarea cancelada - ID: $idTarea");
        
        echo json_encode([
            'success' => true,
            'message' => 'Tarea cancelada correctamente',
            'id' => $idTarea
        ]);
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    
    exit;
}
else {
    http_response_code(405); // Método no permitido
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}