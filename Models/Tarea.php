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
    public string $idSupervisor;
    public bool $es_comun = false;
    public string $turno;
    public ?string $fecha_inicio = null;
    //-------Evaluar
    public $idAsignacion;
    public $tipo;
    public $evaluacion;

    public $evaluacionDirector;
    public $observaciones;
    public $aprobado;

    public ?Area $area = null;
    public ?Departamento $departamento = null;

    public function __construct()
    {
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
    /** @var array $materiales */
    public array $materiales = [];

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
        $this->idSupervisor = (int)$datos['supervisor'] ?? 0;


        if (!$this->es_comun && isset($datos['personal'])) {
            $this->personalAsignado = (array)$datos['personal'];
        }

        if (isset($datos['materiales'])) {
            if (is_string($datos['materiales'])) {
                $this->materiales = json_decode($datos['materiales'], true);
            } else {
                $this->materiales = (array)$datos['materiales'];
            }
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
                 (idArea, idDepartamento, idSupervisor, descripcion, fecha_inicio, estado_tarea, es_comun) 
                 VALUES (:idArea, :idDepartamento,:idSupervisor, :descripcion,:fecha_inicio, :estado, :es_comun)";

        $stmt = $this->db->pdo()->prepare($query);
        $stmt->bindValue(":idArea", $this->idArea, PDO::PARAM_INT);
        $stmt->bindValue(":idDepartamento", $this->idDepartamento, PDO::PARAM_INT);
        $stmt->bindValue(":descripcion", $this->descripcion);
        $stmt->bindValue(":estado", $this->estado_tarea);
        $stmt->bindValue(":es_comun", $this->es_comun, PDO::PARAM_BOOL);
        $stmt->bindValue(":fecha_inicio", $this->fecha_inicio);
        $stmt->bindValue(":idSupervisor", $this->idSupervisor, PDO::PARAM_INT);



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
        $values = [];

        foreach ($idsTrabajadores as $i => $id) {
            $idParam = ":idTarea_" . $i;
            $trabajadorParam = ":trabajador_" . $i;

            $placeholders[] = "($idParam, $trabajadorParam)";
            $values[$idParam] = (int)$this->id;
            $values[$trabajadorParam] = (int)$id;
        }

        $sql = $query . implode(", ", $placeholders);

        $stmt = $this->db->pdo()->prepare($sql);

        if (!$stmt->execute($values)) {
            $error = $stmt->errorInfo();
            throw new Exception("Error al asignar personal: " . $error[2]);
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

        $query = "INSERT INTO recurso (idTarea, idInventario, cantidad, devolucion, cantidadDevolucion) VALUES ";
        $placeholders = [];
        $values = [];

        foreach ($materiales as $i => $material) {
            $idTareaParam = ":idTarea_$i";
            $idInventarioParam = ":idInventario_$i";
            $cantidadParam = ":cantidad_$i";
            $devolucionParam = ":devolucion_$i";
            $cantidadDevolucionParam = ":cantidadDev_$i";

            $placeholders[] = "($idTareaParam, $idInventarioParam, $cantidadParam, $devolucionParam, $cantidadDevolucionParam)";

            $values[$idTareaParam] = (int)$this->id;
            $values[$idInventarioParam] = (int)$material['id'];
            $values[$cantidadParam] = (int)$material['cantidad'];
            $values[$devolucionParam] = 0; // 0 = no hay devolución aún
            $values[$cantidadDevolucionParam] = 0;
        }

        $sql = $query . implode(", ", $placeholders);
        $stmt = $this->db->pdo()->prepare($sql);

        if (!$stmt->execute($values)) {
            $error = $stmt->errorInfo();
            throw new Exception("Error al registrar materiales: " . $error[2]);
        }

        // Restar stock de artículo


        foreach ($materiales as $material) {
            $stmt = $this->db->pdo()->prepare("
                UPDATE articulo 
                SET cantidad = cantidad - :cantidad 
                WHERE id = :id AND cantidad >= :cantidad_check
            ");

            $params = [
                ':cantidad' => (int)$material['cantidad'],
                ':cantidad_check' => (int)$material['cantidad'],
                ':id' => (int)$material['id'],
            ];

            if (!$stmt->execute($params)) {
                $error = $stmt->errorInfo();
                throw new Exception("Error SQL: " . $error[2]);
            }
        }
    }



    //----------------------Evaluar metodos--------------------------------------------

    public function evaluar(array $datos): bool
    {
        $this->db->connect();
        $this->db->pdo()->beginTransaction();

        try {
            $this->mapearEvaluacion($datos);
            
           $this->guardarEvaluacionSupervisor();
         
        // Solo si el supervisor aprobó
             if ($this->evaluacion['aprobacion'] == 1) {
                $this->guardarEvaluacionDirector();
            } 
            // Verificar si hay materiales y si hay alguno con devolución
            if (!empty($this->materiales) && $this->hayMaterialesDevueltos()) {
                $this->actualizarRecursos();
            }

            $this->db->pdo()->commit();
            return true;
        } catch (\Throwable $e) {
            $this->db->pdo()->rollBack();
            $_SESSION['errores'][] = $e->getMessage();
            error_log("Error al evaluar tarea: " . $e->getMessage());
            return false;
        } finally {
            $this->db->disconnect();
        }
    }

    private function hayMaterialesDevueltos(): bool
    {
        foreach ($this->materiales as $m) {
            if ((float)($m['devuelto'] ?? 0) > 0) {
                return true;
            }
        }
        return false;
    }


    private function esValidoEval(): bool
    {
        return $this->idAsignacion > 0 && in_array($this->evaluacion, ['excelente', 'bueno', 'regular', 'deficiente']);
    }

    private function mapearEvaluacion(array $datos): void
    {
        $this->id = (int)($datos['idTarea'] ?? 0);
        $this->evaluacion = [
            'ponderacion' => $datos['ponderacion'] ?? '',
            'comentarios' => $datos['comentarios'] ?? '',
            'aprobacion' => isset($datos['aprobacion']) ? 1 : 0
        ];

         // Nueva: Evaluación del director
        $this->evaluacionDirector = [
            'ponderacion' => $datos['ponderacion_director'] ?? '',
            'comentarios' => $datos['comentarios_director'] ?? '',
            'aprobacion' => isset($datos['aprobacion_director']) ? 1 : 0
        ];

        if (isset($datos['materiales']) && is_string($datos['materiales'])) {
            $this->materiales = json_decode($datos['materiales'], true);
        }
    }
        private function guardarEvaluacionSupervisor(): void
    {
        // Verificar si ya existe una evaluación para esta tarea
        $checkQuery = "SELECT id FROM evaluacion WHERE idTarea = :idTarea";
        $checkStmt = $this->db->pdo()->prepare($checkQuery);
        $checkStmt->execute([':idTarea' => $this->id]);
        $exists = $checkStmt->fetch();

        // Datos base para ambos casos
        $params = [
            ':idTarea' => $this->id,
            ':evaluacion' => $this->evaluacion['ponderacion'],
            ':comentario' => $this->evaluacion['comentarios']
           
        ];

        if ($exists) {
            // Actualizar evaluación existente (solo campos del supervisor)
            $query = "UPDATE evaluacion SET 
                        evaluacion_supervisor = :evaluacion, 
                        comentario_supervisor = :comentario,
                        fecha_evaluacion_supervisor = NOW()
                    WHERE idTarea = :idTarea";
        } else {
            // Insertar nueva evaluación (solo campos del supervisor)
            $query = "INSERT INTO evaluacion (
                        idTarea, 
                        evaluacion_supervisor, 
                        comentario_supervisor,
                        fecha_evaluacion_supervisor
                    ) VALUES (
                        :idTarea, 
                        :evaluacion, 
                        :comentario, 
                        NOW()
                    )";
        }

        $stmt = $this->db->pdo()->prepare($query);
        $stmt->execute($params);
    }

    private function guardarEvaluacionDirector(): void
    {
        // Solo ejecutar si hay aprobación del supervisor
        if ($this->evaluacion['aprobacion'] != 1) {
            return;
        }

        $params = [
            ':idTarea' => $this->id,
            ':evaluacion' => $this->evaluacionDirector['ponderacion'],
            ':comentario' => $this->evaluacionDirector['comentarios']
        ];

        // Actualizar siempre (asumiendo que ya existe registro por el supervisor)
        $query = "UPDATE evaluacion SET 
                    evaluacion_director = :evaluacion, 
                    comentario_director = :comentario,
                    fecha_evaluacion_director = NOW()
                WHERE idTarea = :idTarea";

        $stmt = $this->db->pdo()->prepare($query);
        $stmt->execute($params);
    }

    private function actualizarRecursos(): void
    {
        foreach ($this->materiales as $m) {
            $idTarea = $this->id;
            $idInventario = (int)$m['id'];
            $utilizado = (float)$m['utilizado'];
            $devuelto = (float)$m['devuelto'];

            // 1. Obtener cantidad previa de devolución (por si ya se devolvió antes)
            $stmt = $this->db->pdo()->prepare("
                SELECT cantidadDevolucion 
                FROM recurso 
                WHERE idTarea = :idTarea AND idInventario = :idInventario
            ");
            $stmt->execute([
                ':idTarea' => $idTarea,
                ':idInventario' => $idInventario,
            ]);
            $prev = $stmt->fetch(PDO::FETCH_ASSOC);
            $devueltoAnterior = $prev ? (float)$prev['cantidadDevolucion'] : 0;

            // 2. Calcular devolución neta
            $devolucionNeta = $devuelto - $devueltoAnterior;

            // 3. Actualizar recurso con nueva info
            $query = "UPDATE recurso SET 
                        cantidad = :usada,
                        cantidadDevolucion = :devuelto,
                        devolucion = 1
                    WHERE idTarea = :idTarea AND idInventario = :idInventario";

            $stmt = $this->db->pdo()->prepare($query);
            $stmt->execute([
                ':usada' => $utilizado,
                ':devuelto' => $devuelto,
                ':idTarea' => $idTarea,
                ':idInventario' => $idInventario
            ]);

            // 4. Si hay devolución neta positiva, actualizar el stock
            if ($devolucionNeta > 0) {
                $queryStock = "UPDATE articulo 
                            SET cantidad = cantidad + :devuelto 
                            WHERE id = :id";

                $stmt = $this->db->pdo()->prepare($queryStock);
                $stmt->execute([
                    ':devuelto' => $devolucionNeta,
                    ':id' => $idInventario
                ]);
            }
        }
    }





    public function cancelar()
    {
        $this->db->connect();

        try {
            $query = "UPDATE tarea SET estado_tarea = 'cancelado' WHERE id = :id";
            $stmt = $this->db->pdo()->prepare($query);
            $stmt->bindValue(":id", $this->id, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                throw new Exception("No se pudo cancelar la tarea");
            }
        } finally {
            $this->db->disconnect();
        }
    }


    // Método compatible con Model::cargar()
    public static function cargar(int $id, bool $userBD = false): null|self
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


    public function listarPorEstado($estado)
    {

        $bd = Database::getInstance();
        $bd->connect();
        $query = "SELECT * FROM `tarea` WHERE estado_tarea = :estado ORDER BY fechaCreacion DESC;";

        $consulta = $bd->pdo()->prepare($query);
        $consulta->execute([':estado' => $estado]);
        $consulta->setFetchMode(PDO::FETCH_CLASS, "Tarea");

        $bd->disconnect();

        return $consulta->fetchAll();
    }

    public static function obtenerPorId($id)
    {
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

            // supervisor
            $querySupervisor = "SELECT tr.id AS idSupervisor, tr.nombre, tr.apellido, d.nombre AS departamento
                    FROM tarea t
                    JOIN trabajador tr ON t.idSupervisor = tr.id
                    JOIN departamento d ON tr.idDepartamento = d.id
                    WHERE t.id = :idTarea";

            $stmt = $pdo->prepare($querySupervisor);
            $stmt->execute([':idTarea' => $id]);
            $tarea->supervisor = $stmt->fetchAll(PDO::FETCH_OBJ);

            // Obtener materiales asignados (recursos)
            $queryMateriales = "SELECT 
                        r.idInventario AS id, 
                        a.nombre, 
                        a.descripcion, 
                        a.idMedida,
                        r.cantidad, 
                        r.devolucion, 
                        r.cantidadDevolucion 
                    FROM recurso r
                    JOIN articulo a ON r.idInventario = a.id
                    WHERE r.idTarea = :idTarea";

            $stmt = $pdo->prepare($queryMateriales);
            $stmt->execute([':idTarea' => $id]);
            $tarea->materiales = $stmt->fetchAll(PDO::FETCH_OBJ);

            // Obtener información de evaluación
            $queryEvaluacion = "SELECT 
                        evaluacion_supervisor,
                        comentario_supervisor,
                        fecha_evaluacion_supervisor,
                        evaluacion_director,
                        comentario_director,
                        fecha_evaluacion_director
                    FROM evaluacion
                    WHERE idTarea = :idTarea";

            $stmt = $pdo->prepare($queryEvaluacion);
            $stmt->execute([':idTarea' => $id]);
            $evaluacion = $stmt->fetch(PDO::FETCH_OBJ);

            // Agregar la evaluación al objeto tarea
            if ($evaluacion) {
                $tarea->evaluacion = $evaluacion;
            } else {
                // Si no hay evaluación, puedes asignar un objeto vacío o null
                $tarea->evaluacion = null;
            }


            return $tarea;
        } finally {
            $bd->disconnect();
        }
    }

    //----------para llenar tabla de materiales
    public function listarConCategoriaYUnidad()
    {
        $bd = Database::getInstance();
        $bd->connect();
        $pdo = $bd->pdo();

        $query = "SELECT 
                a.id,
                a.nombre,
                c.nombre AS categoria,
                m.unidad,
                a.cantidad AS disponible
            FROM articulo a
            JOIN categoria c ON a.idCategoria = c.id
            JOIN medida m ON a.idMedida = m.id
            WHERE a.cantidad > 0";  // <- Aquí agregamos la condición

        $stmt = $pdo->prepare($query);
        $stmt->execute();

        $bd->disconnect();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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




    public function getId(): int
    {
        return $this->id;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function getFechaCreacion(): string
    {
        return $this->fechaCreacion;
    }

    public function esAutomatica(): bool
    {
        return isset($this->tareaAutomatica);
    }

    public function getEstado(): string
    {
        return $this->estado_tarea ?? 'Desconocido';
    }
}
