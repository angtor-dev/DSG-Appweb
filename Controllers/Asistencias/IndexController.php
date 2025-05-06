<?php
requiereAutenticacion();
requierePermiso(Modulo::ASISTENCIAS, Permiso::CONSULTAR);
require_once("Models/Enums/Turno.php");
require_once("Models/Enums/Cargo.php");

renderView();
// debug($trabajadores);