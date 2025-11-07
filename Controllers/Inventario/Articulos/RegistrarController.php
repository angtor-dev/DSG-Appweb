<?php
requiereAutenticacion();
requierePermiso(Modulo::ARTICULOS, Permiso::REGISTRAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    $categorias = (new Categoria())->listar();
    usort($categorias, function($a, $b) {
        return strcmp($a->getNombre(), $b->getNombre());
    });
    $medidas = (new Medida())->listar();
    
    require_once "Views/Inventario/Articulos/_Registrar.php";
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $articulo = new Articulo();
    $idCategoria = $_POST['idCategoria'];
    $idMedida = $_POST['idMedida'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $cantidad = 0;
    $esConsumible = isset($_POST['esConsumible']) ? true : false;
    
    $articulo->setDatos(null, $idCategoria, $idMedida, $nombre, $descripcion, $cantidad, $esConsumible);

    if ($articulo->esValido() && $articulo->registrar()) {
        $_SESSION['exitos'][] = "Artículo registrado con exito";
        Bitacora::registrar("Artículo '".$articulo->getNombre()."' registrado");
    }

    redirigir(LOCAL_DIR."/Inventario/Articulos");
}
else
{
    http_response_code(405);
    exit;
}