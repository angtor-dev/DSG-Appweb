<?php 
/**
 * ❌ ✅ 🔎
 * php scriptTest.php areas
 */

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class CustomException extends Exception {}
class TrabajadorSelenium extends LoginSelenium
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

    public function testRegistrarTrabajador(){
        $ok = false;
        try {
            //code...
            $this->print("  Registro de trabajadores",7);
            $this->startContador();
            $this->createSteps();
            $this->goTo('Trabajadores');
            $this->addSteps('p');
            $this->print("  Accediendo al módulo de trabajadores",4);

            $this->click('button[data-bs-target="#modal-generico"][data-bs-url="/DSG-Appweb/Trabajadores/Registrar"]');
            $this->print("  Accediendo al modal de registro de trabajadores",4);
            $this->addSteps('p');
            
            $this->fillForm('#cedula', $this->datosPruebas['cedula']);

            $this->driver->wait(10, 500)->until(// espera que el campo se desabilite
                function () {
                    $nombre = $this->driver->findElement($this->selector('#nombre'));
                    return $this->driver->executeScript("return arguments[0].disabled?false:true;", array($nombre));
                }
            );

            $this->fillForms([
                [
                    'selector' => '#nombre',
                    'value' => $this->datosPruebas['nombre'],
                ],
                [
                    'selector' => '#apellido',
                    'value' => $this->datosPruebas['apellido'],
                ],
                [
                    'selector' => '#telefono',
                    'value' => $this->datosPruebas['telefono'],
                ],
                [
                    "selector" => '#fecha_ingreso',
                    "value" => date('d-m-Y'),
                ]
            ]);
            
            $this->addSteps('p');
            
            $this->fillSelects([
                [
                    "selector" => '#departamento',
                    "value" => $this->datosPruebas['division'],
                    "selectBy" => 'value',
                ],
                [
                    "selector" => '#cargo',
                    "value" => $this->datosPruebas['cargo'],
                ],
                [
                    "selector" => '#turno',
                    "value" => $this->datosPruebas['turno'],
                ],
            ]);

            
            $this->addSteps('p');
            $this->print("  Campos llenados");
            
            
            
            $this->click('#btn-submit-registrar');
            $this->addSteps('p');
            $this->print("  Registrando trabajador");
            $this->waitAlert();
            $this->addSteps('p');
            $this->print("  Trabajador registrado exitosamente");
            // $this->addSteps('p');
            
            // $this->addSteps('p');
            $this->endContador();
            $ok = true;
        } catch (\Throwable $th) {
            $this->blockSteps(6);
        }
        $status = $this->getStatusSteps();
        $this->testLink->reportTest(
            $this->testLink->getTestCaseByNameProp('TCAT07 - Registrar Trabajador')['id'],
            $status,
            $this->getSteps(),
            $this->lastTime
        );
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
            
            $this->addSteps('p');
            
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
        // $this->testLink->reportTest(
        //     "TCAT08 - Actualizar Trabajador",
        //     $status,
        //     $this->getSteps(),
        //     $this->lastTime
        // );
        return $ok;
    }
   

    public function testEliminarTrabajador(){
        $ok = false;
        try {
            $this->createSteps();
            $this->startContador();
            $this->print("  Eliminando trabajador",7);
            $this->goTo('Trabajadores');
            $this->addSteps('p');
            $this->print("  Accediendo al módulo de trabajadores",4);

            sleep(1);
            $this->print("  Buscando trabajador",4);
            $this->fillForm('#dt-search-0', $this->datosPruebas['cedula']);

            $row = $this->findRowInTableByText('#tabla-trabajadores', $this->datosPruebas['cedula'], 1);

            $botonEliminar = $row->findElement($this->selector('.accion-eliminar'));

            $this->print("  Trabajador encontrado",1);

            $botonEliminar->click();
            $this->addSteps('p');

            $this->click(WebDriverBy::xpath('//div[@id="modal-eliminar"]//button[@class="btn btn-danger flex-grow-1 btn-eliminar"]'));
            $this->print("  Eliminando trabajador",1);

            $this->addSteps('p');


            $this->waitAlert();
            $this->addSteps('p');
            $this->endContador();
            $this->print("  Trabajador eliminado exitosamente",1);

            $ok = true;
            
        } catch (\Throwable $th) {
            echo "❌ Error al eliminar el area :: {$th->getMessage()} :: linea {$th->getLine()} :: file {$th->getFile()}\n" ;
            echo $th->getTraceAsString();
            $this->blockSteps(4);
        }
        $status = $this->getStatusSteps();
        $this->testLink->reportTest(
            "TCAT09 - Eliminar Trabajador",
            $status,
            $this->getSteps(),
            $this->lastTime
        );
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
        // $status = $this->getStatusSteps();
        // $this->testLink->reportTest(
        //     $this->testLink->getTestCaseByNameProp('TCAT07 - Registrar Trabajador')['id'],
        //     $status,
        //     $this->getSteps(),
        //     $this->lastTime
        // );
        return $ok;
    }




    public function testActualizarAreaInvalid($datos){
        try {
            //code...
            $this->goTo('Areas');
            $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::invisibilityOfElementLocated($this->selector('#modal-generico')));
            $this->startContador();
            $this->createSteps();


            $nodeContent = $this->testNombre;
            $xpathSelector = "//span[@class='node-name' and text()='{$nodeContent}']/ancestor::button[1]/parent::*";
            $nodeAtion = $this->driver->findElement(WebDriverBy::xpath($xpathSelector));
            $nodeAtion = $nodeAtion->findElement($this->selector('div.node-actions'));
            if(!$nodeAtion){
                throw new Exception("No se encontro el area");
            }
            
            $buttonActualizar = $nodeAtion->findElement($this->selector('div[data-bs-title="Editar"] div[data-bs-toggle="modal"]'));
            if(!$buttonActualizar){
                throw new Exception("No se encontro el boton de actualizar");
            }
            
            $this->scrollTo(WebDriverBy::xpath($xpathSelector."//div[@class='node-actions']"));
            $buttonActualizar->click();
            $this->addSteps('p');  //Hacer click en el botón de actualizar 


            $this->waitElement('#nombre');
            
            $this->fillForm('#nombre', $datos['nombre']);
            $this->addSteps('p'); // El usuario ingresa el nombres inválidos
            
            $this->click('div.modal button[type="submit"]');
            
            if($datos["caso"] == 1){
                $this->waitFormText('#nombre');
                $this->addSteps('p', "caso invalido entrada (".$datos['nombre']." -- ".$datos['esenario'].")");
                $this->print("feedback correcto por el campo", 1);
            }
            else {// repetido
                $this->waitAlert('', 'danger');
                $this->addSteps('p', "caso repetido (".$datos['nombre']." -- ".$datos['esenario'].")");
                $this->print("feedback correcto por alerta", 1);
            }
            
            $this->endContador();
            echo "\n";
        } catch (\Throwable $th) {
            $this->blockSteps(3);
            $this->EliminarArea($datos['nombre']);
        }
        $status = $this->getStatusSteps();
        $this->testLink->reportTest(
            $this->testLink->getTestCaseByNameProp('TCAA05 - Actualizar Área Invalido')['id'],
            $status,
            $this->getSteps(),
            $this->lastTime
        );
    }

    


    public function EliminarArea($nombre){
        try {
            $this->goTo('Areas');
            $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::invisibilityOfElementLocated($this->selector('#modal-generico')));
            $nodeContent = $nombre;
            $xpathSelector = "//span[@class='node-name' and text()='{$nodeContent}']/ancestor::button[1]/parent::*";
            $this->waitElement(WebDriverBy::xpath($xpathSelector));
            $nodeAtion = $this->driver->findElement(WebDriverBy::xpath($xpathSelector));
            $nodeAtion = $nodeAtion->findElement($this->selector('div.node-actions'));
            if(!$nodeAtion){
                throw new Exception("No se encontro el area");
            }

            $this->scrollTo(WebDriverBy::xpath($xpathSelector."//div[@class='node-actions']"));
    
            $buttonEliminar = $nodeAtion->findElement($this->selector('div[data-bs-title="Eliminar"] div[data-bs-toggle="modal"]'));
            if(!$buttonEliminar){
                throw new Exception("No se encontro el boton de actualizar");
            }
            $buttonEliminar->click();

            $this->waitElement(WebDriverBy::xpath('//div[@id="modal-eliminar"]//b[@class="nombre" and text()="'.$nombre.'"]'));
            $this->click('#modal-eliminar a.btn.btn-danger.eliminar');

            $this->wait(
                WebDriverExpectedCondition::invisibilityOfElementLocated(
                    WebDriverBy::xpath($xpathSelector)
                )
                );
            $this->waitAlert();
            //$this->fillForm('#nombre', $this->testNombre);
            echo "✅ Area eliminada - force\n";
        } catch (\Throwable $th) {
            echo "❌ Error al eliminar el area - force :: {$th->getMessage()} :: linea {$th->getLine()} :: file {$th->getFile()}\n" ;
            echo $th->getTraceAsString();
        }
    }



    public function testTrabajador(){
        $this->openSystemDSG();
        $this->print("  Empezando pruebas de trabajadores",5);
        // $casos =[
        //     ['caso' => 1, 'nombre' => '', 'esenario' => 'Campo vacio'],// vacio
        //     ['caso' => 2, 'nombre' => 'Hilandera', 'esenario' => 'Campo repetido'],// repetido
        //     ['caso' => 1, 'nombre' => str_repeat('H', 300), 'esenario' => 'Campo demasiado largo'],// demasiado largo
        //     ['caso' => 1, 'nombre' => "<script>alert('XSS')</script>", 'esenario' => 'XSS'],// demasiado corto
        // ];


        // foreach ($casos as $caso) {
        //     $this->testRegistrarAreaInvalid($caso);
        // }
        // $this->print("Prueba de registro invalida completada");
        $estructura = [
            "valor" => "123456789",
        ];
        $dic = new Diccionario();
        $invalidCases = [
            [
                "invalid" => "cedula",
                "values" => $dic->generateArrayFromDic($estructura, "valor", "/^[0-9]{7,8}$/", false, 'nombres', 'Cedula', ["Numeros Largo"]),
                
            ],
            [
                "invalid" => "nombre",
                "values" => $dic->generateArrayFromDic($estructura, "valor", "/^[A-Za-zá-úÁ-ÚñÑ0-9., ]{1,50}$/", false, 'nombres', 'nombre'),
                
            ],
            [
                "invalid" => "apellido",
                "values" => $dic->generateArrayFromDic($estructura, "valor", "/^[A-Za-zá-úÁ-ÚñÑ0-9., ]{1,50}$/", false, 'nombres', 'apellido'),
                
            ],
            [
                "invalid" => "telefono",
                "values" => $dic->generateArrayFromDic($estructura, "valor", "/^[0-9]{7,8}$/", false, 'nombres', 'Telefono', ["Numeros Largo"]),
                
            ],
            [
                "invalid" => "fecha_ingreso",
                "values" => [
                    "fecha Futuro" => [ "valor" => date('d-m-Y', time() + 60*60*24*2) ],
                ]
            ],
            [
                "invalid" => "division",
                "values" => []
            ],
            [
                "invalid" => "cargo",
                "values" => []
            ],
            [
                "invalid" => "turno",
                "values" => []
            ]
        ];
        echo "\n===================registro de trabajador invalido====================================\n";

        foreach ($invalidCases as $case) {
            $this->testRegistrarTrabajadorInvalido(
                $this->datosPruebas,
                $case["invalid"],
                $case["values"]
            );
        }

        


        echo "\n===================registro de trabajador====================================\n";
        if($this->testRegistrarTrabajador()){
            $this->print("Prueba de registro completada");
            echo "\n===================actualizacion de trabajador====================================\n";
            $this->testActualizarTrabajador();
            echo "\n===================eliminacion de trabajador====================================\n";
            $this->testEliminarTrabajador();
        }
        else{
            // si no se pudo registrar el trabajador no se puede eliminar asi que la prueba se bloquea
            $this->print( "Pruebas bloqueadas, no se pudo registrar el trabajador", 6 );
            $note = "No se pudo registrar el trabajador";
            $this->testLink->reportTestStatusOnly(
                "TCAT09 - Eliminar Trabajador",
                "b",
                notes: $note
            );
            $this->testLink->reportTestStatusOnly(
                "TCAT08 - Actualizar Trabajador",
                "b",
                notes: $note
            );
        }


        
        // $this->print("Prueba de registro completada");
        // $this->testActualizarArea();
        // $this->print("Prueba de actualizacion completada");

        // foreach ($casos as $caso) {
        //     $this->testActualizarAreaInvalid($caso);
        // }
        // $this->print("Prueba de actualizacion invalida completada");
        
        // $this->testEliminarArea();
        // $this->print("Prueba de eliminacion completada");
        
        
        $this->print("  Terminando pruebas de trabajadores",8);
        $this->driver->executeScript("mostrarExito('Pruebas de trabajadores completadas');");
        $this->driver->executeScript("mostrarExito('l');");
        $this->driver->executeScript("mostrarExito('l');");
        sleep(4);
        $this->closeBrowser();
    }



    
}

?>