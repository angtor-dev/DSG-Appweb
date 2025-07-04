<?php
requiereAutenticacion();
requierePermiso(Modulo::TAREAS, Permiso::CONSULTAR);

$conteoTareas = [
    'activo' => (new Tarea())->contarPorEstado('activo'),
    'vencida' => (new Tarea())->contarPorEstado('vencida'),
    'cancelado' => (new Tarea())->contarPorEstado('cancelado')
];

if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    
    $estados = ['activo', 'vencida', 'cancelado', 'evaluada', 'comun'];
    $datos = [];
    
    foreach ($estados as $estado) {
        $tareas = (new Tarea())->listarPorEstado($estado);
       $datos[$estado] = array_map(function($tarea) {
    return [
        'id' => $tarea['id'],
        'area' => $tarea['area_nombre'] ?? '',
        'departamento' => $tarea['departamento_nombre'] ?? '',
        'descripcion' => htmlspecialchars($tarea['descripcion']),
        'fecha' => date('d/m/Y H:i', strtotime($tarea['fechaCreacion'])),
        'estado' => $tarea['estado_tarea']
    ];
}, $tareas);
    }
    
    echo json_encode($datos);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        // Validaciones comunes
        if (!isset($_POST['id'])) {
            throw new Exception('ID de tarea no proporcionado');
        }
        if (!isset($_POST['action'])) {
            throw new Exception('Acción no especificada');
        }

        $idTarea = (int)$_POST['id'];
        $action = $_POST['action'];
        $tarea = Tarea::cargar($idTarea);
        
        if (!$tarea) {
            throw new Exception('Tarea no encontrada');
        }

        // Procesar según la acción
        switch ($action) {
            case 'terminar':
                if ($tarea->getEstado() === 'terminado') {
                    throw new Exception('La tarea ya está terminada');
                }
                if ($tarea->getEstado() === 'cancelado') {
                    throw new Exception('No se puede terminar una tarea cancelada');
                }
                $tarea->terminar();
                Bitacora::registrar("Tarea terminada - ID: $idTarea");
                $message = 'Tarea marcada como terminada correctamente';
                break;
                
            case 'cancelar':
                if ($tarea->getEstado() === 'cancelado') {
                    throw new Exception('La tarea ya está cancelada');
                }
                $tarea->cancelar();
                Bitacora::registrar("Tarea cancelada - ID: $idTarea");
                $message = 'Tarea cancelada correctamente';
                break;
                
            default:
                throw new Exception('Acción no válida');
        }
        
        echo json_encode([
            'success' => true,
            'message' => $message,
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

// Vista normal
renderView();