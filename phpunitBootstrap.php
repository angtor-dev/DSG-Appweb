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

/**
 * Modifica la sesion para que el usuario tenga un rol falso para las pruebas
 * @param int $type tipo de usuario a crear
 * - "1" super admin 
 * - "2" sin permiso de eliminar
 * - "3" sin permiso de actualizar
 * - "4" sin permiso de actualizar-eliminar
 * @return bool true si se logro crear el usuario, false en caso contrario
 */
    function getUserFalseInsesion($type = 1): bool{
        // modificar a conveniencia
        $ok = false;
        switch ($type) {
            case 1:
                $_SESSION['usuario'] = new Usuario(null,null,Rol::cargar(1,true)); // usuario con el rol de super admin
                $ok = true;
            break;
            case 2:
                $_SESSION['usuario'] = new Usuario(null,null,Rol::cargar(15,true)); // usuario con rol sin permiso de eliminar
                $ok = true;
            break;
            case 3:
                $_SESSION['usuario'] = new Usuario(null,null,Rol::cargar(16,true)); // usuario con rol sin permiso de actualizar
                $ok = true;
            break;
            case 4:
                $_SESSION['usuario'] = new Usuario(null,null,Rol::cargar(17,true)); // usuario con rol sin permiso de actualizar-eliminar
                $ok = true;
            break;
            
            default:
                $ok = false;
                break;
        }
        return $ok;

    }
?>