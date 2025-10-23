<?php
class Database
{
    private static ?Database $instance = null;
    private ?PDO $pdo;

    private string $host;
    private string $dbname;
    private string $user;
    private string $password;
    private string $charset;
    private bool $connected = false;
    private bool $isTestConnection = false;
    /**
     * 
     * @var 'normal'|'user' $last_instance
     * * string vacio
     * * 'normal'
     * * 'user'
     */
    public string $last_instance = '';

    private function __construct()
    {
        $this->host = DB_HOST;
        $this->dbname = DB_NAME;
        $this->user = DB_USER;
        $this->password = DB_PASSWORD;
        $this->charset = "utf8mb4";
        $this->last_instance = '';
    }
    
    public static function getInstance() : Database
    {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // para evitar que se pueda optener info del objeto
    public function __debugInfo(){
        return [];
    }

    public function pdo() : PDO
    {
        return $this->pdo;
    }

    public function connect() : bool
    {
        if ($this->isTestConnection && $this->connected) return true;
        try {
            $dns = "mysql:host=".$this->host.";dbname=".$this->dbname.";charset=".$this->charset;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
            $this->pdo = new PDO($dns, $this->user, $this->password, $options);
            $this->connected = true;
            $this->last_instance = "normal";
            
            return true;
        } catch (\PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    /**
     * Establece una conexión a la base de datos de usuarios usando las credenciales
     * y la configuración definidas en las constantes DB_USERS_HOST, DB_USERS_NAME,
     * DB_USERS_USER y DB_USERS_PASSWORD.
     *
     * @return bool Devuelve true si la conexión se establece correctamente.
     * @throws \PDOException Si ocurre un error durante la conexión, muestra el mensaje de error y detiene la ejecución.
     */

    public function connectUser() : bool
    {
        try {
            $dns = "mysql:host=".DB_USERS_HOST.";". DB_USERS_PORT."dbname=".DB_USERS_NAME.";charset=".$this->charset;
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
            $this->pdo = new PDO($dns, DB_USERS_USER, DB_USERS_PASSWORD, $options);
            $this->connected = true;
            $this->last_instance = "user";
            return true;
        } catch (\PDOException $e) {
            $resp = [
                "error" => $e->getMessage(),
                "code" => $e->getCode(),
                "trace" => $e->getTraceAsString()
            ];

            debug($resp);
            
            echo $e->getMessage();
            die();
        }
    }


    /**
     * Conecta a la base de datos correspondiente según el parámetro $userBD, y
     * devuelve un array con la conexión actual, la conexión anterior (si la hubiera),
     * y un booleano que indica si se debe desconectar después de usar la conexión.
     *
     * @param bool $userBD Indica si se debe conectar a la base de datos de usuarios o
     *                      a la base de datos principal.
     *
     * @return array [0 => $conexion_actual,1 => $conexion_anterior,2 => $desconectar_despues]
     *  - en la posición 0 se devuelve la conexión actual
     *  - en la posición 1 se devuelve la conexión anterior (si la hubiera)
     *  - en la posición 2 se devuelve un booleano que indica si se debe desconectar luego de usar la conexión
     * 
     *  * Ejemplo de uso:
     * ```php
     * $conexiones = $this->conectarYmantener(true);
     * $conexionActual = $conexiones['conexion_actual'];
     * $conexionAnterior = $conexiones['conexion_anterior'];
     * $desconectarDespues = $conexiones['desconectar_despues'];
     * ```
     */
    public function conectarYmantener($userBD = false) : array
    {
        $conectedBefore = $this->connected();
        $disconnectAfter = true;
        $auxiliarPDO = null;
    
        if ($userBD) {
            // si ya esta conectado a la base de datos de usuarios
            if ($conectedBefore && $this->last_instance == "user" ) {
                $disconnectAfter = false;
            }
            // si esta conectada pero no a la base de datos de usuarios
            elseif ($conectedBefore && $this->last_instance != 'user') {
                $auxiliarPDO = $this->pdo();
                $this->connectUser();
            }
            // si no esta conectada
            else {
                $this->connectUser();
            }
        } else {
            // si ya esta conectado a la base de datos principal
            if ($conectedBefore && $this->last_instance == 'normal') {
                $disconnectAfter = false;
            }
            // si esta conectada pero no a la base de datos principal
            elseif ($conectedBefore && $this->last_instance != 'normal') {
                $auxiliarPDO = $this->pdo();
                $this->connect();
            }
            // si no esta conectada
            else {
                $this->connect();
            }
        }
    
        return array(
            'conexion_actual' => $this->pdo(),
            'conexion_anterior' => $auxiliarPDO,
            'desconectar_despues' => $disconnectAfter
        );
    }

    public function disconnect() : void
    {
        if ($this->isTestConnection) return;
        $this->pdo = null;
        unset($this->pdo);
        $this->connected = false;
    }

    public function __serialize(): array
    {
        return array();
    }

    public function connected() : bool
    {
        return $this->connected;
    }

    /**
     * Exporta la base de datos a un archivo sql
     * 
     * La funcion exporta la base de datos actual a un archivo sql, 
     * La base de datos se define por la conexion 
     * si no se especifican las tablas, se exportaran las tablas por defecto.
     * Si se especifican las tablas, solo se 
     * exportaran las especificadas.
     * 
     * !Se espera que se utilize en el proceso de un metodo dedicado a manejar la exportacion e importacion¡
     * 
     * @param array $tables Un array con los nombres de las tablas que se 
     *                      desean exportar
     * @param bool $throwException Si es true, se lanzara una excepcion 
     *                             si ocurre un error en el proceso de
     *                             exportacion.
     * @return array Un array con el resultado de la operacion
     */
    public function exportDatabase(array $tables = [], bool $throwException = true) :array{
        try {

            // la conexión debe estar abierta previamente al llamado del importador
            if(!$this->connected()){
                throw new \Exception("La base de datos no se encuentra conectada", 1001);
            }
            if(!$this->pdo()->inTransaction()){
                throw new \Exception("La base de datos no se encuentra en transaccion", 1001);
            }

            $conn = $this->pdo();

            if(empty($tables)){
                $tables = [
                    "trabajador",
                    "ajuste",
                    "cargo",
                    "turno",
                    "area",

                    "categoria",
                    "division",
                    "fechaasistencia",
                    "medida",
                    "articulo",
                    "subarea",
                    "subdivisiones",


                    
                    
                    
                    
                    //"evaluacion",
                    
                    
                    
                    "asignacion_laboral",
                    "asistencia_inasistencia",
                    //"inasistencia",
                   // "asistencia",
                    "inventariohistorial",
                    "entrada",
                    
                    "entradadetalle",
                    
                    "tarea",
                    
                    "tarea_validacion",


                   
                    "tarea_personal",
                     "recurso"
                ];
            }


            $output = '';
            foreach ($tables as $table) {
                
                // Exportar datos
                //$output .= "\n-- Volcado de datos para tabla $table\n\n";

                
                $rows = $conn->query("SELECT * FROM `$table`");
                $insert = "";
                
                $valuesString = "";
                $counter = 0;
                while ($row = $rows->fetch(PDO::FETCH_ASSOC)) {
                    $values = array_map(function($value) use ($conn) {
                        if ($value === null) return 'NULL';
                        return $conn->quote($value);
                    }, array_values($row));
                    //$valuesString .= "\t(" . implode(',', $values) . "),\n";
                    $valuesString .= "(" . implode(',', $values) . "),\n";
                    $counter++;
                    if($counter >= 10) {

                        $insert .= "INSERT INTO `$table` VALUES\n";
                        $insert .= $valuesString;
                        $valuesString = "";



                        $insert = substr($insert, 0, -2);
                        $insert .= ";\n";
                        //$insert .= "\n";
                        $output .= $insert;

                        $insert = "";
                        $counter = 0;
                        
                    }
                }

                if($valuesString != "" ){

                    $insert .= "INSERT INTO `$table` VALUES\n";
                    $insert .= $valuesString;
                    $insert = substr($insert, 0, -2);
                    $insert .= ";\n";
                    // $insert .= "\n";
                    
                    $output .= $insert;
                }
                
            }

            $dbName = $conn->query("SELECT DATABASE()")->fetchColumn();

            $backup_name = "bd_backup/backup_" . $dbName . "_" . date("Y-m-d_H-i-s") . ".sql";
            file_put_contents($backup_name, $output);
            
            
            $resp = [
                "success" => true,
                "message" => "Base de datos exportada con exito"
            ];
            if(DEVELOPER_MODE) $resp["backup_name"] = $backup_name;
        } catch (\Throwable $e) {
            if($throwException) throw $e;

            $resp = [
                "success" => false,
                "message" => "Error al exportar la base de datos"
            ];
            if(DEVELOPER_MODE) $resp["trace"] = $e->getTraceAsString();
            if(!DEVELOPER_MODE) $resp["message"] = $e->getMessage();

        }
        return $resp;
    }


    /**
     * Importa una base de datos desde un archivo .sql
     * 
     * La conexión debe estar abierta previamente al llamado del importador
     * La base de datos debe estar en transacción
     * Si ocurre un error al importar, se lanzará una exception si throwException es true,
     * de lo contrario se devolverá un array con success en false y un mensaje de error
     * 
     * @param string $filePath ruta del archivo .sql a importar
     * @param array $tables un array con los nombres de las tablas que se desean importar este array se recorrera para vaciar las tablas antes de importar
     * @param bool $throwException si es true se lanzará una exception en caso de error, de lo contrario se devolverá un array con el error
     * @return array un array con success en true si se importó correctamente, o false con un mensaje de error
     */
    public function importDatabase(string $filePath, array $tables = [], bool $throwException = true) :array{
        try {
            // la conexión debe estar abierta previamente al llamado del importador
            if(!$this->connected()){
                throw new \Exception("La base de datos no se encuentra conectada", 1001);
            }
            if(!$this->pdo()->inTransaction()){
                throw new \Exception("La base de datos no se encuentra en transaccion", 1001);
            }
            $conn = $this->pdo();
            if(file_exists($filePath) == false){
                throw new \Exception("El archivo no existe", 1001);
            }


            $sql = file_get_contents($filePath);
            if(!$sql) {
                throw new \Exception("No se pudo leer el archivo", 1001);
            }

            if(empty($tables)){
                $tables = [
                    "trabajador",
                    "ajuste",
                    "cargo",
                    "turno",
                    "area",

                    "categoria",
                    "division",
                    "fechaasistencia",
                    "medida",
                    "articulo",
                    "tarea",
                    "tarea_validacion",
                    //"evaluacion",
                    "recurso",
                    "subarea",
                    "subdivisiones",
                    "asignacion_laboral",
                    "tarea_personal",
                    "asistencia_inasistencia",
                    "inasistencia",
                    "asistencia",
                    "inventariohistorial",
                    "entrada",
                    "entradadetalle"
                ];
                $tables = array_reverse($tables);
            }



            foreach ($tables as $table) {
                if(!preg_match('/^[a-zA-Z_]+$/', $table)){
                    throw new \Exception("El nombre de la tabla $table no es valido", 1001);
                }
             //   echo "Vaciando la tabla $table,\n";
                $conn->prepare("DELETE FROM `$table` WHERE 1")->execute();
            }

            

            $querys = explode(';', $sql);
            foreach($querys as $query){
                if (trim($query) != '') {
                   // echo "<br>----------------------------------------------------------------";
                   // echo "<br>";
                  //  echo $query;
                    $conn->exec($query);
                }
            }
            
            
            $resp = [
                "success" => true,
                "message" => "Base de datos importada con exito"
            ];
            
        } catch (\Throwable $th) {
            if($throwException) throw $th;
            $resp = [
                "success" => false,
                "message" => "La base de datos no pudo ser importada: "
            ];
            if($th->getCode() == 1001){
                $resp["message"] = "La base de datos no pudo ser importada: ".$th->getMessage();
            }
            if(DEVELOPER_MODE){
                $resp["trace"] = $th->getTraceAsString();
                $resp["message"] .= $th->getMessage();
            } 

        }
        return $resp;
    }

    public function set_pdo(\PDO $pdo){
        $this->pdo = $pdo;
        $this->connected = true;
    }
    public function set_connected(bool $connected){
        $this->connected = $connected;
    }

    public function setTestConnection(bool $isTestConnection){
        $this->isTestConnection = $isTestConnection;
    }

    public function isTestConnection() {
        return $this->isTestConnection;
    }
}
// TODO manejar error de la conexión por try-catch internamente