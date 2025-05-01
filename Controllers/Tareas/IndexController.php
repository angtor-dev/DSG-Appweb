<?php
requiereAutenticacion();
requierePermiso(Modulo::TAREAS, Permiso::CONSULTAR);

$tareas = Tarea::listar();

renderView();