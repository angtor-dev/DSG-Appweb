<?php
class Tarea extends Model
{
    // Propiedades públicas
    public int $id;
    public int $idArea;
    public int $idDepartamento;
    public string $descripcion;
    public string $fechaCreacion;
    public string $estado_tarea = 'activo';
    public int $idSupervisor;
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
    public ?Division $departamento = null;

    public function __construct()
    {
        parent::__construct();
        // Inicialización condicional
        if (!empty($this->idArea)) {
            $this->area = Area::cargar($this->idArea);
        }
        if (!empty($this->idDepartamento)) {
            $this->departamento = Division::cargar($this->idDepartamento);
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
       
        $queryTarea = "INSERT INTO tarea 
                      (idArea, idDepartamento, descripcion, fecha_inicio, estado_tarea, es_comun) 
                      VALUES (:idArea, :idDepartamento, :descripcion, :fecha_inicio, :estado, :es_comun)";

        $stmtTarea = $this->db->pdo()->prepare($queryTarea);
        $stmtTarea->bindValue(":idArea", $this->idArea, PDO::PARAM_INT);
        $stmtTarea->bindValue(":idDepartamento", $this->idDepartamento, PDO::PARAM_INT);
        $stmtTarea->bindValue(":descripcion", $this->descripcion, PDO::PARAM_STR);
        $stmtTarea->bindValue(":estado", $this->estado_tarea, PDO::PARAM_INT);
        $stmtTarea->bindValue(":es_comun", $this->es_comun, PDO::PARAM_BOOL);
        $stmtTarea->bindValue(":fecha_inicio", $this->fecha_inicio, PDO::PARAM_STR);

        if (!$stmtTarea->execute()) {
            throw new Exception("Error al guardar la tarea principal");
        }

        $idTarea = $this->db->pdo()->lastInsertId();

        // 2. Registrar la asignación para validación
        $queryValidacion = "INSERT INTO tarea_validacion 
                          (idTarea, idSupervisor) 
                          VALUES (:idTarea, :idSupervisor)";

        $stmtValidacion = $this->db->pdo()->prepare($queryValidacion);
        $stmtValidacion->bindValue(":idTarea", $idTarea, PDO::PARAM_INT);
        $stmtValidacion->bindValue(":idSupervisor", $this->idSupervisor, PDO::PARAM_INT);

        if (!$stmtValidacion->execute()) {
            throw new Exception("Error al registrar la validación");
        }

        return $idTarea;
    
    }



    /**
     * Asigna personal a la tarea
     * @param array $idsTrabajadores IDs de los trabajadores a asignar
     * @throws Exception Si no se puede asignar el personal
     */
      private function asignarPersonal(array $idsTrabajadores): void
{
    if (empty($idsTrabajadores) || $this->id <= 0) {
        return;
    }

    // Crear array de parámetros
    $params = [':idTarea' => $this->id];
    
    // Crear placeholders para los IDs de trabajadores
    $placeholders = [];
    foreach ($idsTrabajadores as $i => $id) {
        $paramName = ":trabajador_" . $i;
        $placeholders[] = $paramName;
        $params[$paramName] = $id;
    }

    // Consulta SQL con parámetros nombrados
    $query = "INSERT INTO tarea_personal (idTarea, idAsignacionLaboral)
             SELECT :idTarea, a.id
             FROM asignacion_laboral a
             WHERE a.idTrabajador IN (" . implode(',', $placeholders) . ")
             AND a.esActual = 1";
    
    $stmt = $this->db->pdo()->prepare($query);
    
    if (!$stmt->execute($params)) {
        $error = $stmt->errorInfo();
        throw new Exception("Error al asignar personal: " . $error[2]);
    }
    
    // Verificar que se asignaron todos
    $asignados = $stmt->rowCount();
    if ($asignados < count($idsTrabajadores)) {
        $noAsignados = count($idsTrabajadores) - $asignados;
        throw new Exception(
            "$noAsignados trabajadores no pudieron ser asignados (no tienen asignación laboral activa)"
        );
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

        $query = "INSERT INTO recurso (idTarea, idArticulo, cantidad, devolucion, cantidadDevolucion) VALUES ";
        $placeholders = [];
        $values = [];

        foreach ($materiales as $i => $material) {
            $idTareaParam = ":idTarea_$i";
            $idArticuloParam = ":idInventario_$i";
            $cantidadParam = ":cantidad_$i";
            $devolucionParam = ":devolucion_$i";
            $cantidadDevolucionParam = ":cantidadDev_$i";

            $placeholders[] = "($idTareaParam, $idArticuloParam, $cantidadParam, $devolucionParam, $cantidadDevolucionParam)";

            $values[$idTareaParam] = (int)$this->id;
            $values[$idArticuloParam] = (int)$material['id'];
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

            $this->actualizarEstadoTarea('evaluada');
            
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

    // Evaluación del director
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
    $checkQuery = "SELECT id FROM tarea_validacion WHERE idTarea = :idTarea";
    $checkStmt = $this->db->pdo()->prepare($checkQuery);
    $checkStmt->execute([':idTarea' => $this->id]);
    $exists = $checkStmt->fetch();

    // Construir el campo observación
    $observacion = '';
    if (!empty($this->evaluacion['comentarios'])) {
        $observacion = "[Observación supervisor]: " . $this->evaluacion['comentarios'];
    }

    // Datos base para ambos casos
    $params = [
        ':idTarea' => $this->id,
        ':evaluacion' => $this->evaluacion['ponderacion'],
        ':observacion' => $observacion
    ];

    if ($exists) {
        // Actualizar evaluación existente (solo campos del supervisor)
        $query = "UPDATE tarea_validacion SET 
                    evalSupervisor = :evaluacion, 
                    observacion = :observacion,
                    fechaEval = NOW()
                WHERE idTarea = :idTarea";
    } else {
        // Insertar nueva evaluación (solo campos del supervisor)
        $query = "INSERT INTO tarea_validacion (
                    idTarea, 
                    evalSupervisor, 
                    observacion,
                    fechaEval
                ) VALUES (
                    :idTarea, 
                    :evaluacion, 
                    :observacion, 
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

    // Obtener la observación actual para agregar el comentario del director
    $currentObsQuery = "SELECT observacion FROM tarea_validacion WHERE idTarea = :idTarea";
    $currentObsStmt = $this->db->pdo()->prepare($currentObsQuery);
    $currentObsStmt->execute([':idTarea' => $this->id]);
    $currentObs = $currentObsStmt->fetchColumn();

    // Construir la nueva observación
    $observacion = $currentObs ?: '';
    
    if (!empty($this->evaluacionDirector['comentarios'])) {
        if (!empty($observacion)) {
            $observacion .= "\n"; // Agregar salto de línea si ya hay contenido
        }
        $observacion .= "[Observación director]: " . $this->evaluacionDirector['comentarios'];
    }

    $params = [
        ':idTarea' => $this->id,
        ':evaluacion' => $this->evaluacionDirector['ponderacion'],
        ':observacion' => $observacion
    ];

    // Actualizar siempre (asumiendo que ya existe registro por el supervisor)
    $query = "UPDATE tarea_validacion SET 
                evalSuperior = :evaluacion, 
                observacion = :observacion
            WHERE idTarea = :idTarea";

    $stmt = $this->db->pdo()->prepare($query);
    $stmt->execute($params);
}



    private function actualizarEstadoTarea(string $estado): void
    {
        $query = "UPDATE tarea SET estado_tarea = :estado WHERE id = :id";
        $stmt = $this->db->pdo()->prepare($query);
        $stmt->execute([
            ':estado' => $estado,
            ':id' => $this->id
        ]);
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
                WHERE idTarea = :idTarea AND idArticulo = :idInventario
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
                    WHERE idTarea = :idTarea AND idArticulo = :idInventario";

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

        public function terminar()
    {
        $this->db->connect();

        try {
            $query = "UPDATE tarea SET estado_tarea = 'vencida' WHERE id = :id";
            $stmt = $this->db->pdo()->prepare($query);
            $stmt->bindValue(":id", $this->id, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                throw new Exception("No se pudo terminar la tarea");
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

    public function listarPorEstadoConPersonal($estado) {
    $bd = Database::getInstance();
    $bd->connect();
    
    // Consulta para obtener tareas con información básica
    $query = "SELECT t.*, 
                     a.nombre AS area_nombre,
                     d.nombre AS departamento_nombre
              FROM `tarea` t
              LEFT JOIN `area` a ON t.idArea = a.id
              LEFT JOIN `division` d ON t.idDepartamento = d.id
              WHERE t.estado_tarea = :estado 
              ORDER BY t.fechaCreacion DESC";
    
    $consulta = $bd->pdo()->prepare($query);
    $consulta->execute([':estado' => $estado]);
    $tareas = $consulta->fetchAll(PDO::FETCH_ASSOC);
    
    // Para cada tarea, obtener el personal asignado
    foreach ($tareas as &$tarea) {
        $tarea['personal'] = $this->obtenerPersonalAsignado($tarea['id']);
    }
    
    $bd->disconnect();
    return $tareas;
}

private function obtenerPersonalAsignado($idTarea) {
    $bd = Database::getInstance();
    $bd->connect();
    
    $query = "SELECT t.id, t.nombre, t.apellido, al.idCargo, c.nombre AS cargo_nombre
              FROM `tarea_personal` tp
              JOIN `asignacion_laboral` al ON tp.idAsignacionLaboral = al.id
              JOIN `trabajador` t ON al.idTrabajador = t.id
              LEFT JOIN `cargo` c ON al.idCargo = c.id
              WHERE tp.idTarea = :idTarea
              AND al.esActual = 1";
    
    $consulta = $bd->pdo()->prepare($query);
    $consulta->execute([':idTarea' => $idTarea]);
    $personal = $consulta->fetchAll(PDO::FETCH_ASSOC);
    
    $bd->disconnect();
    
    // Formatear los datos del personal
    return array_map(function($p) {
        return [
            'id' => $p['id'],
            'nombre_completo' => $p['nombre'] . ' ' . $p['apellido'],
            'cargo' => $p['cargo_nombre'] ?? 'Sin cargo'
        ];
    }, $personal);
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
                      LEFT JOIN division d ON t.idDepartamento = d.id
                      WHERE t.id = :id";

            $stmt = $pdo->prepare($query);
            $stmt->execute([':id' => $id]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, "Tarea");
            $tarea = $stmt->fetch();

            if (!$tarea) {
                return null;
            }

            // Obtener personal asignado
           $queryPersonal = "SELECT 
                    t.id AS idTrabajador, 
                    t.nombre, 
                    t.apellido, 
                    d.nombre AS departamento,
                    c.nombre AS cargo,
                    tu.nombre AS turno
                 FROM tarea_personal tp
                 JOIN asignacion_laboral al ON tp.idAsignacionLaboral = al.id
                 JOIN trabajador t ON al.idTrabajador = t.id
                 JOIN division d ON al.idDivision = d.id
                 JOIN cargo c ON al.idCargo = c.id
                 JOIN turno tu ON al.idTurno = tu.id
                 WHERE tp.idTarea = :idTarea
                 AND al.esActual = 1";

            $stmt = $pdo->prepare($queryPersonal);
            $stmt->execute([':idTarea' => $id]);
            $tarea->personal = $stmt->fetchAll(PDO::FETCH_OBJ);

            $querySupervisor = "SELECT 
                      t.id AS idSupervisor, 
                      t.nombre, 
                      t.apellido, 
                      d.nombre AS departamento,
                      c.nombre AS cargo,
                      tv.fechaAsignado AS fechaAsignacion
                   FROM tarea_validacion tv
                   JOIN trabajador t ON tv.idSupervisor = t.id
                   JOIN asignacion_laboral al ON t.id = al.idTrabajador AND al.esActual = 1
                   JOIN division d ON al.idDivision  = d.id
                   JOIN cargo c ON al.idCargo = c.id
                   WHERE tv.idTarea = :idTarea";

            $stmt = $pdo->prepare($querySupervisor);
            $stmt->execute([':idTarea' => $id]);
            $tarea->supervisor = $stmt->fetchAll(PDO::FETCH_OBJ);

            // Obtener materiales asignados (recursos)
            $queryMateriales = "SELECT 
                        r.idArticulo AS id, 
                        a.nombre, 
                        a.descripcion, 
                        a.idMedida,
                        r.cantidad, 
                        r.devolucion, 
                        r.cantidadDevolucion 
                    FROM recurso r
                    JOIN articulo a ON r.idArticulo = a.id
                    WHERE r.idTarea = :idTarea";

            $stmt = $pdo->prepare($queryMateriales);
            $stmt->execute([':idTarea' => $id]);
            $tarea->materiales = $stmt->fetchAll(PDO::FETCH_OBJ);

           $queryEvaluacion = "SELECT 
                    tv.evalSupervisor AS evaluacion_supervisor,
                    tv.observacion AS comentario_supervisor,
                    tv.fechaEval AS fecha_evaluacion_supervisor,
                    tv.evalSuperior AS evaluacion_director,
                    t.nombre AS nombre_supervisor,
                    t.apellido AS apellido_supervisor
                FROM tarea_validacion tv
                LEFT JOIN trabajador t ON tv.idSupervisor = t.id
                WHERE tv.idTarea = :idTarea";
          


            return $tarea;
        } finally {
            $bd->disconnect();
        }
    }

    // ---------------------------PARA LLENAR ORNEDES DE TRABJAO-----------------------------------

    public function obtenerTareasParaOrdenes(array $ids) {
    $bd = Database::getInstance();
    $bd->connect();
    
    // Consulta principal para obtener tareas con información básica
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $query = "SELECT t.*, 
                     a.nombre AS area_nombre,
                     d.nombre AS departamento_nombre,
                     DATE_FORMAT(t.fecha_inicio, '%d/%m/%Y %H:%i') AS fecha_inicio_formateada
              FROM `tarea` t
              LEFT JOIN `area` a ON t.idArea = a.id
              LEFT JOIN `division` d ON t.idDepartamento = d.id
              WHERE t.id IN ($placeholders)
              ORDER BY t.fechaCreacion DESC";
    
    $consulta = $bd->pdo()->prepare($query);
    $consulta->execute($ids);
    $tareas = $consulta->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener información adicional en lotes para mejorar el rendimiento
    $personalPorTarea = $this->obtenerPersonalParaTareas($ids);
    $materialesPorTarea = $this->obtenerMaterialesParaTareas($ids);
    $validacionesPorTarea = $this->obtenerValidacionesParaTareas($ids);
    
    // Combinar toda la información
    foreach ($tareas as &$tarea) {
        $tareaId = $tarea['id'];
        $tarea['personal'] = $personalPorTarea[$tareaId] ?? [];
        $tarea['materiales'] = $materialesPorTarea[$tareaId] ?? [];
       $tarea['validaciones'] = $validacionesPorTarea[$tareaId] ?? [];
    }
    
    $bd->disconnect();
    return $tareas;
}

private function obtenerPersonalParaTareas(array $tareaIds) {
    if (empty($tareaIds)) return [];
    
    $bd = Database::getInstance();
    $bd->connect();
    
    $placeholders = implode(',', array_fill(0, count($tareaIds), '?'));
    $query = "SELECT 
                tp.idTarea,
                t.id AS idTrabajador, 
                t.nombre, 
                t.apellido, 
                c.nombre AS cargo_nombre,
                d.nombre AS departamento_nombre,
                tu.nombre AS turno_nombre,
                tu.horario_entrada,
                tu.horario_salida
              FROM `tarea_personal` tp
              JOIN `asignacion_laboral` al ON tp.idAsignacionLaboral = al.id
              JOIN `trabajador` t ON al.idTrabajador = t.id
              LEFT JOIN `cargo` c ON al.idCargo = c.id
              LEFT JOIN `division` d ON al.idDivision  = d.id
              LEFT JOIN `turno` tu ON al.idTurno = tu.id
              WHERE tp.idTarea IN ($placeholders)
              AND al.esActual = 1
              ORDER BY tp.idTarea, t.apellido, t.nombre";
    
    $consulta = $bd->pdo()->prepare($query);
    $consulta->execute($tareaIds);
    $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
    
    $personalPorTarea = [];
    foreach ($resultados as $row) {
        $tareaId = $row['idTarea'];
        if (!isset($personalPorTarea[$tareaId])) {
            $personalPorTarea[$tareaId] = [];
        }
        
        $personalPorTarea[$tareaId][] = [
            'id' => $row['idTrabajador'],
            'nombre_completo' => $row['nombre'] . ' ' . $row['apellido'],
            'cargo' => $row['cargo_nombre'] ?? 'Sin cargo',
            'departamento' => $row['departamento_nombre'] ?? '',
            'turno' => $row['turno_nombre'] ?? '',
            'horario' => ($row['horario_entrada'] && $row['horario_salida']) 
                ? date('H:i', strtotime($row['horario_entrada'])) . ' - ' . date('H:i', strtotime($row['horario_salida']))
                : ''
        ];
    }
    
    $bd->disconnect();
    return $personalPorTarea;
}

private function obtenerMaterialesParaTareas(array $tareaIds) {
    if (empty($tareaIds)) return [];
    
    $bd = Database::getInstance();
    $bd->connect();
    
    $placeholders = implode(',', array_fill(0, count($tareaIds), '?'));
    $query = "SELECT 
                r.idTarea,
                r.idArticulo AS id, 
                a.nombre, 
                r.cantidad, 
                r.devolucion, 
                r.cantidadDevolucion,
                m.unidad AS medida
              FROM `recurso` r
              JOIN `articulo` a ON r.idArticulo = a.id
              LEFT JOIN `medida` m ON a.idMedida = m.id
              WHERE r.idTarea IN ($placeholders)
              ORDER BY r.idTarea, a.nombre";
    
    $consulta = $bd->pdo()->prepare($query);
    $consulta->execute($tareaIds);
    $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
    
    $materialesPorTarea = [];
    foreach ($resultados as $row) {
        $tareaId = $row['idTarea'];
        if (!isset($materialesPorTarea[$tareaId])) {
            $materialesPorTarea[$tareaId] = [];
        }
        
        $materialesPorTarea[$tareaId][] = [
            'id' => $row['id'],
            'nombre' => $row['nombre'],
            'cantidad' => $row['cantidad'],
            'devolucion' => (bool)$row['devolucion'],
            'cantidadDevolucion' => $row['cantidadDevolucion'],
            'medida' => $row['medida'] ?? 'unid.'
        ];
    }
    
    $bd->disconnect();
    return $materialesPorTarea;
}

private function obtenerValidacionesParaTareas(array $tareaIds) {
    if (empty($tareaIds)) return [];
    
    $bd = Database::getInstance();
    $bd->connect();
    
    $placeholders = implode(',', array_fill(0, count($tareaIds), '?'));
    $query = "SELECT 
                tv.idTarea,
                tv.evalSupervisor,
                tv.evalSuperior,
                tv.observacion,
                tv.fechaEval,
                t.nombre AS supervisor_nombre,
                t.apellido AS supervisor_apellido,
                c.nombre AS supervisor_cargo
              FROM `tarea_validacion` tv
              LEFT JOIN `trabajador` t ON tv.idSupervisor = t.id
              LEFT JOIN `asignacion_laboral` al ON t.id = al.idTrabajador AND al.esActual = 1
              LEFT JOIN `cargo` c ON al.idCargo = c.id
              WHERE tv.idTarea IN ($placeholders)
              ORDER BY tv.idTarea, tv.fechaEval DESC";
    
    $consulta = $bd->pdo()->prepare($query);
    $consulta->execute($tareaIds);
    $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
    
    $validacionesPorTarea = [];
    foreach ($resultados as $row) {
        $tareaId = $row['idTarea'];
        if (!isset($validacionesPorTarea[$tareaId])) {
            $validacionesPorTarea[$tareaId] = [];
        }
        
        $validacionesPorTarea[$tareaId][] = [
            'supervisor' => $row['supervisor_nombre'] . ' ' . $row['supervisor_apellido'],
            'cargo' => $row['supervisor_cargo'] ?? '',
            'evaluacion' => $row['evalSupervisor'] ?? '',
            'evaluacion_superior' => $row['evalSuperior'] ?? '',
            'observacion' => $row['observacion'] ?? '',
            'fecha' => $row['fechaEval'] ? date('d/m/Y H:i', strtotime($row['fechaEval'])) : ''
        ];
    }
    
    $bd->disconnect();
    return $validacionesPorTarea;
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
