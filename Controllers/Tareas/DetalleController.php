<?php
requiereAutenticacion();
requierePermiso(Modulo::TAREAS, Permiso::CONSULTAR);

$idTarea = $_GET['id'] ?? null;

if (!$idTarea || !ctype_digit($idTarea)) {
    redirigir('/Tareas');
}

$tarea = (new Tarea())->obtenerPorId($idTarea);

if (!$tarea) {
    redirigir('/Tareas');
}

require_once "Views/Tareas/_Detalle.php";   