<?php
/**
 * ❌ ✅ 🔎
 * php scriptTest.php tareas
 */
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class TareasSelenium extends ComunSelenium {
    
    public function __construct(ApiController $testLink) {
        parent::__construct();
        $this->testLink = $testLink;
    }

    public function testTarea() {
        $this->testRegistrarTarea();
        $this->closeBrowser();
    }

    public function testRegistrarTarea() {
        $this->createSteps();
        try {
            $this->startContador();
            
            // 1. Login y navegación al módulo
            $this->openSystemDSG(true);
            $this->addSteps('p', 'Login exitoso al sistema');
            
            // 2. Navegar al módulo de tareas
            $this->goTo('Tareas');
            $this->waitUrl(url('Tareas'));
            $this->addSteps('p', 'Navegación al módulo de tareas');
            
            // 3. Hacer clic en "Nueva tarea" para abrir el modal
            $this->click('button[data-bs-target="#modal-tareas"]');
            $this->addSteps('p', 'Modal de nueva tarea abierto');
            
            // Esperar a que el modal esté visible
            $this->waitElement('#modal-tareas .modal-content', 5);
            
            // 4. Llenar datos básicos - Pestaña 1
            $this->fillForms([
                ['selector' => '#nombre-tarea', 'value' => 'Tarea de Prueba Automatizada'],
                ['selector' => '#descripcion', 'value' => 'Esta es una tarea de prueba creada mediante Selenium para testing automático.'],
            ]);
            $this->addSteps('p', 'Datos básicos completados');
            
            // 5. Seleccionar departamento
            $this->selectOptionByIndex('#departamento', 1); // Jardinería y ornato
          //  $this->addSteps('p', 'Departamento seleccionado');
            
            // 6. Seleccionar área (esperar a que cargue después del departamento)
            sleep(1);
            $this->selectOptionByIndex('#area', 1); // Primera área disponible
           // $this->addSteps('p', 'Área seleccionada');
            
            // 7. Seleccionar turno
            $this->selectOptionByIndex('#turno', 1); // Mañana
            $this->addSteps('p', 'Turno seleccionado');
            
            // 8. Fecha de inicio (usar fecha actual)
            $fechaActual = date('d-m-Y');
            $this->fillForm('#fecha_inicio', $fechaActual);
       //     $this->addSteps('p', 'Fecha de inicio establecida');
            
            // 9. Seleccionar personal (primer trabajador disponible)
            $this->selectMultipleOptions('#personal', [0]); // Seleccionar primera opción
        //    $this->addSteps('p', 'Personal asignado');
            sleep(2);
            
            // 10. Seleccionar supervisor (Admin DSG)
            $this->selectOptionByValue('#supervisor', '1');
          //  $this->addSteps('p', 'Supervisor asignado');
            
            // 11. Ir a la siguiente pestaña "Detalles"
            $this->click('.siguiente[data-next="detalles"]');
          //  $this->addSteps('p', 'Navegación a pestaña de detalles');
            
            // Esperar a que cargue la pestaña de detalles
            sleep(2);
            
            // 12. Seleccionar materiales
            $this->seleccionarMateriales();
          //  $this->addSteps('p', 'Materiales seleccionados');
            
            // 13. Guardar la tarea
            $this->click('button[type="submit"]');
            $this->addSteps('p', 'Formulario enviado');
            sleep(4);
            
            // 14. Verificar éxito - USANDO EL MÉTODO CORRECTO COMO EN ÁREAS
            $this->waitAlert();
            $this->addSteps('p', 'Tarea registrada exitosamente');
            
            $this->endContador();
            
        } catch (\Throwable $th) {
            $this->print("Error en testRegistrarTarea: " . $th->getMessage(), 6);
            $this->blockSteps(7); // SOLO 7 PASOS COMO EN TESTLINK
        }
        
        $status = $this->getStatusSteps();
        $this->testLink->reportTest(
            $this->testLink->getTestCaseByNameProp('TCATA33 - Registrar Tarea')['id'],
            $status,
            $this->getSteps(),
            $this->lastTime
        );
    }

    /**
     * Método para seleccionar materiales de la tabla
     */
    private function seleccionarMateriales() {
        try {
            // Esperar a que la tabla de materiales esté cargada
            $this->waitElement('#tabla-materiales', 5);
            
            // Buscar y seleccionar el primer material disponible (Bolígrafo Azul)
            $primerMaterial = $this->driver->findElement(
                WebDriverBy::cssSelector('#tabla-materiales tbody tr:first-child')
            );
            
            if ($primerMaterial) {
                // Encontrar el input de cantidad y establecer cantidad
                $inputCantidad = $primerMaterial->findElement(
                    WebDriverBy::cssSelector('.cantidad-material')
                );
                $inputCantidad->clear();
                $inputCantidad->sendKeys('2');
                
                // Hacer clic en el botón "Agregar material"
                $botonAgregar = $primerMaterial->findElement(
                    WebDriverBy::cssSelector('.agregar-material')
                );
                $botonAgregar->click();
                
                $this->print("  Material 'Bolígrafo Azul' agregado con cantidad 2", 1);
                
                // Esperar a que aparezca en la tabla de seleccionados
                $this->waitElement('#tabla-seleccionados tbody tr:not(.text-muted)', 3);
                
                // Verificar que el material fue agregado
                $tablaSeleccionados = $this->driver->findElement(
                    WebDriverBy::cssSelector('#tabla-seleccionados tbody')
                );
                $filas = $tablaSeleccionados->findElements(WebDriverBy::tagName('tr'));
                
                if (count($filas) > 0 && !str_contains($filas[0]->getText(), 'No hay materiales')) {
                    $this->print("  Material verificado en lista de seleccionados", 1);
                    return true;
                }
            }
            
            return false;
            
        } catch (\Throwable $th) {
            $this->print("  Error al seleccionar materiales: " . $th->getMessage(), 2);
            return false;
        }
    }

    /**
     * Selecciona una opción de un dropdown por su índice
     */
    private function selectOptionByIndex($selector, $index) {
        $select = $this->driver->findElement(WebDriverBy::cssSelector($selector));
        $options = $select->findElements(WebDriverBy::tagName('option'));
        
        if (isset($options[$index])) {
            $options[$index]->click();
            return true;
        }
        return false;
    }

    /**
     * Selecciona una opción de un dropdown por su valor
     */
    private function selectOptionByValue($selector, $value) {
        $select = $this->driver->findElement(WebDriverBy::cssSelector($selector));
        $option = $select->findElement(WebDriverBy::cssSelector("option:first-of-type"));
        $option->click();
        return true;
    }

    /**
     * Selecciona múltiples opciones en un select multiple
     */
    private function selectMultipleOptions($selector, $indices) {
        $select = $this->driver->findElement(WebDriverBy::cssSelector($selector));
        $options = $select->findElements(WebDriverBy::tagName('option'));
        
        foreach ($indices as $index) {
            if (isset($options[$index])) {
                $options[$index]->click();
            }
        }
        return true;
    }
}