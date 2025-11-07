<?php 
use PHPUnit\Framework\TestCase;

class TareaTest extends TestCase
{
    private $tareaObj;
    private $testSuiteControl;
    protected function setUp(): void
    {
        $this->tareaObj = new Tarea;
        $this->tareaObj->setTestingMode(true);
        $this->testSuiteControl = "Tarea";
    }

    public function ListarTarea()
    {
        // This test method is intentionally left blank.
    }
    /**
     * @dataProvider crearTareaProvider
     */
    public function testCrearTarea($idDepartamento, $descripcion, $idArea, $turno, $fecha_inicio, $supervisor, $materiales, $personal, $resultado_esperado)
    {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Crear Tarea de Inventario"))->log();
       

        $datos = [
            'idArea' => (int)$idArea,
            'idDepartamento' => (int)$idDepartamento,
            'descripcion' => trim($descripcion),
            'turno' => $turno,
            'fecha_inicio' => $fecha_inicio,
            'idSupervisor' => (int)($supervisor ?? 0),
            'personalAsignado' => isset($personal) ? (array)$personal : null,
            'materiales' => isset($materiales) ? 
                (is_string($materiales)) ? json_decode($materiales, true) : (array)$materiales 
                : null
        ];

        $temp = [
         "idDepartamento" => $idDepartamento,
         "descripcion" => $descripcion,
         "idArea" => $idArea,
         "turno" => $turno,
         "fecha_inicio" => $fecha_inicio,
         "supervisor" => $supervisor,
         "materiales" => $materiales,
         "personal" => $personal];

        foreach ($temp as $key => $value) {
            if($value == "__NULL__") {
                unset($datos[$key]); // Si el valor es __NULL__ se elimina
            }
        }

        $this->tareaObj->setterArray($datos);

        $resp = $this->tareaObj->registrar();
        $this->assertIsBool($resp, $_logger["dataname"]);
        $this->assertEquals($resultado_esperado, $resp, $_logger["dataname"]);
        if($resultado_esperado == false) {
            $this->assertArrayHasKey("errores", $_SESSION, $_logger["dataname"]);
        }

    }

    public function crearTareaProvider() {
        /*
            /*    {
    "nombre": "xavier",
    "idDepartamento": "1",
    "descripcion": "ddddd",
    "idArea": "9",
    "turno": "1",
    "tipoTarea": "normal",
    "fecha_inicio": "2025-10-23",
    "supervisor": "29",
    "tabla-materiales_length": "10",
    "materiales": "[{\"id\":2,\"nombre\":\"Jabón Líquido\",\"cantidad\":1,\"unidad\":\"Litro\"},{\"id\":1,\"nombre\":\"Bolígrafo Azul\",\"cantidad\":1,\"unidad\":\"Unidad\"}]",
    "personal[]": "34"
    }
*/


        return [
            "Entradas Validas - Crear Tarea" => [
                "idDepartamento" => 1,
                "descripcion" => "Tarea de Inventario",
                "idArea" => 9,
                "turno" => 1,
                "fecha_inicio" => "2023-05-01",
                "supervisor" => 29,
                "materiales" => [
                    [
                        "id" => 2,
                        "nombre" => "Jabón Líquido",
                        "cantidad" => 1,
                        "unidad" => "Litro"
                    ],
                    [
                        "id" => 1,
                        "nombre" => "Bolígrafo Azul",
                        "cantidad" => 1,
                        "unidad" => "Unidad"
                    ]
                ],
                "personal" => [
                    34
                ],
                "resultado esperado" => true
            ],
            "Entradas Invalidas - fecha invalida" => [
                "idDepartamento" => 1,
                "descripcion" => "Tarea de Inventario",
                "idArea" => 9,
                "turno" => 1,
                "fecha_inicio" => "2023-13-01ABC",
                "supervisor" => 29,
                "materiales" => [
                    [
                        "id" => 2,
                        "nombre" => "Jabón Líquido",
                        "cantidad" => 1,
                        "unidad" => "Litro"
                    ],
                    [
                        "id" => 1,
                        "nombre" => "Bolígrafo Azul",
                        "cantidad" => 1,
                        "unidad" => "Unidad"
                    ]
                ],
                "personal" => [
                    34
                ],
                "resultado esperado" => false
            ],
            "Entradas Invalidas - sin idArea" => [
                "idDepartamento" => 1,
                "descripcion" => "Tarea de Inventario",
                "idArea" => '__NULL__',
                "turno" => 1,
                "fecha_inicio" => "2023-05-01",
                "supervisor" => 29,
                "materiales" => [
                    [
                        "id" => 2,
                        "nombre" => "Jabón Líquido",
                        "cantidad" => 1,
                        "unidad" => "Litro"
                    ],
                    [
                        "id" => 1,
                        "nombre" => "Bolígrafo Azul",
                        "cantidad" => 1,
                        "unidad" => "Unidad"
                    ]
                ],
                "personal" => [
                    34
                ],
                "resultado esperado" => false
            ],
            "Entradas Invalidas - sin division" => [
                "idDepartamento" => "__NULL__",
                "descripcion" => "Tarea de Inventario",
                "idArea" => 9,
                "turno" => 1,
                "fecha_inicio" => "2023-05-01",
                "supervisor" => 29,
                "materiales" => [
                    [
                        "id" => 2,
                        "nombre" => "Jabón Líquido",
                        "cantidad" => 1,
                        "unidad" => "Litro"
                    ],
                    [
                        "id" => 1,
                        "nombre" => "Bolígrafo Azul",
                        "cantidad" => 1,
                        "unidad" => "Unidad"
                    ]
                ],
                "personal" => [
                    34
                ],
                "resultado esperado" => false
            ],
            "Entradas Invalidas - descripcion invalida" => [
                "idDepartamento" => 1,
                "descripcion" => "Tarea de Inventario<script>alert('hacked')</script>",
                "idArea" => 9,
                "turno" => 1,
                "fecha_inicio" => "2023-05-01",
                "supervisor" => 29,
                "materiales" => [
                    [
                        "id" => 2,
                        "nombre" => "Jabón Líquido",
                        "cantidad" => 1,
                        "unidad" => "Litro"
                    ],
                    [
                        "id" => 1,
                        "nombre" => "Bolígrafo Azul",
                        "cantidad" => 1,
                        "unidad" => "Unidad"
                    ]
                ],
                "personal" => [
                    34
                ],
                "resultado esperado" => false
            ],
            "Entradas Invalidas - Turno vacio" => [
                "idDepartamento" => 1,
                "descripcion" => "Tarea de Inventario",
                "idArea" => 9,
                "turno" => 0,
                "fecha_inicio" => "2023-05-01",
                "supervisor" => 29,
                "materiales" => [
                    [
                        "id" => 2,
                        "nombre" => "Jabón Líquido",
                        "cantidad" => 1,
                        "unidad" => "Litro"
                    ],
                    [
                        "id" => 1,
                        "nombre" => "Bolígrafo Azul",
                        "cantidad" => 1,
                        "unidad" => "Unidad"
                    ]
                ],
                "personal" => [
                    34
                ],
                "resultado esperado" => false
            ],
            "Entradas Invalidas - Turno invalido" => [
                "idDepartamento" => 1,
                "descripcion" => "Tarea de Inventario",
                "idArea" => 9,
                "turno" => "HOALA",
                "fecha_inicio" => "2023-05-01",
                "supervisor" => 29,
                "materiales" => [
                    [
                        "id" => 2,
                        "nombre" => "Jabón Líquido",
                        "cantidad" => 1,
                        "unidad" => "Litro"
                    ],
                    [
                        "id" => 1,
                        "nombre" => "Bolígrafo Azul",
                        "cantidad" => 1,
                        "unidad" => "Unidad"
                    ]
                ],
                "personal" => [
                    34
                ],
                "resultado esperado" => false
            ],
            "Entradas Invalidas - fecha Vacia" => [
                "idDepartamento" => 1,
                "descripcion" => "Tarea de Inventario",
                "idArea" => 9,
                "turno" => 1,
                "fecha_inicio" => "",
                "supervisor" => 29,
                "materiales" => [
                    [
                        "id" => 2,
                        "nombre" => "Jabón Líquido",
                        "cantidad" => 1,
                        "unidad" => "Litro"
                    ],
                    [
                        "id" => 1,
                        "nombre" => "Bolígrafo Azul",
                        "cantidad" => 1,
                        "unidad" => "Unidad"
                    ]
                ],
                "personal" => [
                    34
                ],
                "resultado esperado" => false
            ],
            "Entradas Invalidas - supervisor vacio" => [
                "idDepartamento" => 1,
                "descripcion" => "Tarea de Inventario",
                "idArea" => 9,
                "turno" => 1,
                "fecha_inicio" => "2023-05-01",
                "supervisor" => 0,
                "materiales" => [
                    [
                        "id" => 2,
                        "nombre" => "Jabón Líquido",
                        "cantidad" => 1,
                        "unidad" => "Litro"
                    ],
                    [
                        "id" => 1,
                        "nombre" => "Bolígrafo Azul",
                        "cantidad" => 1,
                        "unidad" => "Unidad"
                    ]
                ],
                "personal" => [
                    34
                ],
                "resultado esperado" => false
            ],
            "Entradas Invalidas - supervisor invalido" => [
                "idDepartamento" => 1,
                "descripcion" => "Tarea de Inventario",
                "idArea" => 9,
                "turno" => 1,
                "fecha_inicio" => "2023-05-01",
                "supervisor" => "HOALA",
                "materiales" => [
                    [
                        "id" => 2,
                        "nombre" => "Jabón Líquido",
                        "cantidad" => 1,
                        "unidad" => "Litro"
                    ],
                    [
                        "id" => 1,
                        "nombre" => "Bolígrafo Azul",
                        "cantidad" => 1,
                        "unidad" => "Unidad"
                    ]
                ],
                "personal" => [
                    34
                ],
                "resultado esperado" => false
            ],
            "Entradas Invalidas - sin Personal" => [
                "idDepartamento" => 1,
                "descripcion" => "Tarea de Inventario",
                "idArea" => 9,
                "turno" => 1,
                "fecha_inicio" => "2023-05-01",
                "supervisor" => 29,
                "materiales" => [
                    [
                        "id" => 2,
                        "nombre" => "Jabón Líquido",
                        "cantidad" => 1,
                        "unidad" => "Litro"
                    ],
                    [
                        "id" => 1,
                        "nombre" => "Bolígrafo Azul",
                        "cantidad" => 1,
                        "unidad" => "Unidad"
                    ]
                ],
                "personal" => [],
                "resultado esperado" => false
            ],
            "Entradas Invalidas - Personal invalido" => [
                "idDepartamento" => 1,
                "descripcion" => "Tarea de Inventario",
                "idArea" => 9,
                "turno" => 1,
                "fecha_inicio" => "2023-05-01",
                "supervisor" => 29,
                "materiales" => [
                    [
                        "id" => 2,
                        "nombre" => "Jabón Líquido",
                        "cantidad" => 1,
                        "unidad" => "Litro"
                    ],
                    [
                        "id" => 1,
                        "nombre" => "Bolígrafo Azul",
                        "cantidad" => 1,
                        "unidad" => "Unidad"
                    ]
                ],
                "personal" => [
                    "HOALA"
                ],
                "resultado esperado" => false
            ],
            "Entradas Invalidas - Materiales invalidos" => [
                "idDepartamento" => 1,
                "descripcion" => "Tarea de Inventario",
                "idArea" => 9,
                "turno" => 1,
                "fecha_inicio" => "2023-05-01",
                "supervisor" => 29,
                "materiales" => [
                    [
                        "id" => "HOALA",
                        "nombre" => "Jabón Líquido",
                        "cantidad" => 1,
                        "unidad" => "Litro"
                    ],
                    [
                        "id" => 1,
                        "nombre" => "Bolígrafo Azul",
                        "cantidad" => 1,
                        "unidad" => "Unidad"
                    ]
                ],
                "personal" => [
                    34
                ],
                "resultado esperado" => false
            ],
            "Entradas Invalidas - Materiales cantidad invalida" => [
                "idDepartamento" => 1,
                "descripcion" => "Tarea de Inventario",
                "idArea" => 9,
                "turno" => 1,
                "fecha_inicio" => "2023-05-01",
                "supervisor" => 29,
                "materiales" => [
                    [
                        "id" => 2,
                        "nombre" => "Jabón Líquido",
                        "cantidad" => "HOALA",
                        "unidad" => "Litro"
                    ],
                    [
                        "id" => 1,
                        "nombre" => "Bolígrafo Azul",
                        "cantidad" => 1,
                        "unidad" => "Unidad"
                    ]
                ],
                "personal" => [
                    34
                ],
                "resultado esperado" => false
            ],

        ];
    }
    /**
     * 
     * @dataProvider providerEvaluar
     * @return void
     */
    public function testEvaluarTarea($id, $evaluacion, $evaluacionDirector, $materiales, $resultado_esperado){
        $__logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Evaluar Tarea"))->log();
        $datos = [
            'id' => $id,
            'evaluacion' => $evaluacion,
            'evaluacionDirector' => $evaluacionDirector,
            'materiales' => $materiales
        ];
        $this->tareaObj->setterArray($datos);
        $result = $this->tareaObj->evaluar();

        $this->assertEquals($result, $resultado_esperado, $__logger['dataname']);
        

    }
    public function providerEvaluar(){
        $tarea = function (){
            $db = Database::getInstance();
            $db->connect();
            $id = $db->pdo()->query("SELECT id FROM tarea WHERE estado_tarea = 'vencida' limit 1")->fetch(PDO::FETCH_ASSOC)['id'];
            $data = [];
            $data['id'] = $id;
            $materiales = $db->pdo()->query("SELECT r.id, r.idTarea, r.idArticulo, r.cantidad FROM recurso as r WHERE r.idTarea = $id")->fetchAll(PDO::FETCH_ASSOC);
            $data['materiales'] = [];
            foreach($materiales as $material){
                $data['materiales'][] = [
                    'id' => $material['id'],
                    'utilizado' => "0",
                    'devuelto' => $material['cantidad']
                ];
            }
            $db->disconnect();
            /**
             * @var array{
             *  'id': int,
             *  'materiales':array< array{
             *      'id': int,
             *      'utilizado': string,
             *      'devuelto': string
             *      }>
             *  }   $data
             */
            return $data;
        };

        $removeNull = function (&$array) {
            foreach ($array as $key => $value) {
                if ($value === null) {
                    unset($array[$key]);
                }
            }
        };

        $func = function ($id, $ponderacion, $comentarios, $aprobacion, $resultado, $materiales = [], $ponderacionDirector = '', $comentariosDirector = '', $aprobacionDirector = 0, $mensajeEsperado = '') use ($removeNull) {
            $evaluacion = [
                'ponderacion' => $ponderacion,
                'comentarios' => $comentarios,
                'aprobacion' => $aprobacion
            ];
            $evaluacionDirector = [
                'ponderacion' => $ponderacionDirector,
                'comentarios' => $comentariosDirector,
                'aprobacion' => $aprobacionDirector
            ];
            $removeNull($evaluacion);
            $removeNull($evaluacionDirector);
            $mensaje = $resultado? "Evaluacion Registrada con exito": "Error al registrar la evaluacion";

            if($mensajeEsperado != '') $mensaje = $mensajeEsperado;




            return [
                "id" => $id,
                "evaluacion" => $evaluacion,
                "evaluacionDirector" => $evaluacionDirector,
                "materiales" => $materiales,
                "resultado esperado" => $resultado,
                "mensaje esperado" => $mensaje
            ];
        };
        $datos = $tarea();
        
        $idValido = intval($datos['id']);
        $materialesSinDevolver = $datos['materiales'];
        foreach($materialesSinDevolver as $material){
            $material['utilizado'] = $material['devuelto'];
            $material['devuelto'] = 0;
        }
        $materialCantidadUtilizadaInvalida = $datos['materiales'];
        $materialCantidadUtilizadaInvalida[0]['devuelto'] = "Algo";
        $materialCantidadDevueltaInvalida = $datos['materiales'];
        $materialCantidadDevueltaInvalida[0]['utilizado'] = "Algo";
        $idInvalido = 0;
        $pondValida = "buenobueno";
        $comValida = "Excelente";
        $aprobValida = 1;
        $pondInvalida = "<script>Algo</script>";
        $comInvalida = "<script>Algo</script>";


        return [
            "Entrada Valida - Evaluar" => $func($idValido, $pondValida, $comValida, $aprobValida,true,$datos['materiales'], $pondValida, $comValida, $aprobValida),
            "Entrada Valida - Evaluar sin materiales" => $func($idValido, $pondValida, $comValida, $aprobValida,true,$materialesSinDevolver, $pondValida, $comValida, $aprobValida),
            "Entrada Invalida - Id Inexistente" => $func($idInvalido, $pondValida, $comValida, $aprobValida,false),
            "Entrada Invalida - Sin Ponderacion del supervisor" => $func($idValido, null, $comValida, $aprobValida,false,mensajeEsperado: "La ponderación del supervisor es obligatoria"),
            "Entrada Invalida - Ponderacion del supervisor Invalido" => $func($idValido, $pondInvalida, $comValida, $aprobValida,false,mensajeEsperado: "La ponderación del supervisor contiene caracteres inválidos"),
            "Entrada Invalida - Comentario del supervisor Invalido" => $func($idValido, $pondValida, $comInvalida, $aprobValida,false,mensajeEsperado: "Los comentarios del supervisor contienen caracteres inválidos"),
            "Entrada Invalida - Sin Aprobacion del supervisor" => $func($idValido, $pondValida, $comValida, null,false,mensajeEsperado: "La aprobación del supervisor es obligatoria"),
            "Entrada Invalida - Ponderacion del director Invalido" => $func($idValido, $pondValida, $comValida, $aprobValida,false,mensajeEsperado: "La ponderación del director contiene caracteres inválidos", ponderacionDirector: $pondInvalida),
            "Entrada Invalida - Comentario del director Invalido" => $func($idValido, $pondValida, $comValida, $aprobValida,false,mensajeEsperado: "Los comentarios del director contienen caracteres inválidos",comentariosDirector: $comInvalida),
            "Entrada Invalida - Cantidad Devuelta Invalida" => $func($idValido, $pondValida, $comValida, $aprobValida,false,mensajeEsperado: "La cantidad devuelta contiene caracteres inválidos",materiales: $materialCantidadDevueltaInvalida),
            "Entrada Invalida - Cantidad Utilizada Invalida" => $func($idValido, $pondValida, $comValida, $aprobValida,false,mensajeEsperado: "La cantidad utilizada contiene caracteres inválidos",materiales: $materialCantidadUtilizadaInvalida),
        ];
    }
    /**
     * @dataProvider providerTerminar
     */
    public function testTerminarTarea($idTarea, $resultado_esperado){
        $logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Terminar Tarea"))->log();
        /**
         * @var Tarea
         */
        $tarea = Tarea::cargar($idTarea);
        
        if($resultado_esperado){
            $tarea->beginTestTransaction();
            $this->assertInstanceOf(Tarea::class, $tarea, $logger['dataname']);
            if ($tarea->getEstado() !== 'activo') {
                $this->fail("La tarea no se encuentra activa");
            }
            $tarea->terminar();
            /**
             * @var Tarea
             */
            $tarea = Tarea::cargar($idTarea);
            $this->assertEquals('vencida', $tarea->getEstado(), $logger['dataname']);
            $tarea->stopTestTransaction();
        }
        else{
            $this->assertNotInstanceOf(Tarea::class, $tarea, $logger['dataname']);
        }
        

    }
    public function providerTerminar(){
        $tarea = function (){
            $db = Database::getInstance();
            $db->connect();
            $data = $db->pdo()->query("SELECT id FROM tarea WHERE estado_tarea = 'activo' limit 1")->fetch(PDO::FETCH_ASSOC);
            $db->disconnect();
            return $data;
        };

        $idValido = intval($tarea()['id']);
        $idInvalido = 0;

        return [
            "Entrada Valida - Terminar" => [
                "id" => $idValido,
                "resultado esperado" => true,
                "observaciones" => "queso",
                "mensaje esperado" => "Tarea marcada como terminada correctamente"
            ],
            "Entrada Invalida - Id Inexistente" => [
                "id" => $idInvalido,
                "resultado esperado" => false,
                "observaciones" => "queso",
                "mensaje esperado" => "Tarea no encontrada"
            ]
        ];
    }

    /**
     * @dataProvider providerTerminar
     */
    public function CancelarTarea($idTarea, $resultado_esperado){
        $logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Terminar Tarea"))->log();
        /**
         * @var Tarea
         */
        $tarea = Tarea::cargar($idTarea);
        
        if($resultado_esperado){
            $tarea->beginTestTransaction();
            $this->assertInstanceOf(Tarea::class, $tarea, $logger['dataname']);
            if ($tarea->getEstado() !== 'activo') {
                $this->fail("La tarea no se encuentra activa");
            }
            $temp =$tarea->cancelar();
            /**
             * @var Tarea
             */
            $tarea = Tarea::cargar($idTarea);
            $this->assertEquals('cancelado', $tarea->getEstado(), $logger['dataname']);
            $tarea->stopTestTransaction();
        }
        else{
            $this->assertNotInstanceOf(Tarea::class, $tarea, $logger['dataname']);
        }
        

    }


    /**
     * @dataProvider estadoProvider
     */
    public function testListarPorEstado($estado, $resultado_esperado){
        $logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Listar Por Estado"))->log();
        $tareas = $this->tareaObj->listarPorEstado($estado);
        if($resultado_esperado){
            $this->assertIsArray($tareas, $logger['dataname']);
        }
        else{
            $this->assertIsNotArray($tareas, $logger['dataname']);
        }
    }
    /**
     * @dataProvider estadoProvider
     */
    public function testListarPorEstadoConPersonal($estado, $resultado_esperado){
        $logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Listar Por Estado Con Personal"))->log();
        $tareas = $this->tareaObj->listarPorEstadoConPersonal($estado);
        if($resultado_esperado){
            $this->assertIsArray($tareas, $logger['dataname']);
        }
    }

    public function estadoProvider(){
        return [
            "Entrada Valida - Estado Activo" => [
                "estado" => "activo",
                "resultado esperado" => true
            ],
            "Entrada Valida - Estado cancelado" => [
                "estado" => "cancelado",
                "resultado esperado" => true
            ],
            "Entrada Valida - Estado Vencida" => [
                "estado" => "vencida",
                "resultado esperado" => true
            ],
            
        ];
    }



        /**
     * @dataProvider obtenerTareasParaOrdenesProvider
     */
    public function testobtenerTareasParaOrdenes($estado, $resultado_esperado){
        $logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Obtener Tareas Para Ordenes"))->log();
        $tareas = $this->tareaObj->obtenerTareasParaOrdenes($estado);
        if($resultado_esperado){
            $this->assertIsArray($tareas, $logger['dataname']);
        }
    }

    public function obtenerTareasParaOrdenesProvider(){
        $func = function(){
            $obj = new Tarea();
            $lista = $obj->listarPorEstado('activo');
            $resp = [];
            foreach($lista as $tarea){
                $resp[] = $tarea["id"];
            }
            return $resp;
        };
        return [
            "Entrada Valida - lista de tareas" => [
                "ids" => $func(),
                "resultado esperado" => true
            ],
            
            
        ];
    }

    /**
     * @dataProvider SinEntradasProvider
     * @param mixed $resultado_esperado
     * @return void
     */
    public function testListarConCategoriaYUnidad($dato1, $resultado_esperado){
        $logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Listar Con Categoria Y Unidad"))->log();
        $tareas = $this->tareaObj->listarConCategoriaYUnidad();
        $this->assertIsArray($tareas, $logger['dataname']);
        foreach ($tareas as $tarea) {
            $this->assertArrayHasKey("id", $tarea, $logger['dataname']);
            $this->assertArrayHasKey("nombre", $tarea, $logger['dataname']);
            $this->assertArrayHasKey("categoria", $tarea, $logger['dataname']);
            $this->assertArrayHasKey("unidad", $tarea, $logger['dataname']);
            $this->assertArrayHasKey("disponible", $tarea, $logger['dataname']);
        }
    }

    public function SinEntradasProvider(){

        return [
            "Sin Entradas" => [
                "NA" => "No hay entradas",
                "resultado esperado" => true
            ],
        ];
        
    }



    /**
     * @dataProvider estadoProvider
     */
    public function testcontarPorEstado($estado, $resultado_esperado){
        $logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Listar Por Estado Con Personal"))->log();
        $tareas = $this->tareaObj->contarPorEstado($estado);
        if($resultado_esperado){
            $this->assertIsInt($tareas, $logger['dataname']);
        }
    }





    



}