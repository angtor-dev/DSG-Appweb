<?php
requiereAutenticacion();
requierePermiso(Modulo::TRABAJADORES, Permiso::CONSULTAR);

$trabajadorObj = new Trabajador();

$trabajadores = $trabajadorObj->listar();

renderView();