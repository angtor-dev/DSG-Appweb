<?php
requiereAutenticacion();
requierePermiso(Modulo::TAREAS, Permiso::CONSULTAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once 'Views/Tareas/_Cancelar.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    unset($_SESSION['errores']);
    header('Content-Type: application/json');
    $response = ['success' => false];

    // DIFERENCIACIÓN ENTRE CONSULTA Y REGISTRO
    if (isset($_POST['id']) && !isset($_POST['idTarea'])) {
        // CONSULTA POR ID (solo viene el ID para cargar datos)
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
        // REGISTRO DE CANCELACIÓN (viene idTarea + datos de cancelación)
        try {
            // Validaciones básicas
            if (!isset($_POST['idTarea']) || !is_numeric($_POST['idTarea'])) {
                throw new Exception('ID de tarea inválido');
            }

            if (!isset($_POST['comentarios']) || empty(trim($_POST['comentarios']))) {
                throw new Exception('Las observaciones son obligatorias para cancelar la tarea');
            }

            $idTarea = (int)$_POST['idTarea'];
            $tarea = Tarea::cargar($idTarea);
            
            if (!$tarea) {
                throw new Exception('Tarea no encontrada');
            }

            // Verificar que la tarea no esté ya cancelada
            if ($tarea->getEstado() === 'cancelado') {
                throw new Exception('La tarea ya está cancelada');
            }

            
            
            $datosCancelacion = [
                'id' => $idTarea,
                'observaciones' => trim($_POST['comentarios']),
                'materiales' => []
            ];

            // PROCESAR MATERIALES IGUAL QUE EN EVALUARCONTROLLER
            if (isset($_POST['materiales'])) {
                $materiales = $_POST['materiales'];
                
                // Si viene como JSON string, decodificar (igual que en EvaluarController)
                if (is_string($materiales)) {
                    $materiales = json_decode($materiales, true);
                }
                
                // Si es array, procesar
                if (is_array($materiales)) {
                    foreach ($materiales as $material) {
                        if (isset($material['id']) && is_numeric($material['id'])) {
                           $datosCancelacion['materiales'][] = [
                            'id' => (int)$material['id'], 
                            'utilizado' => isset($material['utilizado']) ? (float)$material['utilizado'] : 0,
                            'devuelto' => isset($material['devuelto']) ? (float)$material['devuelto'] : 0
                        ];
                        }
                    }
                }
            }

            // Crear instancia y asignar datos
            $tareaCancelar = new Tarea();
            $tareaCancelar->setterArray($datosCancelacion);
            
            // Ejecutar cancelación
            if ($tareaCancelar->cancelar()) {

                $response = [
                    'success' => true,
                    'message' => "Tarea cancelada correctamente"
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => "No se pudo cancelar la tarea"
                ];
            }
            
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $_SESSION['errores'] ?? []
            ];
            error_log("Error al cancelar tarea: " . $e->getMessage());
        }
        
        echo json_encode($response);
        exit;
    }
}