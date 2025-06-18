<?php

use PhpParser\Node\Expr\Isset_;
class Bitacora extends Model
{
    private ?int $idUsuario;
    private string $registro;
    private string $ruta;
    private string $fecha;
    private string|null $usuario_correo;

    public ?Trabajador $usuario = null;

    function __construct()
    {
        parent::__construct();
    }

    public function listar(int $estado = null) : Array
    {
        $query = "SELECT b.*, u.cedula AS 'usuario_cedula', u.correo as 'usuario_correo'
            FROM bitacora as b
            LEFT JOIN usuario as u ON b.idUsuario = u.id WHERE b.fecha >= DATE_SUB(NOW(), INTERVAL 6 MONTH) ";
        $this->db->connectUser();

        $stmt = $this->db->pdo()->query($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Bitacora');

        $this->db->disconnect();

        if ($stmt->rowCount() == 0) {
            return array();
        }
        $bitacoras = $stmt->fetchAll();

        foreach ($bitacoras as $bitacora) {
            
            $cedula = $bitacora->usuario_cedula;
            if(!empty($cedula)){
                $bitacora->usuario = Trabajador::cargarPorCedula($cedula);
            }
        }
        return $bitacoras;
    }

    /**
     * Registra una accion en la bitacora
     * 
     * @param string $registro accion/actividad a registrar
     */
    public static function registrar(string $registro) : void
    {
        global $requestUri;

        $db = Database::getInstance();
        $idUsuario = !empty($_SESSION['usuario']->id) ? $_SESSION['usuario']->id : "NULL";
        $ruta = $requestUri."/";

        $query = "INSERT INTO bitacora(idUsuario, registro, ruta)
            VALUES($idUsuario, :registro, :ruta)";

        $db->connectUser();

        $stmt = $db->pdo()->prepare($query);
        $stmt->bindParam('registro', $registro);
        $stmt->bindParam('ruta', $ruta);

        $stmt->execute();

        $db->disconnect();
    }

    public static function registrarTransaccion(string $registro,\PDO $pdo) 
    {
        try {
            global $requestUri;
    
            $db = Database::getInstance();
            $auxiliarPDO = $pdo;
            $db->connectUser();
            //$db->connectUser();
            $pdo = $db->pdo();
            $pdo->beginTransaction();
            $idUsuario = !empty($_SESSION['usuario']->id) ? $_SESSION['usuario']->id : "NULL";
            $ruta = $requestUri."/";
            
            $query = "INSERT INTO bitacora(idUsuario, registro, ruta)
            VALUES($idUsuario, :registro, :ruta)";
    
            $stmt = $pdo->prepare($query);
            $stmt->bindParam('registro', $registro);
            $stmt->bindParam('ruta', $ruta);
    
            $stmt->execute();
    
            $pdo->commit();
            $db->disconnect();
            $db->set_pdo($auxiliarPDO);
            
        } catch (\Throwable $th) {

            if(isset($db) and $db->connected() and $db->pdo()->inTransaction()){
                $db->pdo()->rollBack();
                $db->disconnect();
            }
            throw $th;
        }
        
    }

    // Override para impedir eliminar
    public function eliminar(bool $eliminadoLogico = true) : bool
    {
        $_SESSION['errores'][] = "No se puede eliminar un registro de la bitacora";
        return false;
    }

    /**
     * Establece valores en propiedades de la clase.
     *
     * Recibe un array asociativo clave-valor y asigna los valores a las
     * propiedades correspondientes. Si la propiedad existe como setter, llama
     * al setter. Si la propiedad existe como propiedad de lectura y escritura,
     * asigna el valor directamente.
     *
     * @param array $data
     * @return void
     */
    public function setterArray(array $data) : void
    {
        // comentar en español
        foreach ($data as $key => $value) {
            $propiedad = $key;
            $setterMethod = 'set_' . $propiedad;
            if(method_exists($this, $setterMethod)){
                $this->$setterMethod($value);
            } elseif(property_exists($this, $propiedad)){
                $this->$propiedad = $value;
            }
        }
    }

    // Getters
    public function getRegistro() : string {
        return $this->registro;
    }
    public function getRuta() : string {
        return $this->ruta;
    }
    public function getFecha() : string {
        return $this->fecha;
    }
    public function getUsuario_correo() : string|null {
        return $this->usuario_correo;
    }
}