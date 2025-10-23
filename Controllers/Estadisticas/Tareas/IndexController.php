<?php
requiereAutenticacion();
requierePermiso(Modulo::TAREAS, Permiso::CONSULTAR);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    try {
        $tipo = $_POST['tipo'] ?? null;
        $fechaInicio = $_POST['fecha_inicio'] ?? null;
        $fechaFin = $_POST['fecha_fin'] ?? null;
        $departamento = $_POST['departamento'] ?? null;

        if (!$tipo || !$fechaInicio || !$fechaFin) {
            throw new Exception('Faltan parámetros obligatorios');
        }

        switch ($tipo) {
            case 'recurso_consumible':
                $result = Tarea::recursoConsumibleMasUtilizado($fechaInicio, $fechaFin);
                break;
            case 'mes_mas_tareas':
                $result = Tarea::mesConMasTareas($fechaInicio, $fechaFin);
                break;
            case 'departamento_mas_tareas':
                $result = Tarea::departamentoConMasTareas($fechaInicio, $fechaFin);
                break;
            case 'trabajador_mas_tareas':
                $result = Tarea::trabajadorConMasTareas($fechaInicio, $fechaFin, $departamento);
                break;
            default:
                throw new Exception('Tipo de estadística no soportado');
        }

        echo json_encode(['success' => true, 'data' => $result]);
    } catch (Exception $ex) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $ex->getMessage()]);
    }
    exit;
}



else if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    header('Content-Type: application/json');

    try {
        // Asumiendo que tu modelo está bien incluido
        $departamentos = Tarea::departamentosConTrabajadores(); // Usa el nombre correcto del modelo

        echo json_encode([
            'success' => true,
            'data' => $departamentos
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

renderView("Estadisticas/tareas", "");