<?php
require_once "Models/Model.php";

class Bitacora extends Model
{
    private ?int $idUsuario;
    private string $registro;
    private string $ruta;
    private string $fecha;

    public ?Usuario $usuario = null;

    function __construct()
    {
        parent::__construct();
        if (!empty($this->idUsuario)) {
            $this->usuario = Usuario::cargar($this->idUsuario);
        }
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

        $db->connect();

        $stmt = $db->pdo()->prepare($query);
        $stmt->bindParam('registro', $registro);
        $stmt->bindParam('ruta', $ruta);

        $stmt->execute();

        $db->disconnect();
    }

    // Override para impedir eliminar
    public function eliminar(bool $eliminadoLogico = true) : bool
    {
        $_SESSION['errores'][] = "No se puede eliminar un registro de la bitacora";
        return false;
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
}