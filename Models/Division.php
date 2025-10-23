<?php
// divisiones de los departamentos
// antes llamado departamentos
class Division extends Model
{
    public ?int $idDepartamento = null;
    private string $nombre;
    public ?Division $divisionPadre = null;

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
        $query = "SELECT d.*, sub.idPadre as idDepartamento FROM division as d LEFT JOIN subdivisiones as sub on d.id = sub.idHijo WHERE sub.idPadre = :idDepartamento";

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
        
        try {
            $this->db->connect();
            $this->beginTransaction();

            $query = "INSERT INTO division (nombre) VALUES (:nombre)";
            $this->ejecutarStatement($query, ["nombre" => $this->nombre]);
            $id = $this->db->pdo()->lastInsertId();

            if($this->idDepartamento != null){
                $query = "INSERT INTO subdivisiones (idPadre, idHijo) VALUES (:idDepartamento, :idDivision)";
                $parametros = ["idDepartamento" => $this->idDepartamento, "idDivision" => $id];
                $this->ejecutarStatement($query, $parametros);
            }



            if($this->getTestingMode()){
                $this->rollBack();
                $this->beginTransaction();
            }

            $this->commit();
            
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

        try {
            $this->db->connect();
            $this->beginTransaction();

            $sql = "UPDATE division SET nombre = :nombre WHERE id = :id";
            $param=[
                'id' => $this->id,
                'nombre' => $this->nombre
            ];

            $this->ejecutarStatement($sql, $param);

            if($this->idDepartamento != null){

                //valida existencia en subdivisiones si existe lo actualiza si no lo inserta

                $sql = "SELECT * FROM subdivisiones WHERE idHijo = :id AND idPadre = :idDepartamento";
                $param=[
                    'id' => $this->id,
                    'idDepartamento' => $this->idDepartamento
                ];
                $resp =$this->ejecutarStatement($sql, $param);
                if($resp->rowCount() == 0){
                    $sql = "INSERT INTO subdivisiones (idPadre, idHijo) VALUES (:idDepartamento, :id)";
                    $param=[
                        'id' => $this->id,
                        'idDepartamento' => $this->idDepartamento
                    ];
                }
                else{
                    $sql = "UPDATE subdivisiones SET idPadre = :idDepartamento WHERE idHijo = :id";
                    $param=[
                        'id' => $this->id,
                        'idDepartamento' => $this->idDepartamento
                    ];
                }
                // TODO esta verga no sirve XDDDDDD

                $this->ejecutarStatement($sql, $param);


                
            }
            else{
                $sql = "DELETE FROM subdivisiones WHERE idHijo = :id";
                $param=[
                    'id' => $this->id
                ];
                $this->ejecutarStatement($sql, $param);
            }

            if($this->getTestingMode()){
                $this->rollBack();
                $this->beginTransaction();
            }

            $this->commit();
      
            $this->db->disconnect();

            return true;
        } catch (\Throwable $th) {
            if (DEVELOPER_MODE) debug($th);
            $_SESSION['errores'][] = "Ha ocurrido un error al actualizar la División.";
            return false;
        }
    }
    /**
     * Summary of listar
     * @param int $estado
     * @return Division[]
     */
    public function listar(int $estado = null) : array
    {

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

    public function setDatos(?int $id = null, string $nombre, ?int $idDepartamento = null) : void
    {
        if (isset($id)) {
            $this->id = $id;
        }
        $this->nombre = $nombre;
        $this->idDepartamento = $idDepartamento;
    }

    // Getters
    public function getNombre() : string {
        return $this->nombre;
    }
    public function set_idDepartamento($value):void{
        if($value != ''){
          $this->idDepartamento = intval($value);
        }
    }
}
?>