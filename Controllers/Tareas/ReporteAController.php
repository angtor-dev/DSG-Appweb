<?php
requiereAutenticacion();
requierePermiso(Modulo::TAREAS, Permiso::CONSULTAR);

if (isset($_GET['ajax']) && $_GET['ajax'] === 'estadisticas') {
    header('Content-Type: application/json');
    
    try {
        if (!isset($_GET['tipo'], $_GET['fechaInicio'], $_GET['fechaFin'])) {
            throw new Exception('Parámetros incompletos');
        }
        
        $tipo = $_GET['tipo'];
        $fechaInicio = $_GET['fechaInicio'];
        $fechaFin = $_GET['fechaFin'];
        $departamento = $_GET['departamento'] ?? null;
        
        $tarea = new Tarea();
        $datos = $tarea->obtenerEstadisticas($tipo, $fechaInicio, $fechaFin, $departamento);
        
        echo json_encode([
            'success' => true,
            'data' => $datos
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

else if (isset($_GET['ajax']) && $_GET['ajax'] === 'departamentos') {
    header('Content-Type: application/json');
    
    try {
        $db = Database::getInstance();
        $db->connect();
        
        $query = "SELECT id, nombre FROM division ORDER BY nombre";
        $stmt = $db->pdo()->prepare($query);
        $stmt->execute();
        
        echo json_encode([
            'success' => true,
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

require_once "Views/Tareas/_ReporteA.php";
