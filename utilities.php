<?php
/**
 * Imprime la vista final, incluyendo la plantilla.
 * Se puede cambiar la plantilla declarando la variable $_layout con el nombre de la plantilla dentro de cualquier vista
 * Utiliza por defecto la plantilla 'Principal.php'
 * 
 * @param ?string $viewName Nombre de la vista, se usa por defecto el nombre del controlador actual
 * @param ?string $viewPath Ruta de vista, se usa por defecto la misma ruta que el controlador actual
 * @return void
 **/
function renderView(?string $viewName = null, ?string $viewPath = null) : void
{
    $viewName ??= $GLOBALS['controllerName'];
    $viewPath ??= $GLOBALS['controllerPath'];

    $viewName = ucfirst($viewName);
    $viewPath = ucfirst($viewPath);

    foreach ($GLOBALS as $key => $value) $$key = $value;
    
    $_layout ??= "Principal";

    if (!is_file("Views/".$viewPath.$viewName.".php")) {
        http_response_code(404);
        die("[Error] No se encontro la vista en <b>Views/".$viewPath.$viewName.".php</b>");
    }
    if (!is_file("Views/_Plantillas/".$_layout.".php")) {
        http_response_code(404);
        die("[Error] No se encontro la plantilla en <b>Views/_Plantillas/".$_layout.".php</b>");
    }

    try {
        ob_start('saveViewBuffer');
        require "Views/".$viewPath.$viewName.".php";
        ob_end_clean();
    } catch (\Throwable $th) {
        ob_end_clean();
        throw $th;
    }

    require "Views/_Plantillas/".$_layout.".php";
    exit;
}

/** 
 * Almacena el buffer de la vista que sera impresa
 * 
 * @param string $buffer Buffer a almacenar
 * @return string Cadena a imprimir luego de almacenar el buffer (Cadena vacia)
*/
function saveViewBuffer(string $buffer)
{
    $GLOBALS['view'] .= $buffer;
    return "";
}

/**
 * Imprime un componente almacenado en Views/_Componentes/
 * @param string $componente El nombre del componente
 */
function renderComponent($componente) : void {
    foreach ($GLOBALS as $key => $value) $$key = $value;
    require_once "Views/_Componentes/".$componente.".php";
}

/**
 * Valida si el usuario esta autenticado, de lo contrario se redirecciona al login
 */
function requiereAutenticacion() : void {
    if (!isset($_SESSION['usuario'])) {
        redirigir(LOCAL_DIR.'/Login');
    }
}

/**
 * Valida si el usuario tiene el permiso especificado, de no ser asi muestra pantalla de acceso denegado y finaliza el script
 * @param string $modulo El modulo a consultar (en minuscula y plural).
 * @param string $permiso El permiso a validar. Los posibles valores son 
 * consultar, registrar, actualizar y eliminar.
 */
function requierePermiso(string $modulo, string $permiso) : void {
    /** @var Usuario */
    $usuarioSesion = $_SESSION['usuario'];
    if (!$usuarioSesion->rol->tienePermiso($modulo, $permiso)) {
        renderView("AccesoDenegado", "Home/");
        exit();
    }
}

/**
 * Retorna true si el usuario en sesion tiene el permiso especificado, false en caso contrario
 * @param string $modulo El modulo a consultar (en minuscula y plural).
 * @param string $permiso El permiso a validar. Los posibles valores son 
 * 'consultar', 'registrar', 'actualizar' y 'eliminar'.
 */
function tienePermiso(string $modulo, string $permiso) : bool {
    /** @var Usuario */
    $usuarioSesion = $_SESSION['usuario'];
    return $usuarioSesion->rol->tienePermiso($modulo, $permiso);
}

function postTienePermiso(string $modulo, string $permiso) : void {
    if(!tienePermiso($modulo, $permiso)) {
        http_response_code(403);
        exit();
    }
}

/**
 * Redirecciona de forma segura a una url
 * @param string $url Url a donde se redireccionará
 */
function redirigir($url) : void {
    header('location: '.$url);
    exit();
}

/**
* Alamecena el nombre de un script que sera utilizado en la vista
* @param string $scriptName Nombre del script (debe estar almacenado en public/js/)
*/
function agregarScript($scriptName) : void {
    global $viewScripts;

    $viewScripts[] = $scriptName;
}

function imprimirScripts() : void {
    global $viewScripts;
    
    if (!empty($viewScripts)) {
        foreach ($viewScripts as $script) {
            echo '<script src="'.LOCAL_DIR.'/public/js/'.$script.'"></script>';
        }
    }
}
/**
* Alamecena el nombre de un script de las lib que sera utilizado en la vista
* @param string $scriptName Nombre del script (debe estar almacenado en public/lib/)
*/
function agregarLib($scriptName) : void {
    global $viewLibs;

    $viewLibs[] = $scriptName;
}

function imprimirLibs() : void{
    global $viewLibs;
    
    if (!empty($viewLibs)) {
        foreach ($viewLibs as $script) {
            echo '<script src="'.LOCAL_DIR.'/public/lib/'.$script.'"></script>';
        }
    }
}

/**
* Alamecena el nombre de un archivo css que sera utilizado en la vista
* @param string $styleName Nombre del archivo .css (debe estar almacenado en public/css/)
*/
function agregarCss($styleName) : void {
    global $viewStyles;

    $viewStyles[] = $styleName;
}

/**
 * Imprime en el html los enlaces a los archivos css que se han agregado
 * con la funcion agregarCss()
 */
function imprimirEstilos() : void {
    global $viewStyles;
    
    if (!empty($viewStyles)) {
        foreach ($viewStyles as $style) {
            echo '<link rel="stylesheet" href="'.LOCAL_DIR.'/public/css/'.$style.'.css">';
        }
    }
}

/**
 * Imprime el contenido de una variable en un formato legible y finaliza el programa
 * @param mixed $var variable a imprimir
 */
function debug(mixed $var, bool $endProgram = true) : void {
    echo "<pre>";
    print_r($var);
    echo "</pre>";
    if($endProgram) exit;
}

function ImprimirAcordeonesAnidados(array $models, ?int $padreId = null, string $modulo): string {
    if (empty($models)) return '';
    $html = '';
    $table = strtolower(get_class($models[0]));
    $nombrePadre = $table . 'Padre';
    $subAreas = array_filter($models, fn($area) => ($area->$nombrePadre === null && $padreId === null) || ($area->$nombrePadre !== null && $area->$nombrePadre->id === $padreId));

    if (!empty($subAreas)) {
        $html .= 
        '<div class="accordion tree-accordion">
            <div class="accordion-item border-0">';
        foreach ($subAreas as $area) {
            $tieneSubAreas = array_filter($models, fn($subArea) => $subArea->$nombrePadre !== null && $subArea->$nombrePadre->id === $area->id);
            $html .= '
            '.($tieneSubAreas ? '<div class="node-card-container">' : '').'
            <div class="node-card w-100">
                <button class="flex-grow-1" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapse-'.$area->id.'">
                    '.(empty(!$tieneSubAreas) ? '<i class="fa-solid fa-caret-down me-2" style="color: var(--gris)"></i>' : '<i class="me-2" style="width: 10px; display: inline-block;"></i>').'
                    <span class="node-name">'.$area->getNombre().'</span>
                </button>
                <div class="node-actions">';
            if (tienePermiso($modulo, Permiso::ACTUALIZAR)) {
                $html .= '
                <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Editar">
                    <div data-bs-toggle="modal" data-bs-target="#modal-generico"
                        data-bs-url="'.LOCAL_DIR.'/'.$modulo.'/Actualizar?id='.$area->id.'">
                        <i class="fa-solid fa-fw fa-pen"></i>
                    </div>
                </div>';
            }
            if (tienePermiso($modulo, Permiso::ELIMINAR)) {
                $html .= '
                <div class="accion pointer" data-bs-toggle="tooltip" data-bs-title="Eliminar">
                    <div data-bs-toggle="modal" data-bs-target="#modal-eliminar"
                        data-bs-modelo="el área" 
                        data-bs-nombre="'.$area->getNombre().'"
                        data-bs-url="'.LOCAL_DIR.'/'.$modulo.'/Eliminar?id='.$area->id.'">
                        <i class="fa-solid fa-fw fa-trash"></i>
                    </div>
                </div>';
            }
            $html .= '
                </div>
            </div>';
            if ($tieneSubAreas) {
                $html .= '<div id="collapse-'.$area->id.'" class="accordion-collapse collapse show">';
                $html .= ImprimirAcordeonesAnidados($models, $area->id, $modulo);
                $html .= '</div></div>';
            }
        }
        $html .= '</div></div>';
    }

    return $html;
}


/**
 * Lee el contenido de php://input y lo parsea a un array asociativo
 * Si el contenido es un array lo pone en $_POST
 * 
 * @return void
 */
function cargarPost():void {
    $__TEMP_POST = json_decode(file_get_contents("php://input"), true);
    // si la encuentra la pone en $_POST
    if (is_array($__TEMP_POST)) {
        $_POST = $__TEMP_POST;
    }
}

/**
 * Sincroniza los permisos del usuario en la sesión con la BD
 **/
function sincronizarPermisosEnSesion() : void {
    if (!isset($_SESSION['usuario']) || !$_SESSION['usuario'] instanceof Usuario) {
        return;
    }
    
    /** @var Usuario */
    $usuarioSesion = $_SESSION['usuario'];
    $usuarioSesion->rol->SincronizarPermisos();
}

/**
 * Comprueba si el host actual de la conexión PDO es el especificado
 *
 * @param PDO $pdo La conexión PDO a comprobar
 * @param string $hostToCheck El host que se va a buscar en la conexión
 * @return bool True si el host coincide, false en caso contrario
 */
function checkHostBD(PDO $pdo, string $hostToCheck) : bool {
    $host = $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS);
    return preg_match('/^'. $hostToCheck .'/', $host);
}

/**
 * Carga las notificaciones del usuario en la sesión
 **/
function cargarNotificacionesEnSesion() : void {
    if (!isset($_SESSION['usuario']) || !$_SESSION['usuario'] instanceof Usuario) {
        $_SESSION['notificaciones'] = [];
        return;
    }
    
    /** @var Usuario */
    $usuarioSesion = $_SESSION['usuario'];
    $objNotificacion = new Notificacion();
    $_SESSION['notificaciones'] = $objNotificacion->cargarNotificaciones($usuarioSesion->id);
}

/**
 * la funcion recibe una fecha en formato 2025-07-08 11:00:53
 * y retorna la fecha en formato 08/07/2025
 * @param {string} fecha 
 * @param {string} separador 
 */
function getFecha(string $fecha) : string {
    return date("d/m/Y", strtotime($fecha));
}

/**
 * la funcion recibe una fecha en formato "2025-07-08 17:00:53" o "17:00:53"
 * y retorna la hora en formato 5:00 PM
 * @param {string} fecha 
 * @return
 */
function getHora(string $fecha) : string {
    return date("h:i A", strtotime($fecha));
}


/**
 * Verifica si el error de la base de datos es un error de integridad de clave
 * unica (UUID) y si es por el campo especificado.
 * Este es un error poco probable pero puede ocurrir.
 * @param string $error El mensaje de error de la base de datos
 * @param string $campo El nombre del campo que se esta verificando
 * @return bool True si el error es de integridad de clave unica y el campo
 * coincide, false en caso contrario
 */
function checkUUIDError(string $error, string $campo): bool {
    // mensaje de error tipico : "SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '1' for key 'campoUUID'"

    // Verifica si el error contiene la cadena "Duplicate entry" y "PRIMARY"
    return strpos($error, "Duplicate entry") !== false && strpos($error, "for key '$campo'") !== false;
}
