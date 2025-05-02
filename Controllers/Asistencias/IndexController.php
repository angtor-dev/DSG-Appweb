<?php
requiereAutenticacion();
requierePermiso(Modulo::ASISTENCIAS, Permiso::CONSULTAR);

$asistencias = Asistencia::listarAsistencias();



renderView();
// debug($trabajadores);