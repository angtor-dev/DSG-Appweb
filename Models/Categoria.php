<?php

class Categoria extends Model
{
    private string $nombre;
    private string $descripcion;
    private string $color;

    public function esValido() : bool
    {
        if (empty(trim($this->nombre))) {
            $_SESSION['errores'][] = "El nombre del artículo es obligatorio.";
            return false;
        }
        if (!preg_match(REG_ALFANUMERICO, $this->nombre)) {
            $_SESSION['errores'][] = "El nombre del artículo no debe contener caracteres especiales.";
            return false;
        }
        if (!empty(trim($this->descripcion)) && !preg_match(REG_ALFANUMERICO, $this->descripcion)) {
            $_SESSION['errores'][] = "La descripción del artículo no debe contener caracteres especiales.";
            return false;
        }
        if (empty(trim($this->color))) {
            $_SESSION['errores'][] = "El color es obligatorio.";
            return false;
        } elseif (!preg_match('/^[0-9a-fA-F]{6}$/', $this->color)) {
            $_SESSION['errores'][] = "El color debe ser un código hexadecimal válido.";
            return false;
        }
        return true;
    }

    public function registrar() : bool {
        $query = "INSERT INTO categoria (nombre, descripcion, color)
            VALUES (:nombre, :descripcion, :color)";

        try {
            $this->db->connect();

            $stmt = $this->prepare($query);
            $stmt->bindValue("nombre", $this->nombre);
            $stmt->bindValue("descripcion", $this->descripcion);
            $stmt->bindValue("color", $this->color);

            $stmt->execute();

            $this->db->disconnect();

            return true;
        } catch (\Throwable $th) {
            if (DEVELOPER_MODE) $_SESSION['errores'][] = $th->getMessage();
            $_SESSION['errores'][] = "Ocurrio un error al registrar la categoría";
            return false;
        }
    }

    public function actualizar() : bool {
        $query = "UPDATE categoria
            SET nombre = :nombre, descripcion = :descripcion, color = :color
            WHERE id = :id";

        try {
            $this->db->connect();

            $stmt = $this->prepare($query);
            $stmt->bindValue("id", $this->id);
            $stmt->bindValue("nombre", $this->nombre);
            $stmt->bindValue("descripcion", $this->descripcion);
            $stmt->bindValue("color", $this->color);

            $stmt->execute();

            $this->db->disconnect();

            return true;
        } catch (\Throwable $th) {
            if (DEVELOPER_MODE) $_SESSION['errores'][] = $th->getMessage();
            $_SESSION['errores'][] = "Ocurrio un error al actualizar la categoría";
            return false;
        }
    }

    public function mapearFormulario() : bool
    {
        try {if (!empty($_POST['id'])) {
                $this->id = $_POST['id'];
            }
            $this->nombre = $_POST['nombre'] ?? '';
            $this->descripcion = $_POST['descripcion'] ?? '';
            $this->color = substr($_POST['color'] ?? '', 1); // Eliminar el símbolo '#'

            return true;
        } catch (\Throwable $th) {
            $_SESSION['errores'][] = "Error al mapear el formulario: " . $th->getMessage();
            return false;
        }
    }

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
