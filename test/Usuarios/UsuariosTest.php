<?php 
use PHPUnit\Framework\TestCase;
// la clase debe llamarse igual que el archivo
class UsuariosTest extends TestCase
{
    private $usuariosObj;
    private $testSuiteControl;
    private $counter;

    protected function setUp(): void
    {
        $this->usuariosObj = new Usuario;
        $this->usuariosObj->setTestingMode(true);
        $this->testSuiteControl = "Usuarios";
        $this->counter = 0;
    }

    

    
    

    /**
     * @dataProvider ActualizarAsistenciasProvider
     */
    public  function testActualizarUsuario($id,$cedula, $nombre, $apellido, $correo, $rol, $clave, $resultado_esperado, $num_caso, ...$otros){
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Actualizar Usuario"))->log();

        $datos = [
            "id" => $id,
            "cedula" => $cedula,
            "nombre" => $nombre,
            "apellido" => $apellido,
            "correo" => $correo,
            "idRol" => $rol,
            "clave" => $clave
        ];
        // eliminar valores nulos que no se quieran enviar
        foreach ($datos as $key => $value) {
            if ($value === null) {
                unset($datos[$key]);
            }
        }

        $this->usuariosObj->setterArray($datos);
        $respuesta = $this->usuariosObj->actualizarUsuario(false);
        
        $mensaje = "caso ($num_caso)";

        $this->assertNotNull($respuesta);
        $this->assertIsArray($respuesta);

        $this->assertArrayHasKey('success', $respuesta);
        if($respuesta['success']){
            // si es verdadero entonces idModificado debe ser igual al id del usuario actualizado
            $this->assertEquals($id, $respuesta['idModificado'], $mensaje);
        }

        $mensaje = ($respuesta['consoleError'] ?? $respuesta['mensaje'] ?? '') . ' :: '.$mensaje;
        
        $this->assertEquals($resultado_esperado, $respuesta['success'], $mensaje);
        
    }

    public static function ActualizarAsistenciasProvider()
    {
        $caso = 1;
        $aux = function ($id,$cedula, $nombre, $apellido, $correo, $rol, $clave, $resultado_esperado, $num_caso, ...$otros) {
            return [
                "Id"=> $id,
                "Cedula" => $cedula,
                "Nombre" => $nombre,
                "Apellido" => $apellido,
                "Correo" => $correo,
                "Rol" => $rol,
                "Clave" => $clave,
                "resultado esperado" => $resultado_esperado,
                "num_caso" => $num_caso,
                ...$otros
            ];
        };

        $messages = new class{
            public $exito = "Usuario actualizado con éxito";
            public $correoRequerido = "El correo es requerido";
            public $correoInvalido = "El correo es invalido";
            public $rolRequerido = "El rol es requerido";
            public $rolInvalido = "El rol debe ser un número";
            public $usuarioNoSelected = "El usuario no fue seleccionado correctamente";
            public $userNoSelectedDelete = "El usuario a eliminar no fue seleccionado correctamente";
            public $usuarioPropioDelete = "No puedes eliminar tu propio usuario";
            public $claveRequerida = "La clave es requerida";
            public $calveInvalida = "La clave debe tener al menos 6 caracteres, una letra mayúscula, una letra minúscula y un número";
            public $cedulaRequerida = "La cedula es requerida";
            public $cedulaInvalida = "La cedula es invalida debe contener entre 7 y 8 dígitos";
            public $correoRegistrado = "El correo ya se encuentra registrado";
            public $correoRegistradoUserActive = "El correo ya se encuentra registrado con un usuario activo";
            public $correoRegistradoUserInactive = "El correo ya se encuentra registrado con un usuario inactivo";
            public $userNoExist = "El usuario no existe";
            public $cedulaRegistrada = "La cedula ya se encuentra registrada";
            public $nombreRequerido = "El nombre es requerido";
            public $apellidoRequerido = "El apellido es requerido";
            public $nombreInvalido = "El nombre es invalido debe contener solo letras y espacios";
            public $apellidoInvalido = "El apellido es invalido debe contener solo letras y espacios";
        };

        $id = "1";
        $cedulaValida =         "00000001"; // misma del id
        $cedulaValidaNueva =    "00000002";
        $cedulaValidaExiste =   "0777777";
        $cedulaInvalida =       "124478500";
        $cedulaVacia =          "";
        $nombreValido =         "Fernando";
        $nombreVacio =          "";
        $nombreInvalido =       "Fernando123";
        $apellidoValido =       "Perez";
        $apellidoVacio =        "";
        $apellidoInvalido =     "Perez123";
        $correoValido =         "amouat0@cafepress.com";
        $correoVacio =          "";
        $correoInvalido =       "amouat0@cafepress";
        $rolValido =            1;
        $rolVacio =             null;
        $rolInvalido =          "0";
        $claveValida =          "TAOmc53521";
        $claveVacia =           "";
        $claveInvalida =        "hola";




        return [

            "Entradas Validas" =>                           $aux($id, $cedulaValida, $nombreValido, $apellidoValido, $correoValido, $rolValido, $claveValida, true, $caso++,...["mensaje esperado" => $messages->exito]),
            "Entradas Validas - Nueva Cedula" =>            $aux($id, $cedulaValidaNueva, $nombreValido, $apellidoValido, $correoValido, $rolValido, $claveValida, true, $caso++,...["mensaje esperado" => $messages->exito]),
            "Entradas Validas - Nueva Cedula ya existe" =>  $aux($id, $cedulaValidaExiste, $nombreValido, $apellidoValido, $correoValido, $rolValido, $claveValida, false, $caso++,...["mensaje esperado" => $messages->cedulaRegistrada]),
            "Entradas Validas - Sin nueva clave" =>         $aux($id, $cedulaValida, $nombreValido, $apellidoValido, $correoValido, $rolValido, null, true, $caso++,...["mensaje esperado" => $messages->exito]),
            "Entradas Validas - Clave Vacia" =>             $aux($id, $cedulaValida, $nombreValido, $apellidoValido, $correoValido, $rolValido, $claveVacia, true, $caso++,...["mensaje esperado" => $messages->exito]),
            "Entradas Invalidas - Cedula Invalida" =>       $aux($id, $cedulaInvalida, $nombreValido, $apellidoValido, $correoValido, $rolValido, $claveValida, false, $caso++,...["mensaje esperado" => $messages->cedulaInvalida]),
            "Entradas Invalidas - Cedula Vacia" =>          $aux($id, $cedulaVacia, $nombreValido, $apellidoValido, $correoValido, $rolValido, $claveValida, false, $caso++,...["mensaje esperado" => $messages->cedulaRequerida]),
            "Entradas Invalidas - Nombre Invalido" =>       $aux($id, $cedulaValida, $nombreInvalido, $apellidoValido, $correoValido, $rolValido, $claveValida, false, $caso++,...["mensaje esperado" => $messages->nombreInvalido]),
            "Entradas Invalidas - Nombre Vacio" =>          $aux($id, $cedulaValida, $nombreVacio, $apellidoValido, $correoValido, $rolValido, $claveValida, false, $caso++,...["mensaje esperado" => $messages->nombreRequerido]),
            "Entradas Invalidas - Apellido Invalido" =>     $aux($id, $cedulaValida, $nombreValido, $apellidoInvalido, $correoValido, $rolValido, $claveValida, false, $caso++,...["mensaje esperado" => $messages->apellidoInvalido]),
            "Entradas Invalidas - Apellido Vacio" =>        $aux($id, $cedulaValida, $nombreValido, $apellidoVacio, $correoValido, $rolValido, $claveValida, false, $caso++,...["mensaje esperado" => $messages->apellidoRequerido]),
            "Entradas Invalidas - Correo Invalido" =>       $aux($id, $cedulaValida, $nombreValido, $apellidoValido, $correoInvalido, $rolValido, $claveValida, false, $caso++,...["mensaje esperado" => $messages->correoInvalido]),
            "Entradas Invalidas - Correo Vacio" =>          $aux($id, $cedulaValida, $nombreValido, $apellidoValido, $correoVacio, $rolValido, $claveValida, false, $caso++,...["mensaje esperado" => $messages->correoRequerido]),
            "Entradas Invalidas - Rol Invalido" =>          $aux($id, $cedulaValida, $nombreValido, $apellidoValido, $correoValido, $rolInvalido, $claveValida, false, $caso++,...["mensaje esperado" => $messages->rolInvalido]),
            "Entradas Invalidas - Rol Vacio" =>             $aux($id, $cedulaValida, $nombreValido, $apellidoValido, $correoValido, $rolVacio, $claveValida, false, $caso++,...["mensaje esperado" => $messages->rolRequerido]),
            "Entradas Invalidas - Clave Invalida" =>        $aux($id, $cedulaValida, $nombreValido, $apellidoValido, $correoValido, $rolValido, $claveInvalida, false, $caso++,...["mensaje esperado" => $messages->calveInvalida]),
        ];
    }



     /**
     * @dataProvider RegistrosProvider
     */
    public function testRegistrarUsuario($cedula, $nombre, $apellido, $correo, $rol, $clave, $resultado_esperado, $num_caso, ...$otros)
    {

        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Registrar Usuario"))->log();

        $datos = [
            "cedula" => $cedula,
            "nombre" => $nombre,
            "apellido" => $apellido,
            "correo" => $correo,
            "idRol" => $rol,
            "clave" => $clave
        ];


        // eliminar valores nulos que no se quieran enviar
        foreach ($datos as $key => $value) {
            if ($value === null) {
                unset($datos[$key]);
            }
        }

        $this->usuariosObj->setterArray($datos);

        $respuesta = $this->usuariosObj->registrar(false);

        $mensaje = "caso ($num_caso)";

        $this->assertNotNull($respuesta);
        $this->assertIsArray($respuesta);

        $this->assertArrayHasKey('success', $respuesta);
        if ($respuesta['success']) {
            // si es verdadero entonces idInserted debe ser mayor que 0
            $this->assertGreaterThan(0, $respuesta['idInserted'], $mensaje);
        }
        $mensaje = ($respuesta['consoleError'] ?? $respuesta['mensaje']) . ' :: '.$mensaje;

        $this->assertEquals($resultado_esperado, $respuesta['success'], $mensaje);
    }

    public function RegistrosProvider()
    {
        $caso = 1;
        $aux = function ($cedula, $nombre, $apellido, $correo, $rol, $clave, $resultado_esperado, ...$otros) use ($caso) {
            return [
                "Cedula" => $cedula,
                "Nombre" => $nombre,
                "Apellido" => $apellido,
                "Correo" => $correo,
                "Rol" => $rol,
                "Clave" => $clave,
                "resultado esperado" => $resultado_esperado,
                "num_caso" => $caso++,
                ...$otros
            ];
        };

        $messages = new class{
            public $exito = "El usuario se ha registrado correctamente";
            public $activado = "El usuario se ha activado y registrado correctamente";
            public $correoRequerido = "El correo es requerido";
            public $correoInvalido = "El correo es invalido";
            public $rolRequerido = "El rol es requerido";
            public $rolInvalido = "El rol debe ser un número";
            public $usuarioNoSelected = "El usuario no fue seleccionado correctamente";
            public $userNoSelectedDelete = "El usuario a eliminar no fue seleccionado correctamente";
            public $usuarioPropioDelete = "No puedes eliminar tu propio usuario";
            public $claveRequerida = "La clave es requerida";
            public $calveInvalida = "La clave debe tener al menos 6 caracteres, una letra mayúscula, una letra minúscula y un número";
            public $cedulaRequerida = "La cedula es requerida";
            public $cedulaInvalida = "La cedula es invalida debe contener entre 7 y 8 dígitos";
            public $correoRegistrado = "El correo ya se encuentra registrado";
            public $correoRegistradoUserActive = "El correo ya se encuentra registrado con un usuario activo";
            public $correoRegistradoUserInactive = "El correo ya se encuentra registrado con un usuario inactivo";
            public $userNoExist = "El usuario no existe";
            public $cedulaRegistrada = "La cedula ya se encuentra registrada";
            public $nombreRequerido = "El nombre es requerido";
            public $apellidoRequerido = "El apellido es requerido";
            public $nombreInvalido = "El nombre es invalido debe contener solo letras y espacios";
            public $apellidoInvalido = "El apellido es invalido debe contener solo letras y espacios";
        };

        $cedulaValida = "12345678";
        $cedulaUsuarioInactivo = "0777777";
        $cedulaInvalida = "1234";
        $cedulaVacia = "";
        $nombreValido = "Fernando";
        $nombreVacio = "";
        $nombreInvalido = "Fernando123";
        $apellidoValido = "Perez";
        $apellidoVacio = "";
        $apellidoInvalido = "Perez123";
        $correoValido = "Fq8o2@example.com";
        $correoInvalido = "fernandoperezgmail.com";
        $correoVacio = "";
        $rolValido = 8;
        $rolInvalido = 0;
        $rolVacio = null;
        $claveValida = "Fernando123";
        $claveInvalida = "12345";
        $claveVacia = "";



        return [
            "Entradas Validas - Todo Correcto" => $aux($cedulaValida, $nombreValido, $apellidoValido, $correoValido, $rolValido, $claveValida, true,...["mensaje esperado" => ""]),
            "Entradas Validas - Todo Correcto - Activado" => $aux($cedulaUsuarioInactivo, $nombreValido, $apellidoValido, $correoValido, $rolValido, $claveValida, true,...["mensaje esperado" => $messages->activado]),
            "Entradas Invalidas - Cedula Vacia" => $aux($cedulaVacia, $nombreValido, $apellidoValido, $correoValido, $rolValido, $claveValida, false,...["mensaje esperado" => $messages->cedulaRequerida]),
            "Entradas Invalidas - Cedula Invalida" => $aux($cedulaInvalida, $nombreValido, $apellidoValido, $correoValido, $rolValido, $claveValida, false,...["mensaje esperado" => $messages->cedulaInvalida]),
            "Entradas Invalidas - Nombre Vacio" => $aux($cedulaValida, $nombreVacio, $apellidoValido, $correoValido, $rolValido, $claveValida, false,...["mensaje esperado" => $messages->nombreRequerido]),
            "Entradas Invalidas - Nombre Invalido" => $aux($cedulaValida, $nombreInvalido, $apellidoValido, $correoValido, $rolValido, $claveValida, false,...["mensaje esperado" => $messages->nombreInvalido]),
            "Entradas Invalidas - Apellido Vacio" => $aux($cedulaValida, $nombreValido, $apellidoVacio, $correoValido, $rolValido, $claveValida, false,...["mensaje esperado" => $messages->apellidoRequerido]),
            "Entradas Invalidas - Apellido Invalido" => $aux($cedulaValida, $nombreValido, $apellidoInvalido, $correoValido, $rolValido, $claveValida, false,...["mensaje esperado" => $messages->apellidoInvalido]),
            "Entradas Invalidas - Correo Vacio" => $aux($cedulaValida, $nombreValido, $apellidoValido, $correoVacio, $rolValido, $claveValida, false,...["mensaje esperado" => $messages->correoRequerido]),
            "Entradas Invalidas - Correo Invalido" => $aux($cedulaValida, $nombreValido, $apellidoValido, $correoInvalido, $rolValido, $claveValida, false,...["mensaje esperado" => $messages->correoInvalido]),
            "Entradas Invalidas - Rol Vacio" => $aux($cedulaValida, $nombreValido, $apellidoValido, $correoValido, $rolVacio, $claveValida, false,...["mensaje esperado" => $messages->rolRequerido]),
            "Entradas Invalidas - Rol Invalido" => $aux($cedulaValida, $nombreValido, $apellidoValido, $correoValido, $rolInvalido, $claveValida, false,...["mensaje esperado" => $messages->rolInvalido]),
            "Entradas Invalidas - Clave Vacia" => $aux($cedulaValida, $nombreValido, $apellidoValido, $correoValido, $rolValido, $claveVacia, false,...["mensaje esperado" => $messages->claveRequerida]),
            "Entradas Invalidas - Clave Invalida" => $aux($cedulaValida, $nombreValido, $apellidoValido, $correoValido, $rolValido, $claveInvalida, false,...["mensaje esperado" => $messages->calveInvalida]),
        ];
    }

    /**
     * 
     * @dataProvider eliminarProvider
     */
    public function testEliminarUsuario($id, $resultado_esperado, $mensaje_esperado){
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Eliminar Usuario"))->log();
        $_SESSION["usuario"] = Usuario::cargar(1,true);

        $datos = ["id" => $id];


        // eliminar valores nulos que no se quieran enviar
        foreach ($datos as $key => $value) {
            if ($value === null) {
                unset($datos[$key]);
            }
        }

        $this->usuariosObj->setterArray($datos);

        $result = $this->usuariosObj->eliminarUsuario(false);
        $this->assertEquals($resultado_esperado, $result['success'], $_logger['dataname']);
        $this->assertEquals($mensaje_esperado, $result['mensaje'], $_logger['dataname']);
        if($_logger['dataname'] == "Entradas Invalidas - usuario no seleccionado")
            $this->cleanEliminar();

    }

    public function eliminarProvider(){
        // insertamos un usuario para poder eliminarlo
            $db = Database::getInstance();
            $db->connectUser();
            $pdo = $db->pdo();

            $query = "SELECT id FROM usuario WHERE correo = 'PRUEBA@PRUEBA.com' LIMIT 1";
            $idInserted = $pdo->query($query)->fetchColumn();

            if(!$idInserted){
                $query = "INSERT INTO usuario (cedula, idRol, correo, clave, nombre, apellido)
                            VALUES (:cedula, :idRol, :correo, :clave, :nombre, :apellido) on duplicate key ignore";
                $pdo->prepare($query)->execute([
                    "cedula" => "99999999",
                    "idRol" => "1",
                    "correo" => "PRUEBA@PRUEBA.com",
                    "clave" => password_hash("123456", PASSWORD_DEFAULT),
                    "nombre" => "PRUEBANOMBRE",
                    "apellido" => "PRUEBAAPELLIDO"
                ]);
                $idInserted = $pdo->lastInsertId();
            }
            $pdo = null;
            $db->disconnect();

            
        
        //----------------------------------
        $defaultValidationMessages = new class{
            public $userNoSelectedDelete = "El usuario a eliminar no fue seleccionado correctamente";
            public $usuarioPropioDelete = "No puedes eliminar tu propio usuario";
            public $userNoExist = "El usuario no existe";
            public $eliminar = "El usuario se ha eliminado correctamente";
            public $logicDelete = "El usuario se ha desactivado correctamente";
        };
        
        $_SESSION["usuario"] = Usuario::cargar(1,true);
        



        return [
            "Entradas Validas" => [
                "Id" => $idInserted,
                "resultado esperado" => true,
                "mensaje esperado" => $defaultValidationMessages->eliminar
            ],
            "Entradas Invalidas - usuario no existente" => [
                "Id" => 0,
                "resultado esperado" => false,
                "mensaje esperado" => $defaultValidationMessages->userNoExist
            ],
            "Entradas Invalidas - usuario propio" => [
                "Id" => $_SESSION["usuario"]->id,
                "resultado esperado" => false,
                "mensaje esperado" => $defaultValidationMessages->usuarioPropioDelete
            ],
            "Entradas Invalidas - usuario no seleccionado" => [
                "Id" => null,
                "resultado esperado" => false,
                "mensaje esperado" => $defaultValidationMessages->userNoSelectedDelete
            ]
        ];




    }


    public function cleanEliminar(): void
    {
        $db = Database::getInstance();
        $db->connectUser();
        $pdo = $db->pdo();
        $query = "DELETE FROM usuario WHERE correo = 'PRUEBA@PRUEBA.com'";
        $pdo->prepare($query)->execute();
        $pdo = null;
        $db->disconnect();
    }
    /**
     * @dataProvider cargarNombreProvider
     */
    public function testGettersUserInstance($identificador, $method , $resultado_esperado){
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Probar los metodos de carga de usuarios"))->log();

        switch ($method) {
            case 'cargarPorCorreo':
                $resp = Usuario::cargarPorCorreo($identificador); // correo
                break;
            case 'cargarSoloNombres':
                $resp = Usuario::cargarSoloNombres($identificador); // id
                break;
            case 'cargarPorCedula':
                $resp = Usuario::cargarPorCedula($identificador); // cedula
                break;
            
            default:
                $this->fail("Metodo no reconocido");
                break;
        }



        if($resultado_esperado === null){
            $this->assertNull($resp, $_logger['dataname']);
        }else{
            $this->assertInstanceOf(Usuario::class, $resp, $_logger['dataname']);
        }
    }

    public function cargarNombreProvider(){
        /**
         * @param string $id identificador ya se correo, id o cedula
         * @param 'cargarPorCorreo'|'cargarPorCedula'|'cargarSoloNombres' $method
         * @param bool $resultado_esperado resultado esperado en la prueba
         */
        $aux = function ($id, $method = "cargarSoloNombres", $resultado_esperado = true, ...$otros) {
            return [
                "Id" => $id,
                "--metodo" => $method,
                "resultado esperado" => $resultado_esperado ? "Instancia de Usuario" : null,
                ...$otros
            ];
        };

        return [
            "carga por correo" => $aux("admin@dsg.com", "cargarPorCorreo", true),
            "carga por cedula" => $aux("00000001", "cargarPorCedula", true),
            "carga por id" => $aux(1, "cargarSoloNombres", true),
        ];
    }



}