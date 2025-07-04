<?php
requiereAutenticacion();
requierePermiso(Modulo::NOTASENTREGA, Permiso::REGISTRAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    $articulos = (new Articulo())->listar();
    
    require_once "Views/Inventario/NotasEntrega/_Registrar.php";
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $idUsuario = $_SESSION['usuario']->id;
    $fechaEntrada = $_POST['fechaEntrada'] ?? (new DateTime())->format('Y-m-d');
    $numeroDocumento = $_POST['numeroDocumento'] ?? '';
    $observaciones = $_POST['observaciones'] ?? '';
    $articulos = $_POST['articulos'] ?? [];
    $detalles = [];
    
    foreach ($articulos as $articulo => $cantidad) {
        $detalle = new EntradaDetalle();
        $detalle->setDatos(
            idEntrada: 0, // Será asignado por la base de datos
            idArticulo: (int)$articulo,
            cantidad: (float)$cantidad
        );
        $detalles[] = $detalle;
    }

    $entrada = new Entrada();
    $entrada->setDatos(
        fechaEntrega: $fechaEntrada,
        numeroDocumento: $numeroDocumento,
        observaciones: $observaciones,
        idUsuario: $idUsuario,
        detalles: $detalles
    );

    if ($entrada->esValido() && $entrada->registrar()) {
        $_SESSION['exitos'][] = "Nota de entrega registrada con exito";
        Bitacora::registrar("Nota de entrega #'".$entrada->getNumeroDocumento()."' registrada");
    }

    redirigir(LOCAL_DIR."/Inventario/NotasEntrega");
}
else
{
    http_response_code(405);
    exit;
}