<?php
requiereAutenticacion();
requierePermiso(Modulo::ROLES, Permiso::CONSULTAR);

$rolObj = new Rol();
$roles = $rolObj->listar(1);

renderView();