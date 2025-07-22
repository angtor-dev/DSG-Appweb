<?php
requiereAutenticacion();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    exit;
}

header('Content-Type: application/json');
if (empty($_GET['id'])) {
    echo new NotificacionDTO(false, 'Se debe especificar una notificación para marcar como leída');
    exit;
}

/** @var Notificacion */
$notificacion = Notificacion::cargar($_GET['id'], true);
if ($notificacion === null) {
    echo new NotificacionDTO(false, 'No se encontró la notificación especificada');
    exit;
}

if ($notificacion->idUsuario !== $_SESSION['usuario']->id) {
    echo new NotificacionDTO(false, 'No tienes permiso para marcar esta notificación como leída');
    exit;
}

try {
    $notificacion->marcarLeida();
    echo new NotificacionDTO(true);
} catch (\Throwable $th) {
    echo new NotificacionDTO(false, 'Ocurrió un error al marcar la notificación como leída: ' . $th->getMessage());
}
