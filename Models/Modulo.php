<?php
class Modulo extends Model
{
    private string $nombre;

    public const BITACORA = 'bitacora';
    public const ROLES = 'roles';
    public const USUARIOS = 'usuarios';
    public const AREAS = 'areas';
    public const ASISTENCIAS = 'asistencias';
    public const CATEGORIAS = 'categorias';
    public const DEPARTAMENTOS = 'departamentos';
    public const INVENTARIO = 'inventario';
    public const MEDIDAS = 'medidas';
    public const NOTIFICACIONES = 'notificaciones';
    public const TAREAS = 'tareas';
    public const TRABAJADORES = 'trabajadores';
    public const ARTICULOS = 'articulos';
    public const AJUSTES = 'ajustes';
    public const MOVIMIENTOS = 'movimientos';
    public const NOTASENTREGA = 'notasentrega';
    public const CARGOS = 'cargos';
    public const TURNOS = 'turnos';

    // Override para impedir eliminar
    public function eliminar(bool $eliminadoLogico = true) : bool
    {
        $_SESSION['errores'][] = "No se pueden eliminar registros de la tabla modulos.";
        return false;
    }

    /** Recupera la instacia con el nombre especificado */
    public static function cargarPorNombre(string $nombre) : null|self
    {
        $db = Database::getInstance();
        
        $db->connectUser();

        $sql = "SELECT * FROM modulo WHERE nombre = :nombre LIMIT 1";

        $stmt = $db->pdo()->prepare($sql);
        $stmt->bindValue("nombre", $nombre);

        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        
        $db->disconnect();

        if ($stmt->rowCount() == 0) {
            return null;
        }
        return $stmt->fetch();
    }

    public function listar(int $estado = null): array
    {
        return $this->listarDBUser($estado);
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