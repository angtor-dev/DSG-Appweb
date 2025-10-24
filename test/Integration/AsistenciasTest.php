<?php
use PHPUnit\Framework\TestCase;

final class AsistenciasIntTest extends TestCase
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
        $sabado = "1";
        $domingo = "1";
        $datos = [
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
        ];
        
        // Act
        $turno->setterArray($datos);
        $esValido = true;
        try {
            $turno->esValido(Turno::REGISTRAR_TURNO);
        } catch (\Throwable $th) {
            $esValido = false;
        }
        $respuesta = $turno->registrar(false);
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
        $cedula = "11122233";
        $nombre = "Nombre de prueba";
        $apellido = "Apellido de prueba";
        $telefono = "04161234567";
        $idCargo = 1;
        $idTurno = (new Turno())->cargarUltimo()->id; // registrado en el test anterior
        $idDepartamento = (new Division())->cargarUltimo()->id; // registrado en el test anterior
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
        $respuesta = $trabajador->registrar(false);

        // Assert
        $this->assertTrue($esValido, "El trabajador debería ser válido.");
        $this->assertTrue($respuesta['success'], "El trabajador debería haberse registrado correctamente.");
    }

    /** @test */
    final public function registrarAsistencia() : void
    {
        // Arrange
        $asistencia = new Asistencia();
        $fecha = date('Y-m-d', strtotime('+1 day'));
        /** @var Trabajador */
        $trabajador = (new Trabajador())->cargarUltimo(); // registrado en el test anterior
        $turno = (new Turno())->cargarUltimo(); // registrado en el test anterior
        $idDepartamento = $trabajador->idDepartamento;
        $trabajadores = [
            [
                "idAsistencia_inasistencia" => null,
                "idTrabajador" => $trabajador->id,
                "tipo_registro" => "1", // asistencia
                "horaEntrada" => $turno->get_horario_entrada(),
                "horaSalida" => $turno->get_horario_salida(),
                "tipo_justificacion" => null,
                "descripcion_justificacion" => null
            ]
        ];
        $datos = [
            'fecha' => $fecha,
            'turno' => $turno->get_codigo(),
            'idDepartamento' => $idDepartamento,
            'trabajadores' => $trabajadores
        ];

        // Act
        $asistencia->setterArray($datos);
        $esValido = true;
        try {
            $asistencia->esValido(Asistencia::REGISTRAR_ASISTENCIA);
        } catch (\Throwable $e) {
            $esValido = false;
        }
        $respuesta = $asistencia->registrar(false);

        // Assert
        $this->assertTrue($esValido, "La asistencia debería ser válida.");
        $this->assertTrue($respuesta['success'], "La asistencia debería haberse registrado correctamente.");
    }

    /** @test */
    public function listarAsistencias() : void
    {
        // Arrange
        $asistencia = new Asistencia();
        $idDetpartamento = (new Division())->cargarUltimo()->id; // registrado en el test anterior
        $turno = (new Turno())->cargarUltimo(); // registrado en el test anterior
        $fecha = date('Y-m-d', strtotime('+1 day'));
        $datos = [
            'fecha' => $fecha,
            'turno' => $turno->get_codigo(),
            'idDepartamento' => $idDetpartamento,
        ];

        // Act
        $asistencia->setterArray($datos);
        $respuesta = $asistencia->verAsistencias(false);

        // Assert
        $this->assertIsArray($respuesta, "La respuesta debería ser un arreglo.");
        $this->assertNotEmpty($respuesta, "La respuesta no debería estar vacía.");
        $this->assertTrue($respuesta['success'], "La consulta debería haberse realizado correctamente.");
    }

    /** @test */
    public function eliminarAsistencia() : void
    {
        // Arrange
        $asistencia = new Asistencia();
        $idDepartamento = (new Division())->cargarUltimo()->id; // registrado en el test anterior
        $turno = (new Turno())->cargarUltimo(); // registrado en el test anterior
        $fecha = date('Y-m-d', strtotime('+1 day'));
        $datos = [
            'fecha' => $fecha,
            'turno' => $turno->get_codigo(),
            'idDepartamento' => $idDepartamento,
        ];

        // Act
        $asistencia->setterArray($datos);
        $respuesta = $asistencia->eliminarFechaAsistencia(false);

        // Assert
        $this->assertTrue($respuesta['success'], "La asistencia debería haberse eliminado correctamente.");
    }
}