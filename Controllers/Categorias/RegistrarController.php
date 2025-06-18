<?php
requiereAutenticacion();
requierePermiso(Modulo::CATEGORIAS, Permiso::REGISTRAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    $categoriaObj = new Categoria();
    $categorias = $categoriaObj->listar();
    
    require_once "Views/Categorias/_Registrar.php";
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $color = substr($_POST['color'] ?? '', 1); // Eliminar el símbolo '#'

    $categoria = new Categoria();
    $categoria->setDatos($nombre, $descripcion, $color);

    if ($categoria->esValido() && $categoria->registrar()) {
        $_SESSION['exitos'][] = "Categoría registrada con exito";
        Bitacora::registrar("Categoría '".$categoria->getNombre()."' registrada");
    }

    redirigir(LOCAL_DIR."/Categorias");
}
else
{
    http_response_code(405);
    exit;
}