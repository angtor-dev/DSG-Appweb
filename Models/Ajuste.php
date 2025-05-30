<?php
class Ajuste extends Model
{
    public int $idInventario;
    private int $cantidad;
    private string $descripcion;
    private string $fechaIncidente;
    private string $fechaCreacion;
    public Articulo $articulo;

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
        return (new DateTime($this->fechaCreacion))->format('d/m/Y');
    }
}
