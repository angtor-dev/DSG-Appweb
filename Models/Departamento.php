<?php
class Departamento extends Model
{
    public ?int $idDepartamento = null;
    private string $nombre;
    public ?Departamento $departamentoPadre = null;

    public function esValido() : bool {
        if (empty(trim($this->nombre))) {
            $_SESSION['errores'][] = "El nombre del departamento es requerido";
            return false;
        }
        if (!preg_match(REG_ALFANUMERICO, $this->nombre)) {
            $_SESSION['errores'][] = "El nombre del departamento no puede contener caracteres especiales";
            return false;
        }
        return true;
    }

    /**
     * Lista las departamentos que tienen como padre el area actual
     * @return Departamento[]
     */
    public function listarSubdepartamentos() : array
    {
        $query = "SELECT * FROM departamento WHERE idDepartamento = :idDepartamento";

        try {
            $this->db->connect();

            $stmt = $this->prepare($query);
            $stmt->bindValue("idDepartamento", $this->id);
            $stmt->execute();

            $this->db->disconnect();

            $stmt->setFetchMode(PDO::FETCH_CLASS, $this::class);

            if ($stmt->rowCount() <= 0) {
                return [];
            }
            return $stmt->fetchAll();
        } catch (\Throwable $th) {
            if (DEVELOPER_MODE) $_SESSION['errores'][] = $th->getMessage();
            $_SESSION['errores'][] = "Ocurrio un error al listar los sub-departamentos de {$this->nombre}";
            return [];
        }
    }

    public function registrar() : bool {
        $query = "INSERT INTO departamento (nombre, idDepartamento) VALUES (:nombre, :idDepartamento)";

        try {
            $this->db->connect();

            $stmt = $this->prepare($query);
            $stmt->bindValue("nombre", $this->nombre);
            $stmt->bindValue("idDepartamento", $this->idDepartamento);

            $stmt->execute();

            $this->db->disconnect();

            return true;
        } catch (\Throwable $th) {
            if (DEVELOPER_MODE) $_SESSION['errores'][] = $th->getMessage();
            $_SESSION['errores'][] = "Ocurrio un error al registrar el departamento";
            return false;
        }
    }

    public function actualizar() : bool
    {
        $sql = "UPDATE departamento SET nombre = :nombre, idDepartamento = :idDepartamento WHERE id = :id";

        try {
            $this->db->connect();

            $stmt = $this->prepare($sql);
            $stmt->bindValue('nombre', $this->nombre);
            $stmt->bindValue('idDepartamento', $this->idDepartamento);
            $stmt->bindValue('id', $this->id);

            $stmt->execute();
            
            $this->db->disconnect();

            return true;
        } catch (\Throwable $th) {
            if (DEVELOPER_MODE) debug($th);
            $_SESSION['errores'][] = "Ha ocurrido un error al actualizar el departamento.";
            return false;
        }
    }

    public function mapearFormulario() : bool
    {
        try {
            $this->nombre = $_POST['nombre'];
            $this->idDepartamento = !empty($_POST['idDepartamento']) ? intval($_POST['idDepartamento']) : null;
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