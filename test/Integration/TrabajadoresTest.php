<?php
use PHPUnit\Framework\TestCase;

final class TrabajadoresIntTest extends TestCase
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
        $nombre = "Division de prueba";
        
        // Act
        $division->setDatos(
            null,
            $nombre
        );
        $esValido = $division->esValido();
        $seRegistro = $division->registrar();
        /** @var Division */
        $ultimaDivision = $division->cargarUltimo();

        // Assert
        $this->assertTrue($esValido, "La division debería ser válido.");
        $this->assertTrue($seRegistro, "La division debería haberse registrado correctamente.");
        $this->assertEquals(
            $nombre,
            $ultimaDivision->getNombre(),
            "El nombre de la última división debería coincidir con la registrada."
        );
    }

    /** @test */
    public function registrarCargo() : void
    {
        // Arrange
        $cargo = new Cargo();
        $nombre = "Cargo de prueba";
        $nivel = 1;
        
        // Act
        $cargo->setterArray([
            "nombre" => $nombre,
            "nivel" => $nivel,
        ]);
        $esValido = true;
        try {
            $cargo->esValido(Cargo::REGISTRAR_CARGO);
        } catch (\Throwable $th) {
            $esValido = false;
        }
        $respuesta = $cargo->registrar();
        $ultimoCargo = $cargo->cargarUltimo();

        // Assert
        $this->assertTrue($esValido, "El cargo debería ser válido.");
        $this->assertTrue($respuesta['success'], "El cargo debería haberse registrado correctamente.");
        $this->assertEquals(
            $nombre,
            $ultimoCargo->get_nombre(),
            "El nombre del último cargo debería coincidir con el registrado."
        );
    }

    /** @test */
    public function registrarTurno() : void
    {
        // Arrange
        $turno = new Turno();
        $codigo = "TST-001";
        $nombre = "Turno de prueba";
        $horario_entrada = "08:00:00";
        $horario_salida = "17:00:00";
        $horaInicio = $horario_entrada;
        $horaFin = $horario_salida;
        $lunes = "1";
        $martes = "1";
        $miercoles = "1";
        $jueves = "1";
        $viernes = "1";
        $sabado = "0";
        $domingo = "0";
        
        // Act
        $turno->setterArray([
            "codigo" => $codigo,
            "nombre" => $nombre,
            "horario_entrada" => $horario_entrada,
            "horario_salida" => $horario_salida,
            "horaInicio" => $horaInicio,
            "horaFin" => $horaFin,
            "lunes" => $lunes,
            "martes" => $martes,
            "miercoles" => $miercoles,
            "jueves" => $jueves,
            "viernes" => $viernes,
            "sabado" => $sabado,
            "domingo" => $domingo,
        ]);

        $esValido = true;
        try {
            $turno->esValido(Turno::REGISTRAR_TURNO);
        } catch (\Throwable $th) {
            $esValido = false;
        }
        $respuesta = $turno->registrar();
        $ultimoTurno = $turno->cargarUltimo();

        // Assert
        $this->assertTrue($esValido, "El turno debería ser válido.");
        $this->assertTrue($respuesta['success'], "El turno debería haberse registrado correctamente.");
        $this->assertEquals(
            $nombre,
            $ultimoTurno->get_nombre(),
            "El nombre del último turno debería coincidir con el registrado."
        );
    }

    /** @test */
    public function registrarTrabajador() : void
    {
        // Arrange
        $trabajador = new Trabajador();
        $cedula = "87654321";
        $nombre = "Nombre de prueba";
        $apellido = "Apellido de prueba";
        $telefono = "12345678999";
        $idCargo = (new Cargo())->cargarUltimo()->id;
        $idTurno = (new Turno())->cargarUltimo()->id;
        $idDepartamento = (new Division())->cargarUltimo()->id;
        $fechaIngreso = date('Y-m-d');

        // Act
        $trabajador->setterArray([
            "cedula" => $cedula,
            "nombre" => $nombre,
            "apellido" => $apellido,
            "telefono" => $telefono,
            "cargo" => $idCargo,
            "turno" => $idTurno,
            "idDepartamento" => $idDepartamento,
            "fechaIngreso" => $fechaIngreso,
        ]);
        $esValido = true;
        try {
            $trabajador->esValido(Trabajador::REGISTRAR_TRABAJADOR);
        } catch (\Throwable $e) {
            $esValido = false;
        }
        $respuesta = $trabajador->registrar();

        // Assert
        $this->assertTrue($esValido, "El trabajador debería ser válido.");
        $this->assertTrue($respuesta['success'], "El trabajador debería haberse registrado correctamente.");
    }

    /** @test */
    public function listarTrabajadores() : void
    {
        // Arrange
        $trabajador = new Trabajador();

        // Act
        $trabajadores = $trabajador->listar();
        $ultimoTrabajador = end($trabajadores);

        // Assert
        $this->assertIsArray($trabajadores, "El resultado debería ser un array.");
        $this->assertNotEmpty($trabajadores, "El array de trabajadores no debería estar vacío.");
        $this->assertEquals(
            "87654321",
            $ultimoTrabajador->getCedula(),
            "La cédula del último trabajador debería coincidir con la registrada. {$ultimoTrabajador->getNombre()}"
        );
    }

    /** @test */
    public function actualizarTrabajador() : void
    {
        // Arrange
        $trabajador = new Trabajador();
        $trabajadores = $trabajador->listar();
        $ultimoTrabajador = end($trabajadores);
        $nuevoApellido = "Apellido actualizado";
        $nuevoTelefono = "09876543211";

        // Act
        $ultimoTrabajador->setterArray([
            "cedulaSeleccion" => $ultimoTrabajador->getCedula(),
            "apellido" => $nuevoApellido,
            "telefono" => $nuevoTelefono,
            "cargo" => 1,
            "turno" => 1,
        ]);
        $esValido = true;
        try {
            $ultimoTrabajador->esValido(Trabajador::ACTUALIZAR_TRABAJADOR);
        } catch (\Throwable $e) {
            $esValido = false;
        }
        $respuesta = $ultimoTrabajador->actualizar();
        /** @var Trabajador */
        $trabajadorActualizado = Trabajador::cargar($ultimoTrabajador->id);

        // Assert
        $this->assertTrue($esValido, "El trabajador actualizado debería ser válido.");
        $this->assertTrue($respuesta['success'], "El trabajador debería haberse actualizado correctamente.");
        $this->assertEquals(
            $nuevoApellido,
            $trabajadorActualizado->getApellido(),
            "El apellido del trabajador debería haberse actualizado."
        );
        $this->assertEquals(
            $nuevoTelefono,
            $trabajadorActualizado->getTelefono(),
            "El teléfono del trabajador debería haberse actualizado."
        );
    }

    /** @test */
    public function eliminarTrabajador() : void
    {
        // Arrange
        $trabajador = new Trabajador();
        $trabajadores = $trabajador->listar();
        $ultimoTrabajador = end($trabajadores);

        // Act
        $ultimoTrabajador->setterArray([
            "cedulaSeleccion" => $ultimoTrabajador->getCedula(),
        ]);
        $seElimino = $ultimoTrabajador->eliminar();
        /** @var Trabajador */
        $trabajadorEliminado = Trabajador::cargar($ultimoTrabajador->id);
        $estado = (int)$trabajadorEliminado->getEstado();

        // Assert
        $this->assertTrue($seElimino, "El trabajador debería haberse eliminado correctamente.");
        $this->assertEquals(0, $estado, "El estado del trabajador debería ser 0 (eliminado).");
    }
}