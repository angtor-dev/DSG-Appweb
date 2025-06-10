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

    protected function setUp(): void
    {
        $this->cargoObj = new Cargo;
        $this->cargoObj->setTestingMode(true);
    }

    /**
     * @dataProvider RegistrosProvider
     */
    public function testRegistrar($nombre,$nivel,$mensajeEsperado, $resultado_esperado, $num_caso)
    {
        
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

    public function RegistrosProvider()
    {
        global $mensajes;
        return [
            // casos de prueba
            [ "Gerente", 1,$mensajes->registroExitoso , true, "1" ], // valido
            [ "", 1,$mensajes->nombreRequerido , false, "2" ], // nombre requerido
            [ "Gerente", null ,$mensajes->nivelRequerido , false, "3" ], // nivel requerido
            [ "Vigilante", 1,$mensajes->cargoExistente , false, "4" ], // cargo existente

        ];
    }


    /**
     * @dataProvider ActualizarProvider
     */
    public function testActualizar($id, $nombre, $nivel, $mensajeEsperado, $resultado_esperado, $num_caso)
    {
        // Preparar los datos para la prueba
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
    
    public function ActualizarProvider()
    {
        global $mensajes;
        return [
            // casos de prueba
            [ 1, 'Nuevo nombre del cargo', 1, $mensajes->actualizacionExitosa, true, "1" ], // válido
            [ 1, '', 1, $mensajes->nombreRequerido, false, "2" ], // nombre requerido
            [ 1, 'Nuevo nombre del cargo', null, $mensajes->nivelRequerido, false, "3" ], // nivel requerido
            [ 1, 'Vigilante', 1, $mensajes->cargoExistente, false, "4" ], // cargo existente
            [ 999, 'Nuevo nombre del cargo', 1, $mensajes->cargoNoExistente, false, "5" ], // cargo no existente
        ];
    }

    /**
     * @dataProvider EliminarProvider
     */
    public function testEliminar($id, $mensajeEsperado, $resultado_esperado, $num_caso)
    {
        // Preparar los datos para la prueba
        $this->cargoObj->setterArray(['id' => $id]);
    
        // Llamar al método eliminar
        $respuesta = $this->cargoObj->eliminarCargo(false);
    
        $mensaje = "Caso ($num_caso)";
    
        // Verificar el resultado
        $this->assertNotNull($respuesta);
        $this->assertIsArray($respuesta);
    
        $this->assertArrayHasKey('success', $respuesta);
        
        $mensaje = ($respuesta['consoleError'] ?? $respuesta['message']) . ' :: '.$mensaje;
    
        $this->assertEquals($resultado_esperado, $respuesta['success'], $mensaje);
        $this->assertEquals($mensajeEsperado, $respuesta['message'], $mensaje);
    }
    
    public function EliminarProvider()
    {
        global $mensajes;
        return [
            // casos de prueba
            [ 35, $mensajes->eliminacionExitosa, true, "1" ], // válido
            [ 999, $mensajes->cargoNoExistente, false, "2" ], // cargo no existente
            [ 1, $mensajes->cargoRelacionesEliminar, false, "3" ], // cargo con relaciones
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