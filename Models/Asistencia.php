
<?php
class Asistencia extends Model
{
    public int|string $idDepartamento;
    private string $turno;
    private string $fecha;
    private string $fechaIn;
    private string $fechaOut;
    private string $status;
    private string $idTrabajador;
    public Departamento|string $departamento;
    public Trabajador $trabajador;
    private array $trabajadores;
    // lista de acciones para validar
    const LISTAR_TRABAJADORES = 1;

    public function __construct() {
        parent::__construct();
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
        if(empty($_POST)) return;
        $this->fechaIn = $_POST["fechaIn"] ?? "";
        $this->fechaOut = $_POST["fechaOut"] ?? "";
        $this->status = $_POST["status"] ?? "";
        $this->idTrabajador = $_POST["idTrabajador"] ?? "";
        $this->idDepartamento = $_POST["idDepartamento"] ?? "";
        $this->fecha = $_POST["fecha"] ?? "";
        $this->turno = $_POST["turno"] ?? "";
        $this->trabajadores = ( isset($_POST['trabajadores']) and is_array($_POST['trabajadores']) ) ? $_POST['trabajadores'] : Array();
    }

    public function esValido($control) : bool {
        if($control == self::LISTAR_TRABAJADORES) {
            // se espera que el departamento este seteado con el nombre del departametno
            // y se verifica con la base de datos su existencia
            if(empty($this->idDepartamento)) {
                throw new Exception("El departamento no esta seteado");
            }
            if(!preg_match("/^\d$/", trim($this->idDepartamento))) {
                throw new Exception("El departamento no es valido ".$this->idDepartamento);
            }
            $this->db->connect();
            $stmt = $this->db->pdo();
            $stmt = $stmt->prepare("SELECT id FROM departamento WHERE id = :departamento");
            $stmt->bindValue("departamento", $this->idDepartamento);
            $stmt->execute();
            $this->db->disconnect();
            if($stmt->rowCount() == 0) {
                throw new Exception("El departamento no existe en la base de datos");
            }
            $this->idDepartamento = $stmt->fetchColumn();
            return true;
        }
        else {
            return false;
        }
    }


    public function registrar($print) : array {
        $var = "";
        try {
            // Primero registrar la fecha de asistencia en la tabla fechaasistencia si no existe
            $this->db->connect();
            $this->db->pdo()->beginTransaction();
            $stmt = $this->db->pdo()->prepare("SELECT id FROM fechaasistencia WHERE idDepartamento = :idDepartamento AND fecha = :fecha AND turno = :turno");
            $stmt->bindValue("idDepartamento", $this->idDepartamento);
            $stmt->bindValue("fecha", $this->fecha);
            $stmt->bindValue("turno", $this->turno);
            $stmt->execute();
            // TODO validar fields
            // si no existe lo registramos
            if (!$stmt->rowCount() > 0) {
                $stmt = $this->db->pdo()->prepare("INSERT INTO fechaasistencia (idDepartamento, fecha, turno) VALUES (:idDepartamento, :fecha, :turno)");
                $stmt->bindValue("idDepartamento", $this->idDepartamento);
                $stmt->bindValue("fecha", $this->fecha);
                $stmt->bindValue("turno", $this->turno);
                $stmt->execute();
                $idFechaAsistencia = $this->db->pdo()->lastInsertId();
            }
            else {
                $idFechaAsistencia = $stmt->fetchColumn();
            }
    
            // Ahora registrar las asistencias de los trabajadores en la tabla asistencia
            foreach ($this->trabajadores as $trabajador) {
                
                if ($trabajador['idAsistencia']) {
                    // Si el trabajador ya tiene un registro de asistencia actualizarlo
                    if(!preg_match("/^\d+$/", $trabajador['idAsistencia'])) {
                        throw new Exception("El id de asistencia no es valido");
                    }
                    $stmt = $this->db->pdo()->prepare("UPDATE asistencia SET fechaIn = :fechaIn, fechaOut = :fechaOut, `status` = :status WHERE id = :idAsistencia");
                    $stmt->bindValue("fechaIn", ($trabajador['fechaIn'] !="")?$trabajador['fechaIn']:NULL);
                    $stmt->bindValue("fechaOut", ($trabajador['fechaOut'] != "")?$trabajador['fechaOut']:NULL);
                    $stmt->bindValue("status", ($trabajador['inasistencia'] !="")?$trabajador['inasistencia']:0);
                    $stmt->bindValue("idAsistencia", $trabajador['idAsistencia']);
                    $stmt->execute();
                    $idAsistencia = $trabajador['idAsistencia'];
                } else {
                    // Si el trabajador no tiene un registro de asistencia crear
                    $stmt = $this->db->pdo()->prepare("INSERT INTO asistencia (idTrabajador, idFechaAsistencia, fechaIn, fechaOut, `status`) VALUES (:idTrabajador, :idFechaAsistencia, :fechaIn, :fechaOut, :status)");
                    $stmt->bindValue("idTrabajador", $trabajador['idTrabajador']);
                    $stmt->bindValue("idFechaAsistencia", $idFechaAsistencia);
                    $stmt->bindValue("fechaIn", ($trabajador['fechaIn'] !="")?$trabajador['fechaIn']:NULL);
                    $stmt->bindValue("fechaOut", ($trabajador['fechaOut'] != "")?$trabajador['fechaOut']:NULL);
                    $stmt->bindValue("status", ($trabajador['inasistencia'] !="")?$trabajador['inasistencia']:0);
                    $stmt->execute();
                    $idAsistencia = $this->db->pdo()->lastInsertId();
                }
    
                // Si el trabajador tiene una inasistencia y no tiene justificación debemos registrarla
                if (intval($trabajador['inasistencia']) == 1) {
                    $stmt = $this->db->pdo()->prepare("INSERT INTO justificacion (idAsistencias, tipo, observacion) VALUES (:idAsistencias, :tipo, :observacion)
                    ON DUPLICATE KEY UPDATE tipo = :tipoUpdate, observacion = :observacionUpdate");
                    $stmt->bindValue(":idAsistencias", $idAsistencia);
                    $stmt->bindValue(":tipo", ($trabajador['justificacion'] !="")? intval($trabajador['justificacion']):1);
                    $stmt->bindValue(":observacion", $trabajador['justificacion_descripcion']);
                    $stmt->bindValue(":tipoUpdate", ($trabajador['justificacion'] !="")? intval($trabajador['justificacion']):1);
                    $stmt->bindValue(":observacionUpdate", $trabajador['justificacion_descripcion']);
                    $stmt->execute();
                }
                // si no tiene en el arreglo pasamos un delete a la justificacion
                else{
                    $stmt = $this->db->pdo()->prepare("DELETE FROM justificacion WHERE idAsistencias = :idAsistencias");
                    $stmt->bindValue("idAsistencias", $idAsistencia);
                    $stmt->execute();
                }
            }

            $this->db->pdo()->commit();
            $this->db->disconnect();
            $response = [
                "success" => true,
                "message" => "Asistencia registrada con exito"
            ];

            
    
        } catch (\Throwable $th) {
            $response = [
                "success" => false,
                "message" => "Error al registrar la asistencia",
                "Error" => $th->getMessage()." : linea: ".$th->getLine(),
            ];
        }
        if ($print) {
            echo json_encode($response);
        }

        return $response;
    }

    // si $print es true imprime los resultados y no retorna nada
    
    /**
     * Muestra la lista de asistencias de los trabajadores
     * @param bool $print Si es true imprime los resultados y no retorna nada
     * @return stdClass|array Un objeto con la lista de asistencias y trabajadores,
     * o un array con un error si ocurre alguno
     */
    public function listarAsistenciasTrabajadores($print = false)  {
        try {
            $this->esValido(self::LISTAR_TRABAJADORES);
            $this->db->connect();
            $pdo = $this->db->pdo();
            $pdo->beginTransaction();
            // optengo la lista de registros de asistencias que cumplan con los filtros
            $stmt = $pdo->prepare("
            SELECT 
                t.id as idTrabajador
                ,t.cedula
                ,CONCAT(t.nombre,' ',t.apellido) as nombre
                ,a.fechaIn
                ,a.fechaOut
                ,a.status
                ,a.id as idAsistencia
                ,j.tipo + 0 as tipo_justificacion
                ,j.observacion as observacion_justificacion
                ,aj.idAsistenciaAjuste as idAjuste
                ,1 as registro
                
                
            FROM asistencia as a 
            JOIN trabajador as t on t.id = a.idTrabajador

            JOIN fechaasistencia as fa on fa.id = a.idFechaAsistencia
            JOIN departamento as d on d.id = fa.idDepartamento
            LEFT JOIN justificacion as j on j.idAsistencias = a.id
            LEFT JOIN ajusteasistencia as aj on aj.idAsistencia = a.id
            WHERE fa.fecha = :fecha AND d.id = :idDepartamento and fa.turno= :turno
            ");
            $stmt->bindValue("idDepartamento", $this->idDepartamento);
            $stmt->bindValue("fecha", $this->fecha);
            $stmt->bindValue("turno", $this->turno);
            $stmt->execute();

            $asistenciasRegistros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // optengo la lista de trabajadores que cumplan con los filtros

            $stmt = $pdo->prepare("
                SELECT
                    t.id as idTrabajador
                    ,t.cedula
                    ,CONCAT(t.nombre,' ',t.apellido) as nombre
                    ,0 as registro
                    

                FROM
                    trabajador AS t
                JOIN departamento as d on d.id = t.idDepartamento
                WHERE t.turno = :turno AND d.id = :idDepartamento;
            ");
            $stmt->bindValue("idDepartamento", $this->idDepartamento);
            $stmt->bindValue("turno", $this->turno);
            $stmt->execute();

            $trabajadoresRegistros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // se crea un objeto con la lista final a mostrar

            $listaFinal = new stdClass();
            $listaAjustes = [];
            foreach ($asistenciasRegistros as $asistencia) {
                $asistencia["registro"] = 1;
                // guardo el ajuste si existe
                if(isset($asistencia["idAjuste"])) {
                    $listaAjustes[] = [ "idAjuste" => $asistencia["idAjuste"],"idAsistencia" => $asistencia["idAsistencia"]];
                }
                $listaFinal->{"idTrabajador_".$asistencia["idTrabajador"]} = $asistencia;
            }
            foreach ($trabajadoresRegistros as $trabajador) {
                if (!isset($listaFinal->{"idTrabajador_".$trabajador["idTrabajador"]})) {
                    $trabajador["registro"] = 0;
                    $listaFinal->{"idTrabajador_".$trabajador["idTrabajador"]} = $trabajador;
                }
            }

            // si tiene un ajuste se obtiene los datos del ajuste de la base de datos
            // esta ves no tienen que ver los filtros

            if (count($listaAjustes) > 0) {
                $query = "SELECT 
                        a.id
                        ,a.fechaIn
                        ,a.fechaOut
                        ,fa.idDepartamento
                        ,fa.fecha
                        ,fa.turno

                        FROM asistencia as a 
                        LEFT JOIN ajusteasistencia as aj on aj.idAsistenciaAjuste = a.id
                        LEFT JOIN fechaasistencia as fa on fa.id = a.idFechaAsistencia
                        WHERE a.id IN";
                $query .= "(";
                $query .= implode(",", array_fill(0, count($listaAjustes), "?"));
                $query .= ")";

                $stmt = $pdo->prepare($query);
                $stmt->execute(array_column($listaAjustes, "idAsistencia"));
                foreach ($stmt->fetchAll() as $ajuste) {
                    $listaFinal->{"idTrabajador_".$ajuste["idTrabajador"]}["Ajuste"] = $ajuste;
                }
            }

            $pdo->commit();
            $pdo = null;
            $this->db->disconnect();
            if($print) {
                echo json_encode($listaFinal);
            }
            return $listaFinal;
        } catch (Exception $th) {
            if(isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $this->db->disconnect();
            if($print) {
                echo json_encode(["error" => $th->getMessage()]);
            }
            return ["error" => $th->getMessage()];
        }
    }

    


    // Getters

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