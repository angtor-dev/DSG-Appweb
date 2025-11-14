<?php
requiereAutenticacion();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    exit;
}

header('Content-Type: application/json');
try {
    Notificacion::marcarTodasLeidasPorUsuario($_SESSION['usuario']->id);
    echo new NotificacionDTO(true);
} catch (\Throwable $th) {
    echo new NotificacionDTO(false, 'Ocurrió un error al marcar las notificaciones como leídas: ' . $th->getMessage());
}