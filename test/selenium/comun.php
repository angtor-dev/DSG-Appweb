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
        public function fillForms(array $inputs, $timeout = 3, $interval = 500, $mensaje = ''){
            foreach($inputs as $input){
                $this->fillForm($input['selector'], $input['value'], $timeout, $interval, $mensaje);
            }
        }

        /**
         * epera que un elemento se encuentre visible
         * @param WebDriverBy|string  $selector
         * @param int $timeout
         * @param int $interval
         * @param string $mensaje
         * @return RemoteWebElement
         */
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
         * Espera que una alerta se muestre
         * @param string $text
         * @param 'success'|'danger' | 'warning' $type = 'success'
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
                $this->print("Error al esperar la alerta",6);
                echo ":: {$th->getMessage()}\n" ;
                throw $th;
            }


        }

        public function goTo($url){
            $this->driver->get(url($url));
        }
        /**
         * Convierte un selector CSS o un objeto WebDriverBy en un objeto WebDriverBy para utilizarlo en las funciones de espera.
         * Si el par metro es un string, se asume que es un selector CSS.
         * Si el par metro es un objeto WebDriverBy, se devuelve el mismo objeto sin realizar ninguna acci n.
         * @param string|WebDriverBy $selector Un selector CSS, un objeto WebDriverBy, o un elemento ya encontrado.
         * @return WebDriverBy El objeto WebDriverBy resultante de la conversi n.
         */
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
                throw $th;
            }
        }

        /**
         * Espera a que el elemento de mensajes de error de los input/select este visible 
         * * si se le pasa el mensaje verifica que sea el mismo
         * * usage:
         * * <code> $this->waitFormText($this->selector('idInput'), 'El mensaje a esperar'); </code>
         * * input:
         * <code>
         * con data-span
         * <input type="text" data-span="spanId">
         * <span id="spanId" class="form-text invalid-feedback ">El mensaje a esperar</span>
         * 
         * con data-formtext
         * <input type="text" data-formtext="spanId">
         * <span id="spanId" class="form-text invalid-feedback ">El mensaje a esperar</span>
         * 
         * sin data (por el elemento hermano con la clase form-text invalid-feedback )
         * <input type="text">
         * <span class="form-text invalid-feedback ">El mensaje a esperar</span>
         * 
         * sin data (por el elemento padre y luego un elemento hijo con la clase form-text invalid-feedback )
         * <div>
         *   <input id="passID" type="password" name="clave" class="form-control" placeholder="Contraseña" data-span="test">
         *   <div>algo a mitad</div>
         *   <span class="form-text invalid-feedback d-block">queso</span>
         * </div>
         * 
         * </code>
         * 
         * si el idElemnent es un input/textarea/select desde donde encontrar el form-text este metodo tambien validara el valid de html5 ej. required pattern
         * @param string $idElemnent selector css del input
         * @param string $mensaje mensaje a esperar
         * @param bool $xpath si el idElemnent es un xpath al input||.form-text
         * @param mixed $timeout tiempo de espera
         * @param mixed $interval iteraciones de espera
         * 
         * @return RemoteWebElement;
         */
        public function waitFormText($idElemnent, $mensaje = '', $xpath = false, $timeout = 3, $interval = 500){
            try {
                $input = '';
                $this->print("  buscando form-text",5);
                
                if($xpath){
                    $by = WebDriverBy::xpath($idElemnent);
                }
                else{
                    $by = $this->selector("$idElemnent");
                }
                
                $elem = $this->driver->findElement($by);
                
                if(!$elem){
                    throw new Exception("No se pudo encontrar el elemento por su selector");
                }
                
                $class = $elem->getAttribute('class');
                
                $span = '';
                
                if(str_contains($class, 'form-text') || str_contains($class, 'invalid-feedback')){
                    $span = $elem;
                    echo "span: seleccionado directamente\n";
                    $this->print("  form-text encontrado directamente",4);

                }
                else{
                    $span = $elem->getAttribute('data-span') ?? $elem->getAttribute('data-formtext') ?? '';
                    


                    
                    // <div class="input-group">
                    if(empty($span)){
                        $input = $by;
                        
                        try {// buscamos el hermano
                            $next = $elem->findElement(WebDriverBy::xpath(".//following-sibling::*[1][contains(@class, 'form-text') or contains(@class, 'invalid-feedback')]"));
                        } catch (\Throwable $th) {
                            // buscamos el padre y luego el hijo
                            $next = $elem->findElement(WebDriverBy::xpath(".//ancestor::*[1]/descendant::*[contains(@class,'form-text') or contains(@class,'invalid-feedback')]"));
                        }
                        $span = $next;
                        $this->print("  form-text encontrado desde el elemento [input|select|textarea (elemento campo origen)] como hermano o hijo del padre del elemento",4);

                    }
                    else{
                        $span = $this->driver->findElement($this->selector("#$span"));
                        $this->print("  form-text encontrado desde el data-span|data-formtext del elemento",4);
                    }

                }
                

                if(!($span instanceof RemoteWebElement)){
                    throw new Exception("No se pudo encontrar el span de error por su selector");
                }
                if($input instanceof WebDriverBy ){

                    $this->print("  validando con estado de input o elemento .invalid-feedback",4);
                    
                    $this->driver->wait($timeout, $interval)->until(
                        function (RemoteWebDriver $driver) use ($elem, $span, $mensaje) {
                            /**
                             * @var RemoteWebElement $span
                             */
                            $validInput = !$this->driver->executeScript("return arguments[0].validity.valid", [$elem]);
                            if($mensaje != ""){
                                $validSpan = $span->isDisplayed() && $span->getText() == $mensaje;
                            }
                            else{
                                $validSpan = $span->isDisplayed();
                            }

                            return $validInput || $validSpan;
                            
                        }
                    );

                }
                else{
                    $this->driver->wait($timeout, $interval)->until(
                        WebDriverExpectedCondition::visibilityOf($span)
                    );
                    
                }
                if($mensaje != ""){
                    if(!$span->getText() == $mensaje){
                        throw new Exception("El mensaje no es el esperado \nEsperado: {$mensaje} \nObtenido: {$span->getText()} != {$mensaje}");
                    }
                }
                return $span;
                
            } catch (\Throwable $th) {
                $this->print(" Error al esperar el elemento",6);
                echo $th->getMessage();
                throw $th;
            }
            
        }

        
        /**
         * Imprime una cadena con un icono al inicio
         * 
         * @param string $str la cadena a imprimir
         * @param int $icon el icono a imprimir (1: ✅, 2: ⚠ , 3:❌ , 4: 🔎, 5: ༼ つ ◕_◕ ༽つ, 6: `(*>﹏<*)′ )
         */
        public function print($str,$icon = 1){
            $icon--;
            $icons = [
                "✅",
                "⚠️",
                "❌",
                "🔎",
                "༼ つ ◕_◕ ༽つ",

                "`(*>﹏<*)′"
            ];

            preg_match("/^([\t|\s]*)/", $str, $matches);
            $message = preg_replace("/^[\t|\s]*/", "", $str);
            echo $matches[1] . $icons[$icon] .' '. $message . "\n";
        }



    }

 ?>