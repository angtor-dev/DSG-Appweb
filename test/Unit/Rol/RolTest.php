<?php 
use PHPUnit\Framework\TestCase;

class RolTest extends TestCase
{

    private $rolObj;
    private $testSuiteControl;
    protected function setUp(): void
    {
        $this->rolObj = new Rol();
        $this->rolObj->setTestingMode(true);
        $this->testSuiteControl = "Roles";
    }
    /**
     * @dataProvider listarRolProvider
     */
    public function testListarRol()
    {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Listar Roles de Usuarios", "Deben haber roles registrados"))->log();

        $resp = $this->rolObj->listar();
        
        $this->assertIsArray($resp, $_logger["dataname"]);
        
        // Si el array no está vacío, verificar la estructura de los objetos
        if (!empty($resp)) {
            $primerRol = $resp[0];
            
            // Verificar propiedades básicas del ajuste
            $propiedadesRequeridas = [
                'id', 'nombre', 'descripcion', 'estado', 
            ];
            
            foreach ($propiedadesRequeridas as $propiedad) {
                $this->assertObjectHasProperty($propiedad, $primerRol, 
                    "El objeto ajuste debe tener la propiedad: {$propiedad}");
            }
            
            // Verificar que el artículo es una instancia de Articulo
            if (isset($primerRol->articulo)) {
                $this->assertInstanceOf(Rol::class, $primerRol);
            }
        }
    }

    public function listarRolProvider()
    {
        return [
            "Listar Roles" => [
                "N/A" => "No hay entradas para este metodo",
                "resultado esperado" => "Arreglo de Rols",
                "--observacion"=> "Devuelve un arreglo de objetos de Rol o un arreglo vacio",
            ]
        ];
    }







    /**
     * @dataProvider registrarRolProvider
     */
    public function testRegistrarRol($nombre, $descripcion, $resultadoEsperado, ...$otro)
    {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Registrar Rol de Inventario"))->log();

        $this->rolObj->setterArray([
            "nombre" => $nombre,
            "descripcion" => $descripcion]
        );

        
        if($this->rolObj->esValido()){
            $resp = $this->rolObj->registrar();
            $this->assertIsBool($resp, $_logger["dataname"]);
        }
        else{
            $resp = false;
        }
        $this->assertEquals($resultadoEsperado, $resp, $_logger["dataname"]);
        if($resultadoEsperado == false) {
            $this->assertArrayHasKey("errores", $_SESSION, $_logger["dataname"]);
        }
    }

    public function registrarRolProvider()
    {
        $aux = function ($nombre, $descripcion, $resultadoEsperado, ...$otros) {
            return [
                "Nombre" => $nombre,
                "Descripcion" => $descripcion,
                "resultado esperado" => $resultadoEsperado,
                ...$otros
            ];
        };

        return [
            "Registrar - entrada válida" => $aux(
                "Rol 1",
                "Descripcion de la Rol 1",
                true,
                ...["--observacion" => "devuelve true si se pudo registrar el Rol"]
            ),
            "Nombre Vacio - Entrada invalida" =>$aux(
                "",
                "Descripcion de la Rol 1",
                false,
                ...["--observacion" => "devuelve true si se pudo registrar el Rol"]
            ),
            "Nombre invalido- Entrada Invalida" => $aux(
                "Rol<script>",
                "Descripcion de la Rol 1",
                false,
                ...["--observacion" => "devuelve true si se pudo registrar el Rol"]
            ),
            "Descripcion invalida - Entrada Invalida" => $aux(
                "Rol",
                "Descripcion del rol <script>",
                false,
                ...["--observacion" => "devuelve true si se pudo registrar el Rol"]
            ),
            "Nombre Invalido - Nombre Muy Largo" => $aux(
                str_repeat("R", 51),
                "Descripcion de la Rol 1",
                false,
                ...["--observacion" => "devuelve true si se pudo registrar el Rol"]
            ),
            "Descripcion Invalida - Descripcion Muy Larga" => $aux(
                "Rol",
                str_repeat("D", 300),
                false,
                ...["--observacion" => "devuelve true si se pudo registrar el Rol"]
            ),
        ];
    }

    /**
     * @dataProvider actualizarRolProvider
     */
    public function testActualizarRol($id, $nombre, $descripcion, $resultadoEsperado, ...$otro)
    {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Actualizar Rol de usuario"))->log();

        $this->rolObj->setterArray([
            "Id" => $id,
            "nombre" => $nombre,
            "descripcion" => $descripcion]
        );
        if($this->rolObj->esValido()){
            $resp = $this->rolObj->actualizar();
            $this->assertIsBool($resp, $_logger["dataname"]);
        }
        else{
            $resp = false;
        }
        $this->assertEquals($resultadoEsperado, $resp, $_logger["dataname"]);
        if($resultadoEsperado == false) {
            $this->assertArrayHasKey("errores", $_SESSION, $_logger["dataname"]);
        }
    }

    public function actualizarRolProvider()
    {
        $aux = function ($id, $nombre, $descripcion, $resultadoEsperado, ...$otros) {
            return [
                "Id" => $id,
                "Nombre" => $nombre,
                "Descripcion" => $descripcion,
                "resultado esperado" => $resultadoEsperado,
                ...$otros
            ];
        };

        $idValido = 1;

        return [
            "Registrar - entrada válida" => $aux(
                $idValido,
                "Rol 1",
                "Descripcion de la Rol 1",
                true,
                ...["--observacion" => "devuelve true si se pudo actualizar el Rol"]
            ),
            "Rol Inexistente - Entrada Invalida" => $aux(
                0,
                "Rol 1",
                "Descripcion de la Rol 1",
                false,
                ...["--observacion" => "devuelve true si se pudo actualizar el Rol"]
            ),
            "Nombre Vacio - Entrada invalida" =>$aux(
                $idValido,
                "",
                "Descripcion de la Rol 1",
                false,
                ...["--observacion" => "devuelve true si se pudo actualizar el Rol"]
            ),
            "Nombre invalido- Entrada Invalida" => $aux(
                $idValido,
                "Rol<script>",
                "Descripcion de la Rol 1",
                false,
                ...["--observacion" => "devuelve true si se pudo actualizar el Rol"]
            ),
            "Descripcion invalida - Entrada Invalida" => $aux(
                $idValido,
                "Rol",
                "Descripcion del rol <script>",
                false,
                ...["--observacion" => "devuelve true si se pudo actualizar el Rol"]
            ),
        ];
    }

    /**
     * @dataProvider eliminarRolProvider
     */
    public function testEliminarRol($id, $resultadoEsperado, ...$otros){
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Eliminar Rol de usuario"))->log();
        /**
         * @var Rol
         */
        $rol = Rol::cargar($id, true);

        if (empty($rol)) {
            $_SESSION['errores'][] = "El rol que intenta eliminar no existe";
        }
        else{
            $usuarios = Usuario::listarPorRol($rol->id, 1);
    
            if (count($usuarios) > 0) {
                $_SESSION['errores'][] = "Existen usuarios con el rol '".$rol->getNombre(). 
                    "' asignado. Asegurate de asignarles otro rol antes de eliminarlo.";
            }

        }


        if($resultadoEsperado) {
            if ($resp = $rol->eliminarDBUser()) {
                $_SESSION['exitos'][] = "Rol eliminado con exito";
            }

            $this->assertIsBool($resp, $_logger["dataname"]);
            $this->assertArrayHasKey("exitos", $_SESSION, $_logger["dataname"]);
        }
        else{
            $resp = false;
            $this->assertArrayHasKey("errores", $_SESSION, $_logger["dataname"]);
        }
        $this->assertEquals($resultadoEsperado, $resp, $_logger["dataname"]);

    }

    public function eliminarRolProvider(){
        $idEliminable = 12;
        return [
            "Eliminar Rol - Entrada Valida" => [
                "Id" => $idEliminable,
                "resultado esperado" => true,
                ...["--observacion" => "devuelve true si se pudo eliminar el Rol"]
            ],
            "Eliminar Rol Inexistente - Entrada Invalida" => [
                "Id" => 0,
                "resultado esperado" => false,
                ...["--observacion" => "devuelve true si se pudo eliminar el Rol"]
            ],
        ];
    }
    /**
     * @dataProvider tienePermisoProvider
     */
    public function testTienePermiso($id, $permiso, $resultadoEsperado, ...$otros){
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Probar el metodo tienePermiso de Rol", "se prueban los permisos del modulo de categorias"))->log();
        /**
         * @var Rol
         */
        $rolObj2 = Rol::cargar($id, true);

        $this->assertEquals($resultadoEsperado, $rolObj2->tienePermiso(Modulo::CATEGORIAS, $permiso), $_logger["dataname"]);
    }

    public function tienePermisoProvider(){
        $rolSuperUsuario = 1;
        $rolsinEliminar = 15;
        $rolsinActualizar = 16;
        return [
            "Super Usuario Tiene Permiso (Registrar)" => [
                "Id" => $rolSuperUsuario,
                "permiso" => Permiso::REGISTRAR,
                "resultado esperado" => true,
            ],
            "Super Usuario Tiene Permiso (Eliminar)" => [
                "Id" => $rolSuperUsuario,
                "permiso" => Permiso::ELIMINAR,
                "resultado esperado" => true,
            ],
            "Super Usuario Tiene Permiso (Actualizar)" => [
                "Id" => $rolSuperUsuario,
                "permiso" => Permiso::ACTUALIZAR,
                "resultado esperado" => true,
            ],
            "Super Usuario Tiene Permiso (consultar)" => [
                "Id" => $rolSuperUsuario,
                "permiso" => Permiso::CONSULTAR,
                "resultado esperado" => true,
            ],
            "Rol sin permiso de eliminar" => [
                "Id" => $rolsinEliminar,
                "permiso" => Permiso::ELIMINAR,
                "resultado esperado" => false,
            ],
            "Rol sin permiso de actualizar" => [
                "Id" => $rolsinActualizar,
                "permiso" => Permiso::ACTUALIZAR,
                "resultado esperado" => false,
            ],
        ];
    }



}