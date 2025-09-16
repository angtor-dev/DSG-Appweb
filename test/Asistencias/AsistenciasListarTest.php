<?php 
use PHPUnit\Framework\TestCase;
$test_suite = "Asistencias";
// la clase debe llamarse igual que el archivo
class AsistenciasListarTest extends TestCase
{
    private $asistenciaObj;

    protected function setUp(): void
    {
        $this->asistenciaObj = new Asistencia;
    }

    /**
     * @dataProvider ListarAsistenciasProvider
     */
    public  function testListarAsistencias($departamento,$turno,$fecha,$resultado_esperado){
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
            $this->assertArrayNotHasKey('listaTrabajadores',$respuesta, $mensaje.="el arreglo tiene la llave listaTrabajadores pero no deberia");
        }

        global $test_suite;
        (new LoggerPhpUnit($this, $test_suite))->log();
        

        
    }

    public static function ListarAsistenciasProvider()
    {
    
        return [
            "caso 1" => [
                "Division" => "3",
                "Turno" => "2",
                "fecha" => "2025-01-30",
                "respuesta esperada"=>true
            ],
            "caso 2" => [
                "Division" => "3",
                "Turno" => "2",
                "fecha" => "2025-01-29",
                "respuesta esperada"=>true
            ],
            "caso 3" => [
                "Division" => "3",
                "Turno" => "2",
                "fecha" => "2025-01-28",
                "respuesta esperada"=>true
            ],
        ];
    }
}