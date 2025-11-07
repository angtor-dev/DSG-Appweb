<?php
requiereAutenticacion();
requierePermiso(Modulo::ARTICULOS, Permiso::ELIMINAR);
/**
 * @var Area
 */
$objArticulo = Articulo::cargar($_GET['id']);

if (empty($objArticulo)) {
    $_SESSION['errores'][] = "El artículo que intenta eliminar no existe";
    redirigir(LOCAL_DIR."/Inventario/Articulos");
}

if ($objArticulo->eliminar(false)) {
    $_SESSION['exitos'][] = "Artículo eliminada con exito";
    Bitacora::registrar("Artículo '".$objArticulo->getNombre()."' eliminado");
}

redirigir(LOCAL_DIR."/Inventario/Articulos");