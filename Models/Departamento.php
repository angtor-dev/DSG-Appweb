<?php
class Departamento extends Model
{
    public ?int $idDepartamento = null;
    private string $nombre;
    public ?Departamento $departamentoPadre = null;

    // Getters
    public function getNombre() : string {
        return $this->nombre;
    }
}