<?php 
use PHPUnit\Framework\TestCase;



class ArticuloTest extends TestCase
{
    private $articuloObj;
    private $testSuiteControl;
    protected function setUp(): void
    {
        $this->articuloObj = new Articulo();
        $this->testSuiteControl = "Articulos";
        $this->articuloObj->setTestingMode(true);
    }

    /**
     * @dataProvider listarProvider
     */
    public function testListarArticulo()
    {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Listar Articulos"))->log();

        $resp = $this->articuloObj->listar();
        
        $this->assertIsArray($resp, $_logger["dataname"]);
        
        // Si el array no está vacío, verificar la estructura de los objetos
        if (!empty($resp)) {
            
            // Verificar propiedades básicas del ajuste
            $propiedadesRequeridas = [
                'id', 
                'idCategoria',
                'idMedida',
                'nombre',
                'descripcion',
                'cantidad',
                'esConsumible',
                'categoria',
                'medida',
            ];
            foreach ($resp as $articulo) {

                $this->assertInstanceOf(Articulo::class, $articulo, $_logger["dataname"]);

                foreach ($propiedadesRequeridas as $propiedad) {
                    $this->assertObjectHasProperty($propiedad, $articulo, 
                    "El objeto ajuste debe tener la propiedad: {$propiedad}");
                }

                $this->assertInstanceOf(Medida::class, $articulo->medida, $_logger["dataname"]);
                $this->assertInstanceOf(Categoria::class, $articulo->categoria, $_logger["dataname"]);
                
            }
            
        }
    }



     public function listarProvider()
    {
        return [
            "Listar Articulos - caso normal" => [
                "N/A" => "No hay entradas para este metodo",
                "resultado esperado" => "Arreglo de Articulos",
            ]
        ];
    }

    /**
     * @dataProvider registrarProvider
     */
    public function testRegistrarArticulo($nombre, $descripcion, $idMedida, $idCategoria, $esConsumible,$respuestaEsperada, ...$otros ){
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Registrar Articulo"))->log();

        $datos = [
            "nombre" => $nombre,
            "descripcion" => $descripcion,
            "idMedida" => $idMedida,
            "idCategoria" => $idCategoria,
            "esConsumible" => $esConsumible
        ];
        $this->articuloObj->setterArray($datos);


        $resp = $this->articuloObj->registrar();
        $this->assertEquals($respuestaEsperada, $resp, $_logger["dataname"]);

        if($respuestaEsperada == false) {
            $this->assertArrayHasKey("errores", $_SESSION, $_logger["dataname"]);
        }
        // Limpiar sesión luego de cada prueba
        $_SESSION = [];
    }

    public function registrarProvider (){

        $aux = function (string $nombre, string $descripcion, int $idMedida, int $idCategoria, bool $esConsumible, bool $respuestaEsperada, ...$otros) {
            return [
                "Nombre" => $nombre,
                "Descripcion" => $descripcion,
                "IdMedida" => $idMedida,
                "IdCategoria" => $idCategoria,
                "EsConsumible" => $esConsumible,
                "resultado esperado" => $respuestaEsperada,
                ...$otros
            ];
        };

        $idMedidaValida = 1; // unidades
        $idCategoriaValida = 1; // suministros
        $nombreValido = "Articulo 1";
        $descripcionValida = "Descripcion 1";
        $esConsumibleValido = true;
        $nombreRepetido = "Etiquetas Adhesivas";

        return [
            "Entradas Validas - Registrar Articulo" => $aux(
                $nombreValido,
                $descripcionValida,
                $idMedidaValida,
                $idCategoriaValida,
                $esConsumibleValido,
                true,
                ...["mensaje esperado" => "Artículo registrado con exito"]
            ),
            "Entradas Validas - Registrar Articulo descripcion vacía" => $aux(
                $nombreValido,
                "",
                $idMedidaValida,
                $idCategoriaValida,
                $esConsumibleValido,
                true,
                ...["mensaje esperado" => "Artículo registrado con exito"]
            ),
            "Entradas Invalidas - Registrar Articulo nombre vacio" => $aux(
                "",
                $descripcionValida,
                $idMedidaValida,
                $idCategoriaValida,
                $esConsumibleValido,
                false,
                ...["mensaje esperado" => "El nombre no puede estar vacio"]
            ),
            "Entradas Invalidas - Medida no existe" => $aux(
                $nombreValido,
                $descripcionValida,
                0,
                $idCategoriaValida,
                $esConsumibleValido,
                false,
                ...["mensaje esperado" => "La medida/Categoria no existe"]
            ),
            "Entradas Invalidas - Categoria no existe" => $aux(
                $nombreValido,
                $descripcionValida,
                $idMedidaValida,
                0,
                $esConsumibleValido,
                false,
                ...["mensaje esperado" => "La medida/Categoria no existe"]
            ),
            "Entradas Invalidas - Nombre Repetido" => $aux(
                $nombreRepetido,
                $descripcionValida,
                $idMedidaValida,
                $idCategoriaValida,
                $esConsumibleValido,
                false,
                ...["mensaje esperado" => "El articulo con el nombre {$nombreRepetido} ya existe"]
            ),
        ];

    }

    /**
     * @dataProvider actualizarProvider
     */
    public function testActualizarArticulo($id, $nombre, $descripcion, $idMedida, $idCategoria, $esConsumible,$respuestaEsperada, ...$otros ){
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Actualizar Articulo"))->log();

        $datos = [
            "id" => $id,
            "nombre" => $nombre,
            "descripcion" => $descripcion,
            "idMedida" => $idMedida,
            "idCategoria" => $idCategoria,
            "esConsumible" => $esConsumible
        ];
        $this->articuloObj->setterArray($datos);


        $resp = $this->articuloObj->actualizar();
        $this->assertEquals($respuestaEsperada, $resp, $_logger["dataname"]);

        if($respuestaEsperada == false) {
            $this->assertArrayHasKey("errores", $_SESSION, $_logger["dataname"]);
        }
        // Limpiar sesión luego de cada prueba
        $_SESSION = [];
    }

    public function actualizarProvider(){

        $aux = function (int $id, string $nombre, string $descripcion, int $idMedida, int $idCategoria, bool $esConsumible, bool $respuestaEsperada, ...$otros) {
            return [
                "Id" => $id,
                "Nombre" => $nombre,
                "Descripcion" => $descripcion,
                "IdMedida" => $idMedida,
                "IdCategoria" => $idCategoria,
                "EsConsumible" => $esConsumible,
                "resultado esperado" => $respuestaEsperada,
                ...$otros
            ];
        };
        $idArticuloValido = 1; // Bolígrafo Azul
        $idMedidaValida = 1; // unidades
        $idCategoriaValida = 1; // suministros
        $nombreValido = "Articulo 1";
        $descripcionValida = "Descripcion 1";
        $esConsumibleValido = true;
        $nombreRepetido = "Etiquetas Adhesivas";

        return [
            "Entradas Validas - Actualizar Articulo" => $aux(
                $idArticuloValido,
                $nombreValido,
                $descripcionValida,
                $idMedidaValida,
                $idCategoriaValida,
                $esConsumibleValido,
                true,
                ...["mensaje esperado" => "Articulo actualizado con exito"]
            ),
            "Entradas Validas - Actualizar Articulo descripcion vacia" => $aux(
                $idArticuloValido,
                $nombreValido,
                "",
                $idMedidaValida,
                $idCategoriaValida,
                $esConsumibleValido,
                true,
                ...["mensaje esperado" => "Articulo actualizado con exito"]
            ),
            "Entradas Invalidas - Actualizar Articulo id no existe" => $aux(
                0,
                $nombreValido,
                $descripcionValida,
                $idMedidaValida,
                $idCategoriaValida,
                $esConsumibleValido,
                false,
                ...["mensaje esperado" => "El articulo seleccionado no existe"]
            ),
            "Entradas Invalidas - Actualziar Articulo Nombre Repetido" => $aux(
                $idArticuloValido,
                $nombreRepetido,
                $descripcionValida,
                $idMedidaValida,
                $idCategoriaValida,
                $esConsumibleValido,
                false,
                ...["mensaje esperado" => "El articulo con el nombre {$nombreRepetido} ya existe"]
            ),
            "Entrada Invalida - Actualizar Articulo Nombre Vacio" => $aux(
                $idArticuloValido,
                "",
                $descripcionValida,
                $idMedidaValida,
                $idCategoriaValida,
                $esConsumibleValido,
                false,
                ...["mensaje esperado" => "El nombre no puede estar vacio"]
            ),
            "Entradas Invalidas - Actualizar Articulo Medida No Existe" => $aux(
                $idArticuloValido,
                $nombreValido,
                $descripcionValida,
                0,
                $idCategoriaValida,
                $esConsumibleValido,
                false,
                ...["mensaje esperado" => "La medida/Categoria no existe"]
            ),
            "Entradas Invalidas - Actualizar Articulo Categoria No Existe" => $aux(
                $idArticuloValido,
                $nombreValido,
                $descripcionValida,
                $idMedidaValida,
                0,
                $esConsumibleValido,
                false,
                ...["mensaje esperado" => "La medida/Categoria no existe"]
            ),

            
        ];
    }

    /**
     * @dataProvider eliminarProvider
     */
    public function testEliminarArticulo($id, $respuestaEsperada, ...$otros) {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Eliminar Articulo"))->log();
        /** * @var Area */
        $area = Articulo::cargar($id);

        if($respuestaEsperada){
            $this->assertInstanceOf(Articulo::class, $area, $_logger["dataname"]);
        }


        if($area instanceof Articulo){

            $area->setTestingMode($this->articuloObj->getTestingMode());
    
            if (empty($area)) {
                $_SESSION['errores'][] = "El artículo que intenta eliminar no existe";
            }
    
            if ( $resp = $area->eliminar(false)) {
                $_SESSION['exitos'][] = "Artículo eliminada con exito";
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

    public function eliminarProvider(){
        $aux = function (int $id, bool $respuestaEsperada, ...$otros) {
            return [
                "Id" => $id,
                "resultado esperado" => $respuestaEsperada,
                ...$otros
            ];
        };

        $idValido = 30; // no relacionado
        $idRelacionado = 2;

        return [
            "Entradas Validas - Eliminar Articulo" => $aux(
                $idValido,
                true,
                ...["mensaje esperado" => "Articulo eliminado con exito"]
            ),
            "Entradas Invalidas - Eliminar Articulo id no existe" => $aux(
                0,
                false,
                ...["mensaje esperado" => "El articulo seleccionado no existe"]
            ),
            "Entradas Invalidas - Eliminar Articulo id relacionado" => $aux(
                $idRelacionado,
                false,
                ...["mensaje esperado" => "Existen datos relacionados al item seleccionado."]
            ),
        ];
    }
}