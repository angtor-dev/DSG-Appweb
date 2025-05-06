<?php
requiereAutenticacion();
requierePermiso("bitacora", "consultar");

$bitacoras = Bitacora::listar();

renderView();