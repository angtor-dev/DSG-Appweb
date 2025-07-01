<?php
// divisiones de los departamentos
// antes llamado departamentos
class Division extends Model
{
    public ?int $idDepartamento = null;
    private string $nombre;
    public ?Division $departamentoPadre = null;

    public function esValido() : bool {
        if (empty(trim($this->nombre))) {
            $_SESSION['errores'][] = "El nombre de la división es requerido";
            return false;
        }
        if (!preg_match(REG_ALFANUMERICO, $this->nombre)) {
            $_SESSION['errores'][] = "El nombre de la división no puede contener caracteres especiales";
            return false;
        }
        return true;
    }

    /**
     * Lista las departamentos que tienen como padre el area actual
     * @return Division[]
     */
    public function listarSubdepartamentos() : array
    {
        $query = "SELECT * FROM division WHERE idDivision = :idDepartamento";

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
            $_SESSION['errores'][] = "Ocurrió un error al listar las sub-divisiones de {$this->nombre}";
            return [];
        }
    }

    public function registrar() : bool {
        $query = "INSERT INTO division (nombre, idDivision) VALUES (:nombre, :idDepartamento)";

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
            $_SESSION['errores'][] = "Ocurrió un error al registrar el departamento";
            return false;
        }
    }

    public function actualizar() : bool
    {
        $sql = "UPDATE division SET nombre = :nombre, idDivision = :idDepartamento WHERE id = :id";

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
            $_SESSION['errores'][] = "Ha ocurrido un error al actualizar la División.";
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

    /**
     * Establece valores en propiedades de la clase.
     *
     * Recibe un array asociativo clave-valor y asigna los valores a las
     * propiedades correspondientes. Si la propiedad existe como setter, llama
     * al setter. Si la propiedad existe como propiedad de lectura y escritura,
     * asigna el valor directamente.
     *
     * @param array $data
     * @return void
     */
    public function setterArray(array $data) : void
    {
        // comentar en español
        foreach ($data as $key => $value) {
            $propiedad = $key;
            $setterMethod = 'set_' . $propiedad;
            if(method_exists($this, $setterMethod)){
                $this->$setterMethod($value);
            } elseif(property_exists($this, $propiedad)){
                $this->$propiedad = $value;
            }
        }
    }

    // Getters
    public function getNombre() : string {
        return $this->nombre;
    }
}
?>