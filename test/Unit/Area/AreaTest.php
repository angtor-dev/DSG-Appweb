<?php 
use PHPUnit\Framework\TestCase;
// ->
class AreaTest extends TestCase
{
    public $areaObj;
    private $testSuiteControl;
    protected function setUp(): void
    {
        $this->areaObj = new Area();
        $this->areaObj->setTestingMode(true);
        $this->testSuiteControl="Areas";
        if (!isset($_SESSION)) {
            $_SESSION = [];
        }
    }

    /**
     * @dataProvider listarAreaProv
     * 
     */

    public function testListarArea()
    {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Listar Areas"))->log();
        $respuesta = $this->areaObj->listar();

         $this->assertIsArray($respuesta);

         foreach ($respuesta as $item) {
            $this->assertInstanceOf(Area::class, $item);
         }




    }
    public function listarAreaProv()
    {
        return [
            "Listado de las Areas"=>[
                "N/A" => "Sin Entradas",
                "resultado esperado" => "Arreglo de Areas", 
                "--observacion"=> "devuelve un arreglo de objetos de Area",
            ],
        ];
    }









    /**
     * @dataProvider registrarAreaProvider
     */
    public function testRegistrarArea($nombre, $idArea, $respuesta_esperada, ...$otros)
    {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Registrar Area"))->log();

        $datos = [
            "nombre" => $nombre,
            "idArea" => $idArea
        ];
        // eliminar valores nulos que no se quieran enviar
        foreach ($datos as $key => $value) {
            if ($value === null) {
                unset($datos[$key]);
            }
        }

        $this->areaObj->setterArray($datos);

        $validResp = $this->areaObj->esValido();
        $resp = false;
        if($validResp){
            $resp = $this->areaObj->registrar();
        }

        $sumResp = $validResp && $resp;
        
        $this->assertIsBool($resp);
        $this->assertEquals($respuesta_esperada, $sumResp, $_logger["dataname"]);
        
        // Verificar mensaje de error en sesión si la prueba falla
        if (!$respuesta_esperada && isset($_SESSION['errores'])) {
            $ultimoError = end($_SESSION['errores']);
            if (isset($otros["mensaje esperado"])) {
                $this->assertStringContainsString($otros[0]["mensaje esperado"], $ultimoError);
            }
        }
        // Limpiar sesión después de cada prueba
        if (isset($_SESSION['errores'])) {
            $_SESSION['errores'] = [];
        }
    }

    public function registrarAreaProvider()
    {
        $aux = function($nombre, $idArea, $respuesta_esperada, ...$otros) {
            return [
                "nombre" => $nombre,
                "idArea" => $idArea,
                "resultado esperado" => $respuesta_esperada,
                ...$otros
            ];
        };

        // Datos de prueba
        $nombreValido = "Aula H-1";
        $nombreValido2 = "Finanzas";
        $nombreDuplicado = "Hilandera";
        $nombreInvalidoCaracteres = "Area@#";
        $nombreVacio = "";
        $idAreaValido = 1; // ID de área padre que existe en BD
        $idAreaInvalido = 99999; // ID que no existe
        $idAreaNulo = null;

        return [
            "Registrar - área principal (sin idArea)" => $aux(
                $nombreValido,
                $idAreaNulo,
                true
            ),
            
            "Registrar - subárea (con idArea válido)" => $aux(
                $nombreValido2,
                $idAreaValido,
                true
            ),
            
            "Registrar - nombre vacío" => $aux(
                $nombreVacio,
                $idAreaNulo,
                false,
                ...["mensaje esperado" => "El nombre del área es requerido"]
            ),
            
            "Registrar - nombre nulo" => $aux(
                null,
                null,
                false,
                ...["mensaje esperado" => "El nombre del área es requerido"]
            ),
            
            "Registrar - nombre con caracteres especiales" => $aux(
                $nombreInvalidoCaracteres,
                null,
                false,
                ...["mensaje esperado" => "El nombre del área no puede contener caracteres especiales"]
            ),
            
            
            "Registrar - idArea inválido (no existe)" => $aux(
                $nombreValido,
                $idAreaInvalido,
                false,
                ...["mensaje esperado" => "El area padre no existe"]
            ),
            
            "Registrar - área con espacios en nombre" => $aux(
                "Área de Desarrollo Tecnológico",
                null,
                true
            ),
            
            "Registrar - área con acentos" => $aux(
                "Gestión de Proyectos",
                null,
                true
            ),
            
            "Registrar - subárea con nombre válido" => $aux(
                "Reclutamiento",
                $idAreaValido,
                true
            ),
            
            "Registrar - solo idArea sin nombre" => $aux(
                null,
                $idAreaValido,
                false,
                ...["mensaje esperado" => "El nombre del área es requerido"]
            ),
            
            "Registrar - todos los campos nulos" => $aux(
                null,
                null,
                false,
                ...["mensaje esperado" => "El nombre del área es requerido"]
            ),
            
            "Registrar - nombre duplicado" => $aux(
                $nombreDuplicado, // Mismo nombre que el primer caso exitoso
                null,
                false,
                ...["mensaje esperado" => "El nombre del area (".$nombreDuplicado.") ya esta registrado"]
            ),
        ];
    }

    /**
     * @dataProvider actualizarProvider
     */
    public function testActualizarArea($id, $nombre, $idArea, $respuesta_esperada, ...$otros)
    {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Actualizar Area"))->log();

        if($id == "NotFound" || $idArea == "NotFound") {
            $this->fail("El id de area no fue encontrado");
        }

        $datos = [
            "id" => $id,
            "nombre" => $nombre,
            "idArea" => $idArea
        ];
        // eliminar valores nulos que no se quieran enviar
        foreach ($datos as $key => $value) {
            if ($value === null) {
                unset($datos[$key]);
            }
        }
        $this->areaObj->setterArray($datos);

        $resp = $this->areaObj->actualizar();
        
        $this->assertIsBool($resp);
        $this->assertEquals($respuesta_esperada, $resp, $_logger["dataname"]);
        
        // Verificar mensaje de error en sesión si la prueba falla
        if (!$respuesta_esperada && isset($_SESSION['errores'])) {
            $ultimoError = end($_SESSION['errores']);
            if (isset($otros["mensaje esperado"])) {
                $this->assertStringContainsString($otros[0]["mensaje esperado"], $ultimoError);
            }
        }
        
        // Limpiar sesión después de cada prueba
        if (isset($_SESSION['errores'])) {
            $_SESSION['errores'] = [];
        }
    }

    public function actualizarProvider(){
        $aux = function ($id, $nombre, $RespuestaEsperada, $mensaje, $idAreaPadre = null, ...$otros){
            return [
                "Id" => intval($id),
                "Nombre Area" => $nombre,
                "Id Area Padre" => $idAreaPadre,
                "resultado esperado" => $RespuestaEsperada,
                "mensaje esperado" => $mensaje,
                ...$otros
            ];
        };
        $db = Database::getInstance();
        $db->connect();
        $idExistente = $db->pdo()->query("SELECT id from area limit 1")->fetchColumn();
        $idExistente2 = $db->pdo()->query("SELECT id from area limit 2,1")->fetchColumn();

        if(!$idExistente) $idExistente = "NotFound";
        if(!$idExistente2) $idExistente2 = "NotFound";


        return [
            "Entradas Validas - Actualizar Area" => $aux(
                $idExistente,
                "Area Actualizada",
                true,
                "Area actualizada correctamente"
            ),
            "Entradas Validas - Actualizar Subarea" => $aux(
                $idExistente,
                "Subarea Actualizada",
                true,
                "Area actualizada correctamente",
                $idExistente2
            ),
            "Entradas Invalidas - Area padre igual a si mismo" => $aux(
                $idExistente,
                "Subarea Actualizada",
                false,
                "El area no puede ser padre de si mismo",
                $idExistente
            ),
            "Entradas Invalidas - id area no existe" => $aux(
                0,
                "Subarea Actualizada",
                false,
                "El area seleccionada no existe",
                $idExistente),
            "Entradas Invalidas - id area padre no existe" => $aux(
                $idExistente,
                "Subarea Actualizada",
                false,
                "El area padre seleccionada no existe",
                99999),
        ];
    }

    /**
     * 
     * @dataProvider eliminarAreaProvider
     */
    public function testEliminarArea($id, $respuesta_esperada, $mensaje, ...$otros) {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Eliminar Area"))->log();
        if($id == "NotFound") {
            $this->fail("El id de area no fue encontrado");
        }
        $this->areaObj->set_id($id);
        $this->areaObj->eliminarArea();

        if($respuesta_esperada){
            $this->assertArrayHasKey("exitos", $_SESSION, $_logger["dataname"]);
        }
        else{
            $this->assertArrayHasKey("errores", $_SESSION, $_logger["dataname"]);
        }
        
        
        // Limpiar sesión luego de cada prueba
        if (isset($_SESSION['errores'])) {
            $_SESSION['errores'] = [];
        }



    }

    public function eliminarAreaProvider(){
        $aux = function ($id, $RespuestaEsperada, $mensaje, ...$otros){
            return [
                "Id" => intval($id),
                "resultado esperado" => $RespuestaEsperada,
                "mensaje esperado" => $mensaje,
                ...$otros
            ];
        };
        $db = Database::getInstance();
        $db->connect();
        $idExistente = 89;
        $idExistentePadre = $db->pdo()->query("SELECT idAreaPadre from subarea limit 1")->fetchColumn();

        if(!$idExistente) $idExistente = "NotFound";

        return [
            "Entradas Validas - Eliminar Area" => $aux(
                $idExistente,
                true,
                "Área eliminada con exito"
            ),
            "Entradas Invalidas - Eliminar Area no existente" => $aux(
                9999999,
                false,
                "El área que intenta eliminar no existe"
            ),
            "Entradas Validas - Eliminar Area con subareas" => $aux(
                $idExistentePadre,
                false,
                "El área que intenta eliminar tiene subareas, asegurate de eliminarlas primero"
            ),
        ];
    }
    





}