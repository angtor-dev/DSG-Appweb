<?php
requiereAutenticacion();
requierePermiso(Modulo::MEDIDAS, Permiso::ELIMINAR);

$medida = Medida::cargar($_GET['id']);

if (empty($medida)) {
    $_SESSION['errores'][] = "La medida que intenta eliminar no existe";
    redirigir(LOCAL_DIR."/Medidas");
}

if ($medida->eliminar(false)) {
    $_SESSION['exitos'][] = "Medida eliminada con exito";
    Bitacora::registrar("Medida '".$medida->getUnidad()."' eliminada");
}

redirigir(LOCAL_DIR."/Medidas");