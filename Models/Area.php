<?php
class Area extends Model
{
    public ?int $idArea = null;
    private string $nombre;
    public ?Area $areaPadre = null;

    const SHOW_EXCEPTION = 1001;

    

    public function esValido($checkId = false) : bool {

        $ok = true;

        try {
            if (!isset($this->nombre) || empty(trim($this->nombre))) {
                throw new Exception("El nombre del área es requerido",self::SHOW_EXCEPTION);
            }
            if (!preg_match("/^[\s0-9a-zA-ZáÁéÉíÍóÓúÚüÜñÑ.,-]+$/", $this->nombre)) {
                throw new Exception("El nombre del área no puede contener caracteres especiales",self::SHOW_EXCEPTION);
            }
            if($this->idArea != null && !is_numeric($this->idArea)){
                throw new Exception("El id del área padre debe ser un número",self::SHOW_EXCEPTION);
            }
    
            // base de datos 
    
            $query = "SELECT nombre FROM area WHERE nombre = :nombre AND id != :id";
            $query = $checkId ? $query : "SELECT nombre FROM area WHERE nombre = :nombre";
    
            $param = ['nombre' => $this->nombre];
            if($checkId) $param['id'] = $this->id;
    
    
            $this->db->connect();
    
            $resp = $this->ejecutarStatement($query, $param);// valido el nombre que no exista
            // Temporal para las pruebas
            // if($data = $resp->fetch()){
            //     throw new Exception("El nombre del area (".$data["nombre"].") ya esta registrado", self::SHOW_EXCEPTION);
            // }
    
            if($this->idArea != null){
                $query = "SELECT * FROM area WHERE id = :idArea";
                $param = ['idArea' => $this->idArea];
                $resp = $this->ejecutarStatement($query, $param);
                if(!$resp->fetch()){
                    throw new Exception("El area padre no existe", self::SHOW_EXCEPTION);
                }
            }
        } catch (\Throwable $th) {
            echo $th;
            $this->disconectHandlerExeption();
            if (DEVELOPER_MODE && $th->getCode() != self::SHOW_EXCEPTION) $_SESSION['errores'][] = $th->getMessage();
            $_SESSION['errores'][] = ($th->getCode() == self::SHOW_EXCEPTION) ? $th->getMessage() : "Ocurrio un error al validar el área";
            $ok = false;
        }

        return $ok;
        
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

            if(!$this->testHandler()){
                Bitacora::registrarTransaccion("Area {$this->nombre} registrado", $this->db->pdo());
            }

            $this->commit();

            

            $this->db->disconnect();

            return true;
        } catch (\Throwable $th) {
            $this->disconectHandlerExeption();
            if (DEVELOPER_MODE && $th->getCode() != self::SHOW_EXCEPTION) $_SESSION['errores'][] = $th->getMessage();
            $_SESSION['errores'][] = ($th->getCode() == self::SHOW_EXCEPTION) ? $th->getMessage() : "Ocurrio un error al registrar el área";
            return false;
        }
    }

    public function actualizar() : bool
    {
        $sql = "UPDATE area SET nombre = :nombre WHERE id = :id";

        try {
            $this->db->connect();
            $this->beginTransaction();

            

            if($this->idArea == $this->id){
                throw new Exception("El area no puede ser padre de si mismo", self::SHOW_EXCEPTION);
            }

            $original = $this->ejecutarStatement("SELECT * FROM area WHERE id = :id", ['id' => $this->id])->fetch();

            if(!$original){
                throw new Exception("El area seleccionada no existe", self::SHOW_EXCEPTION);
            }
            


            
            $this->ejecutarStatement($sql, ['nombre' => $this->nombre, 'id' => $this->id]);

             $query = 'DELETE FROM subarea WHERE idAreaHijo = :idHijo';
                $param = [
                    'idHijo' => $this->id
                    ];
                $this->ejecutarStatement($query, $param);

            if($this->idArea != null){

                $originalSub = $this->ejecutarStatement("SELECT * FROM area WHERE id = :id", ['id' => $this->idArea])->fetch();
                if(!$originalSub){
                    throw new Exception("El area padre seleccionada no existe", self::SHOW_EXCEPTION);
                }

                $query = "INSERT INTO subarea (idAreaPadre, idAreaHijo) VALUES (:idPadre, :idHijo)";
                $param = [
                    'idPadre' => $this->idArea,
                    'idHijo' => $this->id
                ];
                $this->ejecutarStatement($query, $param);
            }

            if(!$this->testHandler()){
                $mensaje = ($this->nombre != $original['nombre']) ? "Area '{$original['nombre']}' => '{$this->nombre}' actualizado correctamente" : "Area '{$original['nombre']}' actualizado";
                Bitacora::registrarTransaccion($mensaje, $this->db->pdo());
            }
            $this->commit();
            

            
            $this->db->disconnect();

            return true;
        } catch (\Throwable $th) {
            //if (DEVELOPER_MODE) debug($th);
            $_SESSION['errores'][] = ($th->getCode() == self::SHOW_EXCEPTION) ? $th->getMessage() : "Ocurrio un error al registrar el área";
            return false;
        }
    }

    public function eliminarArea(){
        
        try {
            /**
             * @var Area
             */
            $area = $this->cargar($this->id);
            $area->setTestingMode($this->getTestingMode());
    
            if (empty($area)) {
                throw new Exception("El área que intenta eliminar no existe", 1);
            }
    
            $subareas = $area->listarSubareas();
    
            if (count($subareas) > 0) {
                throw new Exception("El área que intenta eliminar tiene subareas, asegurate de eliminarlas primero", 1);
            }
    
            if ($area->eliminar(false)) {
                $_SESSION['exitos'][] = "Área eliminada con exito";
                if(!$this->getTestingMode()){ // si no estamos en modo de pruebas
                    Bitacora::registrar("Área '".$area->getNombre()."' eliminado");
                }
            }
        } catch (\Throwable $th) {
            $_SESSION['errores'][] = $th->getMessage();
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

    public function setDatos(?int $id = null, string $nombre, ?int $idArea = null) : void
    {
        if (isset($id)) {
            $this->id = $id;
        }
        $this->nombre = $nombre;
        $this->idArea = $idArea;
    }

    // Getters
    public function getNombre() : string {
        return $this->nombre;
    }
}