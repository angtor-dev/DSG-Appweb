<?php 
use PHPUnit\Framework\TestCase;
// la clase debe llamarse igual que el archivo
class AsistenciasListarTest extends TestCase
{
    private $asistenciaObj;
    public $testSuiteControl;

    protected function setUp(): void
    {
        $this->asistenciaObj = new Asistencia;
        $this->testSuiteControl = "Asistencias";

    }

    /**
     * @dataProvider ListarAsistenciasProvider
     */
    public  function testListarAsistencias($departamento,$turno,$fecha,$resultado_esperado){
        (new LoggerPhpUnit($this, $this->testSuiteControl))->log();
        $_POST['idDepartamento'] = $departamento;
        $_POST['turno'] = $turno;
        $_POST['fecha'] = $fecha;

        $this->asistenciaObj->setterArray([
            "idDepartamento" => $_POST['idDepartamento'],
            "fecha" => $_POST['fecha'],
            "turno" => $_POST['turno']
        ]);

        $respuesta = $this->asistenciaObj->verAsistencias(false);
        

        $mensaje = "dataset: ".$this->dataName()." - ";
        

        $this->assertNotNull($respuesta);
        $this->assertIsArray($respuesta);

        $this->assertEquals($resultado_esperado, $respuesta['success'], $mensaje.= "success failed");



        
        if($resultado_esperado == true){
            $this->assertArrayHasKey('listaTrabajadores',$respuesta, $mensaje.="el arreglo no tiene la llave listaTrabajadores");
            $this->assertIsObject($respuesta['listaTrabajadores'], $mensaje.="el valor de la llave listaTrabajadores no es un objeto");
        }
        else{
            $this->assertArrayNotHasKey('listaTrabajadores',$respuesta, $mensaje.="el arreglo tiene la llave listaTrabajadores pero no debería");
        }

        
        

        
    }

    public static function ListarAsistenciasProvider()
    {
    
        return [
            "caso 1" => [
                "División" => "3",
                "Turno" => "2",
                "fecha" => "2025-01-30",
                "resultado esperado"=>true
            ],
            "caso 2" => [
                "División" => "3",
                "Turno" => "2",
                "fecha" => "2025-01-29",
                "resultado esperado"=>true
            ],
            "caso 3" => [
                "División" => "3",
                "Turno" => "2",
                "fecha" => "2025-01-28",
                "resultado esperado"=>true
            ],
        ];
    }
}