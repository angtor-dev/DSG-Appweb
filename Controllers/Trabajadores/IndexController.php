<?php
requiereAutenticacion();
requierePermiso(Modulo::TRABAJADORES, Permiso::CONSULTAR);
require_once("Models/Enums/Turno.php");
require_once("Models/Enums/Cargo.php");

$trabajadores = Trabajador::listar();

renderView();