<?php
class Rol extends Model
{
    private string $nombre;
    private ?string $descripcion;

    /** @var Permiso[] */
    public array $permisos = array();

    public function __construct()
    {
        parent::__construct();
        if (!empty($this->id)) {
            $this->permisos = Permiso::listarPorRelacion($this->id, get_class(), null, true);
        }
    }

    public function registrar() : bool
    {
        $query = "INSERT INTO rol (nombre, descripcion)
            VALUES (:nombre, :descripcion)";
            
        try {
            $moduloObj = new Modulo();
            $modulos = $moduloObj->listarDBUser();
            $this->db->connectUser();

            $this->beginTransaction();

            // Registra el rol
            $stmt = $this->prepare($query);
            $stmt->bindValue("nombre", $this->nombre);
            $stmt->bindValue("descripcion", $this->descripcion);

            $stmt->execute();
            $idRol = $this->db->pdo()->lastInsertId();

            // Crea permisos del rol
            $sql = "INSERT INTO permiso(idRol, idModulo)
                VALUES(:idRol, :idModulo)";

            $stmt = $this->prepare($sql);

            foreach ($modulos as $modulo) {
                $stmt->bindParam("idRol", $idRol);
                $stmt->bindParam("idModulo", $modulo->id);

                $stmt->execute();
            }

            if($this->getTestingMode()){
                $this->rollBack();
                $this->beginTransaction();
            }
            else {
                Bitacora::registrarTransaccion("Se ha creado el rol " . $this->nombre, $this->db->pdo());
            }

            // Guarda los cambios
            $this->commit();

            $this->db->disconnect();

            return true;
        } catch (\Throwable $th) {
            $this->disconectHandlerExeption();
            if(DEVELOPER_MODE){
                debug($th);
            }
            $_SESSION['errores'][] = "Ocurrio un error al registrar el rol";
            return false;
        }
    }

    public function actualizar() : bool
    {
        $sql = "UPDATE rol SET nombre = :nombre, descripcion = :descripcion WHERE id = :id";

        try {
            $this->db->connectUser();
            $this->beginTransaction();

            $stmt = $this->prepare($sql);
            $stmt->bindValue('nombre', $this->nombre);
            $stmt->bindValue('descripcion', $this->descripcion);
            $stmt->bindValue('id', $this->id);

            $stmt->execute();

            if($this->getTestingMode()){
                $this->rollBack();
                $this->beginTransaction();
            }
            else {
                Bitacora::registrarTransaccion("Se ha actualizado el rol " . $this->nombre, $this->db->pdo());
            }

            $this->commit();
            
            $this->db->disconnect();

            return true;
        } catch (\Throwable $th) {
            $_SESSION['errores'][] = "Ha ocurrido un error al actualizar el rol.";
            return false;
        }
    }

    public function SincronizarPermisos() : void
    {
        $this->permisos = (!empty($this->id)) ? Permiso::listarPorRelacion($this->id, get_class(), null, true) : [];
    }

    public function listar(int $estado = null): array
    {
        $table = strtolower(static::class);
        $query = "SELECT * FROM $table" . (isset($estado) ? " WHERE estado = :estado" : "");

        $this->db->connectUser();

        $stmt = $this->db->pdo()->prepare($query);
        if (isset($estado))
            $stmt->bindValue('estado', $estado);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, $table);

        $this->db->disconnect();

        if ($stmt->rowCount() == 0) {
            return array();
        }
        return $stmt->fetchAll();
    }


    public function esValido() : bool
    {
        if (empty(trim($this->nombre))) {
            $_SESSION['errores'][] = "El campo 'Nombre' es obligatorio";
            return false;
        }
        if (!preg_match(REG_ALFANUMERICO, $this->nombre)) {
            $_SESSION['errores'][] = "El campo 'Nombre' solo puede contener letras y números";
            return false;
        }
        if (!preg_match(REG_ALFANUMERICO, $this->descripcion)) {
            $_SESSION['errores'][] = "El campo 'Descripcion' solo puede contener letras y números";
            return false;
        }
        return true;
    }

    public function tienePermiso(string $modulo, string $permiso) : bool
    {
        $permiso = "get".ucfirst($permiso);
        
        foreach ($this->permisos as $p) {
            if ($p->modulo->getNombre() == strtolower($modulo) && $p->$permiso()) {
                return true;
            }
        }
        return false;
    }

    public function mapearFormulario() : bool
    {
        try {
            $this->nombre = $_POST['nombre'];
            $this->descripcion = $_POST['descripcion'];
            if (!empty($_POST['id'])) {
                $this->id = $_POST['id'];
            }

            return true;
        } catch (\Throwable $th) {
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
    public function getDescripcion() : string {
        return $this->descripcion;
    }
}