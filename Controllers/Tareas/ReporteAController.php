<?php
requiereAutenticacion();
requierePermiso(Modulo::TAREAS, Permiso::CONSULTAR);


require_once "Views/Tareas/_ReporteA.php";
