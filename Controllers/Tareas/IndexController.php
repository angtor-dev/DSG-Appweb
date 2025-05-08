<?php
requiereAutenticacion();
requierePermiso(Modulo::TAREAS, Permiso::CONSULTAR);

$tareas = (new Tarea())->listar();

renderView();