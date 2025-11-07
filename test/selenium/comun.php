<?php 
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Remote\RemoteWebElement;

    class ComunSelenium{

        public $driver;
        public ApiController $testLink;
        public $steps;
        public $startCounter;
        public $lastTime;

        public function __construct(){
            $this->startCounter = null;
            $this->lastTime = 0;
            $this->steps = array();

            $this->openBrowser();
        }

        public function openSystemDSG($login = true){
            $this->driver->get("http://localhost/DSG-Appweb/");
            if($login){
                $this->login();
            }
        }
        public function login($clave = "Hola123", $correo = "admin@dsg.com"){

            try {
                $this->driver->wait(3, 500)->until(
                    WebDriverExpectedCondition::urlIs("http://localhost/DSG-Appweb/Login")
                );
                $this->driver->findElement(WebDriverBy::cssSelector('input[name="correo"]'))->sendKeys($correo);
                $this->driver->findElement(WebDriverBy::cssSelector('input[name="clave"]'))->sendKeys($clave);
                $this->driver->findElement(WebDriverBy::cssSelector('button[type="submit"]'))->click();
                $this->waitUrl("http://localhost/DSG-Appweb/", 5);
            } catch (\Exception $th) {
                //throw $th;
            }

        }

        public function closeBrowser(){
            $this->driver->quit();
            $this->driver = null;
        }
        public function openBrowser(){
            $host = 'http://localhost:9516'; // Conexión directa a ChromeDriver (Opción 2)
            $capabilities = DesiredCapabilities::chrome();
            $driver = RemoteWebDriver::create($host, $capabilities);
            $this->driver = $driver;
        }
        public function wait($funcion, $timeout = 3, $interval = 500, $mensaje = ''){
            $this->driver->wait($timeout, $interval)->until($funcion, $mensaje);
        }
        
        public function createSteps(){
            $this->steps = array();
        }

        /**
         * @param 'p'|'f' $result
         * @param string $notas
         */
        public function addSteps(string $result, string $notas = ''){
            $contador = count($this->steps);
            $this->steps[] = array(
                'step_number' => $contador + 1,
                'result' => $result,
                'notes' => $notas
            );
        }
        /**
         * Bloquea los pasos de la prueba creando pasos con un resultado "b" hasta que 
         * el contador de pasos llegue a $num a partir del actual
         * @param int $num total de pasos
         */
        public function blockSteps(int $num){
            $this->endContador();
            $contador = count($this->steps);
            $result = "f";
            for($i = $contador + 1; $i <= $num; $i++){
                $this->steps[] = array(
                    'step_number' => $i,
                    'result' => $result,
                    'notes' => ''
                );
                $result = "b";
            }
        }
        public function getStatusSteps(){
            $status = "p";
            foreach($this->steps as $step){
                if($step['result'] == "f"){
                    $status = "f";
                }
                else if ($step['result'] == "b"){
                    $status = "b";
                    break;
                }
            }
            return $status;
        }

        

        public function startContador(){
            $this->startCounter = microtime(true);
        }
        public function endContador(){
            if($this->startCounter == null){
                return 0;
            }
            $time = microtime(true) - $this->startCounter; 
            $this->lastTime = $time;
            $this->startCounter = null;
            return $time;
        }
        public function getSteps(){
            return $this->steps;
        }


        public function click($selector){
            try {
                $selector = $this->selector($selector);
                $this->driver->wait(3, 500)->until(
                    WebDriverExpectedCondition::visibilityOfElementLocated($selector)
                );
                $this->driver->findElement($selector)->click();
            } catch (\Exception $th) {
                echo "Error al hacer click en el elemento :: {$th->getMessage()}\n" ;
            }
        }
        public function fillForm ($selector, $value, $timeout = 3, $interval = 500, $mensaje = ''){
            try {
                $selector = $this->selector($selector);
                $this->driver->wait($timeout, $interval)->until(
                    WebDriverExpectedCondition::visibilityOfElementLocated($selector),
                    $mensaje
                );
                $this->driver->findElement($selector)->clear();
                $this->driver->findElement($selector)->sendKeys($value);
            } catch (\Exception $th) {
                echo "Error al llenar el formulario :: {$th->getMessage()}\n" ;
            }
        }
        /**
         * llena un formulario por un array
         * @param array<array{selector: string, value: string}> $inputs
         * @return void
         */
        public function fillFroms(array $inputs, $timeout = 3, $interval = 500, $mensaje = ''){
            foreach($inputs as $input){
                $this->fillForm($input['selector'], $input['value'], $timeout, $interval, $mensaje);
            }
        }
        public function waitElement($selector, $timeout = 3, $interval = 500, $mensaje = ''){
            try {
                $selector = $this->selector($selector);
                $this->driver->wait($timeout, $interval)->until(
                    WebDriverExpectedCondition::visibilityOfElementLocated($selector), 
                    $mensaje
                );
                return $this->driver->findElement($selector);
            } catch (\Exception $th) {
                echo "Error al esperar el elemento :: {$th->getMessage()}\n" ;
                throw $th;
            }
        }
        public function waitUrl($url, $timeout = 3, $interval = 500, $mensaje = ''){
            try {
                $this->driver->wait($timeout, $interval)->until(
                    WebDriverExpectedCondition::urlIs($url), 
                    $mensaje
                );
            } catch (\Exception $th) {
                echo "Error al esperar la url :: {$th->getMessage()}\n" ;
                throw $th;
            }
        }
        /**
         * Summary of waitAlert
         * @param string $text
         * @param 'success'|'danger' | 'warning' $type
         * @param int $timeout
         * @param int $interval
         * @param string $mensaje
         * @return void
         */
        public function waitAlert($text = '', $type = 'success', $timeout = 3, $interval = 500, $mensaje = ''){
            try {
                $condition = WebDriverExpectedCondition::visibilityOfElementLocated(
                    WebDriverBy::cssSelector('div.toastify.on[style*="--bs-'.$type.'"]')
                );
                if(!empty($text)){
                    $condition = WebDriverExpectedCondition::elementTextContains(
                        WebDriverBy::cssSelector('div.toastify.on[style*="--bs-'.$type.'"]'),
                        $text);
                }
                $this->wait(
                    $condition,
                    $timeout,
                    $interval,
                    $mensaje
                );
                
            } catch (\Exception $th) {
                echo "Error al esperar la alerta :: {$th->getMessage()}\n" ;
                throw $th;
            }


        }

        public function goTo($url){
            $this->driver->get(url($url));
        }
        public function selector(WebDriverBy|string $selector){
            if(is_string($selector)){
                return WebDriverBy::cssSelector($selector);
            }
            else return $selector;
            
        }

        /**
         * Desplaza la página hasta que el elemento especificado (por selector o por objeto) esté visible 
         * y luego se asegura de que sea clicable.
         * @param string|WebDriverBy $selector Un selector CSS/XPath, un objeto WebDriverBy, o un elemento ya encontrado.
         * @return RemoteWebElement|null El elemento encontrado, o null si falla.
         */
        public function scrollTo($selector) {
            $auxSelector = $selector;
            // Asumimos que tienes acceso a $this->driver y a un método $this->wait
            $elemento = null; 

            try {
                // --- 1. Determinar el Objeto del Elemento (RemoteWebElement) ---
                
                // Caso A: Selector pasado como string (asumimos CSS Selector por defecto)
                $by = $this->selector($selector);
                
                // Esperar que el elemento esté visible antes de encontrarlo
                $this->driver->wait(3, 500)->until(
                    WebDriverExpectedCondition::visibilityOfElementLocated($by)
                );
                $elemento = $this->driver->findElement($by);
                
                // Si no se pudo encontrar el elemento por alguna razón
                if (!$elemento) {
                    return null;
                }

                // --- 2. Realizar el Scroll y Esperar la Condición de Clic ---

                // Usamos arguments[0].scrollIntoView(true) para asegurarnos de que el borde superior 
                // del elemento esté en la parte superior de la vista.
                $this->driver->executeScript("arguments[0].scrollIntoView({behavior: 'instant', block: 'center', inline: 'nearest'});", [$elemento]);
                

                $this->wait(WebDriverExpectedCondition::elementToBeClickable($by)); 
                
                return $elemento;

            } catch (\Exception $th) {
                echo "❌ Error al hacer scroll o esperar elemento: {$th->getMessage()}\n";
                return null;
            }
        }



    }

 ?>