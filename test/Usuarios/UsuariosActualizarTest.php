<?php 
use PHPUnit\Framework\TestCase;
// la clase debe llamarse igual que el archivo
class UsuariosActualizarTest extends TestCase
{
    private $usuariosObj;

    protected function setUp(): void
    {
        $this->usuariosObj = new Usuario;
        $this->usuariosObj->setTestingMode(true);
    }

    /**
     * @dataProvider ActualizarAsistenciasProvider
     */
    public  function testActualizarUsuario($id,$correo,$rol,$clave,$resultado_esperado,$num_caso){
        
        $_POST['id'] = $id;// cedula del usuario que sera actualizada
        $_POST['correo'] = $correo;// correo nuevo
        $_POST['idRol'] = $rol;// rol nuevo
        $_POST['clave'] = $clave; // clave nueva o vacia si no se desea cambiar



    

        $this->usuariosObj->mapearFormulario();

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
        return [
            // casos validos
            ["1","amouat0@cafepress.com","1","TAOmc53521","true","1"],
            ["5","pkharchinski1@pcworld.com","1","MABwx87871","true","2"],
            ["7","mefford2@bloomberg.com","8","XIEtp02364","true","3"],
            ["1","pfranke3@cnbc.com","8","SYFia59595","true","4"],
            ["5","lczajkowska4@google.cn","1","XLIph51344","true","5"],
            ["7","hedler5@statcounter.com","8","EHRzs41541","true","6"],
            ["1","hthirst6@moonfruit.com","1","FMAwp78684","true","7"],
            ["5","otomas7@geocities.jp","1","EOXut70482","true","8"],
            ["7","trobelet8@dyndns.org","1","HARqx10311","true","9"],
            ["1","bgehrels9@apache.org","1","QCVxe89516","true","10"],
            ["5","wcanningsa@a8.net","8","NCMwh41198","true","11"],
            ["7","lmancerb@wsj.com","8","CHGjz63474","true","12"],
            ["1","swiddopc@nytimes.com","8","OFAgg71534","true","13"],
            ["5","taindriud@homestead.com","1","QUHbi52190","true","14"],
            ["7","bmariettee@chicagotribune.com","8","SSMjz11794","true","15"],
            ["1","sweddeburnf@zdnet.com","1","AMBqy28272","true","16"],
            ["5","jburnepg@mashable.com","1","OWUgm88507","true","17"],
            ["7","espentonh@sina.com.cn","1","RZNdx40648","true","18"],
            ["1","rivanuschkai@google.it","1","IWIcr47405","true","19"],
            ["5","gkohnemannj@ted.com","8","EQJwe50300","true","20"]

        ];
    }
}