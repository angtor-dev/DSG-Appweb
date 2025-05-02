<?php
requiereAutenticacion();
requierePermiso(Modulo::MEDIDAS, Permiso::CONSULTAR);

$medidas = Medida::listar();

renderView();