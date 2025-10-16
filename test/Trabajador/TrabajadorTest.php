<?php 
use PHPUnit\Framework\TestCase;

class TrabajadorTest extends TestCase
{
    public $trabajadorObj;
    protected function setUp(): void
    {
        // Initialization code here
        $this->trabajadorObj = new Trabajador;
        $this->testSuiteControl = "Trabajador";
        $this->trabajadorObj->setTestingMode(true);
    }
    /**
     * @dataProvider ListarProvider
     * @return void
     */
    public function testListarTrabajador()
    {
        $resp = $this->trabajadorObj->listar();
        $this->assertIsArray($resp);   
    }

    public function ListarProvider(){
        return [
            "Listar Trabajadores" => ["NA"=>"No hay entradas","resultado esperado"=>true],
        ];
    }
    /**
     * @dataProvider registrarProvider
     */
    public function testRegistrarTrabajador( $cedula, $nombre, $apellido, $telefono, $cargo, $turno, $idDepartamento, $fechaIngreso, $respuesta_esperada, ...$otros ){

        $datos = [
            "cedula" => $cedula,
            "nombre" => $nombre,
            "apellido" => $apellido,
            "telefono" => $telefono,
            "cargo" => $cargo,
            "turno" => $turno,
            "idDepartamento" => $idDepartamento,
            "fechaIngreso" => $fechaIngreso
        ];

        foreach ($datos as $key => &$value) {
            if($value == null){
                unset($datos[$key]);
            }
        }

        $this->trabajadorObj->setterArray($datos);

        $resp = $this->trabajadorObj->registrar();
        $this->assertIsArray($resp);
        $this->assertEquals($resp["success"], $respuesta_esperada);
    }

    public function registrarProvider(){
        $auxiliar = function($cedula, $nombre, $apellido, $telefono, $cargo, $turno, $idDepartamento, $fechaIngreso, $respuesta_esperada, ...$otros) {
            return [
                "cedula" => $cedula,
                "nombre" => $nombre,
                "apellido" => $apellido,
                "telefono" => $telefono,
                "cargo" => $cargo,
                "turno" => $turno,
                "idDepartamento" => $idDepartamento,
                "fechaIngreso" => $fechaIngreso,
                "respuesta_esperada" => $respuesta_esperada,
                ...$otros
            ];
        };

        $cedulaValida = "12447850";
        $cedulaInvalida = "124478500";
        $cedulaVacia = "";
        $nombreValido = "Fernando";
        $nombreVacio = "";
        $nombreInvalido = "Fernando123";
        $apellidoValido = "Perez";
        $apellidoVacio = "";
        $apellidoInvalido = "Perez123";
        $telefonoValido = "12345678";
        $telefonoVacio = "";
        $telefonoInvalido = "123456781";
        $cargoValido = "1";
        $cargoVacio = "";
        $cargoInvalido = "0";
        $turnoValido = "1";
        $turnoVacio = "";
        $turnoInvalido = "0";
        $idDepartamentoValido = "1";
        $idDepartamentoVacio = "";
        $idDepartamentoInvalido = "0";
        $fechaIngresoValida = "2020-10-10";
        $fechaIngresoVacia = "";
        $fechaIngresoInvalida = "2020-10-101";
        

        return [
            "Entrada Valida - Registrar trabajador" => $auxiliar($cedulaValida, $nombreValido, $apellidoValido, $telefonoValido, $cargoValido, $turnoValido, $idDepartamentoValido, $fechaIngresoValida, true ,["mensaje esperado" => "Trabajador registrado exitosamente."]),
            "Entrada Invalida - cedula vacía" => $auxiliar($cedulaVacia, $nombreValido, $apellidoValido, $telefonoValido, $cargoValido, $turnoValido, $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Cedula' es obligatorio"]),
            "Entrada Invalida - cedula Invalida exeso de numeros" => $auxiliar($cedulaInvalida, $nombreValido, $apellidoValido, $telefonoValido, $cargoValido, $turnoValido, $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Cedula' solo puede contener números y no puede tener mas de 8 digitos"]),
            "Entrada Invalida - cedula Invalida letras" => $auxiliar("Fernando", $nombreValido, $apellidoValido, $telefonoValido, $cargoValido, $turnoValido, $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Cedula' solo puede contener números y no puede tener mas de 8 digitos"]),
            "Entrada Invalida - Nombre Vacio" => $auxiliar($cedulaValida, $nombreVacio, $apellidoValido, $telefonoValido, $cargoValido, $turnoValido, $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Nombre' es obligatorio"]),
            "Entrada Invalida - Nombre Invalido" => $auxiliar($cedulaValida, $nombreInvalido, $apellidoValido, $telefonoValido, $cargoValido, $turnoValido, $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Nombre' es obligatorio"]),
            
        ];
    }
}