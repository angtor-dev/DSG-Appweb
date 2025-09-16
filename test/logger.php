<?php
// clase LoggerPhpUnit
use PHPUnit\Framework\TestCase;
use PHPUnit\Util\Test;

class testControl extends TestCase implements PhpunitBootstrapMethods{

}

class LoggerPhpUnit
{
    /**
     * Summary of test
     * @var testControl
     */
    private $test;
    private $testSuite;
    public function __construct( $test, $testSuite = "TestSuite") {
        $this->test = $test;
        $this->testSuite = $testSuite;
    }
    /**
     * crea un archivo .txt con el nombre del testsuite y por ahora contiene solo un hola mundo
     * @return void
     */
    public function log() {

        try {
            $name = $this->test->getName(false);
            $dataset = $this->test->getProvidedData();
            $dataname = $this->test->dataName();
            $arregloDePruebas = new stdClass();
            $filePath = "./test/reports/logDatos.json";

            // verifica si el archivo existe
            if(file_exists($filePath)) {
                // lee y obtene el json del archivo
                $file = fopen($filePath, "r");
                $json = fread($file, filesize($filePath));
                fclose($file);
                $arregloDePruebas = json_decode($json);

                if(!empty(get_object_vars($arregloDePruebas))) {
                    $arregloDePruebas = (new logJsonTest())->ArraySerialize($arregloDePruebas);
                }
            }

            // verifico si el arreglo tiene la key testSuite
            if(isset( $arregloDePruebas->{$this->testSuite})){ 
                $objJson = $arregloDePruebas->{$this->testSuite};
                $tempName = $this->test->getName(false);
                if(isset($objJson->testMethods->{$this->test->getName(false)})){
                    $objJsonMethods = $objJson->testMethods->{$this->test->getName(false)};
                }
                else{
                    $objJsonMethods = new logJsonTestMethod();
                }
            }
            else{
                $objJson = new logJsonTest();
                $arregloDePruebas->{$this->testSuite} = $objJson;
                $objJsonMethods = new logJsonTestMethod();
            }
            /**
             * @var logJsonTest $objJson
             */
            $objJson->setTestSuite($this->testSuite);

            

            /**
             * @var logJsonTestMethod $objJsonMethods
             */

            $objJsonMethods->setName($this->test->getName(false));
            $objJsonMethods->addDataset($this->test->getProvidedData(), $this->test->dataName());
            $objJsonMethods->setAssertions($this->test->getNumAssertions());
            $objJson->addMethod($objJsonMethods);



            // borrar todos los testsuites que no sean el actual
            // ya que el archivo es para un solo testsuite
            // mientras no encuentre la forma de hacer que el registro del xml sea por testsuite
            if(false){
                foreach ($arregloDePruebas as $key => $value) {
                    if($key != $this->testSuite) {
                        unset($arregloDePruebas->{$key});
                    }
                }
            }

            

            









            
            $file = fopen($filePath, mode: "w");

            fwrite($file, json_encode($arregloDePruebas, JSON_PRETTY_PRINT));
            fclose($file);
        } catch (\Throwable $th) {
            $this->test->fail("".$th->getMessage()."line ".$th->getLine());
        }

        
    }
    
}

/**
 * @param string $testSuite nombre del testsuite
 * @param string $testMethod nombre del metodo de test
 * @return void
 */
class logJsonTest {
    public $testSuite;
    public $testMethods;

    public function __construct() {
        $this->testMethods = new stdClass();
    }


    public function ArraySerialize($array) {
        foreach ($array as &$list) {
            $list = $this->serialize($list);
        }
        
        return $array;
    }

    public function serialize ($json) {
        $obj = new logJsonTest();
        $obj->setTestSuite($json->testSuite);
        if(is_object($json->testMethods)) {
            foreach ($json->testMethods as $method) {
                $methodControl = new logJsonTestMethod();
                $methodControl->setName($method->name);
                $methodControl->setDataset($method->dataset);
                $methodControl->setNumTest($method->numTest);
                $methodControl->setAssertions($method->assertions);
                $methodControl->setErrors($method->errors);
                $methodControl->setWarnings($method->warnings);
                $methodControl->setFailures($method->failures);
                $methodControl->setTime($method->time);
                $obj->addMethod($methodControl);
            }
        }
        $tempClass = new stdClass();
        return $tempClass->{$obj->getTestSuite()} = $obj;
    }


    public function setMethod(array $method) {
        $this->testMethods = $method;
        return $this;
    }


    public function addMethod(logJsonTestMethod $testMethod) : self {
        $this->testMethods->{$testMethod->getName()} = $testMethod;
        return $this;
    }

    public function getMethods() : array {
        return $this->testMethods;
    }
    public function getMethod(string $name) : logJsonTestMethod {
        return $this->testMethods[$name];
    }
    
    public function setTestSuite(string $testSuite) : self {
        $this->testSuite = $testSuite;
        return $this;
    }
    public function getTestSuite() : string {
        return $this->testSuite;
    }
}

class logJsonTestMethod{
    public $name;
    public $numTest;
    public $assertions;
    public $errors;
    public $warnings;
    public $failures;
    public $time;
    public $dataset;

    public function __construct() {
        $this->dataset = new stdClass();
    }


    






    public function setName(string $name) : self {
        $this->name = $name;
        return $this;
    }
    public function getName() : string {
        return $this->name;
    }

    public function setDataset($dataset) : self {
        $this->dataset = $dataset;
        return $this;
    }
    public function getDataset() : array {
        return $this->dataset;
    }
    public function addDataset(array $dataset, string $name) : self {
        $this->dataset->{$name} = $dataset;
        return $this;
    }

    public function setNumTest(int|null $numTest) : self {
        $this->numTest = $numTest;
        return $this;
    }
    public function getNumTest() : int {
        return $this->numTest;
    }
    public function setAssertions(int|null $assertions) : self {
        $this->assertions = $assertions;
        return $this;
    }
    public function getAssertions() : int {
        return $this->assertions;
    }
    public function setErrors(int|null $errors) : self {
        $this->errors = $errors;
        return $this;
    }
    public function getErrors() : int {
        return $this->errors;
    }
    public function setWarnings(int|null $warnings) : self {
        $this->warnings = $warnings;
        return $this;
    }
    public function getWarnings() : int {
        return $this->warnings;
    }
    public function setFailures(int|null $failures) : self {
        $this->failures = $failures;
        return $this;
    }
    public function getFailures() : int {
        return $this->failures;
    }
    public function setTime(float|null $time) : self {
        $this->time = $time;
        return $this;
    }
    public function getTime() : float {
        return $this->time;
    }
}
 ?>