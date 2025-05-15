<?php
//requiereAutenticacion();
//requierePermiso(Modulo::TAREAS, Permiso::CONSULTAR);

// Endpoint para AJAX
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    
    $estados = ['activo', 'vencida', 'cancelado', 'evaluada'];
    $datos = [];
    
    foreach ($estados as $estado) {
        $tareas = (new Tarea())->listarPorEstado($estado);
        $datos[$estado] = array_map(function($tarea) {
            return [
                'id' => $tarea->id,
                'area' => $tarea->area ? $tarea->area->getNombre() : '',
                'departamento' => $tarea->departamento ? $tarea->departamento->getNombre() : '',
                'descripcion' => htmlspecialchars($tarea->descripcion),
                'fecha' => date('d/m/Y H:i', strtotime($tarea->fechaCreacion)),
                'estado' => $tarea->getEstado()
            ];
        }, $tareas);
    }
    
    echo json_encode($datos);
    exit;
}

// Vista normal
renderView();