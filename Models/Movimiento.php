<?php
class Movimiento extends Model
{
    public int $idArticulo;
    private float $cantidad;
    private float $antes;
    private float $despues;
    private string $fecha;
    public Articulo $articulo;

    /** @return Movimiento[] */
    public function listar(?int $estado = null) : array
    {
        $query = "SELECT
            m.*,
            a.id AS articulo_id,
            a.idCategoria AS articulo_idCategoria,
            a.idMedida AS articulo_idMedida,
            a.nombre AS articulo_nombre,
            a.descripcion AS articulo_descripcion,
            a.cantidad AS articulo_cantidad,
            a.esConsumible AS articulo_esConsumible
            FROM movimiento m
            INNER JOIN articulo a ON m.idArticulo = a.id;";

        $this->db->connect();

        $stmt = $this->db->pdo()->query($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);

        $this->db->disconnect();

        if ($stmt->rowCount() == 0) {
            return [];
        }

        $movimientos = $stmt->fetchAll();

        foreach ($movimientos as $movimiento) {
            $articulo = new Articulo();
            $articulo->setDatos(
                $movimiento->articulo_id,
                $movimiento->articulo_idCategoria,
                $movimiento->articulo_idMedida,
                $movimiento->articulo_nombre,
                $movimiento->articulo_descripcion,
                $movimiento->articulo_cantidad,
                $movimiento->articulo_esConsumible
            );
            $movimiento->articulo = $articulo;
        }
        return $movimientos;
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

    public function getCantidad(): float
    {
        return $this->cantidad;
    }

    public function getAntes(): float
    {
        return $this->antes;
    }

    public function getDespues(): float
    {
        return $this->despues;
    }

    public function getFecha(): DateTime
    {
        return new DateTime($this->fecha);
    }

    public function getFechaLegible(): string
    {
        return (new DateTime($this->fecha))->format('d/m/Y h:m');
    }
    public function getTipo(): string
    {
        return $this->cantidad < 0 ? 'Salida' : 'Entrada';
    }
}
