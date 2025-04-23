<?php
requiereAutenticacion();
requierePermiso(Modulo::TRABAJADORES, Permiso::CONSULTAR);
require_once "Models/Trabajador.php";

$trabajadores = Trabajador::listar();

renderView();