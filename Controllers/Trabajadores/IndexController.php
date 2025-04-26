<?php
requiereAutenticacion();
requierePermiso(Modulo::TRABAJADORES, Permiso::CONSULTAR);

$trabajadores = Trabajador::listar();

renderView();