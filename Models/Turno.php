<?php

/**
 * tabla de turnos
 *  turno	CREATE TABLE `turno` (
 *   `id` int(11) NOT NULL AUTO_INCREMENT,
 *   `nombre` varchar(50) NOT NULL,
 *   `horario_entrada` time NOT NULL,
 *   `horario_salida` time NOT NULL,
 *   `lunes` tinyint(1) NOT NULL DEFAULT 0,
 *   `martes` tinyint(1) NOT NULL DEFAULT 0,
 *   `miercoles` tinyint(1) NOT NULL DEFAULT 0,
 *   `jueves` tinyint(1) NOT NULL DEFAULT 0,
 *   `viernes` tinyint(1) NOT NULL DEFAULT 0,
 *   `sabado` tinyint(1) NOT NULL DEFAULT 0,
 *   `domingo` tinyint(1) NOT NULL DEFAULT 0,
 *   `estado` tinyint(1) NOT NULL DEFAULT 1,
 *   PRIMARY KEY (`id`),
 *   UNIQUE KEY `nombre` (`nombre`)
 *  ) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci	

 * 
 */

class Turno extends Model implements JsonSerializable
{

    private string $nombre;
    private string $horario_entrada;
    private string $horario_salida;
    private string $lunes;
    private string $martes;
    private string $miercoles;
    private string $jueves;
    private string $viernes;
    private string $sabado;
    private string $domingo;


    const SHOW_EXCEPTION_TURNO = 1001;
    const REGISTRAR_TURNO = 1;
    const ACTUALIZAR_TURNO = 2;
    const ELIMINAR_TURNO = 3;
    const OPTENER_TURNO = 4;

    public function __construct() {
        parent::__construct();
        if (!empty($this->id)) {
            //$this->optener_turno($this->id);
        }
    }


    public static function getTurnosOptions($checkedId = null) : string
    {

        $turno = new Turno();

        $turnos = $turno->listarPadre();
        $options = "";
        foreach ($turnos as $turno) {
            $options .= "<option ".($checkedId == $turno->id ? "selected" : "")." value='" . $turno->id . "'>" . $turno->get_nombre() . "</option>";
        }
        return $options;
        
    }

    public function listarPadre(int $estado = null) : Array
    {
        return parent::listar($estado);
    }

    public function listar(int $estado = null): array
	{
		try {
			$lista = parent::listar($estado);

			$resp = [
				"success" => true,
				"data" => $lista,
				"actualizar" => tienePermiso(Modulo::TURNOS, Permiso::ACTUALIZAR),
				"eliminar" => tienePermiso(Modulo::TURNOS, Permiso::ELIMINAR)
			];

		} catch (\Throwable $th) {
			$resp = [
				"success" => false,
				"message" => $th->getMessage()
			];
			if(DEVELOPER_MODE) $resp["trace"] = $th->getTraceAsString();
			if(!DEVELOPER_MODE) $resp["message"] = "Error al obtener la lista de cargos";
		}
		return $resp;

	}

    public function obtenerPorId():Turno
    {
        try {
            $this->db->connect();
            $query = "SELECT * FROM turno WHERE id = :id";
            $parametros = array(
                "id" => $this->id
            );
            $resp = $this->ejecutarStatement($query, $parametros, PDO::FETCH_CLASS, get_class($this));
            $resp = $resp->fetch();
            
            $this->db->disconnect();
            return $resp;
        } catch (\Throwable $th) {
            if(isset($this->db)) $this->db->disconnect();
            echo json_encode(array(
                "success" => false,
                "error" => $th->getMessage(),
                "message" => "Error al obtener el turno")
            );
            exit;
        }        
    }




    /**
     * Registra un nuevo turno en el sistema
     * @param bool $print Si es true imprime los resultados y no retorna nada
     * @return array Un array con el resultado de la operacion
     */
    public function registrar($print = true) : Array
    {
        try {
            
            
            $this->db->connect();
            $this->esValido(self::REGISTRAR_TURNO);       
            $this->beginTransaction();

            $query = "INSERT INTO turno (nombre, horario_entrada, horario_salida, lunes, martes, miercoles, jueves, viernes, sabado, domingo) 
            VALUES (:nombre, :horario_entrada, :horario_salida, :lunes, :martes, :miercoles, :jueves, :viernes, :sabado, :domingo);";
            $parametros = array(
                "nombre" => $this->nombre,
                "horario_entrada" => $this->horario_entrada,
                "horario_salida" => $this->horario_salida,
                "lunes" => $this->lunes ?? 0,
                "martes" => $this->martes ?? 0,
                "miercoles" => $this->miercoles ?? 0,
                "jueves" => $this->jueves ?? 0,
                "viernes" => $this->viernes ?? 0,
                "sabado" => $this->sabado ?? 0,
                "domingo" => $this->domingo ?? 0
            );

            $this->ejecutarStatement($query, $parametros);

            $bitacoraTexto = "Turno '".$this->nombre."' registrado";
            $bitacoraTexto .= " desde: ".$this->horario_entrada." hasta: ".$this->horario_salida;
            $bitacoraTexto .= " L: ".$this->lunes." M: ".$this->martes." X: ".$this->miercoles." J: ".$this->jueves." V: ".$this->viernes." S: ".$this->sabado." D: ".$this->domingo;
            
            Bitacora::registrarTransaccion($bitacoraTexto, $this->db->pdo());

            if($this->getTestingMode()) {
                $this->rollBack();
                $this->beginTransaction();
            }

            $this->commit();

            $this->db->disconnect();

            $resp = array(
                "success" => true,
                "message" => "Turno registrado con exito"
            );
        } catch (\Throwable $th) {
            if( 
                isset($this->db) && 
                $this->db->connected() &&
                $this->db->pdo()->inTransaction()
            ){
                $this->db->pdo()->rollBack();
                $this->db->disconnect();
            }

            $resp = array(
                "success" => false,
                "message" => "Ocurrio un error al registrar al turno"
            );
            if(DEVELOPER_MODE) {
                $resp["error"] = $th->getMessage()." :: Linea: ".$th->getLine();
                $resp["consoleError"] = $th->getMessage()." :: Linea: ".$th->getLine() ."\n :: Trace: ".$th->getTraceAsString();
            }
            if($th instanceof Exception && $th->getCode() == self::SHOW_EXCEPTION_TURNO){ 
                $resp["message"] = $th->getMessage();
            }
            
        }

        if ($print) {
            echo json_encode($resp);
        }



        return $resp;

    }


    public function actualizar($print = true) : Array
    {
        try {
            $this->db->connect();
            $this->esValido(self::ACTUALIZAR_TURNO);       
            $this->beginTransaction();

            $query = "UPDATE turno SET 
                nombre = :nombre,
                horario_entrada = :horario_entrada,
                horario_salida = :horario_salida,
                lunes = :lunes,
                martes = :martes,
                miercoles = :miercoles,
                jueves = :jueves,
                viernes = :viernes,
                sabado = :sabado,
                domingo = :domingo
            WHERE id = :id";
            $parametros = array(
                "id" => $this->id,
                "nombre" => $this->nombre,
                "horario_entrada" => $this->horario_entrada,
                "horario_salida" => $this->horario_salida,
                "lunes" => $this->lunes ?? 0,
                "martes" => $this->martes ?? 0,
                "miercoles" => $this->miercoles ?? 0,
                "jueves" => $this->jueves ?? 0,
                "viernes" => $this->viernes ?? 0,
                "sabado" => $this->sabado ?? 0,
                "domingo" => $this->domingo ?? 0
            );

            $this->ejecutarStatement($query, $parametros);

            $bitacoraTexto = "Turno '".$this->nombre."' actualizado";
            $bitacoraTexto .= " desde: ".$this->horario_entrada." hasta: ".$this->horario_salida;
            $bitacoraTexto .= " L: ".$this->lunes." M: ".$this->martes." X: ".$this->miercoles." J: ".$this->jueves." V: ".$this->viernes." S: ".$this->sabado." D: ".$this->domingo;
            
            Bitacora::registrarTransaccion($bitacoraTexto, $this->db->pdo());

            if($this->getTestingMode()) {
                $this->rollBack();
                $this->beginTransaction();
            }

            $this->commit();

            $this->db->disconnect();

            $resp = array(
                "success" => true,
                "message" => "Turno actualizado con exito"
            );
        } catch (\Throwable $th) {
            if( 
                isset($this->db) && 
                $this->db->connected() &&
                $this->db->pdo()->inTransaction()
            ){
                $this->db->pdo()->rollBack();
                $this->db->disconnect();
            }

            $resp = array(
                "success" => false,
                "message" => "Ocurrio un error al actualizar al turno"
            );
            if(DEVELOPER_MODE) {
                $resp["error"] = $th->getMessage()." :: Linea: ".$th->getLine();
                $resp["consoleError"] = $th->getMessage()." :: Linea: ".$th->getLine()."\n :: Trace: ".$th->getTraceAsString();
            }
            if($th instanceof Exception && $th->getCode() == self::SHOW_EXCEPTION_TURNO){ 
                $resp["message"] = $th->getMessage();
            }
            
        }

        if ($print) {
            echo json_encode($resp);
        }

        return $resp;
    }


    public function eliminarTurno($print = true) : Array
    {
        try {
            $this->db->connect();
            $this->esValido(self::ELIMINAR_TURNO);       
            $this->beginTransaction();

            $query = "DELETE FROM turno WHERE id = :id";
            $parametros = array(
                "id" => $this->id
            );

            $this->ejecutarStatement($query, $parametros);

            $bitacoraTexto = "Turno '".$this->nombre."' eliminado";
            
            Bitacora::registrarTransaccion($bitacoraTexto, $this->db->pdo());

            if($this->getTestingMode()) {
                $this->rollBack();
                $this->beginTransaction();
            }

            $this->commit();

            $this->db->disconnect();

            $resp = array(
                "success" => true,
                "message" => "Turno eliminado con exito"
            );
        } catch (\Throwable $th) {
            if( 
                isset($this->db) && 
                $this->db->connected() &&
                $this->db->pdo()->inTransaction()
            ){
                $this->db->pdo()->rollBack();
                $this->db->disconnect();
            }

            $resp = array(
                "success" => false,
                "message" => "Ocurrio un error al eliminar al turno"
            );
            if(DEVELOPER_MODE) {
                $resp["error"] = $th->getMessage()." :: Linea: ".$th->getLine();
                $resp["consoleError"] = $th->getMessage()." :: Linea: ".$th->getLine()."\n :: Trace: ".$th->getTraceAsString();
            }
            if($th instanceof Exception && $th->getCode() == self::SHOW_EXCEPTION_TURNO){ 
                $resp["message"] = $th->getMessage();
            }
            
        }

        if ($print) {
            echo json_encode($resp);
        }

        return $resp;
    }

    public function eliminar(bool $eliminadoLogico = true): bool
    {
        return false;
    }


    public function esValido(int $operacion) : void
    {
        $mensajes = new class {
            public $nombreRequerido = "El nombre del turno es requerido";
            public $horarioEntradaRequerido = "El horario de entrada es requerido";
            public $horarioSalidaRequerido = "El horario de salida es requerido";
            public $diasRequeridos = "Debe seleccionar al menos un día de la semana";
            public $lunesRequerido = "El día de lunes es requerido";
            public $martesRequerido = "El día de martes es requerido";
            public $miercolesRequerido = "El día de miércoles es requerido";
            public $juevesRequerido = "El día de jueves es requerido";
            public $viernesRequerido = "El día de viernes es requerido";
            public $sabadoRequerido = "El día de sábado es requerido";
            public $domingoRequerido = "El día de domingo es requerido";
            public $horarioEntradaInvalido = "El horario de entrada es invalido";
            public $horarioSalidaInvalido = "El horario de salida es invalido";
            public $horarioEntradaMayorSalida = "El horario de entrada es mayor al horario de salida";
            public $nombreInvalido = "El nombre del turno es invalido";
            public $turnoExistente = "El turno ya existe";
            public $turnoNoExistente = "El turno no existe";
            public $turnoRequerido = "El turno no esta seleccionado";
            public $turnoInvalido = "El turno es invalido";
            public $lunesInvalido = "El día de lunes es invalido";
            public $martesInvalido = "El día de martes es invalido";
            public $miercolesInvalido = "El día de miércoles es invalido";
            public $juevesInvalido = "El día de jueves es invalido";
            public $viernesInvalido = "El día de viernes es invalido";
            public $sabadoInvalido = "El día de sábado es invalido";
            public $domingoInvalido = "El día de domingo es invalido";
            public $turnoRelacionesEliminar = "El turno esta siendo utilizado y no puede ser eliminado";

        };
        // valida campos (validado lógico)
        if($operacion == self::ACTUALIZAR_TURNO || $operacion == self::ELIMINAR_TURNO) {
            if(empty($this->id)) {
                throw new Exception ($mensajes->turnoRequerido, self::SHOW_EXCEPTION_TURNO);
            }
            if(!preg_match(REG_NUMERICO, $this->id)) {
                throw new Exception ($mensajes->turnoInvalido, self::SHOW_EXCEPTION_TURNO);
            }
        }
        if($operacion == self::REGISTRAR_TURNO || $operacion == self::ACTUALIZAR_TURNO) {
            if(empty($this->nombre)) {
                throw new Exception ($mensajes->nombreRequerido, self::SHOW_EXCEPTION_TURNO);
            }
            if(empty($this->horario_entrada)) {
                throw new Exception ($mensajes->horarioEntradaRequerido, self::SHOW_EXCEPTION_TURNO);
            }
            if(empty($this->horario_salida)) {
                throw new Exception ($mensajes->horarioSalidaRequerido, self::SHOW_EXCEPTION_TURNO);
            }
            if($this->lunes != '0' and empty($this->lunes)) {
                throw new Exception ($mensajes->lunesRequerido, self::SHOW_EXCEPTION_TURNO);
            }
            if($this->martes != '0' and empty($this->martes)) {
                
                throw new Exception ($mensajes->martesRequerido." (martes: $this->martes) ", self::SHOW_EXCEPTION_TURNO);
            }
            if($this->miercoles != '0' and empty($this->miercoles)) {
                throw new Exception ($mensajes->miercolesRequerido, self::SHOW_EXCEPTION_TURNO);
            }
            if($this->jueves != '0' and empty($this->jueves)) {
                throw new Exception ($mensajes->juevesRequerido, self::SHOW_EXCEPTION_TURNO);
            }
            if($this->viernes != '0' and empty($this->viernes)) {
                throw new Exception ($mensajes->viernesRequerido, self::SHOW_EXCEPTION_TURNO);
            }
            if($this->sabado != '0' and empty($this->sabado)) {
                throw new Exception ($mensajes->sabadoRequerido, self::SHOW_EXCEPTION_TURNO);
            }
            if($this->domingo != '0' and empty($this->domingo)) {
                throw new Exception ($mensajes->domingoRequerido, self::SHOW_EXCEPTION_TURNO);
            }
            $dias = [
                $this->lunes ?? 0,
                $this->martes ?? 0,
                $this->miercoles ?? 0,
                $this->jueves ?? 0,
                $this->viernes ?? 0,
                $this->sabado ?? 0,
                $this->domingo ?? 0
            ];

            if(!in_array(1, $dias)) {
                throw new Exception($mensajes->diasRequeridos, self::SHOW_EXCEPTION_TURNO);
            }

            $haystack = [0,1,"0","1"];

            if(!in_array($this->lunes ?? 0, $haystack)) {
                throw new Exception($mensajes->lunesInvalido, self::SHOW_EXCEPTION_TURNO);
            }

            if(!in_array($this->martes ?? 0, $haystack)) {
                throw new Exception($mensajes->martesInvalido, self::SHOW_EXCEPTION_TURNO);
            }
            if(!in_array($this->miercoles ?? 0, $haystack)) {
                throw new Exception($mensajes->miercolesInvalido, self::SHOW_EXCEPTION_TURNO);
            }

            if(!in_array($this->jueves ?? 0, $haystack)) {
                throw new Exception($mensajes->juevesInvalido, self::SHOW_EXCEPTION_TURNO);
            }

            if(!in_array($this->viernes ?? 0, $haystack)) {
                throw new Exception($mensajes->viernesInvalido, self::SHOW_EXCEPTION_TURNO);
            }

            if(!in_array($this->sabado ?? 0, $haystack)) {
                throw new Exception($mensajes->sabadoInvalido, self::SHOW_EXCEPTION_TURNO);
            }

            if(!in_array($this->domingo ?? 0, $haystack)) {
                throw new Exception($mensajes->domingoInvalido, self::SHOW_EXCEPTION_TURNO);
            }
        }

        // valida base de datos

        if($operacion == self::ACTUALIZAR_TURNO || $operacion == self::ELIMINAR_TURNO) {
            // valido la existencia por el id
            $query = "SELECT * FROM turno WHERE id = :id";
            $parametros = [
                "id" => $this->id
            ];
            $stmt = $this->ejecutarStatement($query, $parametros);
            if($stmt->rowCount() == 0) {
                throw new Exception ($mensajes->turnoNoExistente, self::SHOW_EXCEPTION_TURNO);
            }
            else if($operacion == self::ELIMINAR_TURNO) {

                $this->nombre = $stmt->fetch(PDO::FETCH_ASSOC)["nombre"];
            }
        }

        if($operacion == self::REGISTRAR_TURNO || $operacion == self::ACTUALIZAR_TURNO) {
            // valida duplicidad en el nombre
            $query = "SELECT * FROM turno WHERE nombre = :nombre";
            $parametros = [
                "nombre" => $this->nombre
            ];

            if($operacion == self::ACTUALIZAR_TURNO) {
                $query .= " AND id <> :id";
                $parametros["id"] = $this->id;
            }

            
            $stmt = $this->ejecutarStatement($query, $parametros);
            if($stmt->rowCount() > 0) {
                throw new Exception ($mensajes->turnoExistente, self::SHOW_EXCEPTION_TURNO);
            }
        }

        if($operacion == self::ELIMINAR_TURNO) {
            // valida relaciones en la base de datos

            $query = "SELECT
                    CASE
                        WHEN EXISTS (SELECT 1 FROM asignacion_laboral as al WHERE al.idTurno = :idAl ) THEN 1
                        ELSE 0
                    END AS tiene_relaciones;";

            $parametros = [
                "idAl" => $this->id,
            ];

            $stmt = $this->ejecutarStatement($query, $parametros);
            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch();
                if($row["tiene_relaciones"] == 1) {
                    throw new Exception ($mensajes->turnoRelacionesEliminar, self::SHOW_EXCEPTION_TURNO);
                }
            }
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

    public function jsonSerialize()
    {
        return [
            "id" => $this->id ?? "",
            "horario_entrada" => $this->horario_entrada,
            "horario_salida" => $this->horario_salida,
            "nombre" => $this->nombre,
            "lunes" => $this->lunes,
            "martes" => $this->martes,
            "miercoles" => $this->miercoles,
            "jueves" => $this->jueves,
            "viernes" => $this->viernes,
            "sabado" => $this->sabado,
            "domingo" => $this->domingo
        ];
    }




    // setters

    PUBLIC function set_lunes($value){
        if(filter_var($value, FILTER_VALIDATE_INT) !== false){
            $this->lunes = (string)$value;
        }
    }
    PUBLIC function set_martes($value){
        if(filter_var($value, FILTER_VALIDATE_INT) !== false){
            $this->martes = (string) ((int)$value);
        }
    }
    PUBLIC function set_miercoles($value){
        if(filter_var($value, FILTER_VALIDATE_INT) !== false){
            $this->miercoles = (string) ((int)$value);
        }
    }
    PUBLIC function set_jueves($value){
        if(filter_var($value, FILTER_VALIDATE_INT) !== false){
            $this->jueves = (string) ((int)$value);
        }
    }
    PUBLIC function set_viernes($value){
        if(filter_var($value, FILTER_VALIDATE_INT) !== false){
            $this->viernes = (string) ((int)$value);
        }
    }
    PUBLIC function set_sabado($value){
        if(filter_var($value, FILTER_VALIDATE_INT) !== false){
            $this->sabado = (string) ((int)$value);
        }
    }
    PUBLIC function set_domingo($value){
        if(filter_var($value, FILTER_VALIDATE_INT) !== false){
            $this->domingo = (string) ((int)$value);
        }
    }




    // Getters

    PUBLIC function get_nombre(){
        return $this->nombre;
    }
    PUBLIC function get_horario_entrada(){
        return preg_replace("/:\d\d$/", "", $this->horario_entrada);

    }
    PUBLIC function get_horario_salida(){
        return preg_replace("/:\d\d$/", "", $this->horario_salida);
    }

    PUBLIC function get_lunes($int = true){
        if($int) return $this->lunes;
        else return ($int) ?"Lunes":"";
    }
    PUBLIC function get_martes($int = true){
        if($int) return $this->martes;
        else return ($int) ?"Martes":"";
    }
    PUBLIC function get_miercoles($int = true){
        if($int) return $this->miercoles;
        else return ($int) ?"Miercoles":"";
    }
    PUBLIC function get_jueves($int = true){
        if($int) return $this->jueves;
        else return ($int) ?"Jueves":"";
    }
    PUBLIC function get_viernes($int = true){
        if($int) return $this->viernes;
        else return ($int) ?"Viernes":"";
    }
    PUBLIC function get_sabado($int = true){
        if($int) return $this->sabado;
        else return ($int) ?"Sabado":"";
    }
    PUBLIC function get_domingo($int = true){
        if($int) return $this->domingo;
        else return ($int) ?"Domingo":"";
    }

}