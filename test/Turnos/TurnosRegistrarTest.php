<?php
use PHPUnit\Framework\TestCase;

class TurnosRegistrarTest extends TestCase
{
    private $turnoObj;
    

    protected function setUp(): void
    {
        $this->turnoObj = new Turno;
        $this->turnoObj->setTestingMode(true);
        
    }

    /**
     * @dataProvider RegistrosProvider
     */
    public function testRegistrar(
        $turnoNombre,
        $horario_entrada,
        $horario_salida,
        $lunes,
        $martes,
        $miercoles,
        $jueves,
        $viernes,
        $sabado,
        $domingo,
        $resultado_esperado,
        $num_caso
        ):void
    {

        $listaDatos = [
            'nombre' => $turnoNombre,
            'horario_entrada' => $horario_entrada,
            'horario_salida' => $horario_salida,
            'lunes' => $lunes,
            'martes' => $martes,
            'miercoles' => $miercoles,
            'jueves' => $jueves,
            'viernes' => $viernes,
            'sabado' => $sabado,
            'domingo' => $domingo
        ];

        // si algun campo es nulo eliminar del array
        foreach ($listaDatos as $key => $value) {
            if ($value === null) {
                unset($listaDatos[$key]);
            }
        }

        $this->turnoObj->setterArray($listaDatos);

        $respuesta = $this->turnoObj->registrar(false);

        $mensaje = "caso ($num_caso)";

        $this->assertNotNull($respuesta);
        $this->assertIsArray($respuesta);

        $this->assertArrayHasKey('success', $respuesta);
        
        $mensaje = ($respuesta['consoleError'] ?? $respuesta['message']) . ' :: '.$mensaje;

        $this->assertEquals($resultado_esperado, $respuesta['success'], $mensaje);
    }

    public function RegistrosProvider()
    {
        return [
            // casos de prueba
            ['turno1', '08:00:00', '18:00:00', 1, 1, 1, 1, 1, 1, 1, true, 1 ],// valido 
            ['turno1', '08:00:00', '18:00:00', "1", "0", "1", "0", "1", "0", "1", true, 2 ],// valido 
            ['', '08:00:00', '18:00:00', 1, 1, 1, 1, 1, 1, 1, false, 3 ],// nombre invalido
            ['turno1', '', '18:00:00', 1, 1, 1, 1, 1, 1, 1, false, 4 ],// hora entrada invalida
            ['turno1', '08:00:00', '', 1, 1, 1, 1, 1, 1, 1, false, 5 ],// hora salida invalida

            ['turno1', '08:00:00', '18:00:00', null, null, null, null, null, null,null , false, 6 ],// dias invalido
            ['turno1', '08:00:00', '18:00:00', 0, 0, 0, 0, 0, 0, 0, false, 7 ],// dias invalido
            ['turno1', '08:00:00', '18:00:00', 1, 1, 1, 1, 1, 1, 1, true, 8 ],// dias valido

        ];
    }


        /**
         * @dataProvider EliminarTurnoProvider
         */
    public function testEliminarTurno(
        $id,
        $mensajeEsperado,
        $resultado_esperado,
        $num_caso
    ): void
    {
        // Preparar los datos para la prueba
        $this->turnoObj->setterArray(['id' => $id]);

        // Llamar al método eliminarTurno
        $respuesta = $this->turnoObj->eliminarTurno(false);
        
        // Mensaje para el caso de prueba
        $mensaje = "Caso ($num_caso)";
        // Verificar el resultado
        $this->assertNotNull($respuesta);
        $this->assertIsArray($respuesta);
        $this->assertArrayHasKey('success', $respuesta);

        $mensaje = ($respuesta['consoleError'] ?? $respuesta['message']) . ' :: '.$mensaje;
        $this->assertEquals($resultado_esperado, $respuesta['success'], $mensaje);
        $this->assertEquals($mensajeEsperado, $respuesta['message'], $mensaje);

    }

    public function EliminarTurnoProvider()
    {
        $mensajeEsperado = new class {
            public $eliminarTurno = "Turno eliminado con exito";
            public $registrarTurno = "Turno registrado con exito";
            public $actualizarTurno = "Turno actualizado con exito";
            public $nombreRequerido = "El nombre del turno es requerido";
            public $horarioEntradaRequerido = "El horario de entrada es requerido";
            public $horarioSalidaRequerido = "El horario de salida es requerido";
            public $diasRequeridos = "Debe seleccionar al menos un día de la semana";
            public $lunesRequerido = "El día de lunes es requerido";
            public $martesRequerido = "El día de martes es requerido";
            public $miercolesRequerido = "El día de miércoles es requerido";
            public $juevesRequerido = "El día de jueves es requerido";
            public $viernesRequerido = "El día de viernes es requerido";
            public $sabadoRequerido = "El día de sábado es requerido";
            public $domingoRequerido = "El día de domingo es requerido";
            public $horarioEntradaInvalido = "El horario de entrada es invalido";
            public $horarioSalidaInvalido = "El horario de salida es invalido";
            public $horarioEntradaMayorSalida = "El horario de entrada es mayor al horario de salida";
            public $nombreInvalido = "El nombre del turno es invalido";
            public $turnoExistente = "El turno ya existe";
            public $turnoNoExistente = "El turno no existe";
            public $turnoRequerido = "El turno no esta seleccionado";
            public $turnoInvalido = "El turno es invalido";
            public $lunesInvalido = "El día de lunes es invalido";
            public $martesInvalido = "El día de martes es invalido";
            public $miercolesInvalido = "El día de miércoles es invalido";
            public $juevesInvalido = "El día de jueves es invalido";
            public $viernesInvalido = "El día de viernes es invalido";
            public $sabadoInvalido = "El día de sábado es invalido";
            public $domingoInvalido = "El día de domingo es invalido";
            public $turnoRelacionesEliminar = "El turno esta siendo utilizado y no puede ser eliminado";

        };

        return [
            // Casos de prueba
            [1, $mensajeEsperado->turnoRelacionesEliminar, false, 1], // Eliminar turno existente
            [15, $mensajeEsperado->eliminarTurno, true, 2], // Eliminar turno existente
            [999, $mensajeEsperado->turnoNoExistente, false, 3], // Eliminar turno inexistente
            ["queso", $mensajeEsperado->turnoRequerido, false, 4], // Eliminar turno con ID nulo
        ];
    }


    /**
     * @dataProvider ActualizarTurnoProvider
     */
    public function testActualizarTurno(
        $id,
        $nombre,
        $horario_entrada,
        $horario_salida,
        $lunes,
        $martes,
        $miercoles,
        $jueves,
        $viernes,
        $sabado,
        $domingo,
        $mensajeEsperado,
        $resultado_esperado,
        $num_caso
    ): void
    {
        // Preparar los datos para la prueba
        $listaDatos = [
            'id' => $id,
            'nombre' => $nombre,
            'horario_entrada' => $horario_entrada,
            'horario_salida' => $horario_salida,
            'lunes' => $lunes,
            'martes' => $martes,
            'miercoles' => $miercoles,
            'jueves' => $jueves,
            'viernes' => $viernes,
            'sabado' => $sabado,
            'domingo' => $domingo
        ];
    
        // si algun campo es nulo eliminar del array
        foreach ($listaDatos as $key => $value) {
            if ($value === null) {
                unset($listaDatos[$key]);
            }
        }
    
        $this->turnoObj->setterArray($listaDatos);
    
        // Llamar al método actualizarTurno
        $respuesta = $this->turnoObj->actualizar(false);
    
        $mensaje = "Caso ($num_caso)";
    
        // Verificar el resultado
        $this->assertNotNull($respuesta);
        $this->assertIsArray($respuesta);
        $this->assertArrayHasKey('success', $respuesta);

    
        $mensaje = ($respuesta['consoleError'] ?? $respuesta['message']) . ' :: '.$mensaje;
        $this->assertEquals($resultado_esperado, $respuesta['success'], $mensaje);
    }
    
    public function ActualizarTurnoProvider()
    {
        $mensajeEsperado = new class {
            public $eliminarTurno = "Turno eliminado con exito";
            public $registrarTurno = "Turno registrado con exito";
            public $actualizarTurno = "Turno actualizado con exito";
            public $nombreRequerido = "El nombre del turno es requerido";
            public $horarioEntradaRequerido = "El horario de entrada es requerido";
            public $horarioSalidaRequerido = "El horario de salida es requerido";
            public $diasRequeridos = "Debe seleccionar al menos un día de la semana";
            public $lunesRequerido = "El día de lunes es requerido";
            public $martesRequerido = "El día de martes es requerido";
            public $miercolesRequerido = "El día de miércoles es requerido";
            public $juevesRequerido = "El día de jueves es requerido";
            public $viernesRequerido = "El día de viernes es requerido";
            public $sabadoRequerido = "El día de sábado es requerido";
            public $domingoRequerido = "El día de domingo es requerido";
            public $horarioEntradaInvalido = "El horario de entrada es invalido";
            public $horarioSalidaInvalido = "El horario de salida es invalido";
            public $horarioEntradaMayorSalida = "El horario de entrada es mayor al horario de salida";
            public $nombreInvalido = "El nombre del turno es invalido";
            public $turnoExistente = "El turno ya existe";
            public $turnoNoExistente = "El turno no existe";
            public $turnoRequerido = "El turno no esta seleccionado";
            public $turnoInvalido = "El turno es invalido";
            public $lunesInvalido = "El día de lunes es invalido";
            public $martesInvalido = "El día de martes es invalido";
            public $miercolesInvalido = "El día de miércoles es invalido";
            public $juevesInvalido = "El día de jueves es invalido";
            public $viernesInvalido = "El día de viernes es invalido";
            public $sabadoInvalido = "El día de sábado es invalido";
            public $domingoInvalido = "El día de domingo es invalido";
            public $turnoRelacionesEliminar = "El turno esta siendo utilizado y no puede ser eliminado";

        };


        return [
            // casos de prueba
            [ 1, 'Nuevo nombre del turno', '08:00', '17:00', 1, 0, 1, 0, 1, 0, 0, $mensajeEsperado->actualizarTurno, true, 1], // valido
            [ 2, 'Mañana', '08:00', '17:00', 1, 0, 1, 0, 1, 0, 0, $mensajeEsperado->turnoExistente, false, 2], // duplicado
            [ 1, 'Mañana', '08:00', '17:00', 0, 0, 0, 0, 0, 0, 0, $mensajeEsperado->diasRequeridos, false, 3], // sin dias

            [ 1, 'turno1', '', '18:00:00', 1, 1, 1, 1, 1, 1, 1,$mensajeEsperado->horarioEntradaRequerido, false, 4 ],// hora entrada invalida
            [ 1, 'turno1', '08:00:00', '', 1, 1, 1, 1, 1, 1, 1,$mensajeEsperado->horarioSalidaRequerido, false, 5 ],// hora salida invalida
            [ 1, 'turno1', '08:00:00', '18:00:00', null, null, null, null, null, null,null ,$mensajeEsperado->diasRequeridos, false, 6 ],// dias invalido
        ];
    }


}