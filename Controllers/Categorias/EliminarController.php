<?php
requiereAutenticacion();
requierePermiso(Modulo::CATEGORIAS, Permiso::ELIMINAR);

$categoria = Categoria::cargar($_GET['id']);

if (empty($categoria)) {
    $_SESSION['errores'][] = "La categoría que intenta eliminar no existe";
    redirigir(LOCAL_DIR."/Categorias");
}

if ($categoria->eliminar(false)) {
    $_SESSION['exitos'][] = "Categoría eliminada con exito";
    Bitacora::registrar("Categoría '".$categoria->getNombre()."' eliminada");
}

redirigir(LOCAL_DIR."/Categorias");