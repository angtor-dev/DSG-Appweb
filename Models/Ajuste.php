<?php
class Ajuste extends Model
{
    public int $idInventario;
    private int $cantidad;
    private string $descripcion;
    private string $fechaIncidente;
    private string $fechaCreacion;
    public Articulo $articulo;

    /**
     * devuelve un arreglo de ajustes
     * @param mixed $estado
     * @return Ajuste[]
     */
    public function listar(?int $estado = null) : array
    {
        $query = "SELECT aj.id, aj.idInventario, aj.cantidad, aj.descripcion, aj.fechaIncidente,
            aj.fechaCreacion, a.id AS articulo_id, a.nombre AS articulo_nombre,
            a.descripcion AS articulo_descripcion, a.cantidad AS articulo_cantidad,
            a.esConsumible AS articulo_esConsumible, a.idCategoria AS articulo_idCategoria,
            a.idMedida AS articulo_idMedida, c.nombre AS categoria_nombre,
            c.descripcion AS categoria_descripcion, c.color AS categoria_color,
            m.unidad AS medida_unidad, m.subunidad AS medida_subunidad
            FROM ajuste aj
            LEFT JOIN articulo a ON aj.idInventario = a.id
            LEFT JOIN categoria c ON a.idCategoria = c.id
            LEFT JOIN medida m ON a.idMedida = m.id;
        ";

        $this->db->connect();

        $stmt = $this->db->pdo()->query($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);

        $this->db->disconnect();

        if ($stmt->rowCount() == 0) {
            return [];
        }

        $ajustes = $stmt->fetchAll();
        foreach ($ajustes as $ajuste) {
            $articulo = new Articulo();
            $articulo->setDatos(
                $ajuste->articulo_id,
                $ajuste->articulo_idCategoria,
                $ajuste->articulo_idMedida,
                $ajuste->articulo_nombre,
                $ajuste->articulo_descripcion,
                $ajuste->articulo_cantidad,
                $ajuste->articulo_esConsumible
            );
            $articulo->categoria = new Categoria();
            $articulo->categoria->setDatos(
                $articulo->idCategoria,
                $ajuste->categoria_nombre,
                $ajuste->categoria_descripcion,
                $ajuste->categoria_color
            );
            $articulo->medida = new Medida();
            $articulo->medida->setDatos(
                $articulo->idMedida,
                $ajuste->medida_unidad,
                $ajuste->medida_subunidad
            );
            $ajuste->articulo = $articulo;
        }
        return $ajustes;
    }

    public function registrar(): bool
    {
        $query = "INSERT INTO ajuste (idInventario, cantidad, descripcion, fechaIncidente)
                  VALUES (:idInventario, :cantidad, :descripcion, :fechaIncidente)";
        try {
            $this->db->connect();
            $this->beginTransaction();

            $stmt = $this->prepare($query);
            $stmt->bindValue("idInventario", $this->idInventario);
            $stmt->bindValue("cantidad", $this->cantidad);
            $stmt->bindValue("descripcion", $this->descripcion);
            $stmt->bindValue("fechaIncidente", $this->fechaIncidente);

            $stmt->execute();

            // Notificar cuando se agota un artículo
            $articuloQuery = "SELECT cantidad, nombre FROM articulo WHERE id = :idInventario";
            $articuloStmt = $this->db->pdo()->prepare($articuloQuery);
            $articuloStmt->bindValue("idInventario", $this->idInventario, PDO::PARAM_INT);
            $articuloStmt->execute();
            $articulo = $articuloStmt->fetch(PDO::FETCH_ASSOC);

            if ($articulo && isset($articulo['cantidad']) && (int)$articulo['cantidad'] === 0) {
                $notificacion = new Notificacion();
                $usuarios = (new Usuario())->listarDBUser();
                foreach ($usuarios as $usuario) {
                    $notificacion->notificarUsuario($usuario->id, "El artículo ".$articulo['nombre']." se ha agotado");
                }
            }

            $this->testHandler();

            $this->commit();
            $this->db->disconnect();

            return true;
        } catch (\Throwable $th) {
            if (DEVELOPER_MODE) $_SESSION['errores'][] = $th->getMessage();
            $_SESSION['errores'][] = "Ocurrió un error al registrar el ajuste";
            return false;
        }
    }

    public function esValido(): bool
    {
        if (!is_numeric($this->cantidad)) {
            $_SESSION['errores'][] = "La cantidad debe ser un número.";
            return false;
        }
        if ($this->cantidad == 0) {
            $_SESSION['errores'][] = "La cantidad no puede ser cero.";
            return false;
        }
        if (empty(trim($this->descripcion))) {
            $_SESSION['errores'][] = "La descripción es obligatoria.";
            return false;
        }
        if (empty($this->fechaIncidente)) {
            $_SESSION['errores'][] = "Debe ingresar la fecha del incidente.";
            return false;
        }
        // Validar formato de fecha (opcional)
        $fecha = date_create_from_format('Y-m-d', $this->fechaIncidente);
        if (!$fecha) {
            $_SESSION['errores'][] = "La fecha del incidente no es válida.";
            return false;
        }
        return true;
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

    public function setDatos(int $idInventario, int $cantidad, string $descripcion, string $fechaIncidente, ?int $id = null): void
    {
        if ($id !== null) {
            $this->id = $id;
        }
        $this->idInventario = $idInventario;
        $this->cantidad = $cantidad;
        $this->descripcion = $descripcion;
        $this->fechaIncidente = $fechaIncidente;
    }

    public function getCantidad(): int
    {
        return $this->cantidad;
    }
    public function getDescripcion(): string
    {
        return $this->descripcion;
    }
    public function getFechaIncidente(): DateTime
    {
        return new DateTime($this->fechaIncidente);
    }
    public function getFechaCreacion(): DateTime
    {
        return new DateTime($this->fechaCreacion);
    }

    public function getFechaIncidenteLegible(): string
    {
        return (new DateTime($this->fechaIncidente))->format('d/m/Y');
    }

    public function getFechaCreacionLegible(): string
    {
        return (new DateTime($this->fechaCreacion))->format('d/m/Y h:ia');
    }
}
