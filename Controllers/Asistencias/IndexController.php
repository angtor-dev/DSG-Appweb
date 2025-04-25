<?php
requiereAutenticacion();
requierePermiso(Modulo::ASISTENCIAS, Permiso::CONSULTAR);
require_once "Models/Asistencia.php";

$asistencias = Asistencia::listarAsistencias();



renderView();
// debug($trabajadores);