<?php
use PHPUnit\Framework\TestCase;

class TurnosRegistrarTest extends TestCase
{
    private $turnoObj;
    public $testSuiteControl;
    

    protected function setUp(): void
    {
        $this->turnoObj = new Turno;
        $this->turnoObj->setTestingMode(true);
        $this->testSuiteControl = "Turnos";
        
    }

    /**
     * @dataProvider ListarTurnosProvider
     */

    public function testTurnosListar($foo, $respuesta_esperada): void
    {
        (new LoggerPhpUnit($this, $this->testSuiteControl))->log();
        switch ($this->dataName()) {
            case 'Listar con todos los permisos':
                    getUserFalseInsesion(1); // usuario con el rol de super admin
                    $eliminar = true;
                    $actualizar = true;
                break;
                case 'Listar sin el permiso de eliminar':
                    getUserFalseInsesion(2); // usuario con rol sin permiso de eliminar
                    $eliminar = false;
                    $actualizar = true;
                break;
                case 'Listar sin el permiso de actualizar':
                    getUserFalseInsesion(3); // usuario con rol sin permiso de actualizar
                    $eliminar = true;
                    $actualizar = false;
                break;
                case 'Listar sin el permiso de actualizar-eliminar':
                    getUserFalseInsesion(4); // usuario con rol sin permiso de actualizar-eliminar
                    $eliminar = false;
                    $actualizar = false;
                break;
            
            default:
                    getUserFalseInsesion(1); // usuario con el rol de super admin
                    $eliminar = true;
                    $actualizar = true;
                break;
        }

        $resp = $this->turnoObj->listar();

        if($respuesta_esperada === true){
            $this->assertEquals($respuesta_esperada, $resp['success']);
            $this->assertEquals($eliminar, $resp['eliminar']);
            $this->assertEquals($actualizar, $resp['actualizar']);
            $this->assertIsArray($resp['data']);
        }
        else{
            $this->assertEquals($respuesta_esperada, $resp['success']);
            $this->assertIsString($resp['message']);
        }
        
        

        

        
    }

    public function ListarTurnosProvider(): array
    {
        return [
            "Listar con todos los permisos" => ["NA"=>"No hay entradas","resultado esperado"=>true],
            "Listar sin el permiso de eliminar" => ["NA"=>"No hay entradas","resultado esperado"=>true],
            "Listar sin el permiso de actualizar"=> ["NA"=>"No hay entradas","resultado esperado"=>true],
            "Listar sin el permiso de actualizar-eliminar"=> ["NA"=>"No hay entradas","resultado esperado"=>true],
            //"Error de conexion" => ["NA"=>"No hay entradas","respuesta esperada"=>false],
        ];
    }





    /**
     * @dataProvider RegistrosProvider
     */
    public function testRegistrarTurnos(
        $turnoNombre,$horario_entrada,$horario_salida,
        $lunes, $martes, $miercoles,
        $jueves, $viernes, $sabado,
        $domingo, $resultado_esperado, $num_caso
        ):void
    {
        (new LoggerPhpUnit($this, $this->testSuiteControl))->log();

        $listaDatos = [
            'nombre' => $turnoNombre,
            'horario_entrada' => $horario_entrada,
            'horario_salida' => $horario_salida,
            'lunes' => $lunes,
            'martes' => $martes,
            'miercoles' => $miercoles,
            'jueves' => $jueves,
            'viernes' => $viernes,
            'sabado' => $sabado,
            'domingo' => $domingo
        ];

        // si algun campo es nulo eliminar del array
        foreach ($listaDatos as $key => $value) {
            if ($value === null) {
                unset($listaDatos[$key]);
            }
        }

        $this->turnoObj->setterArray($listaDatos);

        $respuesta = $this->turnoObj->registrar(false);

        $mensaje = "caso ($num_caso)";

        $this->assertNotNull($respuesta);
        $this->assertIsArray($respuesta);

        $this->assertArrayHasKey('success', $respuesta);
        
        $mensaje = ($respuesta['consoleError'] ?? $respuesta['message']) . ' :: '.$mensaje;

        $this->assertEquals($resultado_esperado, $respuesta['success'], $mensaje);
        
    }

    public function RegistrosProvider()
    {
        return [
    // casos de prueba
    "Caso 1 registro valido"=>[
        "nombre" => 'turno1',
        "hora de entrada" => '08:00:00',
        "hora de salida" => '18:00:00',
        "Lunes" => 1,
        "Martes" => 1,
        "Miercoles" => 1,
        "Jueves" => 1,
        "Viernes" => 1,
        "Sabado" => 1,
        "Domingo" => 1,
        "resultado esperado" => true,
        "num_caso" => 1
    ],
    "caso 2 registro valido con string en los dias" =>[
        "nombre" => 'turno1',
        "hora de entrada" => '08:00:00',
        "hora de salida" => '18:00:00',
        "Lunes" => "1",
        "Martes" => "0",
        "Miercoles" => "1",
        "Jueves" => "0",
        "Viernes" => "1",
        "Sabado" => "0",
        "Domingo" => "1",
        "resultado esperado" => true,
        "num_caso" => 2
    ],// valido 
    "caso 3 registro con nombre invalido"=>[
        "nombre" => '',
        "hora de entrada" => '08:00:00',
        "hora de salida" => '18:00:00',
        "Lunes" => 1,
        "Martes" => 1,
        "Miercoles" => 1,
        "Jueves" => 1,
        "Viernes" => 1,
        "Sabado" => 1,
        "Domingo" => 1,
        "resultado esperado" => false,
        "num_caso" => 3
    ],// nombre invalido
    "caso 4 registro con hora entrada invalida"=>[
        "nombre" => 'turno1',
        "hora de entrada" => '',
        "hora de salida" => '18:00:00',
        "Lunes" => 1,
        "Martes" => 1,
        "Miercoles" => 1,
        "Jueves" => 1,
        "Viernes" => 1,
        "Sabado" => 1,
        "Domingo" => 1,
        "resultado esperado" => false,
        "num_caso" => 4
    ],// hora entrada invalida
    "caso 5 registro con hora salida invalida"=>[
        "nombre" => 'turno1',
        "hora de entrada" => '08:00:00',
        "hora de salida" => '',
        "Lunes" => 1,
        "Martes" => 1,
        "Miercoles" => 1,
        "Jueves" => 1,
        "Viernes" => 1,
        "Sabado" => 1,
        "Domingo" => 1,
        "resultado esperado" => false,
        "num_caso" => 5
    ],// hora salida invalida

    "caso 6 registro con dias invalidos" =>[
        "nombre" => 'turno1',
        "hora de entrada" => '08:00:00',
        "hora de salida" => '18:00:00',
        "Lunes" => null,
        "Martes" => null,
        "Miercoles" => null,
        "Jueves" => null,
        "Viernes" => null,
        "Sabado" => null,
        "Domingo" => null,
        "resultado esperado" => false,
        "num_caso" => 6
    ],// dias invalido
    "caso 7 registro sin dias seleccionados" =>[
        "nombre" => 'turno1',
        "hora de entrada" => '08:00:00',
        "hora de salida" => '18:00:00',
        "Lunes" => 0,
        "Martes" => 0,
        "Miercoles" => 0,
        "Jueves" => 0,
        "Viernes" => 0,
        "Sabado" => 0,
        "Domingo" => 0,
        "resultado esperado" => false,
        "num_caso" => 7
    ],// dias invalido
    ];



    }


        /**
         * @dataProvider EliminarTurnoProvider
         */
    public function testEliminarTurno(
        $id,
        $mensajeEsperado,
        $resultado_esperado,
        $num_caso
    ): void
    {
        (new LoggerPhpUnit($this, $this->testSuiteControl))->log();

        // Preparar los datos para la prueba
        $this->turnoObj->setterArray(['codigo' => $id]);

        // Llamar al método eliminarTurno
        $respuesta = $this->turnoObj->eliminarTurno(false);
        
        // Mensaje para el caso de prueba
        $mensaje = "Caso ($num_caso)";
        // Verificar el resultado
        $this->assertNotNull($respuesta);
        $this->assertIsArray($respuesta);
        $this->assertArrayHasKey('success', $respuesta);

        $mensaje = ($respuesta['consoleError'] ?? $respuesta['message']) . ' :: '.$mensaje;
        $this->assertEquals($resultado_esperado, $respuesta['success'], $mensaje);
        $this->assertEquals($mensajeEsperado, $respuesta['message'], $mensaje);

    }

    public function EliminarTurnoProvider()
    {
        $mensajeEsperado = new class {
            public $eliminarTurno = "Turno eliminado con éxito";
            public $registrarTurno = "Turno registrado con éxito";
            public $actualizarTurno = "Turno actualizado con éxito";
            public $nombreRequerido = "El nombre del turno es requerido";
            public $horarioEntradaRequerido = "El horario de entrada es requerido";
            public $horarioSalidaRequerido = "El horario de salida es requerido";
            public $diasRequeridos = "Debe seleccionar al menos un día de la semana";
            public $lunesRequerido = "El día de lunes es requerido";
            public $martesRequerido = "El día de martes es requerido";
            public $miercolesRequerido = "El día de miércoles es requerido";
            public $juevesRequerido = "El día de jueves es requerido";
            public $viernesRequerido = "El día de viernes es requerido";
            public $sabadoRequerido = "El día de sábado es requerido";
            public $domingoRequerido = "El día de domingo es requerido";
            public $horarioEntradaInvalido = "El horario de entrada es invalido";
            public $horarioSalidaInvalido = "El horario de salida es invalido";
            public $horarioEntradaMayorSalida = "El horario de entrada es mayor al horario de salida";
            public $nombreInvalido = "El nombre del turno es invalido";
            public $turnoExistente = "El turno ya existe";
            public $turnoNoExistente = "El turno no existe";
            public $turnoRequerido = "El turno no esta seleccionado";
            public $turnoInvalido = "El turno es invalido";
            public $lunesInvalido = "El día de lunes es invalido";
            public $martesInvalido = "El día de martes es invalido";
            public $miercolesInvalido = "El día de miércoles es invalido";
            public $juevesInvalido = "El día de jueves es invalido";
            public $viernesInvalido = "El día de viernes es invalido";
            public $sabadoInvalido = "El día de sábado es invalido";
            public $domingoInvalido = "El día de domingo es invalido";
            public $turnoRelacionesEliminar = "El turno esta siendo utilizado y no puede ser eliminado";

        };
        return [
            // Casos de prueba
            "Eliminar turno existente con relaciones"=>[
                "codigo" => "e1617263-8d0a-11f0-91e8-d481d7968c88",
                "mensaje esperado" => $mensajeEsperado->turnoRelacionesEliminar,
                "resultado esperado" => false,
                "num_caso" => 1], // Eliminar turno existente
            "Eliminar turno existente"=>[
                "codigo" => "ec233a83-8dea-11f0-91e8-d481d7968c88", 
                "mensaje esperado" => $mensajeEsperado->eliminarTurno,
                "resultado esperado" => true,
                "num_caso" => 2], // Eliminar turno existente
            "Eliminar turno inexistente"=>[
                "codigo" => "e1617129-8d0a-11f0-91e8-999999999999", 
                "mensaje esperado" => $mensajeEsperado->turnoNoExistente,
                "resultado esperado" => false,
                "num_caso" => 3], // Eliminar turno inexistente
            "Eliminar turno con ID nulo o invalido"=>[
                "codigo" => "", 
                "mensaje esperado" => $mensajeEsperado->turnoRequerido,
                "resultado esperado" => false,
                "num_caso" => 4], // Eliminar turno con ID nulo o invalido
        ];

    }



    /**
     * @dataProvider ActualizarTurnoProvider
     */
    public function testActualizarTurno(
        $id, $nombre, $horario_entrada,
        $horario_salida, $lunes, $martes,
        $miercoles, $jueves, $viernes,
        $sabado, $domingo, $mensajeEsperado,
        $resultado_esperado,
    ): void
    {
        (new LoggerPhpUnit($this, $this->testSuiteControl))->log();

        // Preparar los datos para la prueba
        $listaDatos = [
            'codigo' => $id, 'nombre' => $nombre, 'horario_entrada' => $horario_entrada,
            'horario_salida' => $horario_salida, 'lunes' => $lunes, 'martes' => $martes,
            'miercoles' => $miercoles, 'jueves' => $jueves, 'viernes' => $viernes,
            'sabado' => $sabado, 'domingo' => $domingo
        ];
    
        // si algun campo es nulo eliminar del array
        foreach ($listaDatos as $key => $value) {
            if ($value === null) {
                unset($listaDatos[$key]);
            }
        }
    
        $this->turnoObj->setterArray($listaDatos);
    
        // Llamar al método actualizarTurno
        $respuesta = $this->turnoObj->actualizar(false);
    
        $mensaje = $this->dataName();

        $mensaje = "\n\n\n\n".$mensaje."\n\n\n\n";
    
        // Verificar el resultado
        $this->assertNotNull($respuesta, $mensaje);
        $this->assertIsArray($respuesta, $mensaje);
        $this->assertArrayHasKey('success', $respuesta, $mensaje);
        $this->assertArrayHasKey('message', $respuesta, $mensaje);
        $this->assertSame($mensajeEsperado, $respuesta['message'], $mensaje);


        $this->assertEquals($resultado_esperado, $respuesta['success'], $mensaje);

    }
    
    public function ActualizarTurnoProvider()
    {
        $mensajeEsperado = new class {
            public $eliminarTurno = "Turno eliminado con éxito";
            public $registrarTurno = "Turno registrado con éxito";
            public $actualizarTurno = "Turno actualizado con éxito";
            public $nombreRequerido = "El nombre del turno es requerido";
            public $horarioEntradaRequerido = "El horario de entrada es requerido";
            public $horarioSalidaRequerido = "El horario de salida es requerido";
            public $diasRequeridos = "Debe seleccionar al menos un día de la semana";
            public $lunesRequerido = "El día de lunes es requerido";
            public $martesRequerido = "El día de martes es requerido";
            public $miercolesRequerido = "El día de miércoles es requerido";
            public $juevesRequerido = "El día de jueves es requerido";
            public $viernesRequerido = "El día de viernes es requerido";
            public $sabadoRequerido = "El día de sábado es requerido";
            public $domingoRequerido = "El día de domingo es requerido";
            public $horarioEntradaInvalido = "El horario de entrada es invalido";
            public $horarioSalidaInvalido = "El horario de salida es invalido";
            public $horarioEntradaMayorSalida = "El horario de entrada es mayor al horario de salida";
            public $nombreInvalido = "El nombre del turno es invalido";
            public $turnoExistente = "El turno ya existe";
            public $turnoNoExistente = "El turno no existe";
            public $turnoRequerido = "El turno no esta seleccionado";
            public $turnoInvalido = "El turno es invalido";
            public $lunesInvalido = "El día de lunes es invalido";
            public $martesInvalido = "El día de martes es invalido";
            public $miercolesInvalido = "El día de miércoles es invalido";
            public $juevesInvalido = "El día de jueves es invalido";
            public $viernesInvalido = "El día de viernes es invalido";
            public $sabadoInvalido = "El día de sábado es invalido";
            public $domingoInvalido = "El día de domingo es invalido";
            public $turnoRelacionesEliminar = "El turno esta siendo utilizado y no puede ser eliminado";

        };

        /*

        return [
            // casos de prueba
            "caso 1 (Valido)" => 
            [ "e1616fed-8d0a-11f0-91e8-d481d7968c88", 'Nuevo nombre del turno', '08:00', '17:00', 1, 0, 1, 0, 1, 0, 0, $mensajeEsperado->actualizarTurno, true], // valido

            "caso 2 (nombre duplicado)" => 
            [ "e1617129-8d0a-11f0-91e8-d481d7968c88", 'Mañana', '08:00', '17:00', 1, 0, 1, 0, 1, 0, 0, $mensajeEsperado->turnoExistente, false], // duplicado

            "caso 3 (sin dias seleccionados)" =>  
            [ "e1616fed-8d0a-11f0-91e8-d481d7968c88", 'Mañana', '08:00', '17:00', 0, 0, 0, 0, 0, 0, 0, $mensajeEsperado->diasRequeridos, false], // sin dias

            "caso 4 (Hora de entrada invalida) "=>
            [ "e1616fed-8d0a-11f0-91e8-d481d7968c88", 'turno1', '', '18:00:00', 1, 1, 1, 1, 1, 1, 1,$mensajeEsperado->horarioEntradaRequerido, false],// hora entrada invalida

            "caso 5 (Hora de salida invalida)"=>
            [ "e1616fed-8d0a-11f0-91e8-d481d7968c88", 'turno1', '08:00:00', '', 1, 1, 1, 1, 1, 1, 1,$mensajeEsperado->horarioSalidaRequerido, false],// hora salida invalida

            "caso 6 (Dias invalido)"=>
            [ "e1616fed-8d0a-11f0-91e8-d481d7968c88", 'turno1', '08:00:00', '18:00:00', null, null, null, null, null, null,null ,$mensajeEsperado->diasRequeridos, false],// dias invalido
        ];
        */
        return [
    
            "caso 1 (Valido)" => 
            [
                "codigo" => "e1616fed-8d0a-11f0-91e8-d481d7968c88",
                "nombre" => 'Nuevo nombre del turno',
                "hora de entrada" => '08:00',
                "hora de salida" => '17:00',
                "Lunes" => 1,
                "Martes" => 0,
                "Miercoles" => 1,
                "Jueves" => 0,
                "Viernes" => 1,
                "Sabado" => 0,
                "Domingo" => 0,
                "mensaje esperado" => $mensajeEsperado->actualizarTurno,
                "resultado esperado" => true
            ], // valido

            "caso 2 (nombre duplicado)" => 
            [
                "codigo" => "e1617129-8d0a-11f0-91e8-d481d7968c88",
                "nombre" => 'Mañana',
                "hora de entrada" => '08:00',
                "hora de salida" => '17:00',
                "Lunes" => 1,
                "Martes" => 0,
                "Miercoles" => 1,
                "Jueves" => 0,
                "Viernes" => 1,
                "Sabado" => 0,
                "Domingo" => 0,
                "mensaje esperado" => $mensajeEsperado->turnoExistente,
                "resultado esperado" => false
            ],

            "caso 3 (sin dias seleccionados)" =>  
            [
                "codigo" => "e1616fed-8d0a-11f0-91e8-d481d7968c88",
                "nombre" => 'Mañana',
                "hora de entrada" => '08:00',
                "hora de salida" => '17:00',
                "Lunes" => 0,
                "Martes" => 0,
                "Miercoles" => 0,
                "Jueves" => 0,
                "Viernes" => 0,
                "Sabado" => 0,
                "Domingo" => 0,
                "mensaje esperado" => $mensajeEsperado->diasRequeridos,
                "resultado esperado" => false
            ],

            "caso 4 (Hora de entrada invalida)" =>
            [
                "codigo" => "e1616fed-8d0a-11f0-91e8-d481d7968c88",
                "nombre" => 'turno1',
                "hora de entrada" => '',
                "hora de salida" => '18:00:00',
                "Lunes" => 1,
                "Martes" => 1,
                "Miercoles" => 1,
                "Jueves" => 1,
                "Viernes" => 1,
                "Sabado" => 1,
                "Domingo" => 1,
                "mensaje esperado" => $mensajeEsperado->horarioEntradaRequerido,
                "resultado esperado" => false
            ],

            "caso 5 (Hora de salida invalida)" =>
            [
                "codigo" => "e1616fed-8d0a-11f0-91e8-d481d7968c88",
                "nombre" => 'turno1',
                "hora de entrada" => '08:00:00',
                "hora de salida" => '',
                "Lunes" => 1,
                "Martes" => 1,
                "Miercoles" => 1,
                "Jueves" => 1,
                "Viernes" => 1,
                "Sabado" => 1,
                "Domingo" => 1,
                "mensaje esperado" => $mensajeEsperado->horarioSalidaRequerido,
                "resultado esperado" => false
            ],
            "caso 6 (Dias invalido)"=>
            [
                "Codigo" => "e1616fed-8d0a-11f0-91e8-d481d7968c88",
                "nombre" => 'turno1',
                "hora de entrada" => '08:00:00',
                "hora de salida" => '18:00:00',
                "Lunes" => null,
                "Martes" => null,
                "Miercoles" => null,
                "Jueves" => null,
                "Viernes" => null,
                "Sabado" => null,
                "Domingo" => null,
                "mensaje esperado" => $mensajeEsperado->diasRequeridos,
                "resultado esperado" => false],// dias invalido
        ];
    }


}