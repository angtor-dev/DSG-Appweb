<?php
use PHPUnit\Framework\TestCase;

final class TareasTest extends TestCase
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
        $cedula = "12345679";
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
        echo $idArticulo;

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
    }

    /** @test */
    public function actualizarTarea() : void
    {
        // Arrange
        $tarea = new Tarea();
        $nuevaDescripcion = "Tarea de prueba actualizada";

        // Act
        $ultimaTarea = $tarea->cargarUltimo();
        $ultimaTarea->setDescripcion($nuevaDescripcion);
        $esValido = $ultimaTarea->esValido();
        $seActualizo = $ultimaTarea->actualizar();

        // Assert
        $this->assertTrue($esValido, "La tarea actualizada debería ser válida.");
        $this->assertTrue($seActualizo, "La tarea debería haberse actualizado correctamente.");
    }
}