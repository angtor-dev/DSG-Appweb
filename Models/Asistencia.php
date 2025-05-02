
<?php
require_once "Models/Departamento.php";

class Asistencia extends Model
{
    public int $idDepartamento;
    private string $turno;
    private string $fecha;
    private string $fechaIn;
    private string $fechaOut;
    private string $status;
    private string $idTrabajador;
    public Departamento $departamento;
    public Trabajador $trabajador;
    private array $trabadores;

    public function __construct() {
        if (!empty($this->idDepartamento)) {
            $this->departamento = Departamento::cargar($this->idDepartamento);
        }
    }
    /**
     * llena las propiedades de la clase si el formulario fue enviado
     * y el campo correspondiente no esta vacio
     * @return void
     */
    public function mapearFormulario(){
        $this->fechaIn = $_POST["fechaIn"] ?? "";
        $this->fechaOut = $_POST["fechaOut"] ?? "";
        $this->status = $_POST["status"] ?? "";
        $this->idTrabajador = $_POST["idTrabajador"] ?? "";
        $this->idDepartamento = $_POST["idDepartamento"] ?? "";
    }

    public function esValido() : bool {
        // valida el trabajador en la base de datos
        if (empty($this->idTrabajador)) {
            $_SESSION['errores'][] = "El campo 'Trabajador' es obligatorio";
            return false;
        }
        $stmt = $this->db->prepare("SELECT * FROM trabajador WHERE id = :idTrabajador");
        $stmt->execute([':idTrabajador' => $this->idTrabajador]);
        $trabajador = $stmt->fetch();
        if (!$trabajador) {
            $_SESSION['errores'][] = "El trabajador no existe";
            return false;
        }
        // valida el departamento en la base de datos
        if (empty($this->idDepartamento)) {
            $_SESSION['errores'][] = "El campo 'Departamento' es obligatorio";
            return false;
        }
        $stmt = $this->db->prepare("SELECT * FROM departamento WHERE id = :idDepartamento");
        $stmt->execute([':idDepartamento' => $this->idDepartamento]);
        $departamento = $stmt->fetch();
        if (!$departamento) {
            $_SESSION['errores'][] = "El departamento no existe";
            return false;
        }
        return true;
    }

    public function registrar() : bool {
        $query = "INSERT INTO fechaAsistencia(fecha, turno) VALUES( :fecha, :turno)";
        try {
            $this->db->pdo()->beginTransaction();
            $this->db->connect();
            $stmt = $this->prepare($query);
            $stmt->bindValue("turno", $this->turno);
            $stmt->bindValue("fecha", $this->fecha);
            $stmt->execute();
            $lastId = $this->db->pdo()->lastInsertId();
            
            $stmt = $this->prepare("INSERT INTO asistencia(idTrabajador, idFechaAsistencia, fechaIn, fechaOut, status) VALUES( :idTrabajador, :idFechaAsistencia, :fechaIn, :fechaOut, :status)");
            $stmt->bindValue("idTrabajador", $this->idTrabajador);
            $stmt->bindValue("idFechaAsistencia", $lastId);
            $stmt->bindValue("fechaIn", $this->fechaIn);
            $stmt->bindValue("fechaOut", $this->fechaOut);
            $stmt->bindValue("status", 0);
            $stmt->execute();

            //$this->db->pdo()->commit();

            



            $this->db->disconnect();
            return true;
        } catch (\Throwable $th) {
            if($this->db->pdo() instanceof PDO) 
            {
                if ($this->db->pdo()->inTransaction()) {
                    $this->db->pdo()->rollBack();
                }
            }
            if (DEVELOPER_MODE) debug($th);
            $_SESSION['errores'][] = "Ha ocurrido un error al registrar la asistencia.";
            return false;
        }
    }

    


    // Getters
    public function getNombre() : string {
        return $this->nombre;
    }
    public function getApellido() : string {
        return $this->apellido;
    }
    public function getNombreCompleto() : string {
        return $this->nombre . " " . $this->apellido;
    }
    public function getCedula() : string {
        return $this->cedula;
    }
    public function getfechaOut() :string {
        return $this->fechaOut;
    }
    public function getfechaIn() :string {
        return $this->fechaIn;
    }
    public function getstatus() :string {
        return $this->status;
    }

    /**
     * retorna la celda con el dia de la asistencia y una celda de la tabla con un colspan de 2 que dice inasistente si el status es 1
     * si no retorna la celda con el dia de la asistencia y dos celdas con la hora de entrada y salida
     * @return string
     */
    public function getEntrada() :string {
        if ($this->status == "1") {
            return "
                <td class='text-nowrap'>" . $this->getDia($this->fechaIn) . "</td>
                <td colspan='2'>Inasistente</td>
                <td class='d-none'></td>
                <td class='d-none'></td>
            ";
        }
        else{
            return "
                <td class='text-nowrap'>" . $this->getDia($this->fechaIn) . "</td>
                <td>" . $this->getHora($this->fechaIn) . "</td>
                <td>" . $this->getHora($this->fechaOut) . "</td>
            ";
        }
    }

    /**
     * retorna el dia de una fecha dada
     * @param string $value fecha en formato Y-m-d h:i:s
     * @return string fecha en formato Y-m-d
     */
    public function getDia(string $value) :string {
        return substr($value, 0, 10);
    }

    /**
     * retorna la hora de una fecha dada
     * @param string $value fecha en formato Y-m-d h:i:s
     * @return string hora en formato h:i
     */
    public function getHora(string $value) :string {
        return substr($value, 11);
    }
}

?>