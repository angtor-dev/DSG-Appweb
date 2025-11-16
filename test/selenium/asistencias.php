<?php 
/**
 * ❌ ✅ 🔎
 * php scriptTest.php areas
 */

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class CustomException extends Exception {}
class AsistenciasSelenium extends LoginSelenium
{
    public $testNombre = "NOMBRE PRUEBA";

    public $datosPruebas = array();
    public $datosPruebasActualizar = array();
    public function __construct(ApiController $testLink){
        parent::__construct($testLink);
        $this->datosPruebas = [
            'nombre' => $this->testNombre,
            'cedula' => "99999999",
            'apellido' => 'PRUEBAAPELIDO',
            'telefono' => '04145555555',
            'cargo' => 'Director',
            'turno' => 'Tarde',
            'division' => 2, // Infraestructura
            'fecha_ingreso' => date('d-m-Y')
        ];// validos
        $this->datosPruebasActualizar = [
            'nombre' => "Nuevo Nombre",
            'cedula' => "99999999",
            'apellido' => 'Nuevo Apellido',
            'telefono' => '04148888888',
            'cargo' => 'Aseador',
            'turno' => 'Noche',
            'division' => 4, // Plomeria
            'fecha_ingreso' => date('d-m-Y')
        ];// validos
    }

    public function testRegistrarAsistencia(){
        $ok = false;
        try {
            //code...
            $this->print("  Registro de Asistencias",7);
            $this->startContador();
            $this->createSteps();
            $this->goTo('Asistencias');
            $this->addSteps('p');
            $this->print("  Accediendo al módulo de asistencias",4);

            $this->fillSelects([
                [
                    'selector' => '#departamento',
                    'value' => 1,
                    'selectBy' => 'value',
                ],
                [
                    'selector' => '#turno',
                    'value' => "Mañana",
                ],

            ]);

            $this->click('#btn-cargar');
            $this->print("  Cargando asistencias/inasistencias",4);
            $this->addSteps('p');

            $this->waitElement('#submit-asistencias');
            $this->print("  Asistencias/inasistencias cargadas",4);

            // marcon una fecha como no laborable
            $firstTr = $this->waitElement('#tabla-asistencias-semanales table tbody tr');

            if($firstTr->getText() == "No se encontraron registros"){
                throw new CustomException("Prueba fallida, no se encontraron registros");
            }

            $this->click('#tabla-asistencias-semanales table thead label.switch');

            $this->print("  Marcando dia como no laborable",7);


            $firstTr->findElement($this->selector(".asistencia-checkbox:not(:disabled)"))->click();

            $this->print("  Marcando inasistencia",7);
            $modal = $this->waitElement('#modalInasistencia');
            
            $this->fillSelect('#justificacion', 1, 'value');// injustificado
            
            $modal->findElement($this->selector('button[type="submit"]'))->click();
            $this->print("  Guardando inasistencia",7);

            $this->scrollTo('#submit-asistencias');
            
            $this->click('#submit-asistencias');
            $this->print("  Guardando Registro",7);
            $this->waitAlert();
            $this->print("  Asistencias guardadas",1);
            
            // $this->addSteps('p');
            $this->endContador();
            $ok = true;
        } catch (\Throwable $th) {
            $this->blockSteps(6);
            $this->print("  Prueba fallida",3);
            echo $th->getMessage()."\n";
        }
        $status = $this->getStatusSteps();
        // $this->testLink->reportTest(
        //     $this->testLink->getTestCaseByNameProp('TCAT07 - Registrar Trabajador')['id'],
        //     $status,
        //     $this->getSteps(),
        //     $this->lastTime
        // );
        return $ok;
    }

    public function testActualizarTrabajador(){
        $ok = false;
        try {
            //code...
            $this->print("  Actualización de trabajadores",7);
            $this->startContador();
            $this->createSteps();
            $this->goTo('Trabajadores');
            $this->addSteps('p');
            $this->print("  Accediendo al módulo de trabajadores",4);


            $this->print("  Buscando trabajador",4);
            $this->fillForm('#dt-search-0', $this->datosPruebasActualizar['cedula']);
            

            $row = $this->findRowInTableByText('#tabla-trabajadores', $this->datosPruebasActualizar['cedula'], 1);

            $botonActualizar = $row->findElement($this->selector('.accion.pointer[data-bs-title="Editar"]'));// se obtiene el boton de actualizar

            $this->print("  Trabajador encontrado",1);

            $botonActualizar->click();
            $this->addSteps('p');





            
            
            $this->fillForm('#cedula', $this->datosPruebasActualizar['cedula']);

            $this->driver->wait(10, 500)->until(// espera que el campo se desabilite
                function () {
                    $nombre = $this->driver->findElement($this->selector('#nombre'));
                    return $this->driver->executeScript("return arguments[0].disabled?false:true;", array($nombre));
                }
            );

            $this->fillForms([
                [
                    'selector' => '#nombre',
                    'value' => $this->datosPruebasActualizar['nombre'],
                ],
                [
                    'selector' => '#apellido',
                    'value' => $this->datosPruebasActualizar['apellido'],
                ],
                [
                    'selector' => '#telefono',
                    'value' => $this->datosPruebasActualizar['telefono'],
                ],
                [
                    "selector" => '#fecha_ingreso',
                    "value" => date('d-m-Y'),
                ]
            ]);
            
            
            $this->fillSelects([
                [
                    "selector" => '#departamento',
                    "value" => $this->datosPruebasActualizar['division'],
                    "selectBy" => 'value',
                ],
                [
                    "selector" => '#cargo',
                    "value" => $this->datosPruebasActualizar['cargo'],
                ],
                [
                    "selector" => '#turno',
                    "value" => $this->datosPruebasActualizar['turno'],
                ],
            ]);
            
            $this->addSteps('p');
            $this->print("  Campos llenados");
            
            
            
            $this->click("#cedula");// doy click porque el select estara abierto y cubrira el boton de actualizar
            $this->click('#btn-submit-registrar');
            $this->addSteps('p');
            $this->print("  Registrando trabajador");
            $this->waitAlert();
            $this->addSteps('p');
            $this->print("  Trabajador registrado exitosamente");
            $this->endContador();
            $ok = true;
        } catch (\Throwable $th) {
            $this->blockSteps(6);
        }
        $status = $this->getStatusSteps();
        $this->testLink->reportTest(
            "TCAT08 - Actualizar Trabajador",
            $status,
            $this->getSteps(),
            $this->lastTime
        );
        return $ok;
    }
   

    public function testEliminarAsistencia(){
        $ok = false;
        try {
            //code...
            $this->print("  Registro de Asistencias",7);
            $this->startContador();
            $this->createSteps();
            $this->goTo('Asistencias');
            $this->addSteps('p');
            $this->print("  Accediendo al módulo de asistencias",4);

            $this->fillSelects([
                [
                    'selector' => '#departamento',
                    'value' => 1,
                    'selectBy' => 'value',
                ],
                [
                    'selector' => '#turno',
                    'value' => "Mañana",
                ],

            ]);

            $this->click('#btn-cargar');
            $this->print("  Cargando asistencias/inasistencias",4);
            $this->addSteps('p');

            $this->waitElement('#submit-asistencias');
            $this->print("  Asistencias/inasistencias cargadas",4);

            // marcon una fecha como no laborable
            $firstTr = $this->waitElement('#tabla-asistencias-semanales table tbody tr');

            if($firstTr->getText() == "No se encontraron registros"){
                throw new CustomException("Prueba fallida, no se encontraron registros");
            }

            $this->waitElement('#eliminar-asistencias');
            $this->scrollTo('#eliminar-asistencias');
            $this->click('#eliminar-asistencias');
            $button = $this->waitElement('#modal-eliminar .btn-eliminar');
            $button->click();
            $this->print("  Eliminando asistencias",1);

            $this->waitAlert();
            $this->print("  Asistencias guardadas",1);
            
            // $this->addSteps('p');
            $this->endContador();
            $ok = true;
        } catch (\Throwable $th) {
            $this->blockSteps(6);
            $this->print("  Prueba fallida",3);
            echo $th->getMessage()."\n";
        }
        $status = $this->getStatusSteps();
        // $this->testLink->reportTest(
        //     $this->testLink->getTestCaseByNameProp('TCAT07 - Registrar Trabajador')['id'],
        //     $status,
        //     $this->getSteps(),
        //     $this->lastTime
        // );
        return $ok;
    }

    public function testregistrarAsistenciaInvalido($datos, $invalid, $datasetInvalid){
        $ok = false;
        try {
            //code...
            $this->print("  Registro de Asistencias",7);
            $this->startContador();
            $this->createSteps();
            $this->goTo('Asistencias');
            $this->wait(WebDriverExpectedCondition::invisibilityOfElementLocated($this->selector('.loader')));
            $this->driver->executeScript('mostrarLoader("#menu-lateral .user.acordeon");');
            $this->addSteps('p');
            $this->print("  Accediendo al módulo de asistencias",4);
            if($invalid == "departamento"){
                $this->print ("  probando division vacia",4);
                $this->print($datos["turno"]);
                $this->fillSelects([
                    [
                        'selector' => '#departamento',
                        'value' => '',
                        'selectBy' => 'value',
                    ],
                    [
                        'selector' => '#turno',
                        'value' => $datos['turno'],
                        'selectBy' => 'text',
                    ],
                ]);
                $this->click('#btn-cargar');
                $this->waitFormText('#departamento');
            }
            else{
                $this->fillSelect('#departamento', $datos['departamento'], 'value');
            }

            if($invalid == "turno"){
                $this->print ("  probando turno vacio",4);
                $this->fillSelects([
                    [
                        'selector' => '#departamento',
                        'value' => $datos['departamento'],
                        'selectBy' => 'value',
                    ],
                    [
                        'selector' => '#turno',
                        'value' => '',
                        'selectBy' => 'value',
                    ],
                ]);
                $this->click('#btn-cargar');
                $this->waitFormText('#turno');
            }
            else{
                $this->fillSelect('#turno', $datos['turno']);
            }

            if($invalid == "fecha"){

                foreach ($datasetInvalid as $invalidData) {
                    $this->fillForm('#fecha', $invalidData["valor"]);
                    $this->click('#btn-cargar');
                    $this->waitFormText('#fecha');
                }
            }
            else if(!in_array($invalid, ["departamento", "turno", "fecha"])){
                $this->fillForm('#fecha', $datos['fecha']);

                $this->click('#btn-cargar');
                $this->print("  Cargando asistencias/inasistencias",4);
                $this->addSteps('p');
    
                $this->waitElement('#submit-asistencias');
                $this->print("  Asistencias/inasistencias cargadas",4);
    
                // marcon una fecha como no laborable
                $firstTr = $this->waitElement('#tabla-asistencias-semanales table tbody tr');
    
                if($firstTr->getText() == "No se encontraron registros"){
                    throw new CustomException("Prueba fallida, no se encontraron registros");
                }
                $checkbox = $firstTr->findElement($this->selector(".asistencia-checkbox:not(:disabled)"))->click();
                $this->print("  Marcando inasistencia",7);
                $modal = $this->waitElement('#modalInasistencia');


                if($invalid == "justificacion"){
                    $this->print ("  probando justificacion vacia",4);
                    //$this->fillSelect('#justificacion', '', 'value');
                    $modal->findElement($this->selector('button[type="submit"]'))->click();
                    $this->waitFormText('#justificacion');
                    
                }
                else{
                    $this->fillSelect('#justificacion', $datos['justificacion'], 'value');// injustificado
                }
                if($invalid == "observaciones"){
                    $this->print ("  probando observaciones",4);
                    
                    foreach ($datasetInvalid as $invalidData) {
                        $invalidData = reset($invalidData);
                        $this->fillForm('#observacion', $invalidData["valor"]);
                        $modal->findElement($this->selector('button[type="submit"]'))->click();
                        $this->waitFormText('#observacion');
                    }
                    
                }
                else{
                    $this->fillForm('#observacion', $datos['observaciones']);
                }
            }


            $this->print("  Prueba de registro invalida exitosa",1);
            
            // $this->addSteps('p');
            $this->endContador();
            $ok = true;
        } catch (\Throwable $th) {
            $this->blockSteps(6);
            $this->print("  Prueba fallida",3);
            echo $th->getMessage()."\n line ". $th->getLine()."\n file ". $th->getFile()."\n paht ". $th->getTraceAsString()."\n";
        }
        $status = $this->getStatusSteps();
        // $this->testLink->reportTest(
        //     $this->testLink->getTestCaseByNameProp('TCAT07 - Registrar Trabajador')['id'],
        //     $status,
        //     $this->getSteps(),
        //     $this->lastTime
        // );
        return $ok;
    }



    public function testRegistrarTrabajadorInvalido($datos, $invalid, $datasetInvalid){
        $ok = false;
        try {
            //code...
            $this->print("  Registro de trabajadores",7);
            $this->print("  Probando ivalido de ($invalid)",4);
            $this->startContador();
            $this->createSteps();
            $this->goTo('Trabajadores');
            $this->addSteps('p');
            $this->print("  Accediendo al módulo de trabajadores",4);

            $this->click('button[data-bs-target="#modal-generico"][data-bs-url="/DSG-Appweb/Trabajadores/Registrar"]');
            $this->print("  Accediendo al modal de registro de trabajadores",4);
            $this->addSteps('p');
            $caso = '';

            
            if($invalid == 'cedula'){
                $this->print("  probando cedula",4);
                $caso .= "Casos (Cedula):\n ";

                
                foreach ($datasetInvalid as $key => $value) {
                    $value = reset($value);
                    $caso .= "$key valor {$value['valor']}\n\n";
                    $this->triedStepNote($caso);
                    $this->fillForm('#cedula', $value["valor"]);
                    $this->wait(
                        function() {
                            $class = $this->driver->findElement($this->selector('#cedula'))->getAttribute('class');
                            $procesing = strpos($class, 'is-processing');
                            return $procesing === false;
                        }
                    );
                    $this->waitFormText('#cedula');
                }
                $this->triedStepNote(null);
                $this->addSteps('p', $caso);
                $caso = '';
            }
            else{
                $this->fillForm('#cedula', $datos['cedula']);
                $this->driver->wait(10, 500)->until(// espera que el campo se desabilite
                    function () {
                        $nombre = $this->driver->findElement($this->selector('#nombre'));
                        return $this->driver->executeScript("return arguments[0].disabled?false:true;", array($nombre));
                    }
                );

                if($invalid == 'nombre'){
                    $this->print("  probando nombre",4);
                    $caso .= "Casos (Nombre):\n ";
                    foreach ($datasetInvalid as $key => $value) {
                        $value = reset($value);
                        $caso .= "$key valor {$value['valor']}\n\n";
                        $this->triedStepNote($caso);
                        $this->fillForm('#nombre', $value["valor"]);
                        $this->click('#cedula');
                        $this->waitFormText('#nombre');
                    }
                    $this->triedStepNote(null);
                    $this->addSteps('p', $caso);
                    $caso = '';
                }
                else{
                    $this->fillForm('#nombre', $datos['nombre']);
                }

                if($invalid == 'apellido'){
                    $this->print("  probando apellido",4);
                    $caso .= "Casos (Apellido):\n ";
                    foreach ($datasetInvalid as $key => $value) {
                        $value = reset($value);
                        $caso .= "$key valor {$value['valor']}\n\n";
                        $this->triedStepNote($caso);
                        $this->fillForm('#apellido', $value["valor"]);
                        $this->click('#cedula');
                        $this->waitFormText('#apellido');
                    }
                    $this->triedStepNote(null);
                    $this->addSteps('p', $caso);
                    $caso = '';
                }
                else{
                    $this->fillForm('#apellido', $datos['apellido']);
                }

                if($invalid == 'telefono'){
                    $this->print("  probando telefono",4);
                    $caso .= "Casos (Telefono):\n ";
                    foreach ($datasetInvalid as $key => $value) {
                        $value = reset($value);
                        $caso .= "$key valor {$value['valor']}\n\n";
                        $this->triedStepNote($caso);
                        $this->fillForm('#telefono', $value["valor"]);
                        $this->click('#cedula');
                        $this->waitFormText('#telefono');
                    }
                    $this->triedStepNote(null);
                    $this->addSteps('p', $caso);
                    $caso = '';
                }
                else{
                    $this->fillForm('#telefono', $datos['telefono']);
                }

                if($invalid == 'fecha_ingreso'){
                    $this->print("  probando fecha de ingreso",4);
                    $caso .= "Casos (Fecha de Ingreso):\n ";
                    foreach ($datasetInvalid as $key => $value) {
                        $caso .= "$key valor {$value['valor']}\n\n";
                        $this->triedStepNote($caso);
                        $this->fillForm('#fecha_ingreso', $value["valor"]);
                        $this->click('#cedula');
                        $this->waitFormText('#fecha_ingreso');
                    }
                    $this->triedStepNote(null);
                    $this->addSteps('p', $caso);
                    $caso = '';
                }
                else{
                    $this->fillForm('#fecha_ingreso', $datos['fecha_ingreso']);
                }

                if(in_array($invalid, ["division", 'turno', 'cargo'])) {

                    if($invalid != 'division'){
                        $this->fillSelect('#departamento', $datos['division'], 'value');
                    }
                    if($invalid != 'turno'){
                        $this->fillSelect('#turno', $datos['turno']);
                    }
                    if($invalid != 'cargo'){
                        $this->fillSelect('#cargo', $datos['cargo']);
                    }

                    $this->addSteps('p');
                    $this->click('#btn-submit-registrar');

                    if($invalid == 'division'){
                        $this->print("  probando division",4);
                        $this->triedStepNote("Caso (División): sin seleccionar");
                        $this->waitFormText('#departamento');
                    }
                    if($invalid == 'turno'){
                        $this->print("  probando turno",4);
                        $this->triedStepNote("Caso (Turno): sin seleccionar");
                        $this->waitFormText('#turno');
                    }
                    if($invalid == 'cargo'){
                        $this->print("  probando cargo",4);
                        $this->triedStepNote("Caso (Cargo): sin seleccionar");
                        $this->waitFormText('#cargo');
                    }
                    $this->addSteps('p');
                }
                else{
                    $this->fillSelect('#departamento', $datos['division'], 'value');
                    $this->fillSelect('#turno', $datos['turno']);
                    $this->fillSelect('#cargo', $datos['cargo']);

                    $this->click('#btn-submit-registrar');
                    try {// si no aparece el loader es porque la solicitud no fue enviada
                        $this->waitElement(".loader.loader-body",2);
                        throw new CustomException("Fallo la prueba de caso invalido '{$invalid}'\nLa solicitud fue enviada ", 1);
                    }
                    catch(\CustomException $e){
                        throw $e;// si aparece el loader es porque la solicitud fue enviada
                    }
                     catch (\Throwable $th) {
                        $this->addSteps("p");
                    }
                }
                
            }

           
            $this->endContador();
            $ok = true;
        } catch (\Throwable $th) {
            $this->blockSteps(4);
            $this->print( "Error en el test: {$th->getMessage()}", 3);
        }
        $status = $this->getStatusSteps();
        echo json_encode($this->getSteps(),JSON_PRETTY_PRINT);



        $this->testLink->reportTest(
            'TCAT10 - Registrar Trabajador invalido',
            $status,
            $this->getSteps(),
            $this->lastTime
        );
        return $ok;
    }


    public function testAsistencias(){
        $this->openSystemDSG();
        $this->print("  Empezando pruebas de trabajadores",5);
        $estructura = [
            "valor" => "",
        ];
        $datos = [
            "departamento" => 1,
            "turno" => "Mañana",
            "fecha" => date('d-m-Y'),
            "justificacion" => 1,
            "observaciones" => "observaciones de prueba",
        ];
        $ivalidCases = [
            "departamento" => [],
            "turno" => [],
            "fecha" => [
                "Numero 1 demasiado largo" => [
                    "valor" => str_repeat('1', 100),
                ],
                "Fecha futuro" => [
                    "valor" => date('d-m-Y', strtotime('+1 day')),
                ]
            ],
            "justificacion" => [],
            "observaciones" => (new Diccionario())->generateArrayFromDic(
                $estructura,
                "valor",
                "/^[A-Za-z0-9ÑñÁáÉéÍíÓóÚúÜü\s,.-]{0,255}$/",
                false,
                'nombres',
                'Entrada Observaciones Invalido'
            ),
        ];
        foreach ($ivalidCases as $invalid => $datasetInvalid) {
            $resp = $this->testregistrarAsistenciaInvalido($datos, $invalid, $datasetInvalid);
            if($resp == false){
                break;
            }
        }
        $this->testRegistrarAsistencia();
        $this->testEliminarAsistencia();
   
        $this->closeBrowser();
    }
    
}

?>