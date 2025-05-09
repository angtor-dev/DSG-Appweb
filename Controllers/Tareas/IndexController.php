<?php
requiereAutenticacion();
requierePermiso(Modulo::TAREAS, Permiso::CONSULTAR);

// Obtener tareas filtradas por estado
$tareasActivas = (new Tarea())->listarPorEstado('activo');
$tareasVencidas = (new Tarea())->listarPorEstado('vencida');
$tareasComunes = (new Tarea())->listarPorEstado('comun');


renderView();