<?php
requiereAutenticacion();
requierePermiso(Modulo::AREAS, Permiso::ELIMINAR);
/**
 * @var Area $area
 */
$areaObj = new Area();
$areaObj->setterArray(["id" => $_GET['id']]);
$areaObj->eliminarArea();


redirigir(LOCAL_DIR."/Areas");