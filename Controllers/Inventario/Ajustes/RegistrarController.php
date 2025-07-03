<?php
requiereAutenticacion();
requierePermiso(Modulo::AJUSTES, Permiso::REGISTRAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    $articulos = (new Articulo())->listar();
    require_once "Views/Inventario/Ajustes/_Registrar.php";
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $ajuste = new Ajuste();

    $idInventario = $_POST['idInventario'];
    $cantidad = $_POST['cantidad'];
    $descripcion = $_POST['descripcion'];
    $fechaIncidente = $_POST['fechaIncidente'];

    $ajuste->setDatos($idInventario, $cantidad, $descripcion, $fechaIncidente);

    if ($ajuste->esValido() && $ajuste->registrar()) {
        $_SESSION['exitos'][] = "Ajuste registrado con éxito";
        Bitacora::registrar("Ajuste de inventario para el artículo ID {$ajuste->idInventario} registrado");
    }

    redirigir(LOCAL_DIR."/Inventario/Ajustes");
}
else
{
    http_response_code(405);
    exit;
}