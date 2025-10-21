<?php 

/*
cargo	CREATE TABLE `cargo` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`nombre` varchar(80) NOT NULL,
	`nivel` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci	
 */

use PHPUnit\Framework\TestCase;

class CargoTest extends TestCase
{
    private $cargoObj;
    private $testSuiteControl;

    protected function setUp(): void
    {
        $this->cargoObj = new Cargo;
        $this->testSuiteControl = "Cargos";
        $this->cargoObj->setTestingMode(true);
    }

    /**
     * @dataProvider RegistrosProvider
     */
    public function testRegistrarCargo($nombre,$nivel,$mensajeEsperado, $resultado_esperado, $num_caso)
    {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Registrar Cargo"))->log();
        
        $listaDatos = [
            'nombre' => $nombre,
            'nivel' => $nivel,
        ];

        foreach ($listaDatos as $key => $value) {
            if ($value === null) {
                unset($listaDatos[$key]);
            }
        }


         $this->cargoObj->setterArray($listaDatos);

        $respuesta = $this->cargoObj->registrar(false);

        $mensaje = "caso ($num_caso)";

        $this->assertNotNull($respuesta);
        $this->assertIsArray($respuesta);

        $this->assertArrayHasKey('success', $respuesta);
        
        $mensaje = ($respuesta['consoleError'] ?? $respuesta['message']) . ' :: '.$mensaje;

        $this->assertEquals($resultado_esperado, $respuesta['success'], $mensaje);
        $this->assertEquals($mensajeEsperado, $respuesta['message'], $mensaje);

    }

    public function RegistrosProvider(): array
{
    global $mensajes;
    
    $mensajeEsperado = [
        "registro exitoso" => $mensajes->registroExitoso,
        "nombre requerido" => $mensajes->nombreRequerido,
        "nivel requerido" => $mensajes->nivelRequerido,
        "cargo existente" => $mensajes->cargoExistente,
    ];
    
    $registroValido = [
        "nombre" => "Gerente",
        "nivel" => 1
    ];
    
    return [
        "Registro valido" => [
            "nombre" => $registroValido['nombre'],
            "nivel" => $registroValido['nivel'],
            "mensaje esperado" => $mensajeEsperado['registro exitoso'],
            "resultado esperado" => true,
            "num_caso" => 1,
        ],
        "Nombre vacio" => [
            "nombre" => '',
            "nivel" => $registroValido['nivel'],
            "mensaje esperado" => $mensajeEsperado['nombre requerido'],
            "resultado esperado" => false,
            "num_caso" => 2,
        ],
        "Nivel nulo" => [
            "nombre" => $registroValido['nombre'],
            "nivel" => null,
            "mensaje esperado" => $mensajeEsperado['nivel requerido'],
            "resultado esperado" => false,
            "num_caso" => 3,
        ],
        "Cargo existente" => [
            "nombre" => "Vigilante",
            "nivel" => $registroValido['nivel'],
            "mensaje esperado" => $mensajeEsperado['cargo existente'],
            "resultado esperado" => false,
            "num_caso" => 4,
        ],
    ];
}


    /**
     * @dataProvider ActualizarProvider
     */
    public function testActualizarCargo($id, $nombre, $nivel, $mensajeEsperado, $resultado_esperado, $num_caso)
    {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Actualizar Cargo"))->log();
        $listaDatos = [
            'id' => $id,
            'nombre' => $nombre,
            'nivel' => $nivel,
        ];
    
        // si algun campo es nulo eliminar del array
        foreach ($listaDatos as $key => $value) {
            if ($value === null) {
                unset($listaDatos[$key]);
            }
        }
    
        $this->cargoObj->setterArray($listaDatos);
    
        // Llamar al método actualizar
        $respuesta = $this->cargoObj->actualizar(false);
    
        $mensaje = "Caso ($num_caso)";
    
        // Verificar el resultado
        $this->assertNotNull($respuesta);
        $this->assertIsArray($respuesta);
    
        $this->assertArrayHasKey('success', $respuesta);
        
        $mensaje = ($respuesta['consoleError'] ?? $respuesta['message']) . ' :: '.$mensaje;
    
        $this->assertEquals($resultado_esperado, $respuesta['success'], $mensaje);
        $this->assertEquals($mensajeEsperado, $respuesta['message'], $mensaje);
    }
    
public function ActualizarProvider(): array
{
    global $mensajes;
    
    $mensajeEsperado = [
        "actualizacion exitosa" => $mensajes->actualizacionExitosa,
        "nombre requerido" => $mensajes->nombreRequerido,
        "nivel requerido" => $mensajes->nivelRequerido,
        "cargo existente" => $mensajes->cargoExistente,
        "cargo no existente" => $mensajes->cargoNoExistente,
    ];
    
    $registroValido = [
        "id" => 1,
        "nombre" => "Nuevo nombre del cargo",
        "nivel" => 1
    ];
    
    return [
        "Actualizacion valida" => [
            "id" => $registroValido['id'],
            "nombre" => $registroValido['nombre'],
            "nivel" => $registroValido['nivel'],
            "mensaje esperado" => $mensajeEsperado['actualizacion exitosa'],
            "resultado esperado" => true,
            "num_caso" => 1,
        ],
        "Nombre vacio" => [
            "id" => $registroValido['id'],
            "nombre" => '',
            "nivel" => $registroValido['nivel'],
            "mensaje esperado" => $mensajeEsperado['nombre requerido'],
            "resultado esperado" => false,
            "num_caso" => 2,
        ],
        "Nivel nulo" => [
            "id" => $registroValido['id'],
            "nombre" => $registroValido['nombre'],
            "nivel" => null,
            "mensaje esperado" => $mensajeEsperado['nivel requerido'],
            "resultado esperado" => false,
            "num_caso" => 3,
        ],
        "Cargo existente" => [
            "id" => $registroValido['id'],
            "nombre" => "Vigilante",
            "nivel" => $registroValido['nivel'],
            "mensaje esperado" => $mensajeEsperado['cargo existente'],
            "resultado esperado" => false,
            "num_caso" => 4,
        ],
        "Cargo no existente" => [
            "id" => 999,
            "nombre" => $registroValido['nombre'],
            "nivel" => $registroValido['nivel'],
            "mensaje esperado" => $mensajeEsperado['cargo no existente'],
            "resultado esperado" => false,
            "num_caso" => 5,
        ],
    ];
}
    /**
     * @dataProvider EliminarProvider
     */
    public function testEliminarCargo($id, $mensajeEsperado, $resultado_esperado, $num_caso)
    {
        $_logger = (new LoggerPhpUnit($this, $this->testSuiteControl, "Eliminar Cargo"))->log();
        $this->cargoObj->setterArray(['id' => $id]);
    
        $respuesta = $this->cargoObj->eliminarCargo(false);
    
        $mensaje = "Caso ($num_caso)";
    
        $this->assertNotNull($respuesta);
        $this->assertIsArray($respuesta);
    
        $this->assertArrayHasKey('success', $respuesta);
        
        $mensaje = ($respuesta['consoleError'] ?? $respuesta['message']) . ' :: '.$mensaje;
    
        $this->assertEquals($resultado_esperado, $respuesta['success'], $mensaje);
        $this->assertEquals($mensajeEsperado, $respuesta['message'], $mensaje);
    }
    
    public function EliminarProvider(): array
{
    global $mensajes;
    
    $mensajeEsperado = [
        "eliminacion exitosa" => $mensajes->eliminacionExitosa,
        "cargo no existente" => $mensajes->cargoNoExistente,
        "cargo con relaciones" => $mensajes->cargoRelacionesEliminar,
    ];
    
    return [
        "Eliminacion valida" => [
            "id" => 35,
            "mensaje esperado" => $mensajeEsperado['eliminacion exitosa'],
            "resultado esperado" => true,
            "num_caso" => 1,
        ],
        "Cargo no existente" => [
            "id" => 999,
            "mensaje esperado" => $mensajeEsperado['cargo no existente'],
            "resultado esperado" => false,
            "num_caso" => 2,
        ],
        "Cargo con relaciones" => [
            "id" => 1,
            "mensaje esperado" => $mensajeEsperado['cargo con relaciones'],
            "resultado esperado" => false,
            "num_caso" => 3,
        ],
    ];
}




}


$mensajes = new class {
    public $registroExitoso = "Cargo registrado con exito";
    public $actualizacionExitosa = "Cargo actualizado con exito";
    public $eliminacionExitosa = "Cargo eliminado con exito";
    public $nombreRequerido = "El nombre del cargo es requerido";
    public $nivelRequerido = "El nivel del cargo es requerido";
    public $nombreInvalido = "El nombre del cargo es invalido";
    public $nivelInvalido = "El nivel del cargo es invalido";
    public $cargoExistente = "El cargo ya existe";
    public $cargoNoExistente = "El cargo no existe";
    public $cargoRequerido = "El cargo no esta seleccionado";
    public $cargoInvalido = "El cargo es invalido";
    public $cargoRelacionesEliminar = "El cargo esta siendo utilizado y no puede ser eliminado";

};

?>