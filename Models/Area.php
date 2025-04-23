<?php
class Area extends Model
{
    public ?int $idArea = null;
    private string $nombre;
    public ?Area $areaPadre = null;

    // Getters
    public function getNombre() : string {
        return $this->nombre;
    }
}