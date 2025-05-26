<?php
requiereAutenticacion();
requierePermiso(Modulo::ARTICULOS, Permiso::REGISTRAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    $categorias = (new Categoria())->listar();
    $medidas = (new Medida())->listar();
    
    require_once "Views/Inventario/Articulos/_Registrar.php";
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $articulo = new Articulo();
    $articulo->mapearFormulario();

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