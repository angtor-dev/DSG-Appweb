<?php
requiereAutenticacion();
requierePermiso(Modulo::TAREAS, Permiso::REGISTRAR);


require_once "Views/Tareas/_Orden.php";
