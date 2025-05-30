<?php
requiereAutenticacion();
requierePermiso(Modulo::ARTICULOS, Permiso::ACTUALIZAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    if (empty($_GET['id'])) {
        $_SESSION['errores'][] = "Se debe especificar un articulo para actualizar";
        redirigir(LOCAL_DIR."/Inventario/Articulos");
    }

    $articulo = Articulo::cargar($_GET['id']);

    if (is_null($articulo)) {
        $_SESSION['errores'][] = "El articulo que intenta actulizar no existe";
        redirigir(LOCAL_DIR."/Inventario/Articulos");
    }

    $categorias = (new Categoria())->listar();
    $medidas = (new Medida())->listar();

    require_once "Views/Inventario/Articulos/_Actualizar.php";
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $articulo = new Articulo();
    $articulo->mapearFormulario();

    if ($articulo->esValido() && $articulo->actualizar()) {
        $_SESSION['exitos'][] = "Articulo actualizado con exito";
        Bitacora::registrar("Articulo '".$articulo->getNombre()."' actualizado");
    }

    redirigir(LOCAL_DIR."/Inventario/Articulos");
}
else
{
    http_response_code(405);
    exit;
}