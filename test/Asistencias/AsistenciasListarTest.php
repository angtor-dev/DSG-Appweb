<?php 
use PHPUnit\Framework\TestCase;
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
    public  function testListarAsistencias($departamento,$turno,$fecha,$resultado_esperado,$num_caso){
        
        $_POST['idDepartamento'] = $departamento;
        $_POST['turno'] = $turno;
        $_POST['fecha'] = $fecha;

        $this->asistenciaObj->mapearFormulario();

        $respuesta = $this->asistenciaObj->listarAsistenciasTrabajadores(false);
        

        $mensaje = "caso ($num_caso)";

        $this->assertNotNull($respuesta);
        $this->assertIsArray($respuesta);

        
        $this->assertIsObject($respuesta['listaTrabajadores']);

        $this->assertEquals($resultado_esperado, $respuesta['success'], $mensaje);
        
    }

    public static function ListarAsistenciasProvider()
    {
        return [
            ["3","Tarde","2025-01-31",true,"1"],
            ["3","Especial","2025-03-09",true,"2"],
            ["4","Noche","2025-04-27",true,"3"],
            ["1","Especial","2024-09-23",true,"4"],
            ["2","Especial","2024-07-01",true,"5"],
            ["3","Noche","2024-05-26",true,"6"],
            ["4","Fin de Semana","2025-01-20",true,"7"],
            ["2","Fin de Semana","2024-11-14",true,"8"],
            ["2","Especial","2025-02-18",true,"9"],
            ["2","Mañana","2024-07-29",true,"10"],
            ["2","Tarde","2025-03-26",true,"11"],
            ["3","Fin de Semana","2024-08-25",true,"12"],
            ["3","Fin de Semana","2024-09-12",true,"13"],
            ["3","Noche","2024-06-15",true,"14"],
            ["2","Especial","2025-01-30",true,"15"],
            ["1","Tarde","2024-06-09",true,"16"],
            ["5","Tarde","2025-01-02",true,"17"],
            ["4","Noche","2024-07-07",true,"18"],
            ["4","Especial","2024-11-12",true,"19"],
            ["4","Noche","2024-07-27",true,"20"],
            ["5","Tarde","2024-10-28",true,"21"],
            ["1","Fin de Semana","2024-10-17",true,"22"],
            ["2","Fin de Semana","2025-01-18",true,"23"],
            ["2","Fin de Semana","2024-10-20",true,"24"],
            ["5","Tarde","2025-03-22",true,"25"],
            ["5","Noche","2024-05-21",true,"26"],
            ["5","Mañana","2024-09-22",true,"27"],
            ["4","Fin de Semana","2024-08-14",true,"28"],
            ["2","Tarde","2024-11-02",true,"29"],
            ["4","Fin de Semana","2025-01-23",true,"30"],
            ["1","Fin de Semana","2024-10-17",true,"31"],
            ["2","Especial","2025-03-17",true,"32"],
            ["3","Mañana","2025-03-07",true,"33"],
            ["3","Especial","2024-12-18",true,"34"],
            ["2","Mañana","2024-10-19",true,"35"],
            ["4","Fin de Semana","2024-07-17",true,"36"],
            ["4","Fin de Semana","2024-10-25",true,"37"],
            ["5","Especial","2024-12-10",true,"38"],
            ["1","Tarde","2024-09-02",true,"39"],
            ["1","Mañana","2025-05-13",true,"40"],
            ["2","Noche","2024-08-08",true,"41"],
            ["3","Tarde","2024-09-24",true,"42"],
            ["1","Tarde","2024-09-23",true,"43"],
            ["3","Especial","2024-07-01",true,"44"],
            ["3","Especial","2024-08-01",true,"45"],
            ["4","Noche","2025-03-16",true,"46"],
            ["4","Especial","2025-04-16",true,"47"],
            ["1","Fin de Semana","2024-10-03",true,"48"],
            ["1","Tarde","2024-08-06",true,"49"],
            ["2","Fin de Semana","2024-08-15",true,"50"]
        ];
    }
}