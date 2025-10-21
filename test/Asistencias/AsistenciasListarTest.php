<?php 
use PhpParser\Node\Expr\Cast\Bool_;
use PHPUnit\Framework\TestCase;
// la clase debe llamarse igual que el archivo
class AsistenciasListarTest extends TestCase
{
    private $asistenciaObj;
    public $testSuiteControl;

    public $mañana = "e1616fed-8d0a-11f0-91e8-d481d7968c88";
    public $tarde = "e1617129-8d0a-11f0-91e8-d481d7968c88";
    public $noche = "e16171b0-8d0a-11f0-91e8-d481d7968c88";
    public $fin_de_semana = "e161720c-8d0a-11f0-91e8-d481d7968c88";
    public $especial = "e1617263-8d0a-11f0-91e8-d481d7968c88";
    public $turno_No_existe = "e16172b3-8d0a-11f0-91e8-777777777777";


    public $jardinería = "1"; // jardineria y ornato
    public $infraestructura = "2";
    public $herreria = "3";
    public $plomeria = "4";
    public $DGS= "5" ; // direccion general de servicios
    public $mantenimiento = "10";

    

    protected function setUp(): void
    {
        $this->asistenciaObj = new Asistencia;
        $this->testSuiteControl = "Asistencias";
        $this->asistenciaObj->setTestingMode(true);
        
    }

    /**
     * @dataProvider ListarAsistenciasProvider
     */
    public  function testListarAsistencias($departamento,$turno,$fecha,$resultado_esperado, bool $fechaAsistencia = false, ...$otros ){
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Listar Asistencias/Inasistencias"))->log();
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

        if($fechaAsistencia){
            $this->assertArrayHasKey('fechaAsistencia', $respuesta, $mensaje.="el arreglo no tiene la llave fecha");
            $this->assertIsString($respuesta['fechaAsistencia'], $mensaje.="el valor de la llave fecha no es un string");
            $this->assertNotEmpty($respuesta['fechaAsistencia'], $mensaje.="el valor de la llave fecha no tiene contenido");
        }



        
        if($resultado_esperado == true){
            $this->assertArrayHasKey('listaTrabajadores',$respuesta, $mensaje.="el arreglo no tiene la llave listaTrabajadores");
            $this->assertIsObject($respuesta['listaTrabajadores'], $mensaje.="el valor de la llave listaTrabajadores no es un objeto");
        }
        else{
            $this->assertArrayNotHasKey('listaTrabajadores',$respuesta, $mensaje.="el arreglo tiene la llave listaTrabajadores pero no debería");
            if(isset($_logger["dataset"]["mensaje esperado"])){
                $this->assertEquals($_logger["dataset"]["mensaje esperado"], $respuesta['message'], $mensaje.="el mensaje no es el esperado");
            }
        }
        

        
        

        
    }

    public  function ListarAsistenciasProvider() {
        // codigos turnos
        $Mañana = $this->mañana;
        $Tarde = $this->tarde;
        $Noche = $this->noche;
        $Fin_de_semana = $this->fin_de_semana;
        $Especial = $this->especial;
        $turno_No_existe = $this->turno_No_existe;


        $Jardinería = $this->jardinería;
        $Infraestructura = $this->infraestructura;
        $Herreria = $this->herreria;
        $Plomeria = $this->plomeria;
        $DGS=  $this->DGS;
        $Mantenimiento = $this->mantenimiento;
        $Plomeria2 = "11"; // XD

        $fechaRegistros = "2025-11-15";
        
        
        $mensajes_esperados = [
            "falta_turno" => "Debe seleccionar un turno",
            "falta_departamento" => "Debe seleccionar una ". DEP_NAME_M,
            "falta_fecha" => "Debe seleccionar una fecha",
            "turno_no_existente" => "El turno seleccionado no existe",
        ];

        $auxCasos = function ($division, $turno, $fecha, $resultado_esperado, bool $fechaAsistencia = false, array $otros = []): array {
            return [
                "División" => $division,
                "Turno" => $turno,
                "fecha" => $fecha,
                "resultado esperado" => $resultado_esperado,
                "-not-fechaAsistencia" => $fechaAsistencia,
                ...$otros
            ];
        };
        
        $arrayReturn = [];
        $arrayReturn["Entradas Validas - Con registro guardados"] = 
        $auxCasos($Jardinería, $Especial, $fechaRegistros, true);

        $arrayReturn["Entradas Validas - Sin registro guardados"] = 
        $auxCasos($Infraestructura,
            $Especial,
            $fechaRegistros,
            true,
            false,
            ["--observacion" => "Retornaría un arreglo vacío"]
            );

        $arrayReturn["Entradas Validas - Fecha fin de semana con turno de fin de semana"] = 
        $auxCasos($Jardinería,
            $Fin_de_semana,
            "2025-10-18", // sabado 10 de octubre
            true,
            false,
            ["--observacion" => "Selecciona un turno de fin de semana y una fecha de fin de semana concretamente el sabado 10 de octubre de 2025"]
            );

        $arrayReturn["Entradas Validas - Fecha semana con turno de fin de semana"] = 
        $auxCasos($Jardinería,
            $Mañana,
            "2025-10-18", // sabado 10 de octubre
            false,
            false,
            [
                "mensaje esperado" => "El turno seleccionado no esta programado para el día seleccionado (Sabado)",
                "--observacion" => "Selecciona un turno de semana y una fecha de fin de semana concretamente el sabado 10 de octubre de 2025"]
            );

        $arrayReturn["Entradas Validas - Fecha fin de semana con turno de semana"] = 
        $auxCasos($Jardinería,
            $Fin_de_semana,
            "2025-10-13", // lunes 13 de octubre
            false,
            false,
            [
                "mensaje esperado" => "El turno seleccionado no esta programado para el día seleccionado (Lunes)",
                "--observacion" => "Selecciona un turno de fin de semana y una fecha de semana concretamente el lunes 13 de octubre de 2025"]
            );
        $arrayReturn["Entradas Invalidas - datos vacios"] = 
        $auxCasos("", "", "", false, false, ["mensaje esperado" => $mensajes_esperados["falta_turno"], "--observacion" => "Primero valida los turnos, luego la ". DEP_NAME_M. " y finalmente la fecha"]);

        $arrayReturn["Entradas Invalidas - División Vacia"] = 
        $auxCasos("", $Especial, $fechaRegistros, false, false, ["mensaje esperado" => $mensajes_esperados["falta_departamento"], "--observacion" => "Primero valida los turnos, luego la ". DEP_NAME_M. " y finalmente la fecha"]);
        
        $arrayReturn["Entradas Invalidas - Turno Vacio"] =
        $auxCasos($Jardinería, "", $fechaRegistros, false, false, ["mensaje esperado" => $mensajes_esperados['falta_turno'], "--observacion" => "Primero valida los turnos, luego la ". DEP_NAME_M. " y finalmente la fecha"]);
        
        $arrayReturn["Entradas Invalidas - Fecha Vacia"] =
        $auxCasos($Jardinería, $Especial, "", false, false, ["mensaje esperado" => $mensajes_esperados['falta_fecha'], "--observacion" => "Primero valida los turnos, luego la ". DEP_NAME_M. " y finalmente la fecha"]);
        
        $arrayReturn["Entradas Invalidas - Turno No Existe"] =
        $auxCasos($Jardinería, $turno_No_existe, $fechaRegistros, false, false, ["mensaje esperado" => $mensajes_esperados['turno_no_existente']]);

        $arrayReturn["Entradas Invalidas - División No Existe"] =
        $auxCasos( 999, $Especial, $fechaRegistros, false, false, ["mensaje esperado" => "La división seleccionada no existe"]);

        $arrayReturn["Entradas Invalidas - Fecha Invalida"] =
        $auxCasos( $Jardinería, $Especial, "2025-30-45", false, false, ["mensaje esperado" => "La fecha no es valida"]);

        $arrayReturn["Entradas Invalidas - División invalida (letras)"] =
        $auxCasos( "ABC", $Especial, $fechaRegistros, false, false, ["mensaje esperado" => "La división no es valida"]);

        


        



        return $arrayReturn;
    }
    



    /**
     * @dataProvider RegistrosProvider
     */
    public function testRegistrarAsistencia( $fecha, $turno, $idDepartamento, $trabajadores, $resultado_esperado, $num_caso ): void
    {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Registrar Asistencias/Inasistencias", "Si el tipo de registro es 1 es asistencia y si es 2, es inasistencia"))->log();

        $listaDatos = [
            'fecha' => $fecha,
            'turno' => $turno,
            'idDepartamento' => $idDepartamento,
            'trabajadores' => $trabajadores
        ];

        // eliminar claves nulas para simular input parcial como en otros tests
        foreach ($listaDatos as $k => $v) {
            if ($v === null) {
                unset($listaDatos[$k]);
            }
        }

        $this->asistenciaObj->setterArray($listaDatos);

        $respuesta = $this->asistenciaObj->registrar(false);

        $mensaje = "caso ($num_caso)";

        $this->assertNotNull($respuesta, $mensaje);
        $this->assertIsArray($respuesta, $mensaje);
        $this->assertArrayHasKey('success', $respuesta, $mensaje);

        $this->assertEquals($resultado_esperado, $respuesta['success'], ($respuesta['message'] ?? '') . ' :: ' . $mensaje);
    }



    public function RegistrosProvider(): array
    {
        $validWorker = [
            [
                "idAsistencia_inasistencia" => null,
                "idTrabajador" => "1",
                "tipo_registro" => "1", // asistencia
                "horaEntrada" => "08:00:00",
                "horaSalida" => "17:00:00",
                "tipo_justificacion" => null,
                "descripcion_justificacion" => null
            ]
        ];

        $workerMissingId = [
            [
                "idAsistencia_inasistencia" => null,
                "idTrabajador" => "",
                "tipo_registro" => "1", // asistencia
                "horaEntrada" => "08:00:00",
                "horaSalida" => "17:00:00",
                "tipo_justificacion" => null,
                "descripcion_justificacion" => null
            ]
        ];

        $turnoMañana = "e1616fed-8d0a-11f0-91e8-d481d7968c88";

        return [
            "Registro valido (1 trabajador)" => [
                "fecha" => date('Y-m-d', strtotime('+1 day')),
                "turno" => $turnoMañana,
                "idDepartamento" => '1',
                "trabajadores" => $validWorker,
                "resultado esperado" => true,
                "num_caso" => 1
            ],
            "Fecha no enviada" => [
                "fecha" => '',
                "turno" => $turnoMañana,
                "idDepartamento" => '1',
                "trabajadores" => $validWorker,
                "resultado esperado" => false,
                "num_caso" => 2
            ],
            "Fecha formato invalido" => [
                "fecha" => '10-10-2025',
                "turno" => $turnoMañana,
                "idDepartamento" => '1',
                "trabajadores" => $validWorker,
                "resultado esperado" => false,
                "num_caso" => 3
            ],
            "Trabajador sin id (datos trabajador invalidos)" => [
                "fecha" => date('Y-m-d'),
                "turno" => $turnoMañana,
                "idDepartamento" => '1',
                "trabajadores" => $workerMissingId,
                "resultado esperado" => false,
                "num_caso" => 4
            ],
            "Varios trabajadores válidos " => [
                "fecha" => date('Y-m-d'),
                "turno" => $turnoMañana,
                "idDepartamento" => '1',
                "trabajadores" => [
                    $validWorker[0],
                    [
                        "idAsistencia_inasistencia" => null,
                        "idTrabajador" => "3",
                        "tipo_registro" => "2",
                        "horaEntrada" => null,
                        "horaSalida" => null,
                        "tipo_justificacion" => "2",
                        "descripcion_justificacion" => "Licencia"
                    ]
                ],
                "resultado esperado" => true,
                "num_caso" => 5
            ],
            "Varios trabajadores válidos e invalidos (tipo justificacion no valido)" => [
                "fecha" => date('Y-m-d'),
                "turno" => $turnoMañana,
                "idDepartamento" => '1',
                "trabajadores" => [
                    $validWorker[0],
                    [
                        "idAsistencia_inasistencia" => null,
                        "idTrabajador" => "3",
                        "tipo_registro" => "2",
                        "horaEntrada" => null,
                        "horaSalida" => null,
                        "tipo_justificacion" => null,
                        "descripcion_justificacion" => "Licencia"
                    ]
                ],
                "resultado esperado" => false,
                "num_caso" => 6
            ],
            "Varios trabajadores válidos e invalidos (todos los datos faltantes menos el id)" => [
                "fecha" => date('Y-m-d'),
                "turno" => $turnoMañana,
                "idDepartamento" => '1',
                "trabajadores" => [
                    $validWorker[0],
                    [
                        "idAsistencia_inasistencia" => null,
                        "idTrabajador" => "3",
                        "tipo_registro" => "2",
                        "horaEntrada" => null,
                        "horaSalida" => null,
                        "tipo_justificacion" => null,
                        "descripcion_justificacion" => "Licencia"
                    ]
                ],
                "resultado esperado" => false,
                "num_caso" => 7
            ],
        ];
    }



    /**
     * @dataProvider EliminarProvider
     */
    public function testEliminarFechaAsistencia( $fecha, $turno, $idDepartamento, $resultado_esperado, $num_caso, ...$otros ): void
    {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Eliminar Asistencias/Inasistencias"))->log();

        $datos = [
            "fecha" => $fecha,
            "turno" => $turno,
            "idDepartamento" => $idDepartamento
        ];

        // eliminar claves nulas para simular inputs parciales
        foreach ($datos as $k => $v) {
            if ($v === null) unset($datos[$k]);
        }

        $this->asistenciaObj->setterArray($datos);

        $respuesta = $this->asistenciaObj->eliminarFechaAsistencia(false);

        $mensaje = "caso ($num_caso)";

        $this->assertNotNull($respuesta, $mensaje);
        $this->assertIsArray($respuesta, $mensaje);
        $this->assertArrayHasKey('success', $respuesta, $mensaje);
        $this->assertArrayHasKey('message', $respuesta, $mensaje);

        $this->assertEquals(
            $resultado_esperado,
            $respuesta['success'],
            ($respuesta['message'] ?? '') . ' :: ' . $mensaje
        );
    }

    public function EliminarProvider(): array
    {
        $mensajeEsperado = [
            "eliminacion exitosa" => "Asistencia eliminada con éxito",
            "faltan Campos" => "Todos los campos son obligatorios",
            "fecha_invalida" => "La fecha es invalida",
            "turno_no_existente" => "El turno seleccionado no existe",
            "division_no_existente" => "La division seleccionada no existe",
        ];
        $turnoMañana = $this->mañana;
        $registroValido = [
            "fecha" => "2025-11-15",
            "turno" => $this->especial,
            "division" => $this->jardinería
        ];
        return [
            "Eliminacion valida" => [
                "fecha" => $registroValido['fecha'],
                "turno" => $registroValido['turno'],
                "idDepartamento" => $registroValido['division'],
                "resultado esperado" => true,
                "num_caso" => 1,
                "mensaje esperado" => $mensajeEsperado['eliminacion exitosa'],
            ],
            "Fecha vacia" => [
                "fecha" => '',
                "turno" => $turnoMañana,
                "idDepartamento" => $registroValido['division'],
                "resultado esperado" => false,
                "num_caso" => 2,
                "mensaje esperado" => $mensajeEsperado['faltan Campos'],
            ],
            "Turno vacio" => [
                "fecha" => $registroValido['fecha'],
                "turno" => '',
                "idDepartamento" => $registroValido['division'],
                "resultado esperado" => false,
                "num_caso" => 3,
                "mensaje esperado" => $mensajeEsperado['faltan Campos'],
            ],
            "Division vacia" => [
                "fecha" => $registroValido['fecha'],
                "turno" => $turnoMañana,
                "idDepartamento" => '',
                "resultado esperado" => false,
                "num_caso" => 4,
                "mensaje esperado" => $mensajeEsperado['faltan Campos'],
            ],
            "Fecha Nula" => [
                "fecha" => null,
                "turno" => $turnoMañana,
                "idDepartamento" => $registroValido['division'],
                "resultado esperado" => false,
                "num_caso" => 5,
                "mensaje esperado" => $mensajeEsperado['faltan Campos'],
            ],
            "Turno Nula" => [
                "fecha" => $registroValido['fecha'],
                "turno" => null,
                "idDepartamento" => $registroValido['division'],
                "resultado esperado" => false,
                "num_caso" => 6,
                "mensaje esperado" => $mensajeEsperado['faltan Campos'],
            ],
            "Division Nula" => [
                "fecha" => $registroValido['fecha'],
                "turno" => $turnoMañana,
                "idDepartamento" => null,
                "resultado esperado" => false,
                "num_caso" => 7,
                "mensaje esperado" => $mensajeEsperado['faltan Campos'],
            ],
            "Fecha formato invalido" => [
                "fecha" => '10-10-2025',
                "turno" => $turnoMañana,
                "idDepartamento" => $registroValido['division'],
                "resultado esperado" => false,
                "num_caso" => 8,
                "mensaje esperado" => $mensajeEsperado['fecha_invalida'],
            ],
            "Turno que no existe" => [
                "fecha" => $registroValido['fecha'],
                "turno" => $this->turno_No_existe,
                "idDepartamento" => $registroValido['division'],
                "resultado esperado" => true,
                "num_caso" => 9,
                "mensaje esperado" => $mensajeEsperado['turno_no_existente'],
            ],
            "Division que no existe" => [
                "fecha" => $registroValido['fecha'],
                "turno" => $turnoMañana,
                "idDepartamento" => $this->turno_No_existe,
                "resultado esperado" => true,
                "num_caso" => 10,
                "mensaje esperado" => $mensajeEsperado['division_no_existente'],
            ],
        ];
    }




}