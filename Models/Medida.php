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
}
