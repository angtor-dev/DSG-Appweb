<?php
requiereAutenticacion();
requierePermiso("bitacora", "consultar");
require_once "Models/Bitacora.php";

$bitacoras = Bitacora::listar();

renderView();