<?php
requiereAutenticacion();
requierePermiso(Modulo::CATEGORIAS, Permiso::ACTUALIZAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    if (empty($_GET['id'])) {
        $_SESSION['errores'][] = "Se debe especificar una categoría para actualizar";
        redirigir(LOCAL_DIR."/Categorias");
    }

    $categoria = Categoria::cargar($_GET['id']);

    if (is_null($categoria)) {
        $_SESSION['errores'][] = "La categoría que intenta actulizar no existe";
        redirigir(LOCAL_DIR."/Categorias");
    }

    $categorias = $categoria->listar();

    require_once "Views/Categorias/_Actualizar.php";
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $categoria = new Categoria();
    $categoria->mapearFormulario();

    if ($categoria->esValido() && $categoria->actualizar()) {
        $_SESSION['exitos'][] = "Categoría actualizada con exito";
        Bitacora::registrar("Categoría '".$categoria->getNombre()."' actualizada");
    }

    redirigir(LOCAL_DIR."/Categorias");
}
else
{
    http_response_code(405);
    exit;
}