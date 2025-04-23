<?php
require_once "Models/Model.php";

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
        
        $db->connect();

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

    // Getters
    public function getNombre() : string {
        return $this->nombre;
    }
}