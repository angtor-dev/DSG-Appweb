
<?php
require_once "Models/Departamento.php";

class Asistencia extends Model
{
    public int $idDepartamento;
    private string $cedula;
    private string $nombre;
    private string $apellido;
    private string $fechaIn;
    private string $fechaOut;
    private string $status;
    private string $idTrabajador;
    public Departamento $departamento;

    public function __construct() {
        if (!empty($this->idDepartamento)) {
            $this->departamento = Departamento::cargar($this->idDepartamento);
        }
    }

    public static function listarAsistencias () : array{
        $bd = Database::getInstance();
        $bd->connect();
        $query = "SELECT
                    a.*,
                    t.nombre,
                    t.apellido,
                    t.cedula,
                    t.idDepartamento
                FROM
                    `asistencia` AS a
                LEFT JOIN trabajador AS t
                ON
                    t.id = a.id
                WHERE
                    1;";
        $consulta = $bd->pdo()->prepare($query);
        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_CLASS, "asistencia");


        $bd->disconnect();

        if( $consulta->rowCount() == 0){
            return array();
        }

        return $consulta->fetchall();
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