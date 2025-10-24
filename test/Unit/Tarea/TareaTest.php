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
    public function EvaluarTarea(){

        $datosEvaluacion = [
            'id' => (int)$_POST['idTarea'],
            'evaluacion' => [
                'ponderacion' => $_POST['ponderacion'] ?? '',
                'comentarios' => $_POST['comentarios'] ?? '',
                'aprobacion' => isset($_POST['aprobacion']) ? 1 : 0
            ],
            'evaluacionDirector' => [
                'ponderacion' => $_POST['ponderacion_director'] ?? '',
                'comentarios' => $_POST['comentarios_director'] ?? '',
                'aprobacion' => isset($_POST['aprobacion_director']) ? 1 : 0
            ],
            'materiales' => isset($_POST['materiales']) ? 
                (is_string($_POST['materiales']) ? json_decode($_POST['materiales'], true) : (array)$_POST['materiales']) 
                : null
        ];

    }
    public function providerEvaluar(){
        
    }
}