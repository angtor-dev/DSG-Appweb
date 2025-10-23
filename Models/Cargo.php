<?php 

/*

cargo	CREATE TABLE `cargo` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`nombre` varchar(80) NOT NULL,
	`nivel` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci	

*/


/**
 * 
 */
class Cargo extends Model implements JsonSerializable
{


	private string $nombre;
	private int|string $nivel;

	const REGISTRAR_CARGO = 1;
	const ACTUALIZAR_CARGO = 2;
	const ELIMINAR_CARGO = 3;
	const OPTENER_CARGO = 4;
	const OBTENER_CARGO_POR_ID = 5;
	const SHOW_EXCEPTION_CARGO = 1001;

	function __construct()
	{
		parent::__construct();
	}


	public function registrar($print = true)
	{
		try {

			$this->db->connect();
			$this->esValido(self::REGISTRAR_CARGO);       
			$this->beginTransaction();

			$query = "INSERT INTO cargo (nombre, nivel) VALUES (:nombre, :nivel);";
			$parametros = array(
				"nombre" => $this->nombre,
				"nivel" => $this->nivel
			);

			$this->ejecutarStatement($query, $parametros);

			$bitacoraTexto = "Cargo '".$this->nombre."' registrado";


			if(!$this->testHandler()) {
				Bitacora::registrarTransaccion($bitacoraTexto, $this->db->pdo());
			}

			$this->commit();

			$this->db->disconnect();

			$resp = array(
				"success" => true,
				"message" => "Cargo registrado con exito"
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
            if($th instanceof Exception && $th->getCode() == self::SHOW_EXCEPTION_CARGO){ 
                $resp["message"] = $th->getMessage();
            }
		}

		 if ($print) {
            echo json_encode($resp);
        }



        return $resp;
		
	}

	public function actualizar($print = true)
	{
		try {
			$this->db->connect();
			$this->esValido(self::ACTUALIZAR_CARGO);       
			$this->beginTransaction();

			$query = "UPDATE cargo SET 
				nombre = :nombre,
				nivel = :nivel
			WHERE id = :id";
			$parametros = array(
				"id" => $this->id,
				"nombre" => $this->nombre,
				"nivel" => $this->nivel
			);

			$this->ejecutarStatement($query, $parametros);

			$bitacoraTexto = "Cargo '".$this->nombre."' actualizado";
			
			Bitacora::registrarTransaccion($bitacoraTexto, $this->db->pdo());

			if($this->getTestingMode()) {
				$this->rollBack();
				$this->beginTransaction();
			}

			$this->commit();

			$this->db->disconnect();

			$resp = array(
				"success" => true,
				"message" => "Cargo actualizado con exito"
			);
		} catch (\Throwable $th) {
			 if(isset($this->db) && $this->db->connected() && $this->db->pdo()->inTransaction()){
				$this->db->pdo()->rollBack();
				$this->db->disconnect();
			}

			$resp = array(
				"success" => false,
				"message" => "Ocurrio un error al actualizar al cargo"
			);
			if(DEVELOPER_MODE) {
				$resp["error"] = $th->getMessage()." :: Linea: ".$th->getLine();
				$resp["consoleError"] = $th->getMessage()." :: Linea: ".$th->getLine() ."\n :: Trace: ".$th->getTraceAsString();
			}
			if($th instanceof Exception && $th->getCode() == self::SHOW_EXCEPTION_CARGO){ 
				$resp["message"] = $th->getMessage();
			}
		}

		 if ($print) {
			echo json_encode($resp);
		 }

		return $resp;
	}

	public function eliminarCargo($print = true)
	{
		try {
			$this->db->connect();
			$this->esValido(self::ELIMINAR_CARGO);       
			$this->beginTransaction();

			$query = "DELETE FROM cargo WHERE id = :id";
			$parametros = array(
				"id" => $this->id
			);

			$this->ejecutarStatement($query, $parametros);

			$bitacoraTexto = "Cargo '".$this->nombre."' eliminado";
			
			Bitacora::registrarTransaccion($bitacoraTexto, $this->db->pdo());

			if($this->getTestingMode()) {
				$this->rollBack();
				$this->beginTransaction();
			}

			$this->commit();

			$this->db->disconnect();

			$resp = array(
				"success" => true,
				"message" => "Cargo eliminado con exito"
			);
		} catch (\Throwable $th) {
			 if(isset($this->db) && $this->db->connected() && $this->db->pdo()->inTransaction()){
				$this->db->pdo()->rollBack();
				$this->db->disconnect();
			}

			$resp = array(
				"success" => false,
				"message" => "Ocurrio un error al eliminar al cargo"
			);
			if(DEVELOPER_MODE) {
				$resp["error"] = $th->getMessage()." :: Linea: ".$th->getLine();
				$resp["consoleError"] = $th->getMessage()." :: Linea: ".$th->getLine() ."\n :: Trace: ".$th->getTraceAsString();
			}
			if($th instanceof Exception && $th->getCode() == self::SHOW_EXCEPTION_CARGO){ 
				$resp["message"] = $th->getMessage();
			}
		}

		 if ($print) {
			echo json_encode($resp);
		 }

		return $resp;
	}

	public function obtenerPorId():Cargo
    {
        try {
            $this->db->connect();
            $query = "SELECT * FROM cargo WHERE id = :id";
			$this->esValido(self::OBTENER_CARGO_POR_ID);
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

	

	public static function getCargosOptions($checkedId = null) : string
    {

        $cargoObj = new Cargo();

        $cargos = $cargoObj->listarPadre();
        $options = "";
        foreach ($cargos as $cargo) {
            $options .= "<option ".($checkedId == $cargo->id ? "selected" : "")." value='" . $cargo->id . "'>" . $cargo->get_nombre() . "</option>";
        }
        return $options;
        
    }


	public function esValido($code) : void
	{

		$mensajes = new class {
			public $nombreRequerido = "El nombre del cargo es requerido";
			public $nivelRequerido = "El nivel del cargo es requerido";
			public $nombreInvalido = "El nombre del cargo es invalido";
			public $nivelInvalido = "El nivel del cargo es invalido";
			public $cargoExistente = "El cargo ya existe";
			public $cargoNoExistente = "El cargo no existe";
			public $cargoRequerido = "El cargo no esta seleccionado";
			public $cargoInvalido = "El cargo es invalido";
			public $cargoRelacionesEliminar = "El cargo esta siendo utilizado y no puede ser eliminado";

		};
		// validar Campos
		if($code == self::REGISTRAR_CARGO || $code == self::ACTUALIZAR_CARGO ) {
			if(!isset($this->nombre) || empty(trim($this->nombre))) {
				throw new Exception($mensajes->nombreRequerido, self::SHOW_EXCEPTION_CARGO);
			}
			if(!isset($this->nivel) || empty(trim($this->nivel))) {
				throw new Exception($mensajes->nivelRequerido, self::SHOW_EXCEPTION_CARGO);
			}

			if(!preg_match(REG_ALFABETICO, $this->nombre)) {
				throw new Exception($mensajes->nombreInvalido, self::SHOW_EXCEPTION_CARGO);
			}
			if(!preg_match(REG_NUMERICO, $this->nivel)) {
				throw new Exception($mensajes->nivelInvalido, self::SHOW_EXCEPTION_CARGO);
			}
		}

		if(
			$code == self::ELIMINAR_CARGO ||
			$code == self::ACTUALIZAR_CARGO ||
			$code == self::OBTENER_CARGO_POR_ID
		) {
			if(!isset($this->id) || empty(trim($this->id))) {
				throw new Exception($mensajes->cargoRequerido, self::SHOW_EXCEPTION_CARGO);
			}
			if(!preg_match(REG_NUMERICO, $this->id)) {
				throw new Exception($mensajes->cargoInvalido, self::SHOW_EXCEPTION_CARGO);
			}
		}
		//validar  en base de datos

		if($code == self::ELIMINAR_CARGO || $code == self::ACTUALIZAR_CARGO) {
			// valido la existencia del cargo por id
			$query = "SELECT * FROM cargo WHERE id = :id";
			$parametros = array(
				"id" => $this->id,
			);
			$stmt = $this->ejecutarStatement($query, $parametros);

			if($stmt->rowCount() == 0) {
				throw new Exception($mensajes->cargoNoExistente, self::SHOW_EXCEPTION_CARGO);
			}
			else if ($code == self::ELIMINAR_CARGO) {
				$this->nombre = $stmt->fetch(PDO::FETCH_OBJ)->nombre;
			}
			
		}

		if($code == self::REGISTRAR_CARGO || $code == self::ACTUALIZAR_CARGO) {

			$query = "SELECT id FROM cargo WHERE nombre = :nombre";
			$parametros = array(
				"nombre" => $this->nombre,
			);

			if($code == self::ACTUALIZAR_CARGO) {
				$query .= " AND id <> :id";
				$parametros["id"] = $this->id;
			}

			$stmt = $this->ejecutarStatement($query, $parametros);

			if($stmt->rowCount() > 0) {
				throw new Exception($mensajes->cargoExistente, self::SHOW_EXCEPTION_CARGO);
			}

		}

		if($code == self::ELIMINAR_CARGO) {
			$query = "SELECT c.* FROM cargo as c JOIN asignacion_laboral as al on al.idCargo = c.id WHERE al.idCargo = :id LIMIT 1";
			$parametros = array(
				"id" => $this->id,
			);
			$stmt = $this->ejecutarStatement($query, $parametros);
			if($stmt->rowCount() > 0) {
				throw new Exception($mensajes->cargoRelacionesEliminar, self::SHOW_EXCEPTION_CARGO);
			}
		}

		

	}


	public function eliminar(bool $eliminadoLogico = true): bool
	{
		return false;
	}

	public function listar(int $estado = null): array
	{
		try {
			$lista = parent::listar($estado);

			$resp = [
				"success" => true,
				"data" => $lista,
				"actualizar" => tienePermiso(Modulo::CARGOS, Permiso::ACTUALIZAR),
				"eliminar" => tienePermiso(Modulo::CARGOS, Permiso::ELIMINAR)
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

	public function listarPadre(int $estado = null):array{
		return parent::listar($estado);
	}

	public function cargarUltimo(bool $userBD = false): ?self
	{
		$respuesta = $this->listar();
		if (count($respuesta['data']) === 0) {
			return null;
		}
		return end($respuesta['data']);
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

	public function jsonSerialize()
	{
		$data = [];
		if(isset($this->id)){
			$data["id"] = $this->id;
		}
		if(isset($this->nombre)){
			$data["nombre"] = $this->nombre;
		}
		if(isset($this->nivel)){
			$data["nivel"] = $this->nivel;
		}
		return $data;
	}

	PUBLIC function get_nombre(){
		return $this->nombre;
	}
	PUBLIC function get_nivel(){
		return $this->nivel;
	}

	


}





 ?>