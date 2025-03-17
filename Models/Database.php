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
            
            return true;
        } catch (\PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    public function disconnect() : void
    {
        unset($this->pdo);
    }

    public function __serialize(): array
    {
        return array();
    }
}