<?php 
/**
 * ❌ ✅ 🔎
 * php scriptTest.php areas
 */

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
class AreasSelenium extends LoginSelenium
{
    public $testNombre = "AREASDEPRUEBA";
    public function __construct(ApiController $testLink){
        parent::__construct($testLink);
    }

    public function testRegistrarArea(){
        try {
            //code...
            $this->startContador();
            $this->createSteps();
            $this->goTo('Areas');
            $this->addSteps('p');

            $this->click('button[data-bs-target="#modal-generico"][data-bs-url="/DSG-Appweb/Areas/Registrar"]');
            $this->addSteps('p');
            
            $this->fillForm('#nombre', $this->testNombre);
            $this->addSteps('p');
            
            $this->click('div.modal button[type="submit"]');
            $this->addSteps('p');
            
            $this->waitAlert();
            $this->addSteps('p');
            $this->endContador();
        } catch (\Throwable $th) {
            $this->blockSteps(5);
        }
        $status = $this->getStatusSteps();
        $this->testLink->reportTest(
            $this->testLink->getTestCaseByNameProp('TCAA01 - Registrar Área')['id'],
            $status,
            $this->getSteps(),
            $this->lastTime
        );
    }
    public function testActualizarArea(){
        try {
            $this->createSteps();
            $this->startContador();
            $this->goTo('Areas');
            $this->waitElement('button[data-bs-target="#modal-generico"][data-bs-url="/DSG-Appweb/Areas/Registrar"]');
            $this->addSteps('p');
            // busca el span que contiene la clase node-name y el texto del nodo
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
            $this->addSteps('p');
            $buttonActualizar->click();
            $this->waitElement('#nombre');
            $this->fillForm('#nombre', $this->testNombre);
            $this->addSteps('p');
            $this->click('div.modal button[type="submit"]');
            $this->addSteps('p');

            $this->waitAlert();
            $this->addSteps('p');
            $this->endContador();
    
    
            //$this->fillForm('#nombre', $this->testNombre);
            echo "✅ Area actualizada\n";
        } catch (\Throwable $th) {
            echo "❌ Error al actualizar el area :: {$th->getMessage()}\n" ;
            $this->blockSteps(5);
        }
        $status = $this->getStatusSteps();
        $this->testLink->reportTest(
            $this->testLink->getTestCaseByNameProp('TCAA02 - Actualizar Área')['id'],
            $status,
            $this->getSteps(),
            $this->lastTime
        );
    }

    public function testEliminarArea(){
        try {
            $this->createSteps();
            $this->startContador();
            $this->goTo('Areas');
            // busca el span que contiene la clase node-name y el texto del nodo
            $nodeContent = $this->testNombre;
            $xpathSelector = "//span[@class='node-name' and text()='{$nodeContent}']/ancestor::button[1]/parent::*";
            $this->waitElement(WebDriverBy::xpath($xpathSelector));
            $nodeAtion = $this->driver->findElement(WebDriverBy::xpath($xpathSelector));
            $nodeAtion = $nodeAtion->findElement($this->selector('div.node-actions'));
            if(!$nodeAtion){
                throw new Exception("No se encontro el area");
            }
            $this->addSteps('p');

            $this->scrollTo(WebDriverBy::xpath($xpathSelector."//div[@class='node-actions']"));
    
            $buttonEliminar = $nodeAtion->findElement($this->selector('div[data-bs-title="Eliminar"] div[data-bs-toggle="modal"]'));
            if(!$buttonEliminar){
                throw new Exception("No se encontro el boton de actualizar");
            }
            $buttonEliminar->click();
            $this->addSteps('p');

            $this->waitElement(WebDriverBy::xpath('//div[@id="modal-eliminar"]//b[@class="nombre" and text()="'.$this->testNombre.'"]'));
            $this->click('#modal-eliminar a.btn.btn-danger.eliminar');
            $this->addSteps('p');

            $this->wait(
                WebDriverExpectedCondition::invisibilityOfElementLocated(
                    WebDriverBy::xpath($xpathSelector)
                )
                );
            $this->waitAlert();
            $this->addSteps('p');
            $this->endContador();
    
    
            //$this->fillForm('#nombre', $this->testNombre);
            echo "✅ Area eliminada\n";
        } catch (\Throwable $th) {
            echo "❌ Error al eliminar el area :: {$th->getMessage()} :: linea {$th->getLine()} :: file {$th->getFile()}\n" ;
            echo $th->getTraceAsString();
        }
        $status = $this->getStatusSteps();
        $this->testLink->reportTest(
            $this->testLink->getTestCaseByNameProp('TCAA03 - Eliminar Área')['id'],
            $status,
            $this->getSteps(),
            $this->lastTime
        );
    }


    public function testRegistrarAreaInvalid($datos){
        try {
            //code...
            $this->goTo('Areas');
            $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::invisibilityOfElementLocated($this->selector('#modal-generico')));
            $this->startContador();
            $this->createSteps();

            $this->click('button[data-bs-target="#modal-generico"][data-bs-url="/DSG-Appweb/Areas/Registrar"]');
            $this->addSteps('p'); // Hacer click en el botón "Nueva Area"
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
            $this->testLink->getTestCaseByNameProp('TCAA04 - Registrar Área Invalido')['id'],
            $status,
            $this->getSteps(),
            $this->lastTime
        );
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



    public function testArea(){
        $this->openSystemDSG();
        $casos =[
            ['caso' => 1, 'nombre' => '', 'esenario' => 'Campo vacio'],// vacio
            ['caso' => 2, 'nombre' => 'Hilandera', 'esenario' => 'Campo repetido'],// repetido
            ['caso' => 1, 'nombre' => str_repeat('H', 300), 'esenario' => 'Campo demasiado largo'],// demasiado largo
            ['caso' => 1, 'nombre' => "<script>alert('XSS')</script>", 'esenario' => 'XSS'],// demasiado corto
        ];


        foreach ($casos as $caso) {
            $this->testRegistrarAreaInvalid($caso);
        }
        $this->print("Prueba de registro invalida completada");


        $this->testRegistrarArea();
        $this->print("Prueba de registro completada");
        $this->testActualizarArea();
        $this->print("Prueba de actualizacion completada");

        foreach ($casos as $caso) {
            $this->testActualizarAreaInvalid($caso);
        }
        $this->print("Prueba de actualizacion invalida completada");
        
        $this->testEliminarArea();
        $this->print("Prueba de eliminacion completada");
        
        
        
        $this->closeBrowser();
    }



    
}

?>