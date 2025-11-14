<?php 
// un arreglo de diccionario de valores de pruebas con esenarios de prueba
    class Diccionario {
    
    /** @var array La colección completa de "nombres" y sus detalles. */
    private $dicNombres;

    
    /** @var int El índice del último elemento devuelto (o -1 si no se ha llamado aún). */
    private $indices;
    

    public function __construct() {

        $this->indices = [
            "nombres" => -1
        ];
        $this->dicNombres = [
            ["valor" => "a", "esenario" => "Un solo caracter"],
            ["valor" => "", "esenario" => "Vacio"],
            ["valor" => "123", "esenario" => "Numeros"],
            ["valor" => "Hola-, 123", "esenario" => "Alfanumerico"],
            ["valor" => str_repeat("a", 300) , "esenario" => "Texto Largo"],
            ["valor" => str_repeat("1", 300) , "esenario" => "Numeros Largo"],
            ["valor" => "             ", "esenario" => "Vacio espacio"],
            ["valor" => "123", "esenario" => "Numeros corto"],
            ["valor" => "Nombre - Apellido", "esenario" => "Caracter Especial (-)"],
            ["valor" => "Nombre % Apellido", "esenario" => "Caracter Especial (%)"],
            ["valor" => "Nombre @ Apellido", "esenario" => "Caracter Especial (@)"],
            ["valor" => "Nombre # Apellido", "esenario" => "Caracter Especial (#)"],
            ["valor" => "Nombre $ Apellido", "esenario" => "Caracter Especial ($)"],
            ["valor" => "Nombre & Apellido", "esenario" => "Caracter Especial (&)"],
            ["valor" => "Nombre * Apellido", "esenario" => "Caracter Especial (*)"],
            ["valor" => "Nombre + Apellido", "esenario" => "Caracter Especial (+)"],
            ["valor" => "Nombre = Apellido", "esenario" => "Caracter Especial (=)"],
            ["valor" => "Nombre ; Apellido", "esenario" => "Caracter Especial (;)"],
            ["valor" => "Nombre : Apellido", "esenario" => "Caracter Especial (:)"],
            ["valor" => "Nombre ? Apellido", "esenario" => "Caracter Especial (?)"],
            ["valor" => "Nombre / Apellido", "esenario" => "Caracter Especial (/)"],
            ["valor" => "Nombre ! Apellido", "esenario" => "Caracter Especial (!)"],
            ["valor" => "<script>Alert('XSS')</script>", "esenario" => "ataque XSS"],
            ["valor" => "nombre", "esenario" => "Nombre corto"],
            ["valor" => "áéíóú", "esenario" => "Nombre con acentos"],
            
        ];
    }

    /**
     * devuelve un valor del diccionario segun el regex y el indice
     * @param mixed $regex solo devolvera los que **NO** cumplen con la expresion
     * @param mixed $indice
     * @param mixed $diccionario
     * @param mixed $esenario
     * @return array{"valor":string,"esenario":string}|null
     */
    private function getDicIterationInvalid($regex,&$indice,$diccionario,$esenario = " _NAME_ ", $ignoreCaseEsenario = []){
         


        for ($indice++ ; $indice < count($diccionario); $indice++) {
            $item = $diccionario[$indice];
            if(in_array($item["esenario"], $ignoreCaseEsenario)) continue;
            $nombre = $item["valor"];
            $item["esenario"] = $esenario." - " . $item["esenario"];

            if (!preg_match($regex, $nombre)) {
                return $item;
                
            }
        }
        $indice = -1;
        return null;
    }
    /**
     * devuelve un valor del diccionario segun el regex y el indice
     * @param mixed $regex solo devolvera los que **SI** cumplen con la expresion
     * @param mixed $indice
     * @param mixed $diccionario
     * @param mixed $esenario
     * @return array{"valor":string,"esenario":string}|null
     */
    private function getDicIterationValid($regex,&$indice,$diccionario,$esenario = " _NAME_ ", $ignoreCaseEsenario = []){ 


        for ($indice++ ; $indice < count($diccionario); $indice++) {
            $item = $diccionario[$indice];
            if(in_array($item["esenario"], $ignoreCaseEsenario)) continue;
            $nombre = $item["valor"];
            $item["esenario"] = $esenario." - " . $item["esenario"];

            if (preg_match($regex, $nombre)) {
                return $item;
            }
        }
        $indice = -1;
        return null;
    }

    /**
     * devuelve un valor del diccionario segun el regex y el indice interno del diccionario
     * @param string $regex la expresion regular para buscar
     * @param string $esenario el nombre del escenario para el reporte
     * @param bool $valid si es true devuelve el primer valor que cumple con el regex, si es false devuelve el primer valor que no cumple con el regex
     * @return array|string el valor del diccionario que cumple con el regex y el indice, o null si no se encontró
     */
    public function getNombre($regex,$esenario = " _NAME_ ", $valid = true){
        if($valid) return $this->getDicIterationValid($regex,$this->indices["nombres"],$this->dicNombres,$esenario);
        else return $this->getDicIterationInvalid($regex,$this->indices["nombres"],$this->dicNombres,$esenario);
    }

    /**
     * Genera un array con la estructura definida por $estructura pero
     * reemplaza el valor de $replace por el valor del diccionario
     * que cumple con la expresion regular $regex y el indice interno
     * del diccionario $diccionario.
     * 
     * @param array $estructura la estructura del array a generar
     * @param string $replace el nombre de la clave a reemplazar
     * @param string $regex la expresion regular para buscar
     * @param bool $valid si es true devuelve el primer valor que cumple con el regex, si es false devuelve el primer valor que no cumple con el regex
     * @param 'nombres' $diccionario el nombre del diccionario a utilizar
     * @param string $esenario el nombre del escenario para el reporte
     * @param array $ignoreCase array con los nombres de los escenarios que se deben ignorar
     * @return array el array generado con la estructura definida
     */
    public function generateArrayFromDic($estructura,$replace,$regex,bool $valid, $diccionario, string $esenario = "", $ignoreCase = [] ){
        /*
        $estructura = 
            [
                "nombre" => "gerente",
                "apellido" => "gerente",
                "resultado esperado" => true,
            ]
         */
        if($diccionario == "nombres") {
            $diccionario = $this->dicNombres;
            $indices = $this->indices["nombres"];
        }
        else {
            echo "diccionario no valido";
            return [];
        }
        $array = [];
        $contadorSeguridad = 0;
        while (
            ($valid ? $currentData = $this->getDicIterationValid($regex,$indices,$diccionario,$esenario, $ignoreCase) : $currentData = $this->getDicIterationInvalid($regex,$indices,$diccionario,$esenario, $ignoreCase))
        ) {
            $contadorSeguridad++;
            if($contadorSeguridad > 100) {
                echo "error de contador de seguridad";
                return [];
            };
            $arrayCopiaEstructura = $estructura;
            $arrayCopiaEstructura[$replace] = $currentData["valor"];
            $currentArray = [
                $currentData["esenario"] => [
                    ...$arrayCopiaEstructura
                ]
            ];
            $array[] = $currentArray;
            
        }
        return $array;
    }

    

}
            
?>