<?php
class Notificacion extends Model
{
    public int $idUsuario;
    private string $titulo;
    private string $descripcion;
    private string $fechaCreacion;
    private int $estado;

    public function notificarUsuario(int $idUsuario, string $mensaje) : bool
    {
        $query = "INSERT INTO notificacion (idUsuario, titulo, descripcion) 
                  VALUES (:idUsuario, :titulo, :descripcion)";
        try {
            $this->db->connect();
            $stmt = $this->db->pdo()->prepare($query);
            $stmt->bindValue(':idUsuario', $idUsuario, \PDO::PARAM_INT);
            $stmt->bindValue(':titulo', 'Notificación', \PDO::PARAM_STR);
            $stmt->bindValue(':descripcion', $mensaje, \PDO::PARAM_STR);
            $stmt->execute();
            $this->db->disconnect();
            return true;
        } catch (\Throwable $th) {
            if (DEVELOPER_MODE) $_SESSION['errores'][] = $th->getMessage();
            $_SESSION['errores'][] = "Ocurrió un error al enviar la notificación.";
            return false;
        }
    }

    public function marcarLeida() : void
    {
        $query = "UPDATE notificacion SET estado = :estado WHERE id = :id";

        try {
            $this->db->connect();
            $stmt = $this->db->pdo()->prepare($query);
            $stmt->bindValue(':estado', EstadoNotif::Leida->value, \PDO::PARAM_INT);
            $stmt->bindValue(':id', $this->id, \PDO::PARAM_INT);
            $stmt->execute();
            $this->db->disconnect();
        } catch (\Throwable $th) {
            if (DEVELOPER_MODE) $_SESSION['errores'][] = $th->getMessage();
            $_SESSION['errores'][] = "Ocurrió un error al marcar la notificación como leída.";
        }
    }

    /**
     * Retorna las notificaciones de un usuario específico.
     * @param int $idUsuario
     * @return Notificacion[]
     */
    public function cargarNotificaciones(int $idUsuario) : array
    {
        $query = "SELECT * FROM notificacion WHERE idUsuario = :idUsuario ORDER BY fechaCreacion DESC";
        try {
            $this->db->connect();
            $stmt = $this->db->pdo()->prepare($query);
            $stmt->bindValue(':idUsuario', $idUsuario, \PDO::PARAM_INT);
            $stmt->execute();
            $notificaciones = $stmt->fetchAll(\PDO::FETCH_CLASS, Notificacion::class);
            $this->db->disconnect();
            return $notificaciones;
        } catch (\Throwable $th) {
            if (DEVELOPER_MODE) $_SESSION['errores'][] = $th->getMessage();
            $_SESSION['errores'][] = "Ocurrió un error al cargar las notificaciones.";
            return [];
        }
    }

    // Getters
    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function getFechaCreacion(): DateTime
    {
        return new DateTime($this->fechaCreacion);
    }

    public function tiempoTranscurrido(): string
    {
        $fecha = new DateTime($this->fechaCreacion);
        $ahora = new DateTime();
        $diferencia = $ahora->diff($fecha);

        if ($diferencia->y > 0) {
            return 'Hace ' . $diferencia->y . ' año' . ($diferencia->y > 1 ? 's' : '');
        }
        if ($diferencia->m > 0) {
            return 'Hace ' . $diferencia->m . ' mes' . ($diferencia->m > 1 ? 'es' : '');
        }
        if ($diferencia->d > 0) {
            return 'Hace ' . $diferencia->d . ' día' . ($diferencia->d > 1 ? 's' : '');
        }
        if ($diferencia->h > 0) {
            return 'Hace ' . $diferencia->h . ' hora' . ($diferencia->h > 1 ? 's' : '');
        }
        if ($diferencia->i > 0) {
            return 'Hace ' . $diferencia->i . ' min';
        }
        return 'Hace unos segundos';
    }

    public function getEstado(): EstadoNotif
    {
        return EstadoNotif::from($this->estado);
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
}

class NotificacionDTO
{
    public bool $success;
    public ?string $message = null;

    public function __construct(bool $success, ?string $message = null)
    {
        $this->success = $success;
        $this->message = $message;
    }

    public function __serialize(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message
        ];
    }

    public function __tostring(): string
    {
        return json_encode($this);
    }
}
