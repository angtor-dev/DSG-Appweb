<?php
use PHPUnit\Framework\TestCase;

class UsuariosRegistrarTest extends TestCase
{
    private $usuariosObj;

    protected function setUp(): void
    {
        $this->usuariosObj = new Usuario;
        $this->usuariosObj->setTestingMode(true);
    }

    /**
     * @dataProvider RegistrosProvider
     */
    public function testRegistrar($cedula, $correo, $rol, $clave, $resultado_esperado, $num_caso)
    {
        $_POST['cedula'] = $cedula; // cedula del usuario que sera registrado
        $_POST['correo'] = $correo; // correo nuevo
        $_POST['idRol'] = $rol; // rol nuevo
        $_POST['clave'] = $clave; // clave nueva

        $this->usuariosObj->mapearFormulario();

        $respuesta = $this->usuariosObj->registrar(false);

        $mensaje = "caso ($num_caso)";

        $this->assertNotNull($respuesta);
        $this->assertIsArray($respuesta);

        $this->assertArrayHasKey('success', $respuesta);
        if ($respuesta['success']) {
            // si es verdadero entonces idInserted debe ser mayor que 0
            $this->assertGreaterThan(0, $respuesta['idInserted'], $mensaje);
        }
        $mensaje = ($respuesta['consoleError'] ?? $respuesta['mensaje']) . ' :: '.$mensaje;

        $this->assertEquals($resultado_esperado, $respuesta['success'], $mensaje);
    }

    public function RegistrosProvider()
    {
        return [
            // casos de prueba
            ['34785435', 'correo@example.com', 1, 'Clave123', true, 1],
            ['00000008', 'otrocorreo@example.com', 8, 'OtraClave123', true, 2],
            ['00000009', 'correo@example.com', 1, '', false, 3], // clave vacía
            ['00000010', 'algo@esto.chau', 8, 'Clave123', false, 4], // correo duplicado
        ];
    }
}