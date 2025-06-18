<?php
requiereAutenticacion();
requierePermiso(Modulo::MOVIMIENTOS, Permiso::CONSULTAR);

$movimientos = (new Movimiento())->listar();
$articulos = (new Articulo())->listar();

renderView();