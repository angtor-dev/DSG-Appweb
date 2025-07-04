<?php
class EntradaDetalle extends Model
{
    public int $idEntrada;
    public int $idArticulo;
    private float $cantidad;
    public Articulo $articulo;

    /**
     * @inheritDoc
     */
    public function setterArray(array $data): void {}

    public function setDatos(int $idEntrada, int $idArticulo, float $cantidad, ?int $id = null): void
    {
        if ($id !== null) {
            $this->id = $id;
        }
        $this->idEntrada = $idEntrada;
        $this->idArticulo = $idArticulo;
        $this->cantidad = $cantidad;
    }

    public function getCantidad(): float
    {
        return $this->cantidad;
    }
}
