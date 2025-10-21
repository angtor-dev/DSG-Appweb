<?php 
use PHPUnit\Framework\TestCase;

class MedidaTest extends TestCase
{
    private $medidaObj;
    private $testSuiteControl;
    protected function setUp(): void
    {
        $this->medidaObj = new Medida();
        $this->testSuiteControl = "Medidas";
        $this->medidaObj->setTestingMode(true);
    }

    /**
     * @dataProvider listarMedidaProvider
     */
    public function testListarMedida()
    {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Listar Medidas de Inventario", "Deben haber Medidas registradas"))->log();

        $resp = $this->medidaObj->listar();
        
        $this->assertIsArray($resp, $_logger["dataname"]);
        
        // Si el array no está vacío, verificar la estructura de los objetos
        if (!empty($resp)) {
            $primeraMedida = $resp[0];
            
            // Verificar propiedades básicas del ajuste
            $propiedadesRequeridas = [
                'id', 'unidad', 'subUnidad', 
            ];
            
            foreach ($propiedadesRequeridas as $propiedad) {
                $this->assertObjectHasProperty($propiedad, $primeraMedida, 
                    "El objeto ajuste debe tener la propiedad: {$propiedad}");
            }
            
            // Verificar que el artículo es una instancia de Articulo
            if (isset($primeraMedida->articulo)) {
                $this->assertInstanceOf(Medida::class, $primeraMedida);
            }
        }
    }

   

    public function listarMedidaProvider()
    {
        return [
            "Listar ajustes - caso normal" => [
                "N/A" => "No hay entradas para este metodo",
                "resultado esperado" => "Arreglo de Medidas",
                "--observacion"=> "devuelve un arreglo de objetos de Medida o un arreglo vacio",
            ]
        ];
    }
    /**
     * @dataProvider registrarMedidaProvider
     */
    public function testRegistrarMedida($unidad, $subUnidad, $resultadoEsperado, ...$otro)
    {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Registrar Medida de Inventario"))->log();

        $this->medidaObj->setDatos(null, $unidad, $subUnidad);

        $resp = $this->medidaObj->registrar();
        
        $this->assertIsBool($resp, $_logger["dataname"]);
        $this->assertEquals($resultadoEsperado, $resp, $_logger["dataname"]);
        if($resultadoEsperado == false) {
            $this->assertArrayHasKey("errores", $_SESSION, $_logger["dataname"]);
        }
    }

    public function registrarMedidaProvider()
    {
        $aux = function ($unidad, $subUnidad, $resultadoEsperado, ...$otros) {
            return [
                "Nombre" => $unidad,
                "Descripcion" => $subUnidad,
                "resultado esperado" => $resultadoEsperado,
                ...$otros
            ];
        };

        return [
            "Registrar - entrada válida" => $aux(
                "MEDIDA 1",
                "M",
                true,
                ...["--observacion" => "devuelve true si se pudo registrar la Medida"]
            )
        ];
    }
    /**
     * @dataProvider actualizarMedidaProvider
     */
    public function testActualizarMedida($id, $unidad, $subUnidad, $resultadoEsperado, ...$otro)
    {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Actualizar Medida de Inventario"))->log();

        $this->medidaObj->setDatos($id, $unidad, $subUnidad);

        $resp = $this->medidaObj->actualizar();
        
        $this->assertIsBool($resp, $_logger["dataname"]);
        $this->assertEquals($resultadoEsperado, $resp, $_logger["dataname"]);
        if($resultadoEsperado == false) {
            $this->assertArrayHasKey("errores", $_SESSION, $_logger["dataname"]);
        }
    }

    public function actualizarMedidaProvider()
    {
        $aux = function ($id, $unidad, $subUnidad, $resultadoEsperado, ...$otros) {
            return [
                "Id" => $id,
                "Nombre" => $unidad,
                "Descripcion" => $subUnidad,
                "resultado esperado" => $resultadoEsperado,
                ...$otros
            ];
        };
        $idValido = 1;

        return [
            "Actualizar - entrada válida" => $aux(
                $idValido,
                "Medida 1",
                "M",
                true,
                ...["--observacion" => "devuelve true si se pudo actualizar la Medida"]
            )
        ];
    }

    /**
     * @dataProvider eliminarProviderR
     */
    public function testEliminarMedida($id, $respuestaEsperada, ...$otros) {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Eliminar Articulo", "Debe haber una categoría para eliminar sin relaciones"))->log();
        /** * @var Medida */
        $Medida = Medida::cargar($id);

        if($respuestaEsperada){
            $this->assertInstanceOf(Medida::class, $Medida, $_logger["dataname"]);
        }

        if($Medida instanceof Medida){

            $Medida->setTestingMode($this->medidaObj->getTestingMode());
    
            if (empty($Medida)) {
                $_SESSION['errores'][] = "La categoría que intenta eliminar no existe";
            }
    
            if ( $resp = $Medida->eliminar(false)) {
                $_SESSION['exitos'][] = "Categoría eliminada con exito";
            }

            if($respuestaEsperada){
                $mensaje = $_logger["dataname"];
                $mensaje .= (isset($_SESSION["errores"])) ? " :: ". $_SESSION["errores"][0] : "";
                $this->assertArrayHasKey("exitos", $_SESSION, $mensaje);
            }
            else{
                $this->assertArrayHasKey("errores", $_SESSION, $_logger["dataname"]);
            }
            $this->assertEquals($respuestaEsperada, $resp, $_logger["dataname"]);
        }
        else{
            $resp = false;
            $this->assertEquals($respuestaEsperada, $resp, $_logger["dataname"]);
        }

    }

    public function eliminarProviderR(){
        $aux = function (int $id, bool $respuestaEsperada, ...$otros) {
            return [
                "Id" => $id,
                "resultado esperado" => $respuestaEsperada,
                ...$otros
            ];
        };

        $idValido = 4; // no relacionado
        $idRelacionado = 1;

        return [
            "Entradas Validas - Eliminar Medida" => $aux(
                $idValido,
                true,
                ...["mensaje esperado" => "Categoría eliminada con exito"]
            ),
            "Entradas Invalidas - Eliminar Medida id no existe" => $aux(
                0,
                false,
                ...["mensaje esperado" => "El Medida seleccionado no existe"]
            ),
            "Entradas Invalidas - Eliminar Medida id relacionado" => $aux(
                $idRelacionado,
                false,
                ...["mensaje esperado" => "Existen datos relacionados al item seleccionado."]
            ),
        ];
    }
}