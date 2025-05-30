
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
    private string $idAsistencia;
    public Departamento|string $departamento;
    private array $trabajadores;
    // lista de acciones para validar
    const LISTAR_TRABAJADORES = 1;
    const REGISTRAR_ASISTENCIA = 2;
    const ELIMINAR_ASISTENCIA = 3;

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
        $this->fechaIn = trim($_POST["fechaIn"] ?? "");
        $this->fechaOut = trim($_POST["fechaOut"] ?? "");
        $this->status = trim($_POST["status"] ?? "");
        $this->idTrabajador = trim($_POST["idTrabajador"] ?? "");
        $this->idDepartamento = trim($_POST["idDepartamento"] ?? "");
        $this->fecha = trim($_POST["fecha"] ?? "");
        $this->turno = trim($_POST["turno"] ?? "");
        $this->trabajadores = ( isset($_POST['trabajadores']) and is_array($_POST['trabajadores']) ) ? $_POST['trabajadores'] : Array();
        $this->idAsistencia = trim($_POST["idAsistencia"] ?? "");
    }
    /**
     * Se debe hacer la conexion antes de llamar a esta funcion
     * @param mixed $control
     * @throws \Exception
     * @return bool
     */
    public function esValido($control) : bool {
        // si el control es listar trabajadores o registrar ajuste
        // valida el departamento
        try{
            
            if(
                $control == self::LISTAR_TRABAJADORES ||
                $control == self::REGISTRAR_ASISTENCIA
            ) {
                // valido la fecha de asistencia

                if(!isset($this->fecha) || empty($this->fecha)) {
                    $sms = (DEVELOPER_MODE) ? "La fecha no esta seteada":"Debe seleccionar una fecha" ;
                    throw new Exception($sms);
                }
                $this->fecha = trim($this->fecha ?? "");
                if(empty($this->fecha)) {
                    $sms = (DEVELOPER_MODE) ? "La fecha no esta seteada":"Debe seleccionar una fecha" ;
                    throw new Exception($sms);
                }
                if(!preg_match("/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/", trim($this->fecha))) {
                    $sms = (DEVELOPER_MODE) ? "La fecha no es valida ".$this->fecha:"La fecha no es valida" ;
                    throw new Exception($sms);
                }

                if(!isset($this->idDepartamento) || empty($this->idDepartamento)) {
                    $sms = (DEVELOPER_MODE) ? "El departamento no esta seteado":"Debe seleccionar un departamento" ;
                    throw new Exception($sms);
                }


                $this->idDepartamento = trim($this->idDepartamento ?? "");
                if(empty($this->idDepartamento)) {
                    $sms = (DEVELOPER_MODE) ? "El departamento no esta seteado":"Debe seleccionar un departamento" ;
                    throw new Exception($sms);
                }
                if(!preg_match("/^\d+$/", trim($this->idDepartamento))) {
                    $sms = (DEVELOPER_MODE) ? "El departamento no es valido ".$this->idDepartamento:"El departamento no es valido" ;
                    throw new Exception($sms);
                }
                
                $stmt = $this->prepare("SELECT id FROM departamento WHERE id = :departamento");
                $stmt->bindValue("departamento", $this->idDepartamento);
                $stmt->execute();
                if($stmt->rowCount() == 0) {
                    
                    throw new Exception("El departamento seleccionado no existe en la base de datos");
                }
                
                $this->idDepartamento = $stmt->fetchColumn();
            }

            if($control == self::ELIMINAR_ASISTENCIA){
                if(empty($this->idAsistencia)) {
                    $sms = (DEVELOPER_MODE) ? "El id de asistencia no esta seteado":"Debe seleccionar un asistencia" ;
                    throw new Exception($sms);
                }
                if(!preg_match("/^\d+$/", $this->idAsistencia)) {
                    $sms = (DEVELOPER_MODE) ? "El id de asistencia no es valido ".$this->idAsistencia:"El id de asistencia no es valido" ;
                    throw new Exception($sms);
                }
            }

           
            return true;
       }
        finally {
            
        }
    }


        /**
         * Registra la asistencia de los trabajadores en la tabla asistencia y fechaasistencia
         * @param bool $print Si es true imprime los resultados y no retorna nada
         * @return array Un array con el resultado de la operacion
         * @throws Exception Si ocurre un error al registrar la asistencia
         */
    public function registrar($print) : array {
        try {
            // Primero registrar la fecha de asistencia en la tabla fechaasistencia si no existe
            $this->db->connect();
            $this->esValido(self::REGISTRAR_ASISTENCIA);
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

                if (isset($trabajador['idAsistencia'] )) {

                    // Si el trabajador ya tiene un registro de asistencia actualizarlo

                    $trabajador['idAsistencia'] = trim($trabajador['idAsistencia']);
                    if(!preg_match("/^\d+$/", $trabajador['idAsistencia'])) {
                        throw new Exception("El id de asistencia no es valido");
                    }
                    $stmt = $this->db->pdo()->prepare("UPDATE asistencia SET fechaIn = :fechaIn, fechaOut = :fechaOut, `status` = :status WHERE id = :idAsistencia");
                    $stmt->bindValue("fechaIn", ($trabajador['fechaIn'] !="")?$trabajador['fechaIn']:NULL);
                    $stmt->bindValue("fechaOut", ($trabajador['fechaOut'] != "")?$trabajador['fechaOut']:NULL);
                    $stmt->bindValue("status", ($trabajador['inasistencia'] !="")?$trabajador['inasistencia']:0);
                    $stmt->bindValue("idAsistencia", $trabajador['idAsistencia']);
                    $stmt->execute();
                    $this->idAsistencia = $idAsistencia = $trabajador['idAsistencia'];
                } else {
                    // Si el trabajador no tiene un registro de asistencia crear
                    $stmt = $this->db->pdo()->prepare("INSERT INTO asistencia (idTrabajador, idFechaAsistencia, fechaIn, fechaOut, `status`) VALUES (:idTrabajador, :idFechaAsistencia, :fechaIn, :fechaOut, :status)");
                    $stmt->bindValue("idTrabajador", $trabajador['idTrabajador']);
                    $stmt->bindValue("idFechaAsistencia", $idFechaAsistencia);
                    $stmt->bindValue("fechaIn", ($trabajador['fechaIn'] !="")?$trabajador['fechaIn']:NULL);
                    $stmt->bindValue("fechaOut", ($trabajador['fechaOut'] != "")?$trabajador['fechaOut']:NULL);
                    $stmt->bindValue("status", ($trabajador['inasistencia'] !="")?$trabajador['inasistencia']:0);
                    $stmt->execute();
                    $this->idAsistencia = $idAsistencia = $this->db->pdo()->lastInsertId();
                }
    
                // Si el trabajador tiene una inasistencia y si tiene o no una justificacion la registramos
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

            // verifico que si se hallan guardado las asistencias, se haga el commit, si una asistencia no tiene guardada los trabajadores se lanza un error

            $stmt = $this->db->pdo()->prepare("SELECT * FROM asistencia WHERE idFechaAsistencia = :idFechaAsistencia");
            $stmt->bindValue("idFechaAsistencia", $idFechaAsistencia);
            $stmt->execute();
            if($stmt->rowCount() == 0) {
                throw new Exception("No es posible registrar la asistencia sin registro de entrada/salida de almenos un trabajador");
            }




            $this->db->pdo()->commit();
            $this->db->disconnect();
            $response = [
                "success" => true,
                "message" => "Asistencia registrada con exito"
            ];
    
        } catch (\Throwable $th) {
            if( 
                isset($this->db) &&
                $this->db->pdo() instanceof \PDO &&
                $this->db->pdo()->inTransaction()
            ){
                $this->db->pdo()->rollBack();
                $this->db->disconnect();
            }
            $response = [
                "success" => false,
                "message" => ((DEVELOPER_MODE) ? $th->getMessage(): "Error al registrar la asistencia"),
                "Error" => $th->getMessage()." : linea: ".$th->getLine(),
            ];
        }
        if ($print) {
            echo json_encode($response);
        }

        return $response;
    }

    public function eliminarFechaAsistencia($print = false): array {
        try {
            $this->db->connect();
            $this->esValido(self::ELIMINAR_ASISTENCIA);
            $this->beginTransaction();
            $stmt = $this->prepare("DELETE FROM fechaAsistencia WHERE id = :idAsistencia");
            $stmt->bindValue("idAsistencia", $this->idAsistencia);
            $stmt->execute();

            $bitacoraSms = "Asistencia eliminada";

            Bitacora::registrarTransaccion($bitacoraSms, $this->db->pdo());

            $this->commit();
            $this->db->disconnect();
            $response = [
                "success" => true,
                "message" => "Asistencia eliminada con exito"
            ];
            if ($print) {
                echo json_encode($response);
            }
        } catch (\Throwable $th) {
            if( 
                isset($this->db) &&
                $this->db->pdo() instanceof \PDO &&
                $this->db->pdo()->inTransaction()
            ){
                $this->db->pdo()->rollBack();
                $this->db->disconnect();
            }
            $response = [
                "success" => false,
                "message" => ((DEVELOPER_MODE) ? $th->getMessage(): "Error al eliminar la asistencia"),
                "Error" => $th->getMessage()." : linea: ".$th->getLine(),
            ];
            if ($print) {
                echo json_encode($response);
            }
        }
        return $response;
    }

    /**
     * Muestra la lista de asistencias de los trabajadores
     * @param bool $print Si es true imprime los resultados y no retorna nada
     * @return stdClass|array Un objeto con la lista de asistencias y trabajadores,
     * o un array con un error si ocurre alguno
     */
    public function verAsistencias($print = false)  {
        try {
            $this->db->connect();
            $this->esValido(self::LISTAR_TRABAJADORES);
            $this->beginTransaction();
            // optengo la lista de registros de asistencias que cumplan con los filtros
            $stmt = $this->prepare("
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
                ,1 as registro
                
                
            FROM asistencia as a 
            JOIN trabajador as t on t.id = a.idTrabajador

            JOIN fechaasistencia as fa on fa.id = a.idFechaAsistencia
            JOIN departamento as d on d.id = fa.idDepartamento
            LEFT JOIN justificacion as j on j.idAsistencias = a.id
            WHERE fa.fecha = :fecha AND d.id = :idDepartamento and fa.turno= :turno
            ");
            $stmt->bindValue("idDepartamento", $this->idDepartamento);
            $stmt->bindValue("fecha", $this->fecha);
            $stmt->bindValue("turno", $this->turno);
            $stmt->execute();

            $asistenciasRegistros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // optengo la lista de trabajadores que cumplan con los filtros

            $stmt = $this->prepare("
                SELECT
                    t.id as idTrabajador
                    ,t.cedula
                    ,CONCAT(t.nombre,' ',t.apellido) as nombre
                    ,0 as registro
                    

                FROM
                    trabajador AS t
                JOIN departamento as d on d.id = t.idDepartamento
                WHERE t.turno = :turno AND d.id = :idDepartamento and :fecha > t.fechaIngreso;
            ");
            $stmt->bindValue("idDepartamento", $this->idDepartamento);
            $stmt->bindValue("turno", $this->turno);
            $stmt->bindValue("fecha", $this->fecha);
            $stmt->execute();

            $trabajadoresRegistros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // se crea un objeto con la lista final a mostrar
            
            $listaFinal = new stdClass();
            foreach ($asistenciasRegistros as $asistencia) {
                $asistencia["registro"] = 1;
                $listaFinal->{"idTrabajador_".$asistencia["idTrabajador"]} = $asistencia;
            }
            foreach ($trabajadoresRegistros as $trabajador) {
                if (!isset($listaFinal->{"idTrabajador_".$trabajador["idTrabajador"]})) {
                    $trabajador["registro"] = 0;
                    $listaFinal->{"idTrabajador_".$trabajador["idTrabajador"]} = $trabajador;
                }
            }
            // obtener la fechaAsistencia si existe
            $stmt = $this->prepare("
                SELECT id FROM fechaasistencia WHERE idDepartamento = :idDepartamento AND fecha = :fecha AND turno = :turno
            ");
            $stmt->bindValue("idDepartamento", $this->idDepartamento);
            $stmt->bindValue("fecha", $this->fecha);
            $stmt->bindValue("turno", $this->turno);
            $stmt->execute();
            $resp = ["success" => true];
            if ($stmt->rowCount() > 0) {
                $resp["fechaAsistencia"] = $stmt->fetchColumn();
            }
            else {
                $resp["fechaAsistencia"] = "";
            }

            $resp["listaTrabajadores"] = $listaFinal;


            $this->commit();
            $this->db->disconnect();
            if($print) {
                echo json_encode($resp);
            }
            return $resp;
        } catch (Exception $th) {
            $this->rollBack();
            $this->db->disconnect();
            if($print) {
                echo json_encode([
                    "success" => false, 
                    "message" => "Error al obtener la lista de asistencias",
                    "error" => $th->getMessage()
                ]);
            }
            return [
                "success" => false, 
                "message" => "Error al obtener la lista de asistencias" ,
                "error" => $th->getMessage()
            ];
        }
    }

    /**
     * Generates a report of attendances.
     *
     * @param string $fechaInicio The start date for the report in 'YYYY-MM-DD' format.
     * @param string $fechaFin The end date for the report in 'YYYY-MM-DD' format.
     * @param string $idDepartamento The ID of the department to filter the report.
     * @param string $grupo The grouping criteria for the report (e.g., trabajadores, departamentos).
     * @return array The generated report as an array.
     * @throws Exception If a database error occurs.
     */

    public function reporte(?string $fechaInicio, ?string $fechaFin, ?string $idDepartamento = null, ?string $turno = null, ?string $grupo = null, ?bool $print = false) :array {


        try {
            $this->db->connect();
            $pdo = $this->db->pdo();


            $pdo->beginTransaction();
            $headerTable = [];
            $queryGroup = " ";

            if($grupo == null)
            {
                $querySelect ="SELECT
                t.cedula,
                t.nombre,
                t.apellido,
                fa.fecha,
                a.fechaIn,
                a.fechaOut,
                d.nombre,
                fa.turno,
                if(a.status=0,'Asistente','Inasistente') as status ";
                $headerTable = [
                    "Cedula",
                    "Nombre",
                    "Apellido",
                    "Fecha",
                    "Entrada",
                    "Salida",
                    "Departamento",
                    "Turno",
                    "Estado"
                ];
            }
            else if($grupo == "trabajadores")
            {
                $querySelect ="SELECT
                    t.cedula,
                    t.nombre,
                    t.apellido,
                    COUNT(if(a.status=1,1,NULL)) as inasitencias,
                    COUNT(if(a.status=1,NULL,1)) as asitencias
                    ";
                $queryGroup = "GROUP BY t.cedula";
                $headerTable = [
                    "Cedula",
                    "Nombre",
                    "Apellido",
                    "Inasistencias",
                    "Asistencias"
                ];
            }
            else if($grupo == "departamentos")
            {
                $querySelect ="SELECT
                    d.id,
                    d.nombre,
                    COUNT(if(a.status=1,1,NULL)) as inasitencias,
                    COUNT(if(a.status=1,NULL,1)) as asitencias
                    ";
                $queryGroup = "GROUP BY d.id";
                $headerTable = [
                    "Id",
                    "Departamento",
                    "Inasistencias",
                    "Asistencias"
                ];
            }
            else if($grupo == "turnos")
            {
                $querySelect ="SELECT
                    fa.turno,
                    COUNT(if(a.status=0,1,NULL)) as inasitencias,
                    COUNT(if(a.status=0,NULL,1)) as asitencias
                    ";
                $queryGroup = "GROUP BY fa.turno";
                $headerTable = [
                    "Turno",
                    "Inasistencias",
                    "Asistencias"
                ];
            }
            $queryWhere = " ";

            if($idDepartamento != null){
                $queryWhere .= "AND fa.idDepartamento = :idDepartamento";
            }

            if($turno != null){
                $queryWhere .= " AND fa.turno = :turno";
            }
                


            $query = "
            $querySelect 
            FROM
                `asistencia` AS a
            JOIN fechaasistencia AS fa on fa.id = a.idFechaAsistencia
            JOIN trabajador as t on t.id = a.idTrabajador
            JOIN departamento as d on d.id = fa.idDepartamento
            WHERE fa.fecha >= :fechaInicio AND fa.fecha <= :fechaFinal $queryWhere
            $queryGroup
            ";





            $stmt = $pdo->prepare($query);

            $stmt->bindValue("fechaInicio", $fechaInicio);
            $stmt->bindValue("fechaFinal", $fechaFin);
            if($idDepartamento != null){
                $stmt->bindValue("idDepartamento", $idDepartamento);
            }
            if($turno != null){
                $stmt->bindValue("turno", $turno);
            }



            $stmt->execute();
            $listaFinal = $stmt->fetchAll(PDO::FETCH_NUM);
            $pdo->commit();
            $pdo = null;
            $this->db->disconnect();
            $respuesta = [ 
                "success" => true,
                "headers" => $headerTable,
                "data" => $listaFinal 
            ];

        } catch (\Throwable $th) {

            if( 
                isset($this->db) &&
                $this->db->pdo() instanceof \PDO &&
                $this->db->pdo()->inTransaction()
            ){
                $this->db->pdo()->rollBack();
                $this->db->disconnect();
            }
            $respuesta = [
                "success" => false,
                "message" => ((DEVELOPER_MODE) ? $th->getMessage(): "Error al reportar asistencias")
            ];
        }

        if($print) {
                echo json_encode($respuesta);
            }
        return $respuesta;
    }

    public function reporteEstadistica($print = false) {
        try {
            $this->db->connect();
            $pdo = $this->db->pdo();
            $pdo->beginTransaction();
            $query ="SELECT
                        DATE_FORMAT(fa.fecha, '%Y-%m') as mes,
                        COUNT(if(a.status=1,1,NULL)) as inasitencias,
                        COUNT(if(a.status=1,NULL,1)) as asitencias
                        
                    FROM
                        asistencia AS a
                    JOIN fechaasistencia AS fa
                    ON
                        fa.id = a.idFechaAsistencia
                    left join Trabajador as t on t.id = a.idTrabajador
                    WHERE";

            if(!empty($this->idTrabajador)){
                $query .= " t.cedula = :cedula AND";
            }
            else if(!empty($this->idDepartamento)){
                $query .= " fa.idDepartamento = :idDepartamento AND";
            }

            $query .= " 1 AND fa.fecha BETWEEN :inicio AND :fin GROUP BY mes";



            $stmt = $pdo->prepare($query);

            $stmt->bindValue("inicio", $this->fechaIn);
            $stmt->bindValue("fin", $this->fechaOut);

            if(!empty($this->idTrabajador)){
                $stmt->bindValue("cedula", $this->idTrabajador);
            }
            else if(!empty($this->idDepartamento)){
                $stmt->bindValue("idDepartamento", $this->idDepartamento);
            }

            $stmt->execute();
            $listaFinal = $stmt->fetchAll(PDO::FETCH_NUM);
            $pdo->commit();
            $pdo = null;
            $this->db->disconnect();
            $respuesta = [
                "success" => true,
                "lista" => $listaFinal
            ];
            
        } catch (\Throwable $th) {
            if( 
                isset($this->db) && $this->db->connected() &&
                $this->db->pdo() instanceof \PDO &&
                $this->db->pdo()->inTransaction()
            ){
                $this->db->pdo()->rollBack();
                $this->db->disconnect();
            }
            $respuesta = [
                "success" => false,
                "message" => ((DEVELOPER_MODE) ? $th->getMessage(): "Error al reportar asistencias")
            ];

        }
        if($print) {
            echo json_encode($respuesta);
        }
        return $respuesta;
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