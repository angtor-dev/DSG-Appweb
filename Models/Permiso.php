<?php
class Permiso extends Model
{
    public int $id;
    private int $idRol;
    private int $idModulo;
    private bool $consultar = false;
    private bool $registrar = false;
    private bool $actualizar = false;
    private bool $eliminar = false;
    public Modulo $modulo;

    public const CONSULTAR = 'consultar';
    public const REGISTRAR = 'registrar';
    public const ACTUALIZAR = 'actualizar';
    public const ELIMINAR = 'eliminar';

    public function __construct($idRol = null, $idModulo = null, $consultar = false, $registrar = false, $actualizar = false, $eliminar = false)
    {
        parent::__construct();
        if (!empty($idRol) && !empty($idModulo)) {
            $this->idRol = $idRol;
            $this->idModulo = $idModulo;
            $this->consultar = $consultar;
            $this->registrar = $registrar;
            $this->actualizar = $actualizar;
            $this->eliminar = $eliminar;
        }
        if (!empty($this->idModulo)) {
            $this->modulo = Modulo::cargar($this->idModulo, true);
        }
    }

    public function registrar() : bool
    {
        $query = "INSERT INTO permiso(idRol, idModulo, consultar, registrar, actualizar, eliminar)
            VALUES(:idRol, :idModulo, :consultar, :registrar, :actualizar, :eliminar)";
        
        try {
            $this->db->connectUser();

            $stmt = $this->prepare($query);
            $stmt->bindValue("idRol", $this->idRol);
            $stmt->bindValue("idModulo", $this->idModulo);
            $stmt->bindValue("consultar", $this->consultar, PDO::PARAM_BOOL);
            $stmt->bindValue("registrar", $this->registrar, PDO::PARAM_BOOL);
            $stmt->bindValue("actualizar", $this->actualizar, PDO::PARAM_BOOL);
            $stmt->bindValue("eliminar", $this->eliminar, PDO::PARAM_BOOL);

            $stmt->execute();
            
            $this->db->disconnect();

            return true;
        } catch (\Throwable $th) {
            $_SESSION['errores'][] = "Ha ocurrido un error al registrar los permisos de rol.";
            return false;
        }
    }

    public function actualizar() : bool
    {
        $query = "UPDATE permiso
            SET consultar = :consultar, registrar = :registrar, actualizar = :actualizar, eliminar = :eliminar
            WHERE id = $this->id";

        try {
            $this->db->connectUser();

            $stmt = $this->prepare($query);
            $stmt->bindValue('consultar', $this->consultar);
            $stmt->bindValue('registrar', $this->registrar);
            $stmt->bindValue('actualizar', $this->actualizar);
            $stmt->bindValue('eliminar', $this->eliminar);

            $stmt->execute();
            
            $this->db->disconnect();

            return true;
        } catch (\Throwable $th) {
            if (DEVELOPER_MODE) debug($th);
            $_SESSION['errores'][] = "Ha ocurrido un error al actualizar los permisos de rol.";
            return false;
        }
    }

    // Getters
    public function getConsultar() : bool {
        return $this->consultar;
    }
    public function getActualizar() : bool {
        return $this->actualizar;
    }
    public function getRegistrar() : bool {
        return $this->registrar;
    }
    public function getEliminar() : bool {
        return $this->eliminar;
    }

    // Setters
    public function setAllFalse() : void
    {
        $this->consultar = false;
        $this->registrar = false;
        $this->actualizar = false;
        $this->eliminar = false;
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

    public function setPermiso(string $permiso, bool $valor = false)
    {
        $this->$permiso = $valor;
    }
}