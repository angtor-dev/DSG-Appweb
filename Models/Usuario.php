<?php
require_once "Models/Model.php";
require_once "Models/Rol.php";
require_once "Models/Bitacora.php";

class Usuario extends Model
{
    public int $idRol;
    private string $correo;
    private string $nombre;
    private string $apellido;
    private int $estado;
    private string $clave;
    public ?Rol $rol;
    
    public function __construct()
    {
        parent::__construct();
        if (!empty($this->idRol)) {
            $this->rol = Rol::cargar($this->idRol);
        }
    }

    public static function login(string $correo, string $clave) : bool
    {
        if (empty($correo) || empty($clave)) {
            return false;
        }

        $usuario = Usuario::cargarPorCorreo($correo);

        if (is_null($usuario) || !$usuario->validarClave($clave)) {
            return false;
        }

        session_start();
        $_SESSION['usuario'] = $usuario;

        return true;
    }

    private function validarClave(string $clave) : bool
    {
        return password_verify($clave, $this->clave);
    }

    public static function cargarPorCorreo(string $correo, int $estado = 1) : Usuario | null
    {
        $bd = Database::getInstance();
        $query = "SELECT * FROM usuario WHERE correo = :correo AND estado = :estado";

        $bd->connect();
        
        $stmt = $bd->pdo()->prepare($query);
        $stmt->bindValue("correo", $correo);
        $stmt->bindValue("estado", $estado);

        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Usuario");

        $bd->disconnect();

        if ($stmt->rowCount() == 0) {
            return null;
        }
        return $stmt->fetch();
    }

    /** @return array<self> */
    public static function listarPorRol(int $idRol, int $estado = null) : array
    {
        $bd = Database::getInstance();
        $query = "SELECT * FROM usuario WHERE idRol = $idRol" . (isset($estado) ? " AND estado = $estado" : "");

        $bd->connect();

        $stmt = $bd->pdo()->query($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Usuario");

        $bd->disconnect();

        if ($stmt->rowCount() == 0) {
            return array();
        }
        return $stmt->fetchAll();
    }

    public function registrar() : bool
    {
        $query = "INSERT INTO usuario (idRol, nombre, apellido, correo, clave)
            VALUES (:idRol, :nombre, :apellido, :correo, :clave)";
            
        try {
            $this->db->connect();

            $stmt = $this->prepare($query);
            $stmt->bindValue("idRol", $this->idRol);
            $stmt->bindValue("nombre", $this->nombre);
            $stmt->bindValue("apellido", $this->apellido);
            $stmt->bindValue("correo", $this->correo);
            $stmt->bindValue("clave", password_hash($this->clave, PASSWORD_DEFAULT));

            $stmt->execute();

            $this->db->disconnect();

            return true;
        } catch (\Throwable $th) {
            $_SESSION['errores'][] = "Ocurrio un error al registrar a el usuario";
            return false;
        }
    }

    public function actualizar() : bool
    {
        $query = "UPDATE usuario SET idRol = :idRol, nombre = :nombre,
            apellido = :apellido, correo = :correo WHERE id = :id";
            
        try {
            $this->db->connect();
            
            $stmt = $this->prepare($query);
            $stmt->bindValue("idRol", $this->idRol);
            $stmt->bindValue("nombre", $this->nombre);
            $stmt->bindValue("apellido", $this->apellido);
            $stmt->bindValue("correo", $this->correo);
            $stmt->bindValue("id", $this->id);

            $stmt->execute();

            $this->db->disconnect();

            return true;
        } catch (\Throwable $th) {
            if (DEVELOPER_MODE) debug($th);
            $_SESSION['errores'][] = "Ocurrio un error al actualizar a el usuario";
            return false;
        }
    }

    public function mapearFormulario() : bool
    {
        try {
            $this->idRol = $_POST['idRol'];
            $this->nombre = $_POST['nombre'];
            $this->apellido = $_POST['apellido'];
            $this->correo = $_POST['correo'];
            if (!empty($_POST['id'])) {
                $this->id = $_POST['id'];
            } else {
                $this->clave = $_POST['clave'];
            }

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function esValido() : bool
    {
        if (empty(trim($this->nombre))) {
            $_SESSION['errores'][] = "El campo 'Nombre' es obligatorio";
            return false;
        }
        if (!preg_match(REG_ALFANUMERICO, $this->nombre)) {
            $_SESSION['errores'][] = "El campo 'Nombre' solo puede contener letras y números";
            return false;
        }
        if (empty(trim($this->apellido))) {
            $_SESSION['errores'][] = "El campo 'Apellido' es obligatorio";
            return false;
        }
        if (!preg_match(REG_ALFANUMERICO, $this->apellido)) {
            $_SESSION['errores'][] = "El campo 'Apellido' solo puede contener letras y números";
            return false;
        }
        return true;
    }

    // Getters
    public function getNombreCompleto() : string {
        return $this->nombre." ".$this->apellido;
    }
    
    public function getCorreo() : string {
        return $this->correo;
    }
    public function getNombre() : string {
        return $this->nombre;
    }
    public function getApellido() : string {
        return $this->apellido;
    }
    public function getEstado() : int {
        return $this->estado;
    }
}