<?php
// TODO agregar indice unico a la cedula del trabajador
// TODO agregar Alias a los trabajadores
// (para que Sir Reginald Pomposo siga siendo Chui )
class Trabajador extends Model
{
    public int|string $idDepartamento;
    private string $cedula;
    private string $cedulaSeleccion;
    private string $nombre;
    private string $apellido;
    private string $telefono;
    private string $fechaIngreso;
    private Cargo|string $cargo;
    private Turno|string $turno;
    public Departamento $departamento;

    const REGISTRAR_TRABAJADOR = 1;
    const ELIMINAR_TRABAJADOR = 2;
    const ACTUALIZAR_TRABAJADOR = 3;
    const SHOW_EXCEPTION = 1001;

    public function __construct() {
        parent::__construct();
        if (!empty($this->idDepartamento)) {
            $this->departamento = Departamento::cargar($this->idDepartamento);
        }
    }

    public static function cargarPorCedula (string $cedula) : mixed{
        $bd = Database::getInstance();
        $bd->connect();
        $query = "SELECT * FROM `trabajador` WHERE cedula = :cedula;";

        $consulta = $bd->pdo()->prepare($query);
        $consulta->execute([':cedula'=>$cedula]);
        $consulta->setFetchMode(PDO::FETCH_CLASS, "Trabajador");


        $bd->disconnect();

        if( $consulta->rowCount() == 0){
            return array();
        }

        return $consulta->fetch();
    }

    public function mapearFormulario() : bool
    {
        try {
            $this->cedula = $_POST['cedula'] ?? "";
            $this->nombre = $_POST['nombre']  ?? "";
            $this->apellido = $_POST['apellido']  ?? "";
            $this->telefono = $_POST['telefono']  ?? "";
            $this->cargo = $_POST['cargo']  ?? "";
            $this->turno = $_POST['turno']  ?? "";
            $this->idDepartamento = $_POST['departamento']  ?? "";
            $this->fechaIngreso = $_POST['fecha_ingreso']  ?? "";
            $this->cedulaSeleccion = $_POST['cedulaSeleccion'] ?? "";
            
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function esValido($control) : void
    {
        // throw con el codigo 1001 se mostrara en la vista

        // valido campos

        if( $control == self::REGISTRAR_TRABAJADOR || $control == self::ACTUALIZAR_TRABAJADOR ) {

            if ( !isset($this->cedula) || empty(trim($this->cedula))) {
                throw new Exception("El campo 'Cedula' es obligatorio", self::SHOW_EXCEPTION);
            }
            if (!preg_match(REG_CEDULA, $this->cedula)) {
                throw new Exception("El campo 'Cedula' solo puede contener números",self::SHOW_EXCEPTION );
            }

            if (empty(trim($this->nombre))) {
                throw new Exception("El campo 'Nombre' es obligatorio", );
            }
            if (!preg_match(REG_ALFABETICO, $this->nombre)) {
                throw new Exception("El campo 'Nombre' solo puede contener letras y números", self::SHOW_EXCEPTION);
            }
            if (empty(trim($this->apellido))) {
                throw new Exception("El campo 'Apellido' es obligatorio", );
            }
            if (!preg_match(REG_ALFABETICO, $this->apellido)) {
                throw new Exception("El campo 'Apellido' solo puede contener letras y números",self::SHOW_EXCEPTION );
            }
            if (empty(trim($this->telefono))) {
                throw new Exception("El campo 'Telefono' es obligatorio",self::SHOW_EXCEPTION );
            }
            if (!preg_match(REG_TELEFONO, $this->telefono)) {
                throw new Exception("El campo 'Telefono' solo puede contener números", self::SHOW_EXCEPTION);
            }
            if (empty($this->cargo)) {
                throw new Exception("El campo 'Cargo' es obligatorio",self::SHOW_EXCEPTION );
            }
            if (empty($this->turno)) {
                throw new Exception("El campo 'Turno' es obligatorio",self::SHOW_EXCEPTION );
            }
            if (empty($this->idDepartamento)) {
                throw new Exception("El campo 'Departamento' es obligatorio",self::SHOW_EXCEPTION );
            }
            if (empty($this->fechaIngreso)) {
                throw new Exception("El campo 'Fecha de Ingreso' es obligatorio", self::SHOW_EXCEPTION);
            }
            if (!preg_match(REG_FECHA, $this->fechaIngreso)) {
                throw new Exception("El campo 'Fecha de Ingreso' solo puede contener números",self::SHOW_EXCEPTION );
            }
        }

        if($control == self::ACTUALIZAR_TRABAJADOR || $control == self::ELIMINAR_TRABAJADOR){
            if (empty(trim($this->cedulaSeleccion))) {
                throw new Exception("Error al obtener la cedula del trabajador seleccionado", self::SHOW_EXCEPTION);
            }
            if (!preg_match(REG_CEDULA, $this->cedulaSeleccion)) {
                throw new Exception("Error al obtener la cedula del trabajador seleccionado",self::SHOW_EXCEPTION );
            }
        }


        // valido base de datos

        if($control == self::REGISTRAR_TRABAJADOR){
            $trabajador = Trabajador::cargarPorCedula($this->cedula);
            if(!empty($trabajador)){
                throw new Exception("El trabajador con cedula $this->cedula ya existe en la base de datos", self::SHOW_EXCEPTION);
            }
        }
        if($control == self::ACTUALIZAR_TRABAJADOR || $control == self::ELIMINAR_TRABAJADOR){
            $trabajador = Trabajador::cargarPorCedula($this->cedulaSeleccion);
            if(empty($trabajador)){
                throw new Exception("El trabajador selecionado no existe en la base de datos", self::SHOW_EXCEPTION);
            }



            if($this->cedula != $this->cedulaSeleccion && $control == self::ACTUALIZAR_TRABAJADOR){
                $trabajador = Trabajador::cargarPorCedula($this->cedula);
                if(!empty($trabajador)){
                    throw new Exception("El trabajador con cedula $this->cedula ya existe en la base de datos", self::SHOW_EXCEPTION);
                }
            }
            
        }

        if($control == self::REGISTRAR_TRABAJADOR || $control == self::ACTUALIZAR_TRABAJADOR){

            // valida la existencia del departamento

            $departamento = Departamento::cargar($this->idDepartamento);
            if(empty($departamento)){
                throw new Exception("El departamento selecionado no existe en la base de datos", self::SHOW_EXCEPTION);
            }

            // valida el turno 
            $turno = Turno::from($this->turno);
            if(empty($turno)){
                throw new Exception("El turno selecionado no es valido", self::SHOW_EXCEPTION);
            }
            //valida el cargo
            $cargo = Cargo::from($this->cargo);
            if(empty($cargo)){
                throw new Exception("El cargo selecionado no es valido", self::SHOW_EXCEPTION);
            }
            
        }

        if($control == self::ELIMINAR_TRABAJADOR){

            // verifico la existencia de un usuario relacionado al trabajador
            $usuario = Usuario::cargarPorCedula($this->cedulaSeleccion);
            if(!empty($usuario)){
                throw new Exception("El trabajador selecionado tiene un usuario asociado y no puede ser eliminado", self::SHOW_EXCEPTION);
            }
        }

    }

    public function registrar($print = true) : Array
    {
        $query = "INSERT INTO trabajador (cedula, nombre, apellido, telefono, cargo, turno, idDepartamento,fechaIngreso) VALUES (:cedula, :nombre, :apellido, :telefono, :cargo, :turno, :idDepartamento, :fechaIngreso);";
        try {
            

            $this->esValido(self::REGISTRAR_TRABAJADOR);

            $this->db->connect();
            $this->db->pdo()->beginTransaction();

            $stmt = $this->prepare($query);

            $stmt->bindValue("cedula", $this->cedula);
            $stmt->bindValue("nombre", $this->nombre);
            $stmt->bindValue("apellido", $this->apellido);
            $stmt->bindValue("telefono", $this->telefono);
            $stmt->bindValue("cargo", $this->cargo);
            $stmt->bindValue("turno", $this->turno);
            $stmt->bindValue("idDepartamento", $this->idDepartamento);
            $stmt->bindValue("fechaIngreso", $this->fechaIngreso);
            $stmt->execute();

            Bitacora::registrarTransaccion("Trabajador '".$this->getNombreCompleto()."' registrado", $this->db->pdo());

            //$this->db->pdo()->commit();
            $this->db->disconnect();

            $resp = array(
                "success" => true,
                "message" => "Trabajador registrado con exito"
            );
        } catch (\Throwable $th) {
            if( 
                isset($this->db) && 
                $this->db->connected() &&
                $this->db->pdo() instanceof \PDO &&
                $this->db->pdo()->inTransaction()
            ){
                $this->db->pdo()->rollBack();
                $this->db->disconnect();
            }

            $resp = array(
                "success" => false,
                "message" => "Ocurrio un error al registrar al trabajador"
            );
            if(DEVELOPER_MODE) {
                $resp["error"] = $th->getMessage().":: Linea: ".$th->getLine();
                $resp["trace"] = $th->getTraceAsString();
            }
            if($th instanceof Exception && $th->getCode() == self::SHOW_EXCEPTION){ 
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
            $this->esValido(self::ACTUALIZAR_TRABAJADOR);

            $this->db->connect();
            $this->db->pdo()->beginTransaction();

            $query = "UPDATE trabajador SET cedula = :cedula, nombre = :nombre, apellido = :apellido, telefono = :telefono, cargo = :cargo, turno = :turno, idDepartamento = :idDepartamento, fechaIngreso = :fechaIngreso WHERE cedula = :cedulaSeleccion;";
            $stmt = $this->prepare($query);
            $stmt->bindValue("cedula", $this->cedula);
            $stmt->bindValue("nombre", $this->nombre);
            $stmt->bindValue("apellido", $this->apellido);
            $stmt->bindValue("telefono", $this->telefono);
            $stmt->bindValue("cargo", $this->cargo);
            $stmt->bindValue("turno", $this->turno);
            $stmt->bindValue("idDepartamento", $this->idDepartamento);
            $stmt->bindValue("fechaIngreso", $this->fechaIngreso);
            $stmt->bindValue("cedulaSeleccion", $this->cedulaSeleccion);
            $stmt->execute();

            Bitacora::registrarTransaccion("Trabajador '".$this->getNombreCompleto()."' actualizado", $this->db->pdo());

            $this->db->pdo()->commit();
            $this->db->disconnect();

            $resp = array(
                "success" => true,
                "message" => "Trabajador actualizado con exito"
            );
        } catch (\Throwable $th) {
            if( 
                isset($this->db) && 
                $this->db->connected() &&
                $this->db->pdo() instanceof \PDO &&
                $this->db->pdo()->inTransaction()
            ){
                $this->db->pdo()->rollBack();
                $this->db->disconnect();
            }
            $resp = array(
                "success" => false,
                "message" => "Ocurrio un error al actualizar al trabajador"
            );
            if(DEVELOPER_MODE) {
                $resp["error"] = $th->getMessage().":: Linea: ".$th->getLine();
                $resp["trace"] = $th->getTraceAsString();
            }
            if($th instanceof Exception && $th->getCode() == self::SHOW_EXCEPTION){ 
                $resp["message"] = $th->getMessage();
            }
        }

        if ($print) {
            echo json_encode($resp);
        }

        return $resp;
    }


    public function eliminarTrabajador($logicDelete = false, $print = true) : Array
    {
        try {
            $this->esValido(self::ELIMINAR_TRABAJADOR);


            $this->db->connect();
            $this->db->pdo()->beginTransaction();

            if( !$logicDelete ) {
                
                $query = "DELETE FROM trabajador WHERE cedula = :cedulaSeleccion;";
                $stmt = $this->prepare($query);
                $stmt->bindValue("cedulaSeleccion", $this->cedulaSeleccion);
                $stmt->execute();
            }
            else{

                $query = "UPDATE trabajador SET estado = 0 WHERE cedula = :cedulaSeleccion;";
                $stmt = $this->prepare($query);
                $stmt->bindValue("cedulaSeleccion", $this->cedulaSeleccion);
                $stmt->execute();
            }

            

            

            //$this->db->pdo()->commit();
            $this->db->disconnect();

            $resp = array(
                "success" => true,
                "message" => "Trabajador eliminado con exito"
            );
        } catch (\Throwable $th) {
            if( 
                isset($this->db) && 
                $this->db->connected() &&
                $this->db->pdo() instanceof \PDO &&
                $this->db->pdo()->inTransaction()
            ){
                $this->db->pdo()->rollBack();
                $this->db->disconnect();
            }
            $resp = array(
                "success" => false,
                "message" => "Ocurrio un error al eliminar al trabajador"
            );
            if(DEVELOPER_MODE) {
                $resp["error"] = $th->getMessage().":: Linea: ".$th->getLine();
                $resp["trace"] = $th->getTraceAsString();
            }
            if($th instanceof Exception && $th->getCode() == self::SHOW_EXCEPTION){ 
                $resp["message"] = $th->getMessage();
            }
            // si falla por la clave foranea
            if(!$logicDelete and preg_match("/a foreign key constraint fails/", $th->getMessage())){
                $resp = $this->eliminarTrabajador(true, false);
            }

        }

        if ($print) {
            echo json_encode($resp);
        }

        return $resp;
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
    public function getTelefono() : string {
        return $this->telefono;
    }
    public function getCargo() : Cargo {
        return is_string($this->cargo) ? Cargo::from(lcfirst($this->cargo)) : $this->cargo;
    }
    public function getTurno() : Turno {
        return is_string($this->turno) ? Turno::from(ucfirst($this->turno)) : $this->turno;
    }
    public function getFechaIngreso() : string {
        return $this->fechaIngreso; 
    }
    public function getIdDepartamento() : int {
        return ($this->departamento instanceof Departamento) ? $this->departamento->id : $this->idDepartamento?? "";
    }
}