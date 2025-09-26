<?php
class Entrada extends Model
{
    private string $fechaEntrada;
    private string $fechaRegistro;
    private string $numeroDocumento;
    private string $observaciones;
    /** @var EntradaDetalle[] */
    private array $detalles = [];
    public int $idUsuario;
    public ?Usuario $usuario;

    public function __construct() {
        parent::__construct();
        if (!empty($this->idUsuario)) {
            $this->usuario = Usuario::cargarSoloNombres($this->idUsuario);
        }
    }

    public function esValido(): bool
    {
        $valido = true;

        if (empty($this->fechaEntrada)) {
            $_SESSION['errores'][] = "La fecha de entrega es obligatoria.";
            $valido = false;
        } elseif (!date_create_from_format('Y-m-d', $this->fechaEntrada)) {
            $_SESSION['errores'][] = "La fecha de entrega no es válida.";
            $valido = false;
        }

        if (empty(trim($this->numeroDocumento))) {
            $_SESSION['errores'][] = "El número de documento es obligatorio.";
            $valido = false;
        }

        if (empty($this->idUsuario) || !is_numeric($this->idUsuario)) {
            $_SESSION['errores'][] = "Debe seleccionar un responsable válido.";
            $valido = false;
        }

        if (empty($this->detalles) || !is_array($this->detalles)) {
            $_SESSION['errores'][] = "Debe agregar al menos un artículo a la nota de entrega.";
            $valido = false;
        } else {
            foreach ($this->detalles as $detalle) {
                if (method_exists($detalle, 'esValido')) {
                    if (!$detalle->esValido()) {
                        $valido = false;
                    }
                } else {
                    // Validación básica si no existe el método esValido en EntradaDetalle
                    if (empty($detalle->idArticulo) || !is_numeric($detalle->idArticulo)) {
                        $_SESSION['errores'][] = "Uno de los artículos seleccionados no es válido.";
                        $valido = false;
                    }
                    if (empty($detalle->getCantidad()) || !is_numeric($detalle->getCantidad()) || $detalle->getCantidad() <= 0) {
                        $_SESSION['errores'][] = "La cantidad de uno de los artículos no es válida.";
                        $valido = false;
                    }
                }
            }
        }
        return $valido;
    }

    /**
     * Lista todas las entradas con sus detalles asociados.
     * @param int|null $estado
     * @return Entrada[]
     */
    public function listar(int $estado = null): array
    {
        $query = "SELECT * FROM entrada e
                  ORDER BY e.fechaEntrada DESC";

        $this->db->connect();

        $stmt = $this->query($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);

        $entradas = $stmt->fetchAll();

        // Obtener los detalles para cada entrada
        foreach ($entradas as $entrada) {
            $this->db->connect();
            $queryDetalles = "SELECT * FROM entradadetalle WHERE idEntrada = :idEntrada";
            $stmtDetalles = $this->prepare($queryDetalles);
            $stmtDetalles->bindValue("idEntrada", $entrada->id);
            $stmtDetalles->execute();
            $stmtDetalles->setFetchMode(PDO::FETCH_CLASS, 'EntradaDetalle');
            $detalles = $stmtDetalles->fetchAll();

            $entrada->detalles = $detalles;

            // Cargar usuario si corresponde
            if (!empty($entrada->idUsuario)) {
                $entrada->usuario = Usuario::cargarSoloNombres($entrada->idUsuario);
            }
        }

        $this->db->disconnect();

        return $entradas;
    }

    /**
     * Carga una entrada específica por su id, incluyendo sus detalles y artículos.
     * @param int $id
     * @return null|Entrada
     */
    public static function cargarConDetalles(int $id): ?Entrada
    {
        $db = Database::getInstance();
        $db->connect();

        // Cargar datos de la entrada
        $query = "SELECT * FROM entrada WHERE id = :id";
        $stmt = $db->pdo()->prepare($query);
        $stmt->bindValue("id", $id);
        $stmt->execute();

        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        $entrada = $stmt->fetch();

        if (!$entrada) {
            $db->disconnect();
            return null;
        }

        // Cargar detalles
        $db->connect();
        $queryDetalles = "SELECT * FROM entradadetalle WHERE idEntrada = :idEntrada";
        $stmtDetalles = $db->pdo()->prepare($queryDetalles);
        $stmtDetalles->bindValue("idEntrada", $id);
        $stmtDetalles->execute();
        $stmtDetalles->setFetchMode(PDO::FETCH_CLASS, 'EntradaDetalle');
        $detalles = $stmtDetalles->fetchAll();

        // Cargar artículo para cada detalle
        foreach ($detalles as $detalle) {
            if (!empty($detalle->idArticulo)) {
                $detalle->articulo = Articulo::cargar($detalle->idArticulo);
            }
        }

        $entrada->detalles = $detalles;

        // Cargar usuario si corresponde
        if (!empty($entrada->idUsuario)) {
            $entrada->usuario = Usuario::cargarSoloNombres($entrada->idUsuario);
        }

        $db->disconnect();
        return $entrada;
    }

    public function registrar(): bool
    {
        $queryEntrada = "INSERT INTO entrada (fechaEntrada, numeroDocumento, observaciones, idUsuario)
                         VALUES (:fechaEntrada, :numeroDocumento, :observaciones, :idUsuario)";
        $queryDetalle = "INSERT INTO entradadetalle (idEntrada, idArticulo, cantidad)
                         VALUES (:idEntrada, :idArticulo, :cantidad)";

        try {
            $this->db->connect();
            $this->beginTransaction();

            // Registrar la entrada
            $stmtEntrada = $this->prepare($queryEntrada);
            $stmtEntrada->bindValue("fechaEntrada", $this->fechaEntrada);
            $stmtEntrada->bindValue("numeroDocumento", $this->numeroDocumento);
            $stmtEntrada->bindValue("observaciones", $this->observaciones);
            $stmtEntrada->bindValue("idUsuario", $this->idUsuario);
            $stmtEntrada->execute();

            $idEntrada = $this->db->pdo()->lastInsertId();

            // Registrar los detalles
            $stmtDetalle = $this->prepare($queryDetalle);
            foreach ($this->detalles as $detalle) {
                $stmtDetalle->bindValue("idEntrada", $idEntrada);
                $stmtDetalle->bindValue("idArticulo", $detalle->idArticulo);
                $stmtDetalle->bindValue("cantidad", $detalle->getCantidad());
                $stmtDetalle->execute();

                // Notificar cuando se agota un artículo
                $articuloQuery = "SELECT cantidad, nombre FROM articulo WHERE id = :idArticulo";
                $articuloStmt = $this->db->pdo()->prepare($articuloQuery);
                $articuloStmt->bindValue("idArticulo", $detalle->idArticulo, PDO::PARAM_INT);
                $articuloStmt->execute();
                $articulo = $articuloStmt->fetch(PDO::FETCH_ASSOC);

                if ($articulo && isset($articulo['cantidad']) && (int)$articulo['cantidad'] === 0) {
                    $notificacion = new Notificacion();
                    $usuarios = (new Usuario())->listarDBUser();
                    foreach ($usuarios as $usuario) {
                        $notificacion->notificarUsuario($usuario->id, "El artículo ".$articulo['nombre']." se ha agotado");
                    }
                }
            }

            $this->commit();
            $this->db->disconnect();

            return true;
        } catch (\Throwable $th) {
            $this->rollBack();
            $this->db->disconnect();
            if (DEVELOPER_MODE) $_SESSION['errores'][] = $th->getMessage();
            $_SESSION['errores'][] = "Ocurrió un error al registrar la nota de entrega.";
            return false;
        }
    }

    public function setDatos(
        string $fechaEntrega,
        string $numeroDocumento,
        string $observaciones,
        int $idUsuario,
        array $detalles = [],
        ?int $id = null
    ): void {
        $this->fechaEntrada = $fechaEntrega;
        $this->numeroDocumento = $numeroDocumento;
        $this->observaciones = $observaciones;
        $this->idUsuario = $idUsuario;
        $this->detalles = $detalles;
        if ($id !== null) {
            $this->id = $id;
        }
    }

    public function setterArray(array $data): void {}

    public function getFechaEntrada(): DateTime {
        return new DateTime($this->fechaEntrada);
    }

    public function getFechaRegistro(): DateTime {
        return new DateTime($this->fechaRegistro);
    }

    public function getFechaEntradaLegible(): string {
        return (new DateTime($this->fechaEntrada))->format('d/m/Y');
    }

    public function getFechaRegistroLegible(): string {
        return (new DateTime($this->fechaRegistro))->format('d/m/Y');
    }

    public function getNumeroDocumento(): string {
        return $this->numeroDocumento;
    }

    public function getObservaciones(): string {
        return $this->observaciones;
    }

    /** @return EntradaDetalle[] */
    public function getDetalles(): array {
        return $this->detalles;
    }
}
