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
        $query = "SELECT d.*, sub.idPadre as idDepartamento FROM division as d LEFT JOIN subdivisiones as sub on d.id = sub.idHijo WHERE sub.idPadre = :idDepartamento";

        $query = "SELECT * FROM area as a LEFT JOIN subarea as sub on a.id = sub.idAreaHijo WHERE sub.idAreaPadre = :idArea";


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

    public function listar($var = null) : array{

        /*
                $query = "SELECT d.*, sub.idPadre as idDepartamento FROM division as d LEFT JOIN subdivisiones as sub on d.id = sub.idHijo ORDER BY d.id";

        $this->db->connect();

        $parametros = [];

        if(isset($estado)) $parametros['estado'] = $estado;

        $stmt = $this->ejecutarStatement($query, $parametros, PDO::FETCH_CLASS, $this::class);

        $this->db->disconnect();

        if ($stmt->rowCount() == 0) {
            return array();
        }
        return $stmt->fetchAll();
    }
         */
        
         $query = "SELECT a.*, s.idAreaPadre as idArea FROM area as a LEFT JOIN subarea as s on a.id = s.idAreaHijo";

        $this->db->connect();

        $parametros = [];

        $stmt = $this->ejecutarStatement($query, $parametros, PDO::FETCH_CLASS, $this::class);

        $this->db->disconnect();

        if ($stmt->rowCount() == 0) {
            return array();
        }
        return $stmt->fetchAll();

        
    }

    public function registrar() : bool {

        try {
            $this->db->connect();
            $this->beginTransaction();
            $query = "INSERT INTO area (nombre) VALUES (:nombre)";
            $param = [
                'nombre' => $this->nombre
            ];
            $this->ejecutarStatement($query, $param);
            $id = $this->db->pdo()->lastInsertId();
            if($this->idArea != null){
                $query = "INSERT INTO subarea (idAreaPadre, idAreaHijo) VALUES (:idPadre, :idHijo)";
                $param = [
                    'idPadre' => $this->idArea,
                    'idHijo' => $id
                ];
                $this->ejecutarStatement($query, $param);
            }

            $this->commit();

            

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
        $sql = "UPDATE area SET nombre = :nombre WHERE id = :id";

        try {
            $this->db->connect();

            $stmt = $this->prepare($sql);
            $stmt->bindValue('nombre', $this->nombre);
            $stmt->bindValue('id', $this->id);

            $stmt->execute();

             $query = 'DELETE FROM subarea WHERE idAreaHijo = :idHijo';
                $param = [
                    'idHijo' => $this->id
                    ];
                $this->ejecutarStatement($query, $param);

            if($this->idArea != null){

               

                $query = "INSERT INTO subarea (idAreaPadre, idAreaHijo) VALUES (:idPadre, :idHijo)";
                $param = [
                    'idPadre' => $this->idArea,
                    'idHijo' => $this->id
                ];
                $this->ejecutarStatement($query, $param);
            }
            

            
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