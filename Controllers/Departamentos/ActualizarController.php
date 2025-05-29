<?php
requiereAutenticacion();
requierePermiso(Modulo::DEPARTAMENTOS, Permiso::ACTUALIZAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    if (empty($_GET['id'])) {
        $_SESSION['errores'][] = "Se debe especificar un departamento para actualizar";
        redirigir(LOCAL_DIR."/Departamentos");
    }

    $departamento = Departamento::cargar($_GET['id']);

    if (is_null($departamento)) {
        $_SESSION['errores'][] = "El departamento que intenta actulizar no existe";
        redirigir(LOCAL_DIR."/Departamentos");
    }

    $departamentos = $departamento->listar();

    require_once "Views/Departamentos/_Actualizar.php";
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $departamento = new Departamento();
    $departamento->mapearFormulario();

    if ($departamento->esValido() && $departamento->actualizar()) {
        $_SESSION['exitos'][] = "Departamento actualizada con exito";
        Bitacora::registrar("Departamento '".$departamento->getNombre()."' actualizada");
    }

    redirigir(LOCAL_DIR."/Departamentos");
}
else
{
    http_response_code(405);
    exit;
}