<?php
requiereAutenticacion();
requierePermiso("roles", "actualizar");

$rol = Rol::cargar($_GET['id']);
$permisos = Permiso::listarPorRelacion($rol->id, "Rol");
$modulos = Modulo::listar();

renderComponent("_ModalPermisos");