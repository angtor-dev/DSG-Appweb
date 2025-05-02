<?php
require_once "Models/Model.php";
require_once "Models/Rol.php";
require_once "Models/Bitacora.php";

class Usuario extends Model
{
    public string|null $idUsuario = null;
    public int|null $idRol = null;
    private string|null $correo = null;
    private int|string|null $estado = null;
    private string|null $clave = null;
    public Rol|int|null  $rol = null;
    public Trabajador|null $trabajador = null;
    private string|null $cedula = null;
    private string|null $idTrabajador = null;
    
    
    public function __construct(
        string $correo = null,
        string $estado = null,
        Rol|int $rol = null,
        string $idTrabajador = null
    )
    {
        parent::__construct();
        $this->correo = $correo ?? $this->correo;
        $this->estado = $estado ?? $this->estado;
        $this->rol =  $rol ?? $this->rol;
        if (!empty($this->idRol)) {
            $this->rol = Rol::cargar($this->idRol);
        }
        if(!empty($this->idTrabajador)) {
            $this->trabajador = Trabajador::cargar($this->idTrabajador);
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

    public static function cargarPorCedula(string $cedula, int $estado = 1) : Usuario | null
    {
        $bd = Database::getInstance();
        $query = "SELECT u.*, t.id as idTrabajador FROM Trabajador as t left join usuario as u on t.id = u.id WHERE t.cedula = :cedula AND u.estado = :estado";

        $bd->connect();
        
        $stmt = $bd->pdo()->prepare($query);
        $stmt->bindValue("cedula", $cedula);
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
        

        $query = "INSERT INTO usuario (idTrabajador, idRol, correo, clave)
            VALUES (:idTrabajador, :idRol, :correo, :clave)";
            
        try {
            $this->db->connect();

            //$this->db->pdo()->beginTransaction();

            $stmt = $this->prepare($query);
            $stmt->bindValue("idRol", $this->idRol);
            $stmt->bindValue("correo", $this->correo);
            $stmt->bindValue("clave", password_hash($this->clave, PASSWORD_DEFAULT));
            $stmt->bindValue("idTrabajador", Trabajador::cargarPorCedula($this->cedula)->id);

            $stmt->execute();

            $this->db->disconnect();

            return true;
        } catch (\Throwable $th) {
            $_SESSION['errores'][] = "Ocurrio un error al registrar a el usuario";
            $_SESSION['consoleError'][] = "`".addslashes($th->getMessage())." :: File:".addslashes($th->getFile())." :: Linea:".addslashes($th->getLine())."`"; // "`". $th->getMessage()."\nFile:".$th->getFile()."\nLinea:".$th->getLine()."`";
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
            $this->cedula = $_POST['cedula'];
            $this->idRol = $_POST['idRol'];
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
    public function getCorreo() : string {
        return $this->correo;
    }
    
    public function getEstado() : int {
        return $this->estado;
    }
    public function getNombre() : string {
        if($this->trabajador instanceof Trabajador) return $this->trabajador->getNombre();
        return "NO_NAME";
    }
    public function getNombreCompleto() : string {
        if($this->trabajador instanceof Trabajador) return $this->trabajador->getNombreCompleto();
        return null;
    }
    public function getTrabajador() : Trabajador {
        return $this->trabajador;
    }
}