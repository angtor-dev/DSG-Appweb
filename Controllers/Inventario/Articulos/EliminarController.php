<?php
requiereAutenticacion();
requierePermiso(Modulo::ARTICULOS, Permiso::ELIMINAR);

$area = Articulo::cargar($_GET['id']);

if (empty($area)) {
    $_SESSION['errores'][] = "El artículo que intenta eliminar no existe";
    redirigir(LOCAL_DIR."/Inventario/Articulos");
}

if ($area->eliminar(false)) {
    $_SESSION['exitos'][] = "Artículo eliminada con exito";
    Bitacora::registrar("Artículo '".$area->getNombre()."' eliminado");
}

redirigir(LOCAL_DIR."/Inventario/Articulos");