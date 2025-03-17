<?php
requiereAutenticacion();
requierePermiso("roles", "actualizar");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die();
}

$idRol = intval($_POST['idRol']);
/** @var Rol */
$rol = Rol::cargar($idRol);
unset($_POST['idRol']);

try {
    foreach ($rol->permisos as $permiso) {
        $permiso->setAllFalse();
        $permiso->actualizar();
    }
    foreach ($_POST as $modulo => $permisos) {
        $existePermiso = false;
        if (!empty($rol->permisos)) {
            foreach ($rol->permisos as $permiso) {
                if ($permiso->modulo->getNombre() == $modulo) {
                    foreach ($permisos as $nombrePermiso => $valor) {
                        $permiso->setPermiso($nombrePermiso, $valor);
                    }
                    $permiso->actualizar();
    
                    $existePermiso = true;
                    break;
                }
            }
        }
        // Si no existe, lo crea (Esto por si se agregan mas permisos no hay que
        // agregarlos manualmente en la bd para cada rol)
        if (!$existePermiso) {
            $objModulo = Modulo::cargarPorNombre($modulo);
            $permiso = new Permiso(
                $idRol, $objModulo->id,
                $permisos['consultar'] ?? false,
                $permisos['registrar'] ?? false,
                $permisos['actualizar'] ?? false,
                $permisos['eliminar'] ?? false
            );
            $permiso->registrar();
        }
    }
    // actuliza usuario en sesión
    $usuario = $_SESSION['usuario'];
    if ($rol->getNombre() == $usuario->rol->getNombre()) {
        $usuario = Usuario::cargar($usuario->id);
        $_SESSION['usuario'] = $usuario;
    }
} catch (\Throwable $th) {
    throw $th;
    if (empty($_SESSION['errores'])) {
        $_SESSION['errores'][] = $th->getMessage();
    }
    redirigir(LOCAL_DIR."/Seguridad/Roles");
}

$_SESSION['exitos'][] = "Permisos del rol '".$rol->getNombre()."' actualizados correctamente.";
Bitacora::registrar("Permisos del rol ".$rol->getNombre()." actualizados");
redirigir(LOCAL_DIR."/Seguridad/Roles");