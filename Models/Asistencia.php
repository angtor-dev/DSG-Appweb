<?php
class Asistencia extends Model
{
    public int|string $idDepartamento;
    public int|string $idDivision;
    private string $turno;
    private string $fecha;
    private string $fechaIn; // hora de ingreso
    private string $fechaOut; // hora de salida
    private string $status;
    private string $idTrabajador;
    private string $idAsistencia;
    public Division|string $departamento;
    private array $trabajadores;
    // lista de acciones para validar
    const LISTAR_TRABAJADORES = 1;
    const LISTAR_TRABAJADORES_SEMANAL = 4;
    const REGISTRAR_ASISTENCIA = 2;
    const ELIMINAR_ASISTENCIA = 3;

    const SHOW_EXCEPTIONS = 1001;

    public function __construct() {
        parent::__construct();
        if(!empty($this->idDivision)) $this->idDepartamento = $this->idDivision;
        if (!empty($this->idDepartamento)) {
            $this->departamento = Division::cargar($this->idDepartamento);
        }
    }

    public function setterArray(array $data) : void
    {
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
    /**
     * Se debe hacer la conexion antes de llamar a esta funcion
     * @param mixed $control
     * @throws \Exception
     * @return bool
     */
    public function esValido($control, mixed &$datosDevueltos = null) : bool {
        // si el control es listar trabajadores o registrar ajuste
        // valida el departamento

        $messages = new class{
            public string $fecha_no_select = "Debe seleccionar una fecha";
            public string $fecha_invalida = "La fecha no es valida";
            public string $turno_no_select = "Debe seleccionar un turno";
            public string $turno_ivalido = "El turno no es valido";
            public string $departamento_no_select = "Debe seleccionar una ". DEP_NAME_M ;
            public string $departamento_invalido = "La ". DEP_NAME_M . " no es valida";
            public string $departamento_no_existe = "La ". DEP_NAME_M . " seleccionada no existe";
            public string $idAsistencia_no_select = "Debe seleccionar una asistencia";
            public string $idAsistencia_invalido = "La asistencia no es valida";
            public string $idAsistencia_no_existe = "La asistencia seleccionada no existe";
            public string $turno_no_existente = "El turno seleccionado no existe";

        };

        if(
            $control == self::LISTAR_TRABAJADORES ||
            $control == self::REGISTRAR_ASISTENCIA ||
            $control == self::LISTAR_TRABAJADORES_SEMANAL
            
        ) {
            // valido la fecha de asistencia

            if(!isset($this->turno) || empty(trim($this->turno))) {
                throw new Exception($messages->turno_no_select, self::SHOW_EXCEPTIONS);
            }

            if(!isset($this->fecha) || empty(trim($this->fecha))) {
                throw new Exception($messages->fecha_no_select, self::SHOW_EXCEPTIONS);
            }
            if(!preg_match("/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/", trim($this->fecha))) {
                throw new Exception($messages->fecha_invalida, self::SHOW_EXCEPTIONS);
            }

            if(!isset($this->idDepartamento) || empty(trim($this->idDepartamento))) {
                throw new Exception($messages->departamento_no_select, self::SHOW_EXCEPTIONS);
            }
            
            if(!preg_match("/^\d+$/", trim($this->idDepartamento))) {
                throw new Exception($messages->departamento_invalido, self::SHOW_EXCEPTIONS);
            }

            $stmt = $this->ejecutarStatement("SELECT id FROM division WHERE id = :departamento", ["departamento" => $this->idDepartamento]);
            
            if($stmt->rowCount() == 0) {
                throw new Exception($messages->departamento_no_existe, self::SHOW_EXCEPTIONS);
            }

        }

        if($control == self::LISTAR_TRABAJADORES){
            // primero optengo los horarios del turno seleccionado

            $stmt = $this->ejecutarStatement("SELECT * FROM turno WHERE codigo = :turno", ["turno" => $this->turno]);
            
            if($stmt->rowCount() == 0) {
                throw new Exception($messages->turno_no_existente, self::SHOW_EXCEPTIONS);
            }

            $resp = $stmt->fetch(PDO::FETCH_ASSOC);

            $datosDevueltos = [
                "hora_entrada" => $resp["horario_entrada"],
                "hora_salida" => $resp["horario_salida"]
            ];

             // validar que la fecha seleccionada sea valida para el turno seleccionado

             $diaDeLaSemana = date("w", strtotime($this->fecha));
             $dias = [
                 "domingo",
                 "lunes",
                 "martes",
                 "miercoles",
                 "jueves",
                 "viernes",
                 "sabado"
             ];

             if($resp[$dias[$diaDeLaSemana]] == 0) {
                throw new Exception("El turno seleccionado no esta programado para el día seleccionado (".ucfirst($dias[$diaDeLaSemana]) .")", self::SHOW_EXCEPTIONS);
             }
             

            

        }
    
        return true;
    }





/**
 * Registra una asistencia y sus trabajadores relacionados.
 *
 * Esta función recibe un array de trabajadores y los registra en la base de datos.
 * El array de trabajadores debe tener la siguiente estructura:
 * [
 *      "idAsistencia_inasistencia" => int,
 *      "idTrabajador" => int,
 *      "tipo_registro" => string,
 *      "hora_entrada" => string,
 *      "hora_salida" => string,
 *      "tipo_justificacion" => string,
 *      "descripcion_justificacion" => string
 * ]
 *
 * @param boolean $print Si se debe imprimir el resultado en formato json.
 * @return array Regresa un array con el resultado de la operacion.
 * @throws Throwable Si ocurre un error al registrar la asistencia.
 */
    public function registrar($print = true):array {
        try {
            $this->db->connect();
            $this->beginTransaction();

          
            $query = "CALL sp_registrar_asistencia(
                :id_asistencia_inasistencia,
                :fecha,
                :trabajador_id,
                :tipo_registro,
                :hora_entrada,
                :hora_salida,
                :tipo_inasistencia,
                :descripcion
            )";

            foreach ($this->trabajadores as $trabajador) {

                $setterAuxiliar = function ($value){
                    return (empty($value)) ? NULL : $value;
                };


                // valido la fecha
                // TODO Validaciones echas depues de las pruebas
                if(!preg_match("/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/", trim($this->fecha))) {
                    throw new Exception("La fecha es invalida", self::SHOW_EXCEPTIONS);
                }
                
                $parametros = [
                    "fecha" => $this->fecha,
                    // datos desde el array de trabajador
                    "id_asistencia_inasistencia" => $setterAuxiliar($trabajador["idAsistencia_inasistencia"]),
                    "trabajador_id" => $setterAuxiliar($trabajador["idTrabajador"]),
                    "tipo_registro" => $setterAuxiliar($trabajador["tipo_registro"]),
                    "hora_entrada" => $setterAuxiliar($trabajador["horaEntrada"]),
                    "hora_salida" => $setterAuxiliar($trabajador["horaSalida"]),
                    "tipo_inasistencia" => $setterAuxiliar($trabajador["tipo_justificacion"]),
                    "descripcion" => $setterAuxiliar($trabajador["descripcion_justificacion"])?? "",
                ];

                $this->ejecutarStatement($query, $parametros);
                
            }


            
            Bitacora::registrarTransaccion("Asistencia de la fecha ".$this->fecha." registrada con éxito", $this->db->pdo());

            $this->testHandler();

            $this->commit();
            $this->db->disconnect();
            $response = [
                "success" => true,
                "message" => "Asistencia registrada con éxito"
            ];
        } catch (\Throwable $th) {
            $this->disconectHandlerExeption();

            $response = [
                "success" => false,
                "message" => "Error al registrar la asistencia"
            ];
            if(DEVELOPER_MODE){
                $response['Error'] = $th->getMessage()." : linea: ".$th->getLine();
                $response['Trace'] = $th->getTraceAsString();
            }
            if(strpos($th->getMessage(), "Show::") !== false) {

                // se debe borrar el "show::" de la cadena y todo lo que esta antes de el
                $response['message'] = substr($th->getMessage(), strpos($th->getMessage(), "Show::") + strlen("Show::"));
                
            }
            
        }
        if ($print) {
            echo json_encode($response);
        }
        return $response;
    }

    public function registrarSemanal($print = true):array {
        try {


            $this->db->connect();
            $this->beginTransaction();

            /*

            CALL `sp_registrar_asistencia`(
                p_id_asistencia_inasistencia INT,
                p_fecha DATE,
                p_trabajador_id INT,
                p_tipo_registro ENUM,
                p_hora_entrada TIME,
                p_hora_salida TIME,
                p_tipo_inasistencia ENUM,
                p_descripcion TEXT
            );
            */
            
            $query = "CALL sp_registrar_asistencia_semanal(
                :p_fecha,
                :p_cedula,
                :p_codigo_asistencia_inasistencia,
                :p_turno,
                :p_tipo_inasistencia,
                :p_descripcion,
                :p_laborable
            )";

            

            foreach ($this->trabajadores as $trabajador) {

                $setterAuxiliar = function ($value){
                    return (empty($value)) ? NULL : $value;
                };
                $setterAuxiliarArray = function ($array, $value){
                    if(isset($array[$value])) return (empty($array[$value])) ? NULL : $array[$value];
                    else return NULL;
                };
                foreach ($trabajador["dias"] as $dia) {

                    

                    $parametros = [
                        ":p_fecha" => $setterAuxiliarArray($dia, "fecha"),
                        ":p_cedula" => $trabajador["cedula"],
                        ":p_codigo_asistencia_inasistencia" => $setterAuxiliarArray($dia, "idAsistencia_inasistencia"),
                        ":p_turno" => $this->turno,
                        ":p_tipo_inasistencia" => $setterAuxiliarArray($dia, "tipo_justificacion"),
                        ":p_descripcion" => $setterAuxiliarArray($dia, "descripcion_justificacion") ?? "",
                        ":p_laborable" => $dia["laborable"],
                    ];
                    $this->ejecutarStatement($query, $parametros);
                    
                }


                
                

                //$this->ejecutarStatement($query, $parametros);
                
            }
            
            
            Bitacora::registrarTransaccion("Asistencia de la fecha ".$this->fecha." registrada con exito", $this->db->pdo());
            
            $this->testHandler();

            $this->commit();
            $this->db->disconnect();
            $response = [
                "success" => true,
                "message" => "Asistencia registrada con éxito"
            ];
        } catch (\Throwable $th) {
            $this->disconectHandlerExeption();

            $response = [
                "success" => false,
                "message" => "Error al registrar la asistencia"
            ];
            if(DEVELOPER_MODE){
                $response['Error'] = $th->getMessage()." : linea: ".$th->getLine();
                $response['Trace'] = $th->getTraceAsString();
            }

            // si $th->getMessage tiene la palabra "Show::" borrar todos desde el comienzo hasta la palabra "Show::" y mostrar el resultado
            if(strpos($th->getMessage(), "Show::") !== false) {
                $response['message'] = substr($th->getMessage(), strpos($th->getMessage(), "Show::") + strlen("Show::"));
            }

            


            

            
        }
        if ($print) {
            echo json_encode($response);
        }
        return $response;
    }

    public function eliminarFechaAsistencia($print = false): array {
        try {
            $this->db->connect();
            //$this->esValido(self::ELIMINAR_ASISTENCIA);
            $this->beginTransaction();

            foreach (["turno", "fecha", "idDepartamento"] as $value) {
                if(!isset($this->$value) || empty($this->$value)) {
                    throw new Exception("Todos los campos son obligatorios", self::SHOW_EXCEPTIONS);
                }
            }
            if(!preg_match("/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/", trim($this->fecha))) {
                throw new Exception("La fecha es invalida", self::SHOW_EXCEPTIONS);
            }

            
            




            $query = "DELETE ai
            FROM asistencia_inasistencia as ai 
            JOIN fechaasistencia as fa
                ON fa.id = ai.idFechaAsistencia
            JOIN asignacion_laboral al 
                ON al.id = ai.idAsignacionLaboral
            JOIN turno as tu on tu.id = al.idTurno
            WHERE 
            fa.fecha = :fecha AND tu.codigo = :turno AND al.idDivision =:idDepartamento;";



            $parametros = [
                "fecha" => $this->fecha,
                "turno" => $this->turno,
                "idDepartamento" => $this->idDepartamento
            ];

            $this->ejecutarStatement($query, $parametros);

            

            $bitacoraSms = "Asistencia eliminada de la fecha $this->fecha, turno $this->turno y departamento $this->idDepartamento con éxito";

            Bitacora::registrarTransaccion($bitacoraSms, $this->db->pdo());

            if($this->getTestingMode()) {
                $this->rollBack();
                $this->beginTransaction();
            }

            $this->commit();
            $this->db->disconnect();
            $response = [
                "success" => true,
                "message" => "Asistencia eliminada con éxito"
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
    public function eliminarFechaAsistenciaSemanales($print = false): array {
        try {
            $semana = $this->obtenerSemana();
            $this->db->connect();
            //$this->esValido(self::ELIMINAR_ASISTENCIA);
            $this->beginTransaction();



            $query = "DELETE ai
            FROM asistencia_inasistencia as ai 
            JOIN fechaasistencia as fa
                ON fa.id = ai.idFechaAsistencia
            JOIN asignacion_laboral al 
                ON al.id = ai.idAsignacionLaboral
            JOIN turno tu 
                ON tu.id = al.idTurno
            WHERE 
             tu.codigo = :turno AND al.idDivision =:idDepartamento and fa.fecha between :lunes and :domingo; ";

            

            $lunes = $semana['lunes']['fecha'];
            $domingo = $semana['domingo']['fecha'];

            $parametros = [
                "lunes" => $lunes,
                "domingo" => $domingo,
                "turno" => $this->turno,
                "idDepartamento" => $this->idDepartamento
            ];

            $this->ejecutarStatement($query, $parametros);

            

            $bitacoraSms = "Asistencia eliminada entre las fechas $lunes y $domingo , turno $this->turno y departamento $this->idDepartamento con exito";

            Bitacora::registrarTransaccion($bitacoraSms, $this->db->pdo());

            
            $this->testHandler();

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
            $this->esValido(self::LISTAR_TRABAJADORES, $horarioDevuelto);
            $this->beginTransaction();
            // optengo la lista de registros de asistencias que cumplan con los filtros
            $query = "SELECT
                    t.id as idTrabajador
                    ,t.cedula
                    ,t.nombre
                    ,t.apellido
                    ,CONCAT(t.nombre,' ',t.apellido) as fullName
                    ,if(a.idAsistencia_inasistencia is NULL,0,1) as 'Es_Asistencia'
                    ,a.horaEntrada
                    ,a.horaSalida
                    ,ai.id as idAsistencia_inasistencia
                    ,i.tipo + 0 as tipo_justificacion
                    ,i.descripcion descripcion_justificacion
                    ,1 as registro
                FROM
                    asistencia_inasistencia ai
                JOIN fechaasistencia fa 
                    ON fa.id = ai.idFechaAsistencia
                JOIN asignacion_laboral as al 
                    ON al.id = ai.idAsignacionLaboral
                JOIN trabajador as t on t.id = al.idTrabajador
                JOIN turno as tu on tu.id = al.idTurno
                

                LEFT JOIN inasistencia i on i.idAsistencia_inasistencia = ai.id
                LEFT JOIN asistencia a on a.idAsistencia_inasistencia = ai.id

                WHERE fa.fecha = :fecha AND tu.codigo = :turno and al.idDivision = :idDepartamento;";

            $parametros = [
                "idDepartamento" => $this->idDepartamento,
                "fecha" => $this->fecha,
                "turno" => $this->turno
            ];

            $asistenciasRegistros = $this->ejecutar($query, $parametros, PDO::FETCH_ASSOC);
            

            $query = "SELECT
                        t.id as idTrabajador
                        ,t.cedula
                        ,t.nombre
                        ,t.apellido
                        ,CONCAT(t.nombre,' ',t.apellido) as fullName
                        ,0 as registro
                    FROM
                        trabajador AS t
                    JOIN asignacion_laboral al ON
                        al.idTrabajador = t.id AND al.esActual = 1
                    JOIN turno as tu on 
                        tu.id = al.idTurno
                    WHERE tu.codigo = :turno AND al.idDivision = :idDepartamento AND :fecha > t.fechaIngreso";

            $trabajadoresRegistros = $this->ejecutar($query, $parametros);

            // se crea un objeto con la lista final a mostrar
            $fechaAsistencia = "";
            $listaFinal = new stdClass();
            foreach ($asistenciasRegistros as $asistencia) {
                $asistencia["registro"] = 1;
                $listaFinal->{"idTrabajador_".$asistencia["idTrabajador"]} = $asistencia;
                $fechaAsistencia = $this->fecha;
            }
            foreach ($trabajadoresRegistros as $trabajador) {
                if (!isset($listaFinal->{"idTrabajador_".$trabajador["idTrabajador"]})) {
                    $trabajador["registro"] = 0;
                    $listaFinal->{"idTrabajador_".$trabajador["idTrabajador"]} = $trabajador;
                }
            }


            $resp = ["success" => true];
            $resp["fechaAsistencia"] = $fechaAsistencia;
            $resp["listaTrabajadores"] = $listaFinal;
            $resp["turnoHorario"] = $horarioDevuelto;


            $this->commit();
            $this->db->disconnect();
            if($print) {
                echo json_encode($resp);
            }
            return $resp;
        } catch (Exception $th) {

            $this->disconectHandlerExeption();
            $resp = [
                "success" => false, 
                "message" => "Error al obtener la lista de asistencias",
            ];
            if(DEVELOPER_MODE) {
                $resp["error"] = $th->getMessage();
                $resp["trace"] = $th->getTraceAsString();
            }

            if($th->getCode() == self::SHOW_EXCEPTIONS) {
                $resp["message"] = $th->getMessage();
            }

            if($print) {
                echo json_encode($resp);
            }
            return $resp;
        }
    }


    public function verAsistenciasSemanal($print = false)  {
        try {
            $semana = $this->obtenerSemana();
            $this->db->connect();
            $this->esValido(self::LISTAR_TRABAJADORES_SEMANAL, $horarioDevuelto);
            $this->beginTransaction();

            $query = "SELECT
                concat('trabajador_',t.cedula) as trabajador
                ,t.cedula
                ,t.id AS idTrabajador
                ,ca.fecha
                ,t.nombre 
                ,t.apellido 
                ,CONCAT(t.nombre,' ',t.apellido) as fullName 
                ,ca.horaEntrada 
                ,ca.horaSalida 
                ,ca.justificacion as tipo_justificacion 
                ,ca.descripcion as descripcion_justificacion 
                ,ca.idAsistencias as idAsistencia_inasistencia 
                ,(if(ca.horaEntrada is not null, 1, 0)) as esAsistencia
                
                ,al.id asignacion_laboral
                ,ca.laborable as laborable
                
            FROM
                trabajador AS t
            JOIN asignacion_laboral as al ON al.idTrabajador = t.id AND al.esActual IS true
            JOIN turno as tu ON tu.id = al.idTurno
            LEFT JOIN (
                    SELECT 
                        in_ai.codigoAsistencia as idAsistencias, #asistencia_inasistencia
                        in_fa.fecha,
                        in_al.idTrabajador,
                        in_a.horaEntrada,
                        in_a.horaSalida,
                        CAST(in_i.tipo AS INT) as justificacion,
                        in_i.tipo as justificacionName,
                        in_i.descripcion,
                        in_ai.laborable

                        
                    FROM fechaasistencia as in_fa
                    JOIN asistencia_inasistencia as in_ai on in_ai.idFechaAsistencia = in_fa.id
                    JOIN asignacion_laboral as in_al on in_al.id = in_ai.idAsignacionLaboral
                    LEFT JOIN asistencia as in_a on in_ai.id = in_a.idAsistencia_inasistencia
                    LEFT JOIN inasistencia as in_i on in_ai.id = in_i.idAsistencia_inasistencia
                ) as ca # control asistencias
                on ca.idTrabajador = t.id AND ca.fecha BETWEEN :lunes AND :domingo


            WHERE t.estado is true
            AND tu.codigo = :turno
            AND al.idDivision = :idDepartamento
            ORDER BY t.id, ca.fecha";

            $parametros = [
                "turno"=> $this->turno,
                "idDepartamento" => $this->idDepartamento,
                "lunes" => $semana["lunes"]["fecha"],
                "domingo" => $semana["domingo"]["fecha"],
            ];

            $lista = $this->ejecutarStatement($query, $parametros);

            $lista = $lista->fetchAll(PDO::FETCH_GROUP);

            $listaNueva = [];

            foreach($lista as $keyTrabajador => $trabajadorValue) {
                $listaNueva[$keyTrabajador] = [
                    "cedula" => $trabajadorValue[0]["cedula"],
                    "idTrabajador" => $trabajadorValue[0]["idTrabajador"],
                    "fullName" => $trabajadorValue[0]["fullName"],
                    "asignacion_laboral" => $trabajadorValue[0]["asignacion_laboral"],
                    "nombre" => $trabajadorValue[0]["nombre"],
                    "apellido"=> $trabajadorValue[0]["apellido"],
                    "controlAsistencias" => [],
                ];
                foreach($trabajadorValue as $trabajadorDatos) {
                    $datos = [];
                    $datos["fecha"] = $trabajadorDatos["fecha"];
                    $datos["horaEntrada"] = $trabajadorDatos["horaEntrada"];
                    $datos["horaSalida"] = $trabajadorDatos["horaSalida"];
                    $datos["justificacion"] = $trabajadorDatos["tipo_justificacion"];
                    $datos["descripcion_justificacion"] = $trabajadorDatos["descripcion_justificacion"];
                    $datos["idAsistencia_inasistencia"] = $trabajadorDatos["idAsistencia_inasistencia"];
                    $datos["esAsistencia"] = $trabajadorDatos["esAsistencia"];
                    $datos["laborable"] = $trabajadorDatos["laborable"];
                    $listaNueva[$keyTrabajador]["controlAsistencias"][$datos["fecha"]] = $datos;
                }

            }


            




            
            



            if($this->getTestingMode()) {
                $this->rollBack();
                $this->beginTransaction();
            }

            $this->commit();
            $this->db->disconnect();
            $resp = [
                "success"=> true,
                "listaTrabajadores" => $listaNueva,
                "semana" => $semana,
            ];
            
            
        } catch (\Throwable $th) {
            $this->disconectHandlerExeption();

            $resp = [
                "success" => false, 
                "message" => "Error al obtener la lista de asistencias",
            ];
            if(DEVELOPER_MODE) {
                $resp["error"] = $th->getMessage();
                $resp["trace"] = $th->getTraceAsString();
            }

            if($th->getCode() == self::SHOW_EXCEPTIONS) {
                $resp["message"] = $th->getMessage();
            }
            
            
        }
        if($print) {
            echo json_encode($resp);
        }
        return $resp;
    }


    public function obtenerSemana() {
        $turno_obj = new Turno();
        $turno_obj->setterArray([
            "codigo" => $this->turno
        ]);
        $turno_obj = $turno_obj->obtenerPorId();
        if( !($turno_obj instanceof Turno)) {
            throw new \Exception("Error al obtener el turno", self::SHOW_EXCEPTIONS);
        }
        

        $fechaStr = $this->fecha;


        // Valida que la cadena de fecha no esté vacía
        if (empty($fechaStr)) {
            throw new \Exception("La cadena de fecha no puede estar vacía.", self::SHOW_EXCEPTIONS);
        }

        // Convierte la cadena de fecha a un objeto DateTime
        try {
            $fecha = new DateTime($fechaStr);
        } catch (Exception $e) {
            // En caso de que la fecha no sea válida, devuelve un arreglo vacío
            throw new \Exception("La cadena de fecha no es una fecha válida.",self::SHOW_EXCEPTIONS);
        }

        // Encuentra el lunes de la semana actual
        if ($fecha->format('N') != 1) { // 1 representa el lunes en el formato ISO 8601
            $fecha->modify('last monday');
        }

        // Crea el arreglo para almacenar las fechas de la semana
        $semana = [];
        $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];

        // Itera a través de los 7 días de la semana
        for ($i = 0; $i < 7; $i++) {
            $semana[$dias[$i]] = [
                "fecha" => $fecha->format('Y-m-d'),
                "laborable" => (bool)$turno_obj->{"get_" . $dias[$i]}()
            ];
            $fecha->modify('+1 day');
        }

        return $semana;




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

    public function reporte(
        ?string $fechaInicio, 
        ?string $fechaFin,
        ?string $idDepartamento = null,
        ?string $turno = null,
        ?string $grupo = null,
        ?bool $print = false
        ) :array {


        try {

            $numerico = function ($value) :bool {
                if(isset($value) && !empty($value)) {
                    return preg_match(REG_NUMERICO, $value);
                }
                else return true;
            };

            if (!empty($fechaInicio) && !preg_match("/^\d{4}-\d{2}-\d{2}$/", trim($fechaInicio))) {
                throw new Exception("La fecha de inicio no es valida", self::SHOW_EXCEPTIONS);
            }
            if (!empty($fechaFin) && !preg_match("/^\d{4}-\d{2}-\d{2}$/", trim($fechaFin))) {
                throw new Exception("La fecha de fin no es valida", self::SHOW_EXCEPTIONS);
            }
            if(!$numerico($idDepartamento)) {
                throw new Exception("El Departamento no es valido", self::SHOW_EXCEPTIONS);
            }
            if(!$numerico($turno)) {
                throw new Exception("El Turno no es valido", self::SHOW_EXCEPTIONS);
            }
            
            $this->db->connect();
            $pdo = $this->db->pdo();


            $pdo->beginTransaction();
            $headerTable = [];
            $where = " WHERE fecha between :fechaInicio and :fechaFin";
            $groupBy = "";

            if($grupo == null || $grupo == "") {
                $querySelect = "SELECT 
                 cedula,
                 nombre,
                 apellido,
                 fecha,
                 if(esAsistencia,horaEntrada,tipo) as entrada,
                 if(esAsistencia,horaSalida,descripcion) as salida,
                 division,
                 turno,
                 if(esAsistencia, 'Asistencia', 'Inasistencia') as status
                 from vista_asistencias";
                $headerTable = [
                    "Cedula",
                    "Nombre",
                    "Apellido",
                    "Fecha",
                    "Entrada",
                    "Salida",
                    DEP_NAME,
                    "Turno",
                    "Estado"
                ];
            }
            else if($grupo == "trabajadores"){
                $querySelect = "SELECT 
                    cedula,
                    nombre,
                    apellido,
                    COUNT(if((esAsistencia = 0 and (tipo+0) = 1 ), 1, NULL)) as injusti,
                    COUNT(if(esAsistencia=0,1,NULL)) as inasitencias,
                    COUNT(if(esAsistencia=1,1,NULL)) as asistencias,
                    COUNT(cedula) as TOTAL
                    from vista_asistencias";
                $groupBy = " group by cedula";
                $headerTable = [
                    "Cedula",
                    "Nombre",
                    "Apellido",
                    "Inasistencias Injustificadas",
                    "Inasistencias",
                    "Asistencias",
                    "Total"
                ];
            }
            else if($grupo == "departamentos"){
                $querySelect = "SELECT 
                    idDivision,
                    division,
                    COUNT(if(esAsistencia=0,1,NULL)) as inasitencias,
                    COUNT(if(esAsistencia=1,1,NULL)) as asistencias
                    from vista_asistencias";
                $groupBy = " group by idDivision";
                $headerTable = [
                    "Id",
                    "División",
                    "Inasistencias",
                    "Asistencias"
                ];
            }
            else if($grupo == "turnos"){
                $querySelect = "SELECT 
                turno,
                COUNT(if(esAsistencia=0,1,NULL)) as inasitencias,
                COUNT(if(esAsistencia=1,1,NULL)) as asistencias
                from vista_asistencias";
                $groupBy = " group by turno";
                $headerTable = [
                    "Turno",
                    "Inasistencias",
                    "Asistencias"
                ];
            }
            else if($grupo == "semana"){
                // 1. Calcular el Lunes y el Domingo de la semana de $fechaInicio
                $dateTime = new \DateTime($fechaInicio);
                
                // El 'w' en PHP date() devuelve 0 (Domingo) a 6 (Sábado).
                // El 'N' en PHP date() devuelve 1 (Lunes) a 7 (Domingo), que es más útil.
                // Restamos (Día de la semana - 1) días para llegar al Lunes (Día 1)
                $diaSemana = (int)$dateTime->format('N');
                $fechaLunes = $dateTime->modify('-' . ($diaSemana - 1) . ' days')->format('Y-m-d');
                
                // Usamos el Lunes calculado para obtener el Domingo (6 días después)
                $dateTimeDomingo = new \DateTime($fechaLunes);
                $fechaMartes = $dateTimeDomingo->modify('+1 days')->format('Y-m-d');
                $fechaMiercoles = $dateTimeDomingo->modify('+1 days')->format('Y-m-d');
                $fechaJueves = $dateTimeDomingo->modify('+1 days')->format('Y-m-d');
                $fechaViernes = $dateTimeDomingo->modify('+1 days')->format('Y-m-d');
                $fechaSabado = $dateTimeDomingo->modify('+1 days')->format('Y-m-d');
                $fechaDomingo = $dateTimeDomingo->modify('+1 days')->format('Y-m-d');
                
                // Se ajustan las fechas de inicio y fin al rango semanal
                $parametros["fechaInicio"] = $fechaLunes; 
                $parametros["fechaFin"] = $fechaDomingo;
                $fechaInicio = $fechaLunes; // Se actualizan para la explicación del header si fuera necesario
                $fechaFin = $fechaDomingo;
                
                // 2. Consulta SQL para el reporte semanal (se utiliza un pivote condicional)
                // Se usa la función DAYOFWEEK() de MySQL (1=Domingo, 2=Lunes, ..., 7=Sábado)
                // En el resultado se transformará a Lunes=1 a Domingo=7 para el procesamiento en PHP
                $querySelect = "SELECT 
                    t.cedula,
                    t.nombre,
                    t.apellido,
                    
                    MAX(CASE WHEN v.fecha = '$fechaLunes' THEN IF(v.esAsistencia = 1, 'Si', if(v.esAsistencia = 0,'No', 'N/A') ) ELSE 'N/A' END) as lunes,
                    MAX(CASE WHEN v.fecha = '$fechaMartes' THEN IF(v.esAsistencia = 1, 'Si', if(v.esAsistencia = 0,'No', 'N/A') ) ELSE 'N/A' END) as martes,
                    MAX(CASE WHEN v.fecha = '$fechaMiercoles' THEN IF(v.esAsistencia = 1, 'Si', if(v.esAsistencia = 0,'No', 'N/A') ) ELSE 'N/A' END) as miercoles,
                    MAX(CASE WHEN v.fecha = '$fechaJueves' THEN IF(v.esAsistencia = 1, 'Si', if(v.esAsistencia = 0,'No', 'N/A') ) ELSE 'N/A' END) as jueves,
                    MAX(CASE WHEN v.fecha = '$fechaViernes' THEN IF(v.esAsistencia = 1, 'Si', if(v.esAsistencia = 0,'No', 'N/A') ) ELSE 'N/A' END) as viernes,
                    MAX(CASE WHEN v.fecha = '$fechaSabado' THEN IF(v.esAsistencia = 1, 'Si', if(v.esAsistencia = 0,'No', 'N/A') ) ELSE 'N/A' END) as sabado,
                    MAX(CASE WHEN v.fecha = '$fechaDomingo' THEN IF(v.esAsistencia = 1, 'Si', if(v.esAsistencia = 0,'No', 'N/A') ) ELSE 'N/A' END) as domingo
                    
                FROM 
                    trabajador as t
                LEFT JOIN 
                    vista_asistencias as v 
                    ON t.cedula = v.cedula
                    AND v.fecha BETWEEN '$fechaLunes' AND '$fechaDomingo'";
                    
                $groupBy = " group by t.cedula, t.nombre, t.apellido, t.fechaIngreso";
                $where = " WHERE ( (fecha between :fechaInicio and :fechaFin) OR fecha is null )";
                $dateTimeDomingo = new \DateTime($fechaLunes);
                // 3. Encabezados de la tabla
                $headerTable = [
                    "Cedula",
                    "Nombre",
                    "Apellido",
                    "Lunes ({$dateTimeDomingo->format('d/m/Y')})",
                    "Martes ({$dateTimeDomingo->modify('+1 days')->format('d/m/Y')})",
                    "Miércoles ({$dateTimeDomingo->modify('+1 days')->format('d/m/Y')})",
                    "Jueves ({$dateTimeDomingo->modify('+1 days')->format('d/m/Y')})",
                    "Viernes ({$dateTimeDomingo->modify('+1 days')->format('d/m/Y')})",
                    "Sábado ({$dateTimeDomingo->modify('+1 days')->format('d/m/Y')})",
                    "Domingo ({$dateTimeDomingo->modify('+1 days')->format('d/m/Y')})"
                ];
            }


            else{
                throw new Exception("Error al generar el reporte", self::SHOW_EXCEPTIONS);
            }

            
            $parametros = [
                "fechaInicio" => $fechaInicio,
                "fechaFin" => $fechaFin
            ];

            if($idDepartamento != null) {
                $where .= " and idDivision = :idDepartamento";
                $parametros["idDepartamento"] = $idDepartamento;
            }
            if($turno != null) {
                $where .= " and idTurno = :turno";
                $parametros["turno"] = $turno;
            }

            $querySelect = $querySelect . $where . $groupBy;


            $stmt = $this->ejecutarStatement($querySelect, $parametros, PDO::FETCH_NUM);


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

           $this->disconectHandlerExeption();
            $respuesta = [
                "success" => false,
                "message" => "Error al reportar asistencias",
                "data" => [],
                "headers" => []
            ];

            if(DEVELOPER_MODE){
                $respuesta["message"] = $th->getMessage();
                $respuesta["trace"] = $th->getTraceAsString();
                $respuesta["line"] = $th->getFile()."::".$th->getLine();
            }
            
        }

        if($print) {
                echo json_encode($respuesta);
            }
        return $respuesta;
    }


    public function reporteEstadistica($print = false) {
        try {
            $this->db->connect();

            // validaciones
            
            if(empty($this->fechaIn) || empty($this->fechaOut)) {
            throw new InvalidArgumentException("Las fechas de inicio y fin no pueden estar vaciás");
            }

            if(!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $this->fechaIn) || !preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $this->fechaOut)) {
                throw new InvalidArgumentException("Las fechas de inicio y fin deben tener formato AAAA-MM-DD");
            }

            if(!empty($this->idTrabajador) && !preg_match("/^[0-9]{7,8}$/", $this->idTrabajador)) {
                throw new InvalidArgumentException("La cedula debe tener 7 u 8 dígitos");
            }
            else if(!empty($this->idTrabajador)) {
                $query = "SELECT 1 FROM trabajador WHERE cedula = :cedula";
                $parametros = [
                    "cedula" => $this->idTrabajador
                ];
                $stmt = $this->ejecutarStatement($query, $parametros, PDO::FETCH_ASSOC);
                if(!$stmt->fetch() ) {
                    throw new InvalidArgumentException("El trabajador no existe");
                }
            }
            if(strtotime($this->fechaIn) >= strtotime($this->fechaOut)) {
                throw new InvalidArgumentException("La fecha de inicio debe ser anterior o igual a la fecha final");
            }

            



            $queryN = "SELECT
                        DATE_FORMAT(fecha, '%Y-%m') as mes,
                        COUNT(if(esAsistencia=1,NULL,1)) as inasitencias,
                        COUNT(if(esAsistencia=1,1,NULL)) as asitencias
                        from vista_asistencias
            ";
            $where = " WHERE fecha Between :inicio AND :fin";


            

            $parametros = [
                "inicio" => $this->fechaIn,
                "fin" => $this->fechaOut
            ];

            if(!empty($this->idTrabajador)){
                $where .= " AND cedula = :cedula";
                $parametros["cedula"] = $this->idTrabajador;

            }
            else if(!empty($this->idDepartamento)){
                $where .= " AND idDivision = :idDepartamento";
                $parametros["idDepartamento"] = $this->idDepartamento;

            }

            $where .= " GROUP BY mes";
            


            $query = $queryN . $where;

          

            $listaFinal = $this->ejecutar($query, $parametros, PDO::FETCH_NUM);

            

            // obtener promedio de asistencias y inasistencias

            $query = "SELECT
                        v.division as division,
                        ROUND((SUM(CASE WHEN v.esAsistencia = 1 THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(v.idDivision),0) ),2) as porcentajeAsistencias,
                        ROUND((SUM(CASE WHEN v.esAsistencia = 0 THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(v.idDivision),0) ),2) as porcentajeInasistencias,
                        count(if(v.esAsistencia=1,1,NULL)) as asistencias,
                        count(if(v.esAsistencia=0,1,NULL)) as inasistencias

                    FROM
                        vista_asistencias as v
                    WHERE
                        v.fecha BETWEEN :inicio AND :fin
                    GROUP BY v.idDivision order by porcentajeAsistencias DESC";

            $parametros = [
                "inicio" => $this->fechaIn,
                "fin" => $this->fechaOut
            ];

            $promedio = $this->ejecutar($query, $parametros, PDO::FETCH_ASSOC);
            if(empty($promedio)){
                $promedio = [];
            }
            
            $this->db->disconnect();
            $respuesta = [
                "success" => true,
                "lista" => $listaFinal
                ,"promedio" => $promedio
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


    public function llenarAsistenciasDatosDePrueba(){
        // para evitar que el devsense me moleste con el error del no reachable code
        // phpcs:disable
        try 
        {
            // NO INTENTEN ESTO EN CASA T_T
            return;
            $this->setTestingMode(true);
            ini_set('memory_limit', '256M');
            set_time_limit(0);

            $fechaInicio = "2024-05-10";
            $fechaFinal = "2025-07-01";
            // primero crear un bucle para recorrer las fechas
            $fechaActual = new DateTime($fechaInicio);
            $fechaFinal = new DateTime($fechaFinal);
            $intervalo = new DateInterval('P1D'); // Intervalo de un día

            // esto recorrera todas las fechas al usarse en un foreach
            $fechas = new DatePeriod($fechaActual, $intervalo, $fechaFinal);

            // ahora optenemos los departamentos y los turnos
            $departamentos = (new Departamento)->listar();
            $turnos = (new Turno())->listarPadre();

            foreach ($fechas as $fecha) {
                $fechaActual = $fecha->format('Y-m-d');
                foreach ($departamentos as $departamento) {
                    $idDepartamento = $departamento->id;
                    foreach ($turnos as $turno) {
                        $idTurno = $turno->id;

                        $this->setterArray([
                            "idDepartamento" => $idDepartamento,
                            "turno" => $idTurno,
                            "fecha" => $fechaActual
                        ]);
                        $respuestaVerAsistencias = $this->verAsistencias(false);
                        if(!$respuestaVerAsistencias["success"]) continue;
                        if ((new ArrayObject($respuestaVerAsistencias["listaTrabajadores"])) ->count() <=0 ) continue; 
                        $objetoTrabajadores = $respuestaVerAsistencias["listaTrabajadores"];
                        $turnoHorarios = $respuestaVerAsistencias["turnoHorario"];
                        // pasar el turnoHorarios de formato HH:MM:SS a HH:MM
                        $turnoHorarios["hora_entrada"] = substr($turnoHorarios["hora_entrada"], 0, 5);
                        $turnoHorarios["hora_salida"] = substr($turnoHorarios["hora_salida"], 0, 5);
                        $arregloTrabajadores = [];
                        foreach ($objetoTrabajadores as $trabajador) {
                            $prepareTrabajador = [
                                "idTrabajador"=> $trabajador["idTrabajador"],
                                "idAsistencia_inasistencia"=> $trabajador["idAsistencia_inasistencia"] ?? "",
                                "tipo_registro"=> "",
                                "horaEntrada"=> "",
                                "horaSalida"=> "",
                                "tipo_justificacion"=> "",
                                "descripcion_justificacion"=> "",
                                "registrado"=> ""
                            ];


                            // valor aleatorio entre 1 y 2
                            // 1 = asistencia
                            // 2 = inasistencia
                            $tipoRegistro = rand(1, 2);
                            $prepareTrabajador["tipo_registro"] = $tipoRegistro;

                            if($tipoRegistro == 1){

                                $prepareTrabajador["horaEntrada"] = generarHoraAleatoria($turnoHorarios["hora_entrada"], $turnoHorarios["hora_salida"]);
                                $prepareTrabajador["horaSalida"] = generarHoraAleatoria($prepareTrabajador["horaEntrada"], $turnoHorarios["hora_salida"]);

                            }
                            else{
                                $prepareTrabajador["tipo_justificacion"] = rand(1, 8);
                            }


                            $arregloTrabajadores[] = $prepareTrabajador;
                        }

                        $this->setterArray([
                            "idDepartamento" => $idDepartamento,
                            "turno" => $idTurno,
                            "fecha" => $fechaActual,
                            "trabajadores" => $arregloTrabajadores
                        ]);
                        $this->registrar(false);
                    }
                }
            }
            debug("Proceso de llenado de asistencias completado",false );
            
        } catch (\Throwable $th) {
            debug( $th->getMessage(),false);
            debug( $th->getTraceAsString(), false);
        }
        // phpcs:enable
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


/*
    vista

    CREATE OR REPLACE ALGORITHM = TEMPTABLE VIEW vista_asistencias AS
    SELECT 
        t.cedula
        ,t.nombre
        ,t.apellido
        ,fa.fecha
        ,a.horaEntrada
        ,a.horaSalida
        ,i.tipo
        ,i.descripcion
        ,d.id as idDepartamento
        ,d.nombre as departamento
        ,tu.id as idTurno
        ,tu.nombre as turno
        ,if(a.idAsistencia_inasistencia IS NOT NULL, 1, 0) as esAsistencia
        
    FROM asistencia_inasistencia AS ai 
    JOIN fechaasistencia as fa 
        on fa.id = ai.idFechaAsistencia
    JOIN asignacion_laboral as al
        on al.id = ai.idAsignacionLaboral
    JOIN trabajador as t on t.id = al.idTrabajador
    JOIN turno as tu on tu.id = al.idTurno
    JOIN departamento as d on d.id = al.idDivision
    LEFT JOIN asistencia as a on a.idAsistencia_inasistencia = ai.id
    LEFT JOIN inasistencia as i on i.idAsistencia_inasistencia = ai.id;

 */

/**
 * Generates a random time in HH:MM format within a specified range.
 *
 * @param string $horaInicio The start time in HH:MM format (e.g., "08:00").
 * @param string $horaFin The end time in HH:MM format (e.g., "17:30").
 * @return string A random time in HH:MM format, or an empty string if the input is invalid.
 */
function generarHoraAleatoria(string $horaInicio, string $horaFin): string
{
    // Validate input format
    if (!preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $horaInicio) ||
        !preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $horaFin)) {
        return ""; // Or throw an exception for invalid format
    }

    // Convert times to total minutes from midnight for easier calculation
    list($hInicio, $mInicio) = explode(':', $horaInicio);
    $minutosInicio = (int)$hInicio * 60 + (int)$mInicio;

    list($hFin, $mFin) = explode(':', $horaFin);
    $minutosFin = (int)$hFin * 60 + (int)$mFin;

    // Handle cases where the end time is on the next day (e.g., 22:00 - 02:00)
    if ($minutosFin < $minutosInicio) {
        $minutosFin += 24 * 60; // Add a day's worth of minutes
    }

    // Generate a random number of minutes within the range
    $minutosAleatorios = mt_rand($minutosInicio, $minutosFin);

    // If the random minutes exceed a day, subtract a day to bring it back to 0-23:59
    if ($minutosAleatorios >= 24 * 60) {
        $minutosAleatorios -= 24 * 60;
    }

    // Convert minutes back to HH:MM format
    $hora = floor($minutosAleatorios / 60);
    $minutos = $minutosAleatorios % 60;

    return sprintf('%02d:%02d', $hora, $minutos);
}

?>