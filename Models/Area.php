<?php
class Area extends Model
{
    public ?int $idArea = null;
    private string $nombre;
    public ?Area $areaPadre = null;

    public function esValido() : bool {
        if (empty(trim($this->nombre))) {
            $_SESSION['errores'][] = "El nombre del área es requerido";
            return false;
        }
        return true;
    }

    public function registrar() : bool {
        $query = "INSERT INTO area (nombre, idArea) VALUES (:nombre, :idArea)";

        try {
            $this->db->connect();

            $stmt = $this->prepare($query);
            $stmt->bindValue("nombre", $this->nombre);
            $stmt->bindValue("idArea", $this->idArea);

            $stmt->execute();

            $this->db->disconnect();

            return true;
        } catch (\Throwable $th) {
            if (DEVELOPER_MODE) debug($th);
            $_SESSION['errores'][] = "Ocurrio un error al registrar el área";
            return false;
        }
    }

    public function mapearFormulario() : bool
    {
        try {
            $this->nombre = $_POST['nombre'];
            $this->idArea = !empty($_POST['idArea']) ? intval($_POST['idArea']) : null;
            if (!empty($_POST['id'])) {
                $this->id = $_POST['id'];
            }

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
}