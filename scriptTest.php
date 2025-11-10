<?php
require_once 'vendor/autoload.php';
/*
    php scriptTest.php [test1 test2 ...]
    ❌
    ✅
*/
define("LIST_TESTS", ["login", "areas"]);



echo "\n Begin \n";

$apicontroller = new ApiController();

$listTest = LIST_TESTS;

// IGNORAR DESDE AQUI

    $searchTest = false;

    $list = [];
    if($argc > 1) {
        foreach ($argv as $elem) {
            $list[] = $elem;
            if(in_array($elem, $listTest) ) $searchTest = true;
        }
    }
    if(in_array("projects", $list)) {$apicontroller->getProjects(); die;}
    if(in_array("plans", $list)) {$apicontroller->getTestPlans(1); die;}
    if(in_array("builds", $list)) {$apicontroller->getBuildsForTestPlan(10); die;}
    if(in_array("testsuite", $list)) {$apicontroller->getTestSuitesForTestPlan(10); die;}
    if(in_array("testcase", $list)) {$apicontroller->getTestCasesForTestSuite($apicontroller->testSuiteAlfa['id']); die;}

    $__executeTest = function ($test) use ($searchTest, $list, $listTest) {
        if(!$searchTest ) return true; // si no esta buscando un test especifico, se ejecutan todos
        else{
            if(in_array($test, $list) and in_array($test, $listTest)) return true;
            else return false;
        }
    };

// IGNORAR HASTA AQUI

if($__executeTest("login")) (new LoginSelenium($apicontroller))->testLogin();
if($__executeTest("areas")) (new AreasSelenium($apicontroller))->testArea();


echo "\n END \n";














/**
 * devuelve la url por ejemplo 'http://localhost/DSG-Appweb/' si el parametro esta vacio
 * @param mixed $url agrega al final de la url ej.(Area) http://localhost/DSG-Appweb/Area
 * @return string
 */
function url($url='') { return APP_URL.$url; }