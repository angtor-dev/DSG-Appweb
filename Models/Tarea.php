<?php
require_once "Models/Area.php";
require_once "Models/Departamento.php";

class Tarea extends Model
{
    // Propiedades públicas
    public int $id;
    public int $idArea;
    public int $idDepartamento;
    public string $descripcion;
    public string $fechaCreacion;
    public string $estado_tarea = 'activo';
    public bool $es_comun = false;
    public string $turno;
    public string $fecha_inicio;
    
    public ?Area $area = null;
    public ?Departamento $departamento = null;

     public function __construct() {
        parent::__construct();
        // Inicialización condicional
        if (!empty($this->idArea)) {
            $this->area = Area::cargar($this->idArea);
        }
        if (!empty($this->idDepartamento)) {
            $this->departamento = Departamento::cargar($this->idDepartamento);
        }
    }
    
    private array $personalAsignado = [];
    private array $materiales = [];

    // Métodos públicos

    /**
     * Registra una nueva tarea en el sistema
     * @param array $datos Datos del formulario
     * @return bool True si se registró correctamente
     */
    public function registrar(array $datos): bool 
    {
        $this->db->connect();
        $this->db->pdo()->beginTransaction();

        try {
            // Validación y mapeo de datos
            $this->mapearDatos($datos);
            
            if (!$this->esValido()) {
                throw new Exception("Datos de tarea inválidos");
            }

            // Guardar la tarea principal
            $this->id = $this->guardarTarea();

            // Asignar personal si no es tarea común
            if (!$this->es_comun && !empty($this->personalAsignado)) {
                $this->asignarPersonal($this->personalAsignado);
            }

            // Asignar materiales si existen
            if (!empty($this->materiales)) {
                $this->asignarMateriales($this->materiales);
            }

            $this->db->pdo()->commit();
            return true;

        } catch (\Throwable $th) {
            $this->db->pdo()->rollBack();
            $_SESSION['errores'][] = $th->getMessage();
            error_log("Error al registrar tarea: " . $th->getMessage());
            return false;
        } finally {
            $this->db->disconnect();
        }
    }

    /**
     * Mapea los datos del formulario a las propiedades del modelo
     * @param array $datos Datos del formulario
     */
    public function mapearDatos(array $datos): void
    {
        $this->idArea = (int)$datos['idArea'];
        $this->idDepartamento = (int)$datos['idDepartamento'];
        $this->descripcion = trim($datos['descripcion']);
        $this->es_comun = ($datos['tipoTarea'] ?? 'normal') === 'comun';
        $this->turno = $datos['turno'];
        $this->fecha_inicio = $datos['fecha_inicio'];
        
        if (!$this->es_comun && isset($datos['personal'])) {
            $this->personalAsignado = (array)$datos['personal'];
        }
        
        if (isset($datos['materiales'])) {
            $this->materiales = (array)$datos['materiales'];
        }
    }

    /**
     * Valida los datos de la tarea
     * @return bool True si los datos son válidos
     */
    public function esValido(): bool
    {
        $valido = true;
        
        if (empty($this->idArea)) {
            $_SESSION['errores'][] = "El campo 'Área' es obligatorio";
            $valido = false;
        }
        
        if (empty($this->idDepartamento)) {
            $_SESSION['errores'][] = "El campo 'Departamento' es obligatorio";
            $valido = false;
        }
        
        if (empty($this->descripcion)) {
            $_SESSION['errores'][] = "El campo 'Descripción' es obligatorio";
            $valido = false;
        }
        
        if (empty($this->turno)) {
            $_SESSION['errores'][] = "El campo 'Turno' es obligatorio";
            $valido = false;
        }
        
        if (empty($this->fecha_inicio)) {
            $_SESSION['errores'][] = "El campo 'Fecha de inicio' es obligatorio";
            $valido = false;
        }
        
        if (!$this->es_comun && empty($this->personalAsignado)) {
            $_SESSION['errores'][] = "Debe seleccionar al menos un trabajador";
            $valido = false;
        }
        
        return $valido;
    }

    // Métodos privados

    /**
     * Guarda la tarea principal en la base de datos
     * @return int ID de la tarea recién creada
     * @throws Exception Si no se puede guardar la tarea
     */
    private function guardarTarea(): int
    {
        $query = "INSERT INTO tarea 
                 (idArea, idDepartamento, descripcion, fechaCreacion, estado_tarea, es_comun) 
                 VALUES (:idArea, :idDepartamento, :descripcion, NOW(), :estado, :es_comun)";

        $stmt = $this->db->pdo()->prepare($query);
        $stmt->bindValue(":idArea", $this->idArea, PDO::PARAM_INT);
        $stmt->bindValue(":idDepartamento", $this->idDepartamento, PDO::PARAM_INT);
        $stmt->bindValue(":descripcion", $this->descripcion);
        $stmt->bindValue(":estado", $this->estado_tarea);
        $stmt->bindValue(":es_comun", $this->es_comun, PDO::PARAM_BOOL);

        
        if (!$stmt->execute()) {
            throw new Exception("No se pudo guardar la tarea principal");
        }

        return $this->db->pdo()->lastInsertId();
    }

    /**
     * Asigna personal a la tarea
     * @param array $idsTrabajadores IDs de los trabajadores a asignar
     * @throws Exception Si no se puede asignar el personal
     */
    private function asignarPersonal(array $idsTrabajadores): void 
    {
        if (empty($idsTrabajadores)) {
            return;
        }

        $query = "INSERT INTO tarea_personal (idTarea, idTrabajador) VALUES ";
        $placeholders = [];
        $values = [":idTarea" => $this->id];
        
        foreach ($idsTrabajadores as $i => $id) {
            $placeholders[] = "(:idTarea, :idTrabajador_$i)";
            $values[":idTrabajador_$i"] = (int)$id;
        }
        
        $stmt = $this->db->pdo()->prepare($query . implode(", ", $placeholders));
        
        if (!$stmt->execute($values)) {
            throw new Exception("No se pudo asignar el personal a la tarea");
        }
    }

    /**
     * Asigna materiales a la tarea
     * @param array $materiales Array de materiales con sus cantidades
     * @throws Exception Si no se pueden asignar los materiales
     */
    private function asignarMateriales(array $materiales): void 
    {
        if (empty($materiales)) {
            return;
        }

        $query = "INSERT INTO tarea_material (idTarea, idMaterial, cantidad) VALUES ";
        $placeholders = [];
        $values = [":idTarea" => $this->id];
        
        foreach ($materiales as $i => $material) {
            $placeholders[] = "(:idTarea, :idMaterial_$i, :cantidad_$i)";
            $values[":idMaterial_$i"] = (int)$material['id'];
            $values[":cantidad_$i"] = (int)($material['cantidad'] ?? 1);
        }
        
        $stmt = $this->db->pdo()->prepare($query . implode(", ", $placeholders));
        
        if (!$stmt->execute($values)) {
            throw new Exception("No se pudieron asignar los materiales a la tarea");
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

    /* public function listar(int $estado = null) : array {
        $bd = Database::getInstance();
        $bd->connect();
        $query = "SELECT * FROM `tarea` ORDER BY fechaCreacion DESC;";

        $consulta = $bd->pdo()->prepare($query);
        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_CLASS, "Tarea");

        $bd->disconnect();

        return $consulta->fetchAll();
    } */
    public function listarPorEstado($estado) {

        $bd = Database::getInstance();
        $bd->connect();
        $query = "SELECT * FROM `tarea` WHERE estado_tarea = :estado ORDER BY fechaCreacion DESC;";

        $consulta = $bd->pdo()->prepare($query);
        $consulta->execute([':estado' => $estado]);
        $consulta->setFetchMode(PDO::FETCH_CLASS, "Tarea");

        $bd->disconnect();

        return $consulta->fetchAll();
    }

    public static function obtenerPorId($id) {
        $bd = Database::getInstance();
        $bd->connect();
        
        try {
            $pdo = $bd->pdo();
            // Obtener datos básicos de la tarea
            $query = "SELECT t.*, a.nombre as area_nombre, d.nombre as departamento_nombre 
                      FROM tarea t
                      LEFT JOIN area a ON t.idArea = a.id
                      LEFT JOIN departamento d ON t.idDepartamento = d.id
                      WHERE t.id = :id";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute([':id' => $id]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, "Tarea");
            $tarea = $stmt->fetch();
            
            if (!$tarea) {
                return null;
            }
            
            // Obtener personal asignado
            $queryPersonal = "SELECT tp.idTrabajador, tr.nombre, tr.apellido, d.nombre as departamento
                              FROM tarea_personal tp
                              JOIN trabajador tr ON tp.idTrabajador = tr.id
                              JOIN departamento d ON tr.idDepartamento = d.id
                              WHERE tp.idTarea = :idTarea";
            
            $stmt = $pdo->prepare($queryPersonal);
            $stmt->execute([':idTarea' => $id]);
            $tarea->personal = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            // materiales y comentarios
            
            return $tarea;
            
        } finally {
            $bd->disconnect();
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

    public function getEstado() : string {
        return $this->estado_tarea ?? 'Desconocido';
    }
}