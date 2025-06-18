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

    private function __construct()
    {
        $this->host = DB_HOST;
        $this->dbname = DB_NAME;
        $this->user = DB_USER;
        $this->password = DB_PASSWORD;
        $this->charset = "utf8mb4";
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
        try {
            $dns = "mysql:host=".$this->host.";dbname=".$this->dbname.";charset=".$this->charset;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
            $this->pdo = new PDO($dns, $this->user, $this->password, $options);
            $this->connected = true;
            
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
            if ($conectedBefore && checkHostBD($this->pdo(), DB_USERS_HOST)) {
                $disconnectAfter = false;
            }
            // si esta conectada pero no a la base de datos de usuarios
            elseif ($conectedBefore && !checkHostBD($this->pdo(), DB_USERS_HOST)) {
                $auxiliarPDO = $this->pdo();
                $this->connectUser();
            }
            // si no esta conectada
            else {
                $this->connectUser();
            }
        } else {
            // si ya esta conectado a la base de datos principal
            if ($conectedBefore && checkHostBD($this->pdo(), DB_HOST)) {
                $disconnectAfter = false;
            }
            // si esta conectada pero no a la base de datos principal
            elseif ($conectedBefore && !checkHostBD($this->pdo(), DB_HOST)) {
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


    public function set_pdo(\PDO $pdo){
        $this->pdo = $pdo;
        $this->connected = true;
    }
}
// TODO manejar error de la conexión por try-catch internamente