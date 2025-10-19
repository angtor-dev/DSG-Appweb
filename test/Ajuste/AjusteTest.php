<?php 
use PHPUnit\Framework\TestCase;
session_start();


class AjusteTest extends TestCase {
    private $ajusteObj;
    private $testSuiteControl;
    protected function setUp(): void
    {
        $this->ajusteObj = new Ajuste();
        $this->testSuiteControl = "Ajustes";
        $this->ajusteObj->setTestingMode(true);
        
    }

    /**
     * @dataProvider registrarAjusteProvider
     */

    public function testRegistrarAjuste($idInventario, $cantidad, $descripcion, $fechaIncidente, $respuesta_esperada, ...$otros)
    {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl))->log();

        $this->ajusteObj->setDatos($idInventario, $cantidad, $descripcion, $fechaIncidente);

        $resp = $this->ajusteObj->registrar();
        
        $this->assertIsBool($resp);
        $this->assertEquals($respuesta_esperada, $resp, $_logger["dataname"]);
        
        if (!$respuesta_esperada && isset($_SESSION['errores'])) {
            $ultimoError = end($_SESSION['errores']);
            if (isset($otros[0]["mensaje error esperado"])) {
                $this->assertStringContainsString($otros[0]["mensaje error esperado"], $ultimoError);
            }
        }
        
        if (isset($_SESSION['errores'])) {
            $_SESSION['errores'] = [];
        }
    }

    public function registrarAjusteProvider()
    {
        $aux = function($idInventario, $cantidad, $descripcion, $fechaIncidente, $respuesta_esperada, ...$otros) {
            return [
                "idInventario" => $idInventario,
                "cantidad" => $cantidad,
                "descripcion" => $descripcion,
                "fechaIncidente" => $fechaIncidente,
                "resultado esperado" => $respuesta_esperada,
                ...$otros
            ];
        };

        // Datos de prueba
        $idInventarioValido = 2; // ID que existe en la base de datos de pruebas
        $idInventarioInvalido = 99999; // ID que no existe
        $cantidadPositiva = 5;
        $cantidadNegativa = -3;
        $cantidadCero = 0;
        $descripcionValida = "Ajuste por pérdida de inventario";
        $descripcionVacia = "";
        $fechaValida = "2024-01-15";
        $fechaInvalida = "fecha-invalida";

        return [
            "Registrar - entrada válida" => $aux(
                $idInventarioValido,
                $cantidadPositiva,
                $descripcionValida,
                $fechaValida,
                true
            ),
            
            "Registrar - cantidad negativa" => $aux(
                $idInventarioValido,
                $cantidadNegativa,
                $descripcionValida,
                $fechaValida,
                true 
            ),
            
            "Registrar - cantidad cero" => $aux(
                $idInventarioValido,
                $cantidadCero,
                $descripcionValida,
                $fechaValida,
                true
            ),
            
            "Registrar - descripción vacía" => $aux(
                $idInventarioValido,
                $cantidadPositiva,
                $descripcionVacia,
                $fechaValida,
                true 
            ),
            
            "Registrar - idInventario inválido" => $aux(
                $idInventarioInvalido,
                $cantidadPositiva,
                $descripcionValida,
                $fechaValida,
                false,
                ["mensaje error esperado" => "Ocurrió un error al registrar el ajuste"]
            ),
            // TODO activar esto
            // "Registrar - fecha inválida" => $aux(
            //     $idInventarioValido,
            //     $cantidadPositiva,
            //     $descripcionValida,
            //     $fechaInvalida,
            //     false,
            //     ["mensaje error esperado" => "Ocurrió un error al registrar el ajuste"]
            // ),
            
            // "Registrar - idInventario nulo" => $aux(
            //     null,
            //     $cantidadPositiva,
            //     $descripcionValida,
            //     $fechaValida,
            //     false,
            //     ["mensaje error esperado" => "Ocurrió un error al registrar el ajuste"]
            // ),
            
            // "Registrar - cantidad nula" => $aux(
            //     $idInventarioValido,
            //     null,
            //     $descripcionValida,
            //     $fechaValida,
            //     false,
            //     ["mensaje error esperado" => "Ocurrió un error al registrar el ajuste"]
            // ),
            
            // "Registrar - descripción nula" => $aux(
            //     $idInventarioValido,
            //     $cantidadPositiva,
            //     null,
            //     $fechaValida,
            //     false,
            //     ["mensaje error esperado" => "Ocurrió un error al registrar el ajuste"]
            // ),
            
            // "Registrar - fecha nula" => $aux(
            //     $idInventarioValido,
            //     $cantidadPositiva,
            //     $descripcionValida,
            //     null,
            //     false,
            //     ["mensaje error esperado" => "Ocurrió un error al registrar el ajuste"]
            // ),
            
            // "Registrar - todos los campos nulos" => $aux(
            //     null,
            //     null,
            //     null,
            //     null,
            //     false,
            //     ["mensaje error esperado" => "Ocurrió un error al registrar el ajuste"]
            // ),
            
            "Registrar - ajuste que agota artículo" => $aux(
                $idInventarioValido, 
                -1000, 
                "Ajuste por pérdida total",
                $fechaValida,
                true
            ),
        ];
    }

    /**
     * @dataProvider listarAjustesProvider
     */
    public function testListarAjustes()
    {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl))->log();

        $resp = $this->ajusteObj->listar();
        
        $this->assertIsArray($resp, $_logger["dataname"]);
        
        // Si el array no está vacío, verificar la estructura de los objetos
        if (!empty($resp)) {
            $primerAjuste = $resp[0];
            
            // Verificar propiedades básicas del ajuste
            $propiedadesRequeridas = [
                'id', 'idInventario', 'cantidad', 'descripcion', 
                'fechaIncidente', 'fechaCreacion', 'articulo'
            ];
            
            foreach ($propiedadesRequeridas as $propiedad) {
                $this->assertObjectHasAttribute($propiedad, $primerAjuste, 
                    "El objeto ajuste debe tener la propiedad: {$propiedad}");
            }
            
            // Verificar que el artículo es una instancia de Articulo
            if (isset($primerAjuste->articulo)) {
                $this->assertInstanceOf(Articulo::class, $primerAjuste->articulo);
            }
        }
    }

   

    public function listarAjustesProvider()
    {
        return [
            "Listar ajustes - caso normal" => [
                "N/A" => "No hay entradas para este metodo",
                "resultado esperado" => "Arreglo de Ajustes",
            ]
        ];
    }





    
}