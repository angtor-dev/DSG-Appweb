<?php
requiereAutenticacion();
requierePermiso(Modulo::ASISTENCIAS, Permiso::CONSULTAR);
require_once "Models/Asistencias.php";

$asistencias = Asistencia::listarAsistencias();



renderView();
// debug($trabajadores);