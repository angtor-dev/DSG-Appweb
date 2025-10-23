<?php
use PHPUnit\Framework\TestCase;

final class TareasIntTest extends TestCase
{
    private static Usuario $usuario;

    public static function setUpBeforeClass() : void
    {
        parent::setUpBeforeClass();
        self::$usuario = Usuario::cargar(1, true);
        self::$usuario->beginTestTransaction();
        $_SESSION['usuario'] = self::$usuario;
    }

    public static function tearDownAfterClass() : void
    {
        parent::tearDownAfterClass();
        self::$usuario->stopTestTransaction();
        unset($_SESSION['usuario']);
    }

    /** @test */
    public function registrarDivision() : void
    {
        // Arrange
        $division = new Division();
        $nombre = "Division de pruebas";
        
        // Act
        $division->setDatos(
            null,
            $nombre
        );
        $esValido = $division->esValido();
        $seRegistro = $division->registrar();

        // Assert
        $this->assertTrue($esValido, "La división debería ser válida.");
        $this->assertTrue($seRegistro, "La división debería haberse registrado correctamente.");
    }

    /** @test */
    public function registrarArea() : void
    {
        // Arrange
        $area = new Area();
        $nombre = "Area de pruebas";
        
        // Act
        $area->setDatos(
            null,
            $nombre
        );
        $esValido = $area->esValido();
        $seRegistro = $area->registrar();

        // Assert
        $this->assertTrue($esValido, "El área debería ser válida.");
        $this->assertTrue($seRegistro, "El área debería haberse registrado correctamente.");
    }

    /** @test */
    public function registrarTrabajador() : void
    {
        // Arrange
        $trabajador = new Trabajador();
        $cedula = "12345671";
        $nombre = "Trabajador de pruebas";
        $apellido = "Apellido de prueba";
        $telefono = "12345678912";
        $cargo = 1;
        $turno = 1;
        $idDepartamento = 1;
        $fechaIngreso = date('Y-m-d');

        // Act
        $trabajador->setterArray([
            "cedula" => $cedula,
            "nombre" => $nombre,
            "apellido" => $apellido,
            "telefono" => $telefono,
            "cargo" => $cargo,
            "turno" => $turno,
            "idDepartamento" => $idDepartamento,
            "fechaIngreso" => $fechaIngreso,
        ]);
        $seRegistro = $trabajador->registrar();

        // Assert
        $this->assertTrue($seRegistro['success'], "El trabajador debería haberse registrado correctamente.");
    }

    /** @test */
    public function registrarTarea() : void
    {
        // Arrange
        $tarea = new Tarea();
        $descripcion = "Tarea de prueba";
        $idArea = (new Area())->cargarUltimo()->id;
        $idDepartamento = (new Division())->cargarUltimo()->id;
        $turno = 1;
        $fecha_inicio = date('Y-m-d');
        $idSupervisor = (new Trabajador())->cargarUltimo()->id;
        $idTrabajador = (new Trabajador())->cargarUltimo()->id;
        $idArticulo = (new Articulo())->cargarUltimo()->id;

        // Act
        $datos = [
            'idArea' => $idArea,
            'idDepartamento' => $idDepartamento,
            'descripcion' => $descripcion,
            'turno' => $turno,
            'fecha_inicio' => $fecha_inicio,
            'idSupervisor' => $idSupervisor,
            'personalAsignado' => [$idTrabajador],
            'materiales' => [
                [
                    'id' => $idArticulo,
                    'cantidad' => 1
                ],
            ]
        ];
        $tarea->setterArray($datos);

        $esValido = $tarea->esValido();
        $seRegistro = $tarea->registrar();

        // Assert
        $this->assertTrue($esValido, "La tarea debería ser válida.");
        $this->assertTrue($seRegistro, "La tarea debería haberse registrado correctamente.");
    }

    /** @test */
    public function listarTareas() : void
    {
        // Arrange
        $tarea = new Tarea();

        // Act
        /** @var Tarea[] $tareas */
        $tareas = $tarea->listar();
        $ultimaTarea = end($tareas);

        // Assert
        $this->assertIsArray($tareas, "El resultado debería ser un array.");
        $this->assertNotEmpty($tareas, "El array de tareas no debería estar vacío.");
        $this->assertEquals(
            "Tarea de prueba",
            $ultimaTarea->getDescripcion(),
            "La descripción de la última tarea debería coincidir con la registrada."
        );
        $this->assertEquals(
            "activo",
            $ultimaTarea->getEstado(),
            "El estado de la última tarea debería ser 'activo'."
        );
    }

    /** @test */
    public function terminarTarea() : void
    {
        // Arrange
        $tarea = new Tarea();

        // Act
        /** @var Tarea $ultimaTarea */
        $ultimaTarea = $tarea->cargarUltimo();
        $ultimaTarea->terminar();
        $tareaActualizada = $tarea->obtenerPorId($ultimaTarea->id);

        // Assert
        $this->assertEquals(
            $ultimaTarea->id,
            $tareaActualizada->id,
            "El ID de la tarea debería coincidir después de actualizarla."
        );
        $this->assertEquals(
            "vencida",
            $tareaActualizada->getEstado(),
            "El estado de la tarea debería ser 'vencida' después de finalizarla."
        );
    }

    public function evaluarTarea() : void
    {
        // Arrange
        $tarea = new Tarea();

        /** @var Tarea $ultimaTarea */
        $ultimaTarea = $tarea->cargarUltimo();
        $idArticulo = (new Articulo())->cargarUltimo()->id;
        $ponderacion = 'buenobueno';
        $comentarios = 'Observaciones de prueba del supervisor';
        $aprobacion = 1;

        $ponderacion_director = 'buenomedio';
        $comentarios_director = 'Observaciones de prueba del director';
        $aprobacion_director = 1;
        $materiales = [
            ['id' => $idArticulo, 'utilizado' => 1, 'devuelto' => 0]
        ];

        $datosEvaluacion = [
            'id' => $ultimaTarea->id,
            'evaluacion' => [
                'ponderacion' => $ponderacion,
                'comentarios' => $comentarios,
                'aprobacion' => $aprobacion
            ],
            'evaluacionDirector' => [
                'ponderacion' => $ponderacion_director,
                'comentarios' => $comentarios_director,
                'aprobacion' => $aprobacion_director
            ],
            'materiales' => $materiales
        ];

        // Act
        $tarea->setterArray($datosEvaluacion);
        $seEvaluo = $tarea->evaluar();
        $tareaEvaluada = $tarea->obtenerPorId($ultimaTarea->getId());

        // Assert
        $this->assertTrue($seEvaluo, "La tarea debería haberse evaluado correctamente.");
        $this->assertEquals(
            $ultimaTarea->getId(),
            $tareaEvaluada->getId(),
            "El ID de la tarea debería coincidir después de evaluarla."
        );
        $this->assertEquals(
            'evaluada',
            $tareaEvaluada->getEstado(),
            "El estado de la tarea debería ser 'evaluada' después de evaluarla."
        );
    }
}