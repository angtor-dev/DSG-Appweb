<?php
requiereAutenticacion();
requierePermiso("bitacora", "consultar");

$bitacoraObj = new Bitacora();
$bitacoras = $bitacoraObj->listar();

renderView();