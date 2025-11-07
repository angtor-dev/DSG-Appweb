<?php
requiereAutenticacion();
requierePermiso(Modulo::TAREAS, Permiso::CONSULTAR);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        $tipo = $_POST['tipo_reporte'] ?? null;
        $fechaInicio = $_POST['fechaInicio'] ?? null;
        $fechaFin = $_POST['fechaFin'] ?? null;
        $filtroTrabajador = $_POST['filtroTrabajador'] ?? null;
        $filtroDivision = $_POST['filtroDivision'] ?? null;

        if (!$tipo || !$fechaInicio || !$fechaFin) {
            throw new Exception('Faltan parámetros obligatorios');
        }

        switch ($tipo) {
            case 'productividad_trabajador':
                $result = Tarea::reporteProductividadTrabajador($fechaInicio, $fechaFin, $filtroTrabajador);
                break;
            case 'rendimiento_division':
                $result = Tarea::reporteRendimientoDivision($fechaInicio, $fechaFin, $filtroDivision);
                break;
            case 'general_extenso':
            $result = Tarea::reporteGeneralExtenso($fechaInicio, $fechaFin);
            break;
            default:
                throw new Exception('Tipo de reporte no soportado');
        }

        echo json_encode([
            'success' => true, 
            'data' => $result,
            'filtros' => [
                'tipo' => $tipo,
                'fechaInicio' => $fechaInicio,
                'fechaFin' => $fechaFin
            ]
        ]);
        
    } catch (Exception $ex) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => $ex->getMessage()
        ]);
    }
    exit;
}

// Para cargar datos iniciales (trabajadores, divisiones)
else if (isset($_GET['ajax']) && $_GET['ajax'] === 'cargar_datos') {
    header('Content-Type: application/json');
    
    try {
        $trabajadores = Tarea::obtenerTrabajadoresActivos();
        $divisiones = Tarea::obtenerDivisionesActivas();
        
        echo json_encode([
            'success' => true,
            'data' => [
                'trabajadores' => $trabajadores,
                'divisiones' => $divisiones
            ]
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

renderView("Reportes/tareas", "");