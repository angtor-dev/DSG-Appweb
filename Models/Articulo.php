<?php
class Articulo extends Model
{
    public int $idCategoria;
    public int $idMedida;
    private string $nombre;
    private ?string $descripcion;
    private int $cantidad;
    private bool $esConsumible;
    public Categoria $categoria;
    public Medida $medida;

    /**
     * Retorna un array de objetos Articulo con todos los registros de la tabla articulos.
     * @return Articulo[]
     */
    public function listar(int $estado = null): array
    {
        $query = "SELECT a.id, a.idCategoria, a.idMedida, a.nombre, a.descripcion, a.cantidad,
            a.esConsumible, c.nombre AS categoria_nombre, c.descripcion AS categoria_descripcion,
            c.color AS categoria_color, m.unidad AS medida_unidad, m.subunidad AS medida_subunidad
            FROM articulo a
            LEFT JOIN categoria c ON a.idCategoria = c.id
            LEFT JOIN medida m ON a.idMedida = m.id
        ";

        $this->db->connect();

        $stmt = $this->db->pdo()->query($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);

        $this->db->disconnect();

        if ($stmt->rowCount() == 0) {
            return [];
        }

        $articulos = $stmt->fetchAll();
        foreach ($articulos as $articulo) {
            $articulo->categoria = new Categoria();
            $articulo->categoria->setDatos(
                $articulo->idCategoria,
                $articulo->categoria_nombre,
                $articulo->categoria_descripcion,
                $articulo->categoria_color
            );
            $articulo->medida = new Medida();
            $articulo->medida->setDatos(
                $articulo->idMedida,
                $articulo->medida_unidad,
                $articulo->medida_subunidad
            );
        }

        return $articulos;
    }

    public function esValido() : bool
    {
        if (empty(trim($this->nombre))) {
            $_SERVER['errores'][] = "El nombre del artículo es obligatorio.";
            return false;
        } elseif (strlen($this->nombre) < 3 || strlen($this->nombre) > 100) {
            $_SERVER['errores'][] = "El nombre del artículo debe tener entre 3 y 100 caracteres.";
            return false;
        }

        if ($this->idCategoria <= 0) {
            $_SERVER['errores'][] = "Debe seleccionar una categoría válida.";
            return false;
        }

        if ($this->idMedida <= 0) {
            $_SERVER['errores'][] = "Debe seleccionar una medida válida.";
            return false;
        }

        return true;
    }

    public function registrar() : bool {
        $query = "INSERT INTO articulo (idCategoria, idMedida, nombre, descripcion, esConsumible, cantidad)
            VALUES (:idCategoria, :idMedida, :nombre, :descripcion, :esConsumible, :cantidad)";

        try {
            $this->db->connect();

            $stmt = $this->prepare($query);
            $stmt->bindValue("idCategoria", $this->idCategoria);
            $stmt->bindValue("idMedida", $this->idMedida);
            $stmt->bindValue("nombre", $this->nombre);
            $stmt->bindValue("descripcion", $this->descripcion);
            $stmt->bindValue("esConsumible", $this->esConsumible ?? false, PDO::PARAM_BOOL);
            $stmt->bindValue("cantidad", 0);

            $stmt->execute();

            $this->db->disconnect();

            return true;
        } catch (\Throwable $th) {
            if (DEVELOPER_MODE) $_SESSION['errores'][] = $th->getMessage();
            $_SESSION['errores'][] = "Ocurrio un error al registrar el área";
            return false;
        }
    }

    public function mapearFormulario() : bool
    {
        try {if (!empty($_POST['id'])) {
                $this->id = $_POST['id'];
            }
            $this->nombre = $_POST['nombre'];
            $this->descripcion = $_POST['descripcion'];
            $this->esConsumible = $_POST['esConsumible'] ?? false;
            $this->idCategoria = $_POST['idCategoria'];
            $this->idMedida = $_POST['idMedida'];

            return true;
        } catch (\Throwable $th) {
            $_SESSION['errores'][] = "Error al mapear el formulario: " . $th->getMessage();
            return false;
        }
    }

    // Getters
    public function getNombre() : string {
        return $this->nombre;
    }
    public function getDescripcion() : ?string {
        return $this->descripcion;
    }
    public function getCantidad() : int {
        return $this->cantidad;
    }
    public function getEsConsumible() : bool {
        return $this->esConsumible;
    }
}
