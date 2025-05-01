<?php
require_once "Models/Area.php";
require_once "Models/Departamento.php";

class Tarea extends Model
{
    public int $id;
    public int $idArea;
    public int $idDepartamento;
    public string $descripcion;
    public string $fechaCreacion;
    public Area $area;
    public Departamento $departamento;
    public ?TareaAutomatica $tareaAutomatica;

    public function __construct() {
        parent::__construct();
        if (!empty($this->idArea)) {
            $this->area = Area::cargar($this->idArea);
        }
        if (!empty($this->idDepartamento)) {
            $this->departamento = Departamento::cargar($this->idDepartamento);
        }
        if (!empty($this->id)) {
            $this->tareaAutomatica = TareaAutomatica::cargarPorTarea($this->id);
        }
    }

    // Método compatible con Model::cargar()
    public static function cargar(int $id) : null|self
    {
        $bd = Database::getInstance();
        $bd->connect();
        $query = "SELECT * FROM `tarea` WHERE id = :id;";

        $consulta = $bd->pdo()->prepare($query);
        $consulta->execute([':id' => $id]);
        $consulta->setFetchMode(PDO::FETCH_CLASS, "Tarea");

        $bd->disconnect();

        if ($consulta->rowCount() == 0) {
            return null;
        }

        return $consulta->fetch();
    }

    public static function listar(...$args) : array {
        $bd = Database::getInstance();
        $bd->connect();
        $query = "SELECT * FROM `tarea` ORDER BY fechaCreacion DESC;";

        $consulta = $bd->pdo()->prepare($query);
        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_CLASS, "Tarea");

        $bd->disconnect();

        return $consulta->fetchAll();
    }

    public function mapearFormulario() : bool {
        try {
            $this->idArea = $_POST['idArea'];
            $this->idDepartamento = $_POST['idDepartamento'];
            $this->descripcion = $_POST['descripcion'];
            
            if (isset($_POST['esAutomatica']) && $_POST['esAutomatica'] == '1') {
                $this->tareaAutomatica = new TareaAutomatica();
                $this->tareaAutomatica->numTrabajadores = $_POST['numTrabajadores'];
                $this->tareaAutomatica->tiempoEstimado = $_POST['tiempoEstimado'];
            }
            
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function esValido() : bool {
        $valido = true;
        
        if (empty($this->idArea)) {
            $_SESSION['errores'][] = "El campo 'Área' es obligatorio";
            $valido = false;
        }
        
        if (empty($this->idDepartamento)) {
            $_SESSION['errores'][] = "El campo 'Departamento' es obligatorio";
            $valido = false;
        }
        
        if (empty(trim($this->descripcion))) {
            $_SESSION['errores'][] = "El campo 'Descripción' es obligatorio";
            $valido = false;
        }
        
        if (isset($this->tareaAutomatica)) {
            if (empty($this->tareaAutomatica->numTrabajadores) || $this->tareaAutomatica->numTrabajadores <= 0) {
                $_SESSION['errores'][] = "El número de trabajadores debe ser mayor que cero";
                $valido = false;
            }
            
            if (empty($this->tareaAutomatica->tiempoEstimado) || $this->tareaAutomatica->tiempoEstimado <= 0) {
                $_SESSION['errores'][] = "El tiempo estimado debe ser mayor que cero";
                $valido = false;
            }
        }
        
        return $valido;
    }

    public function registrar() : bool {
        $this->db->connect();
        $this->db->pdo()->beginTransaction();
        
        try {
            $queryTarea = "INSERT INTO tarea (idArea, idDepartamento, descripcion, fechaCreacion) 
                          VALUES (:idArea, :idDepartamento, :descripcion, NOW());";
            
            $stmt = $this->db->pdo()->prepare($queryTarea);
            $stmt->bindValue(":idArea", $this->idArea);
            $stmt->bindValue(":idDepartamento", $this->idDepartamento);
            $stmt->bindValue(":descripcion", $this->descripcion);
            $stmt->execute();
            
            $idTarea = $this->db->pdo()->lastInsertId();
            
            if (isset($this->tareaAutomatica)) {
                $queryAutomatica = "INSERT INTO tareaautomatica (idTarea, numTrabajadores, tiempoEstimado)
                                  VALUES (:idTarea, :numTrabajadores, :tiempoEstimado);";
                
                $stmt = $this->db->pdo()->prepare($queryAutomatica);
                $stmt->bindValue(":idTarea", $idTarea);
                $stmt->bindValue(":numTrabajadores", $this->tareaAutomatica->numTrabajadores);
                $stmt->bindValue(":tiempoEstimado", $this->tareaAutomatica->tiempoEstimado);
                $stmt->execute();
            }
            
            $this->db->pdo()->commit();
            $this->db->disconnect();
            return true;
            
        } catch (\Throwable $th) {
            $this->db->pdo()->rollBack();
            $this->db->disconnect();
            
            $_SESSION['errores'][] = "Ocurrió un error al registrar la tarea: " . $th->getMessage();
            return false;
        }
    }

    public function getId() : int {
        return $this->id;
    }
    
    public function getDescripcion() : string {
        return $this->descripcion;
    }
    
    public function getFechaCreacion() : string {
        return $this->fechaCreacion;
    }
    
    public function esAutomatica() : bool {
        return isset($this->tareaAutomatica);
    }
}

class TareaAutomatica extends Model 
{
    public int $id;
    public int $idTarea;
    public int $numTrabajadores;
    public int $tiempoEstimado;
    
    public static function cargarPorTarea(int $idTarea) : ?TareaAutomatica {
        $bd = Database::getInstance();
        $bd->connect();
        $query = "SELECT * FROM `tareaautomatica` WHERE idTarea = :idTarea;";

        $consulta = $bd->pdo()->prepare($query);
        $consulta->execute([':idTarea' => $idTarea]);
        $consulta->setFetchMode(PDO::FETCH_CLASS, "TareaAutomatica");

        $bd->disconnect();

        if ($consulta->rowCount() == 0) {
            return null;
        }

        return $consulta->fetch();
    }
}