<?php
requiereAutenticacion();
requierePermiso(Modulo::DEPARTAMENTOS, Permiso::REGISTRAR);

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    $departamentoObj = new Departamento();
    $departamentos = $departamentoObj->listar();
    
    require_once "Views/Departamentos/_Registrar.php";
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $departamento = new Departamento();
    $departamento->mapearFormulario();

    if ($departamento->esValido() && $departamento->registrar()) {
        $_SESSION['exitos'][] = "Área registrada con exito";
        Bitacora::registrar("Área '".$departamento->getNombre()."' registrada");
    }

    redirigir(LOCAL_DIR."/Departamentos");
}
else
{
    http_response_code(405);
    exit;
}