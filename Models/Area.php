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
        if (!preg_match(REG_ALFANUMERICO, $this->nombre)) {
            $_SESSION['errores'][] = "El nombre del área no puede contener caracteres especiales";
            return false;
        }
        return true;
    }

    /**
     * Lista las areas que tienen como padre el area actual
     * @return Area[]
     */
    public function listarSubareas() : array
    {
        $query = "SELECT * FROM area WHERE idArea = :idArea";

        try {
            $this->db->connect();

            $stmt = $this->prepare($query);
            $stmt->bindValue("idArea", $this->id);
            $stmt->execute();

            $this->db->disconnect();

            $stmt->setFetchMode(PDO::FETCH_CLASS, $this::class);

            if ($stmt->rowCount() <= 0) {
                return [];
            }
            return $stmt->fetchAll();
        } catch (\Throwable $th) {
            if (DEVELOPER_MODE) $_SESSION['errores'][] = $th->getMessage();
            $_SESSION['errores'][] = "Ocurrio un error al listar las subáreas de {$this->nombre}";
            return [];
        }
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
            if (DEVELOPER_MODE) $_SESSION['errores'][] = $th->getMessage();
            $_SESSION['errores'][] = "Ocurrio un error al registrar el área";
            return false;
        }
    }

    public function actualizar() : bool
    {
        $sql = "UPDATE area SET nombre = :nombre, idArea = :idArea WHERE id = :id";

        try {
            $this->db->connect();

            $stmt = $this->prepare($sql);
            $stmt->bindValue('nombre', $this->nombre);
            $stmt->bindValue('idArea', $this->idArea);
            $stmt->bindValue('id', $this->id);

            $stmt->execute();
            
            $this->db->disconnect();

            return true;
        } catch (\Throwable $th) {
            if (DEVELOPER_MODE) debug($th);
            $_SESSION['errores'][] = "Ha ocurrido un error al actualizar el área.";
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