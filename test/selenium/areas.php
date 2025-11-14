<?php 
/**
 * ❌ ✅
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
            $this->addSteps(result: 'p');
            echo "✅ ingresando a areas \n";

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

    public function testArea(){
        $this->openSystemDSG();
        $this->testRegistrarArea();
        $this->testActualizarArea();
        $this->testEliminarArea();
        $this->closeBrowser();
    }
}

?>