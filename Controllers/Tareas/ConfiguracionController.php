<?php
requiereAutenticacion();
requierePermiso(Modulo::TAREAS, Permiso::CONSULTAR); // Necesitamos permiso de edición

$idTarea = $_GET['id'] ?? null;

// Validación del ID
if (!$idTarea || !ctype_digit($idTarea)) {
    redirigir('/Tareas');
}

// Obtener la tarea
$tarea = (new Tarea())->obtenerPorId($idTarea);

if (!$tarea) {
    redirigir('/Tareas');
}

// Verificar que sea una tarea común (si aplica)
if ($tarea->getEstado() !== 'comun') {
    redirigir('/Tareas');
}

// Obtener configuración existente (simulado - deberías implementar tu modelo)
$configuracion = [
    'activa' => true,
    'periodicidad' => 'semanal',
    'dias_semana' => ['lunes', 'miercoles', 'viernes'],
    'hora_ejecucion' => '08:00',
    'fecha_inicio' => date('Y-m-d'),
    'fecha_fin' => null
];

// Si es una solicitud POST (guardar configuración)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Procesar los datos del formulario
    $nuevaConfiguracion = [
        'activa' => isset($_POST['activa']),
        'periodicidad' => $_POST['periodicidad'] ?? null,
        'dias_semana' => $_POST['dias_semana'] ?? [],
        'dia_mes' => $_POST['dia_mes'] ?? null,
        'hora_ejecucion' => $_POST['hora_ejecucion'] ?? '08:00',
        'fecha_inicio' => $_POST['fecha_inicio'] ?? date('Y-m-d'),
        'fecha_fin' => $_POST['fecha_fin'] ?? null
    ];
    
    // Aquí iría la lógica para guardar en la base de datos
    // $guardado = (new TareaComun())->guardarConfiguracion($idTarea, $nuevaConfiguracion);
    
    // Por ahora simulamos un guardado exitoso
    if (true /* $guardado */) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Configuración guardada correctamente']);
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error al guardar la configuración']);
        exit;
    }
}

// Mostrar la vista de configuración
require_once "Views/Tareas/_Configuracion.php";