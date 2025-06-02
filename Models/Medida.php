<?php
class Medida extends Model
{
    private string $unidad;
    private string $subUnidad;

    public function esValido() : bool
    {
        if (empty(trim($this->unidad))) {
            $_SESSION['errores'][] = "La unidad de la medida es obligatorio.";
            return false;
        }
        if (!preg_match(REG_ALFANUMERICO, $this->unidad)) {
            $_SESSION['errores'][] = "La unidad de la medida no debe contener caracteres especiales.";
            return false;
        }
        if (empty(trim($this->subUnidad))) {
            $_SESSION['errores'][] = "La subUnidad de la medida es obligatorio.";
            return false;
        }
        if (!preg_match(REG_ALFANUMERICO, $this->subUnidad)) {
            $_SESSION['errores'][] = "La sub-unidad de la medida no debe contener caracteres especiales.";
            return false;
        }
        return true;
    }

    public function registrar() : bool {
        $query = "INSERT INTO medida (unidad, subUnidad)
            VALUES (:unidad, :subUnidad)";

        try {
            $this->db->connect();

            $stmt = $this->prepare($query);
            $stmt->bindValue("unidad", $this->unidad);
            $stmt->bindValue("subUnidad", $this->subUnidad);

            $stmt->execute();

            $this->db->disconnect();

            return true;
        } catch (\Throwable $th) {
            if (DEVELOPER_MODE) $_SESSION['errores'][] = $th->getMessage();
            $_SESSION['errores'][] = "Ocurrio un error al registrar la medida";
            return false;
        }
    }

    public function actualizar() : bool {
        $query = "UPDATE medida
            SET unidad = :unidad, subUnidad = :subUnidad
            WHERE id = :id";

        try {
            $this->db->connect();

            $stmt = $this->prepare($query);
            $stmt->bindValue("id", $this->id);
            $stmt->bindValue("unidad", $this->unidad);
            $stmt->bindValue("subUnidad", $this->subUnidad);

            $stmt->execute();

            $this->db->disconnect();

            return true;
        } catch (\Throwable $th) {
            if (DEVELOPER_MODE) $_SESSION['errores'][] = $th->getMessage();
            $_SESSION['errores'][] = "Ocurrio un error al actualizar la medida";
            return false;
        }
    }

    public function mapearFormulario() : bool
    {
        try {if (!empty($_POST['id'])) {
                $this->id = $_POST['id'];
            }
            $this->unidad = $_POST['unidad'] ?? '';
            $this->subUnidad = $_POST['subUnidad'] ?? '';

            return true;
        } catch (\Throwable $th) {
            $_SESSION['errores'][] = "Error al mapear el formulario: " . $th->getMessage();
            return false;
        }
    }

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
