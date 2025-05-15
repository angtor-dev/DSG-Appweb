<?php
requiereAutenticacion();
requierePermiso(Modulo::AREAS, Permiso::ELIMINAR);

$area = Area::cargar($_GET['id']);

if (empty($area)) {
    $_SESSION['errores'][] = "El área que intenta eliminar no existe";
    redirigir(LOCAL_DIR."/Areas");
}

$subareas = $area->listarSubareas();

if (count($subareas) > 0) {
    $_SESSION['errores'][] = "Existen áreas que pertenecen a '".$area->getNombre(). 
        "'. Asegurate de eliminar todas sus sub-áreas primero.";
    redirigir(LOCAL_DIR."/Areas");
}

if ($area->eliminar(false)) {
    $_SESSION['exitos'][] = "Área eliminada con exito";
    Bitacora::registrar("Área '".$area->getNombre()."' eliminado");
}

redirigir(LOCAL_DIR."/Areas");