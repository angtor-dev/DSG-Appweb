<?php 
use PHPUnit\Framework\TestCase;

class CategoriaTest extends TestCase
{
    private $categoriaObj;
    private $testSuiteControl;
    protected function setUp(): void
    {
        $this->categoriaObj = new Categoria();
        $this->testSuiteControl = "Categorias";
        $this->categoriaObj->setTestingMode(true);
    }

    /**
     * * @dataProvider listarCategoriaProvider
     * 
     */
    public function testListarCategoria()
    {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Listar Categorias de Inventario", "Deben haber categorias registradas"))->log();

        $resp = $this->categoriaObj->listar();
        
        $this->assertIsArray($resp, $_logger["dataname"]);
        
        // Si el array no está vacío, verificar la estructura de los objetos
        if (!empty($resp)) {
            $primeraCategoria = $resp[0];
            
            // Verificar propiedades básicas del ajuste
            $propiedadesRequeridas = [
                'id', 'nombre', 'descripcion', 'color', 
            ];
            
            foreach ($propiedadesRequeridas as $propiedad) {
                $this->assertObjectHasProperty($propiedad, $primeraCategoria, 
                    "El objeto ajuste debe tener la propiedad: {$propiedad}");
            }
            
            // Verificar que el artículo es una instancia de Articulo
            if (isset($primeraCategoria->articulo)) {
                $this->assertInstanceOf(Categoria::class, $primeraCategoria);
            }
        }
    }

   

    public function listarCategoriaProvider()
    {
        return [
            "Listar Categorias" => [
                "N/A" => "No hay entradas para este metodo",
                "resultado esperado" => "Arreglo de Categorias",
                "--observacion"=> "Devuelve un arreglo de objetos de Categoria o un arreglo vacio",
            ]
        ];
    }
    /**
     * @dataProvider registrarCategoriaProvider
     */
    public function testRegistrarCategoria($nombre, $descripcion, $color, $resultadoEsperado, ...$otro)
    {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Registrar Categoria de Inventario"))->log();

        $this->categoriaObj->setDatos(null, $nombre, $descripcion, $color);

        $resp = $this->categoriaObj->registrar();
        
        $this->assertIsBool($resp, $_logger["dataname"]);
        $this->assertEquals($resultadoEsperado, $resp, $_logger["dataname"]);
        if($resultadoEsperado == false) {
            $this->assertArrayHasKey("errores", $_SESSION, $_logger["dataname"]);
        }
    }

    public function registrarCategoriaProvider()
    {
        $aux = function ($nombre, $descripcion, $color, $resultadoEsperado, ...$otros) {
            return [
                "Nombre" => $nombre,
                "Descripcion" => $descripcion,
                "Color" => $color,
                "resultado esperado" => $resultadoEsperado,
                ...$otros
            ];
        };

        return [
            "Registrar - entrada válida" => $aux(
                "Categoria 1",
                "Descripcion de la categoria 1",
                "#FFFFFF",
                true,
                ...["--observacion" => "devuelve true si se pudo registrar la categoria"]
            )
        ];
    }
    /**
     * @dataProvider actualizarCategoriaProvider
     */
    public function testActualizarCategoria($id, $nombre, $descripcion, $color, $resultadoEsperado, ...$otro)
    {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Actualizar Categoria de Inventario"))->log();

        $this->categoriaObj->setDatos($id, $nombre, $descripcion, $color);

        $resp = $this->categoriaObj->actualizar();
        
        $this->assertIsBool($resp, $_logger["dataname"]);
        $this->assertEquals($resultadoEsperado, $resp, $_logger["dataname"]);
        if($resultadoEsperado == false) {
            $this->assertArrayHasKey("errores", $_SESSION, $_logger["dataname"]);
        }
    }

    public function actualizarCategoriaProvider()
    {
        $aux = function ($id, $nombre, $descripcion, $color, $resultadoEsperado, ...$otros) {
            return [
                "Id" => $id,
                "Nombre" => $nombre,
                "Descripcion" => $descripcion,
                "Color" => $color,
                "resultado esperado" => $resultadoEsperado,
                ...$otros
            ];
        };
        $idValido = 1;

        return [
            "Actualizar - entrada válida" => $aux(
                $idValido,
                "Categoria 1",
                "Descripcion de la categoria 1",
                "#FFFFFF",
                true,
                ...["--observacion" => "devuelve true si se pudo actualizar la categoria"]
            )
        ];
    }

    /**
     * @dataProvider eliminarProviderR
     */
    public function testEliminarCategoria($id, $respuestaEsperada, ...$otros) {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Eliminar Articulo", "Debe haber una categoría para eliminar sin relaciones"))->log();
        /** * @var Categoria */
        $categoria = Categoria::cargar($id);

        if($respuestaEsperada){
            $this->assertInstanceOf(Categoria::class, $categoria, $_logger["dataname"]);
        }

        if($categoria instanceof Categoria){

            $categoria->setTestingMode($this->categoriaObj->getTestingMode());
    
            if (empty($categoria)) {
                $_SESSION['errores'][] = "La categoría que intenta eliminar no existe";
            }
    
            if ( $resp = $categoria->eliminar(false)) {
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

        $idValido = 37; // no relacionado
        $idRelacionado = 2;

        return [
            "Entradas Validas - Eliminar Categoria" => $aux(
                $idValido,
                true,
                ...["mensaje esperado" => "Categoría eliminada con exito"]
            ),
            "Entradas Invalidas - Eliminar Categoria id no existe" => $aux(
                0,
                false,
                ...["mensaje esperado" => "El Categoria seleccionado no existe"]
            ),
            "Entradas Invalidas - Eliminar Categoria id relacionado" => $aux(
                $idRelacionado,
                false,
                ...["mensaje esperado" => "Existen datos relacionados al item seleccionado."]
            ),
        ];
    }
}