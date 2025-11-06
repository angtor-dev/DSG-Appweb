<?php
requiereAutenticacion();
requierePermiso(Modulo::ARTICULOS, Permiso::CONSULTAR);

$objArticulo = new Articulo();
$articulos = $objArticulo->listar();

renderView();