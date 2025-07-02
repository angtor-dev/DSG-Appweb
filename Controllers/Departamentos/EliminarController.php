<?php
requiereAutenticacion();
requierePermiso(Modulo::DEPARTAMENTOS, Permiso::ELIMINAR);

$departamento = Division::cargar($_GET['id']);

if (empty($departamento)) {
    $_SESSION['errores'][] = "El área que intenta eliminar no existe";
    redirigir(LOCAL_DIR."/Departamentos");
}

$subdepartamentos = $departamento->listarSubdepartamentos(); 

if (count($subdepartamentos) > 0) {
    $_SESSION['errores'][] = "Existen departamentos que pertenecen a '".$departamento->getNombre(). 
        "'. Asegurate de eliminar todos sus sub-departamentos primero.";
    redirigir(LOCAL_DIR."/Departamentos");
}

if ($departamento->eliminar(false)) {
    $_SESSION['exitos'][] = "Departamento eliminado con exito";
    Bitacora::registrar("Departamento '".$departamento->getNombre()."' eliminado");
}

redirigir(LOCAL_DIR."/Departamentos");