<?php
requiereAutenticacion();
requierePermiso(Modulo::NOTASENTREGA, Permiso::CONSULTAR);

$entradas = (new Entrada())->listar();
$articulos = (new Articulo())->listar();

renderView();