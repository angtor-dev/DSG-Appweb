<?php
requiereAutenticacion();
requierePermiso(Modulo::AJUSTES, Permiso::CONSULTAR);

$ajusteObj = new Ajuste();
$ajustes = $ajusteObj->listar();

renderView();