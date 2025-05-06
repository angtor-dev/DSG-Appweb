<?php
requiereAutenticacion();
requierePermiso("roles", "actualizar");

$rol = Rol::cargar($_GET['id']);
$permisos = Permiso::listarPorRelacion($rol->id, "Rol");
$moduloObj = new Modulo();
$modulos = $moduloObj->listar();

renderComponent("_ModalPermisos");