<?php

class Categoria extends Model
{
    private string $nombre;
    private string $descripcion;
    private string $color;


    // Getters
    public function getNombre(): string {
        return $this->nombre;
    }
    public function getDescripcion(): string {
        return $this->descripcion;
    }
    public function getColor(): string {
        return $this->color;
    }

    // Setters
    public function setDatos(int $id = null, string $nombre, string $descripcion, string $color): void {
        if (isset($id)) {
            $this->id = $id;
        }
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->color = $color;
    }
}
