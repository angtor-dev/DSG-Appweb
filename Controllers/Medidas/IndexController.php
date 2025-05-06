<?php
requiereAutenticacion();
requierePermiso(Modulo::MEDIDAS, Permiso::CONSULTAR);

$medidaObj = new Medida();
$medidas = $medidaObj->listar();

renderView();