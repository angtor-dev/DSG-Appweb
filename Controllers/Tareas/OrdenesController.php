<?php
requiereAutenticacion();
requierePermiso(Modulo::TAREAS, Permiso::CONSULTAR);


if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    
    $estados = ['activo'];
    $datos = [];
    
   foreach ($estados as $estado) {
    $tareas = (new Tarea())->listarPorEstadoConPersonal($estado);
    $datos[$estado] = array_map(function($tarea) {
        return [
            'id' => $tarea['id'],
            'area' => $tarea['area_nombre'] ?? '',
            'departamento' => $tarea['departamento_nombre'] ?? '',
            'departamento_id' => $tarea['idDepartamento'],
            'descripcion' => htmlspecialchars($tarea['descripcion']),
            'fecha' => date('d/m/Y H:i', strtotime($tarea['fechaCreacion'])),
            'estado' => $tarea['estado_tarea'],
            'personal' => $tarea['personal'],
            'personal_nombre' => implode(', ', array_column($tarea['personal'], 'nombre_completo')),
            'personal_id' => array_column($tarea['personal'], 'id')
        ];
    }, $tareas);
}
    

    
    echo json_encode($datos);
    exit;
}
else if (isset($_POST['ids'])) {
    header('Content-Type: application/json');
    
    try {
        $ids = explode(',', $_POST['ids']);
        $ids = array_map('intval', $ids);
        $ids = array_filter($ids);
        
        if (empty($ids)) {
            throw new Exception("No se proporcionaron IDs válidos");
        }
        
        $tareas = (new Tarea())->obtenerTareasParaOrdenes($ids);
        
        echo json_encode([
            'success' => true,
            'data' => $tareas
        ]);
        exit;
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
        exit;
    }
}
else if ($_GET['modal']) {
    require_once "Views/Tareas/_Ordenes.php";
}


else {
    http_response_code(405);
    exit;
}
