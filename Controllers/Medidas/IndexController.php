<?php
requiereAutenticacion();
requierePermiso(Modulo::MEDIDAS, Permiso::CONSULTAR);
require_once "Models/Medida.php";

$medidas = Medida::listar();

renderView();