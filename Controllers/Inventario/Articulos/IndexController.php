<?php
requiereAutenticacion();
requierePermiso(Modulo::ARTICULOS, Permiso::CONSULTAR);

$articuloObj = new Articulo();
$articulos = $articuloObj->listar();

renderView();