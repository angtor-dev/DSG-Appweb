<?php
class Medida extends Model
{
    private string $unidad;
    private string $subUnidad;

    // Getters
    public function getUnidad() : string {
        return $this->unidad;
    }
    public function getSubUnidad() : string {
        return $this->subUnidad;
    }

    // Setters
    public function setDatos(int $id = null, string $unidad, string $subUnidad): void {
        if (isset($id)) {
            $this->id = $id;
        }
        $this->unidad = $unidad;
        $this->subUnidad = $subUnidad;
    }
}
