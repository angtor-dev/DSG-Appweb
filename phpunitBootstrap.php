<?php 

    require_once __DIR__ . '/vendor/autoload.php';
    require_once __DIR__ . '/test/logger.php';
    interface PhpunitBootstrapMethods
    {
        /**
         * devuelve un array con los datos del dataset
         * @return array
         */
        public function getProvidedData(): array;

        /**
         * devuelve el nombre del dataset como string
         * @param bool $includeData  defalut true si se incluyen los datos 
         * @return void
         */
        public function getDataSetAsString(bool $includeData = true): string;

        /**
         * Obtener el numero de aserciones
         * @return int
         */
        public function getNumAssertions():int;
        /**
         * optiene el nombre del metodo de pueba
         * @param bool $withDataSet default true con el dataset
         * @return void
         */
        public function getName(bool $withDataSet = true): string;

        public function dataName();
        
    }
?>