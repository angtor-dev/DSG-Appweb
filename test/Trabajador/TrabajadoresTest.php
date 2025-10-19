<?php 
use PHPUnit\Framework\TestCase;

class TrabajadoresTest extends TestCase
{
    private $trabajadorObj;
    public $testSuiteControl;

    protected function setUp(): void
    {
        // Initialization code here
        $this->trabajadorObj = new Trabajador;
        $this->testSuiteControl = "Trabajadores";
        $this->trabajadorObj->setTestingMode(true);
    }
    
    /**
     * @dataProvider ListarProvider
     */
    public function testListarTrabajador()
    {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl))->log();
        if($_logger["dataname"] == "listar Trabajadores Vacio"){
            $resp = $this->trabajadorObj->listar(999); // Usar un estado que no exista para simular sin entradas
            $this->assertIsArray($resp);
            $arrayVacioPrueba = [];
            $this->assertEmpty($arrayVacioPrueba);

        }
        else{
            $resp = $this->trabajadorObj->listar();
            $this->assertIsArray($resp);
            $this->assertNotEmpty($resp);
            if(!empty($resp)){
                foreach($resp as $trabajador){
                    $this->assertInstanceOf(Trabajador::class, $trabajador);
                }
            }
        }
    }

    public function ListarProvider(){
        return [
            "Listar Trabajadores" => ["NA"=>"No hay entradas","resultado esperado"=>true, "--observacion" => "Retorna un array de objetos Trabajador"],
            "listar Trabajadores Vacio" => ["NA"=>"No hay entradas","resultado esperado"=>true, "--observacion" => "Retorna un array vacio"],
        ];
    }
    /**
     * @dataProvider registrarProvider
     */
    public function testRegistrarTrabajador( $cedula, $nombre, $apellido, $telefono, $cargo, $turno, $idDepartamento, $fechaIngreso, $respuesta_esperada, ...$otros ){
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl))->log();
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
            if($value === null){
                unset($datos[$key]);
            }
        }

        $this->trabajadorObj->setterArray($datos);

        $resp = $this->trabajadorObj->registrar(false);
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
                "resultado esperado" => $respuesta_esperada,
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
        $telefonoValido = "04145555555";
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
            "Entrada Invalida - Apellido Vacio" => $auxiliar($cedulaValida, $nombreValido, $apellidoVacio, $telefonoValido, $cargoValido, $turnoValido, $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Apellido' es obligatorio"]),
            "Entrada Invalida - Apellido Invalido" => $auxiliar($cedulaValida, $nombreValido, $apellidoInvalido, $telefonoValido, $cargoValido, $turnoValido, $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Apellido' es obligatorio"]),
            "Entrada Invalida - Telefono Vacio" => $auxiliar($cedulaValida, $nombreValido, $apellidoValido, $telefonoVacio, $cargoValido, $turnoValido, $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Telefono' es obligatorio"]),
            "Entrada Invalida - Telefono Invalido" => $auxiliar($cedulaValida, $nombreValido, $apellidoValido, $telefonoInvalido, $cargoValido, $turnoValido, $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Telefono' solo puede contener números y no puede tener mas de 11 digitos"]),
            "Entrada Invalida - Turno Vacio" => $auxiliar($cedulaValida, $nombreValido, $apellidoValido, $telefonoValido, $cargoValido, $turnoVacio, $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Turno' es obligatorio"]),
            "Entrada Invalida - Turno Invalido" => $auxiliar($cedulaValida, $nombreValido, $apellidoValido, $telefonoValido, $cargoValido, $turnoInvalido, $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Turno' es obligatorio"]),
            "Entrada Invalida - Departamento Vacio" => $auxiliar($cedulaValida, $nombreValido, $apellidoValido, $telefonoValido, $cargoValido, $turnoValido, $idDepartamentoVacio, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Departamento' es obligatorio"]),
            "Entrada Invalida - Departamento Invalido" => $auxiliar($cedulaValida, $nombreValido, $apellidoValido, $telefonoValido, $cargoValido, $turnoValido, $idDepartamentoInvalido, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Departamento' es obligatorio"]),
            "Entrada Invalida - Fecha de Ingreso Vacio" => $auxiliar($cedulaValida, $nombreValido, $apellidoValido, $telefonoValido, $cargoValido, $turnoValido, $idDepartamentoValido, $fechaIngresoVacia, false, ["mensaje esperado" => "El campo 'Fecha de Ingreso' es obligatorio"]),
            "Entrada Invalida - Fecha de Ingreso Invalida" => $auxiliar($cedulaValida, $nombreValido, $apellidoValido, $telefonoValido, $cargoValido, $turnoValido, $idDepartamentoValido, $fechaIngresoInvalida, false, ["mensaje esperado" => "El campo 'Fecha de Ingreso' debe ser una fecha valida"]),
            "Entrada Invalida - Cargo Vacio" => $auxiliar($cedulaValida, $nombreValido, $apellidoValido, $telefonoValido, $cargoVacio, $turnoValido, $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Cargo' es obligatorio"]),
            "Entrada Invalida - Cargo Invalido" => $auxiliar($cedulaValida, $nombreValido, $apellidoValido, $telefonoValido, $cargoInvalido, $turnoValido, $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Cargo' es obligatorio"]),
        ];
    }

    /**
     * @dataProvider actualizarProvider
     */
    public function testActualizarTrabajador($id, $cedulaSeleccion, $cedula, $nombre, $apellido, $telefono, $cargo, $turno, $idDepartamento, $fechaIngreso, $respuesta_esperada, ...$otros)
    {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl))->log();

        // preparar datos para el setterArray (incluye cedulaSeleccion)
        $datos = [
            "id" => $id,
            "cedulaSeleccion" => $cedulaSeleccion,
            "cedula" => $cedula,
            "nombre" => $nombre,
            "apellido" => $apellido,
            "telefono" => $telefono,
            "cargo" => $cargo,
            "turno" => $turno,
            "idDepartamento" => $idDepartamento,
            "fechaIngreso" => $fechaIngreso
        ];

        // eliminar valores nulos que no se quieran enviar
        foreach ($datos as $key => $value) {
            if ($value === null) {
                unset($datos[$key]);
            }
        }
        
        $this->trabajadorObj->setterArray($datos);

        $resp = $this->trabajadorObj->actualizar(false);
        $this->assertIsArray($resp);
        $this->assertEquals($respuesta_esperada, $resp["success"], $_logger["dataname"]);
    }

    public function actualizarProvider()
    {
        $aux = function($id, $cedulaSeleccion, $cedula, $nombre, $apellido, $telefono, $cargo, $turno, $idDepartamento, $fechaIngreso, $respuesta_esperada, ...$otros) {
            return [
                "id" => $id,
                "cedulaSeleccion" => $cedulaSeleccion,
                "cedula" => $cedula,
                "nombre" => $nombre,
                "apellido" => $apellido,
                "telefono" => $telefono,
                "cargo" => $cargo,
                "turno" => $turno,
                "idDepartamento" => $idDepartamento,
                "fechaIngreso" => $fechaIngreso,
                "resultado esperado" => $respuesta_esperada,
                ...$otros
            ];
        };
        $id = "1";
        $cedulaSeleccion = "00000001";
        $cedulaValida = "12447850";
        $cedulaInvalida = "124478500";
        $cedulaVacia = "";
        $nombreValido = "Fernando";
        $nombreVacio = "";
        $nombreInvalido = "Fernando123";
        $apellidoValido = "Perez";
        $apellidoVacio = "";
        $apellidoInvalido = "Perez123";
        $telefonoValido = "04145555555";
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
            "Actualizar - Entrada válida" =>    $aux($id, $cedulaSeleccion, $cedulaValida, $nombreValido, $apellidoValido, $telefonoValido, $cargoValido, $turnoValido, $idDepartamentoValido, $fechaIngresoValida, true, ["mensaje esperado" => "Trabajador actualizado con éxito"]),
            "Actualizar - Cedula Vacía" =>      $aux($id, $cedulaSeleccion, $cedulaVacia, $nombreValido, $apellidoValido, $telefonoValido, $cargoValido, $turnoValido, $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "Error al obtener la cedula del trabajador seleccionado"]),
            "Actualizar - Cedula Inválida" =>   $aux($id, $cedulaSeleccion, $cedulaInvalida, $nombreValido, $apellidoValido, $telefonoValido, $cargoValido, $turnoValido, $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "Error al obtener la cedula del trabajador seleccionado"]),
            "Actualizar - Nombre Vacío" =>      $aux($id, $cedulaSeleccion, $cedulaValida, $nombreVacio, $apellidoValido, $telefonoValido, $cargoValido, $turnoValido, $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Nombre' es obligatorio"]),
            "Actualizar - Nombre Invalido" =>   $aux($id, $cedulaSeleccion, $cedulaValida, $nombreInvalido, $apellidoValido, $telefonoValido, $cargoValido, $turnoValido, $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Nombre' es obligatorio"]),
            "Actualizar - Apellido Vacío" =>    $aux($id, $cedulaSeleccion, $cedulaValida, $nombreValido, $apellidoVacio, $telefonoValido, $cargoValido, $turnoValido, $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Apellido' es obligatorio"]),
            "Actualizar - Apellido Invalido" => $aux($id, $cedulaSeleccion, $cedulaValida, $nombreValido, $apellidoInvalido, $telefonoValido, $cargoValido, $turnoValido, $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Apellido' es obligatorio"]),
            "Actualizar - Telefono Vacío" =>    $aux($id, $cedulaSeleccion, $cedulaValida, $nombreValido, $apellidoValido, $telefonoVacio, $cargoValido, $turnoValido, $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Telefono' es obligatorio"]),
            "Actualizar - Telefono Invalido" => $aux($id, $cedulaSeleccion, $cedulaValida, $nombreValido, $apellidoValido, $telefonoInvalido, $cargoValido, $turnoValido, $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Telefono' solo puede contener números y no puede tener mas de 11 digitos"]),
            "Actualizar - Fecha Inválida" =>    $aux($id, $cedulaSeleccion, $cedulaValida, $nombreValido, $apellidoValido, $telefonoValido, $cargoValido, $turnoValido, $idDepartamentoValido, $fechaIngresoInvalida, false, ["mensaje esperado" => "El campo 'Fecha de Ingreso' debe ser una fecha valida"]),
            "Actualizar - Fecha Vacía" =>       $aux($id, $cedulaSeleccion, $cedulaValida, $nombreValido, $apellidoValido, $telefonoValido, $cargoValido, $turnoValido, $idDepartamentoValido, $fechaIngresoVacia, false, ["mensaje esperado" => "El campo 'Fecha de Ingreso' es obligatorio"]),
            "Actualizar - Cargo Vacío" =>       $aux($id, $cedulaSeleccion, $cedulaValida, $nombreValido, $apellidoValido, $telefonoValido, $cargoVacio, $turnoValido, $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Cargo' es obligatorio"]),
            "Actualizar - Cargo Invalido" =>    $aux($id, $cedulaSeleccion, $cedulaValida, $nombreValido, $apellidoValido, $telefonoValido, $cargoInvalido, $turnoValido, $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Cargo' solo puede contener números y no puede tener mas de 5 digitos"]),
            "Actualizar - Turno Vacío" =>       $aux($id, $cedulaSeleccion, $cedulaValida, $nombreValido, $apellidoValido, $telefonoValido, $cargoValido, $turnoVacio, $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Turno' es obligatorio"]),
            "Actualizar - Turno Invalido" =>    $aux($id, $cedulaSeleccion, $cedulaValida, $nombreValido, $apellidoValido, $telefonoValido, $cargoValido, $turnoInvalido, $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Turno' solo puede contener números y no puede tener mas de 5 digitos"]),
            "Actualizar - División Vacío" =>    $aux($id, $cedulaSeleccion, $cedulaValida, $nombreValido, $apellidoValido, $telefonoValido, $cargoValido, $turnoValido, $idDepartamentoVacio, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Departamento' es obligatorio"]),
            "Actualizar - División Invalido" => $aux($id, $cedulaSeleccion, $cedulaValida, $nombreValido, $apellidoValido, $telefonoValido, $cargoValido, $turnoValido, $idDepartamentoInvalido, $fechaIngresoValida, false, ["mensaje esperado" => "El campo 'Departamento' solo puede contener números y no puede tener mas de 5 digitos"]),
            "Actualizar - Turno Inexistente" => $aux($id, $cedulaSeleccion, $cedulaValida, $nombreValido, $apellidoValido, $telefonoValido, $cargoValido, "99999", $idDepartamentoValido, $fechaIngresoValida, false, ["mensaje esperado" => "El turno no existe"]),
        ];
    }


     /**
     * @dataProvider eliminarProvider
     */
    public function testEliminarTrabajador($cedulaSeleccion, $respuesta_esperada, ...$otros)
    {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl))->log();

        $datos = [
            "cedulaSeleccion" => $cedulaSeleccion
        ];

        // eliminar valores nulos que no se quieran enviar
        foreach ($datos as $key => $value) {
            if ($value === null) {
                unset($datos[$key]);
            }
        }

        $this->trabajadorObj->setterArray($datos);

        $resp = $this->trabajadorObj->eliminarTrabajador(false,false);
        $this->assertIsArray($resp);
        $this->assertEquals($respuesta_esperada, $resp["success"], $_logger["dataname"]);
    }

    public function eliminarProvider()
    {
        $aux = function( $cedulaSeleccion, $respuesta_esperada, ...$otros) {
            return [
                "Cedula Seleccionada" => $cedulaSeleccion,
                "resultado esperado" => $respuesta_esperada,
                ...$otros
            ];
        };

        $cedulaExistente =  "00000001"; // no se puede eliminar el trabajador con cedula 00000001
        $cedulaInexistente ="99999999";
        $cedulaInvalida =   "124478500";
        $cedulaVacia =      "";

        return [
            "Eliminar - entrada válida" =>    $aux( $cedulaExistente,    true, ["mensaje esperado" => "Trabajador eliminado con éxito"]),
            "Eliminar - cedula vacía" =>             $aux( $cedulaVacia,        false, ["mensaje esperado" => "Error al obtener la cedula del trabajador seleccionado"]),
            "Eliminar - cedula inválida" =>          $aux( $cedulaInvalida,     false, ["mensaje esperado" => "Error al obtener la cedula del trabajador seleccionado"]),
            "Eliminar - cedula nula" =>              $aux( null,                false, ["mensaje esperado" => "Error al obtener la cedula del trabajador seleccionado"]),
            "Eliminar - trabajador inexistente" =>   $aux( $cedulaInexistente,  false, ["mensaje esperado" => "El trabajador seleccionado no existe en la base de datos"]),
        ];
    }

}