<?php
requiereAutenticacion();
requierePermiso(Modulo::AREAS, Permiso::ELIMINAR);
/**
 * @var Area $area
 */
$areaObj = new Area();
$areaObj->setTestingMode(true);
$areaObj->setterArray(["id" => $_GET['id']]);
$areaObj->eliminarArea();


redirigir(LOCAL_DIR."/Areas");