<?php
requiereAutenticacion();
requierePermiso("roles", "actualizar");

$rol = Rol::cargar($_GET['id'], true);
$permisos = Permiso::listarPorRelacion($rol->id, "Rol", null, true);
$moduloObj = new Modulo();
$modulos = $moduloObj->listar();

renderComponent("_ModalPermisos");