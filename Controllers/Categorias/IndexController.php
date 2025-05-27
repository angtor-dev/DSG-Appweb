<?php
requiereAutenticacion();
requierePermiso(Modulo::CATEGORIAS, Permiso::CONSULTAR);

$categoriaObj = new Categoria();
$categorias = $categoriaObj->listar();

renderView();