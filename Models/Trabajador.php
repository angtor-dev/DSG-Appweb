<?php
class Trabajador extends Model
{
    public null|int|string $idDepartamento;
    public null|int|string $idDivision;
    private string $cedula;
    private string $cedulaSeleccion;
    private string $nombre;
    private string $apellido;
    private string $telefono;
    private string $fechaIngreso;
    private Cargo|string $cargo;
    private Turno|string $turno;
    private string $cargoNivel;
    public Division $departamento;
    public null|string $idTurno;
    public null|string $idCargo;


    private string $estado;

    const REGISTRAR_TRABAJADOR = 1;
    const ELIMINAR_TRABAJADOR = 2;
    const ACTUALIZAR_TRABAJADOR = 3;
    const SHOW_EXCEPTION = 1001;
    const TRABAJADOR_ACTIVO = "1";
    const TRABAJADOR_INACTIVO = "0";

    public function __construct() {
        parent::__construct();
        if(!empty($this->idDivision)) $this->idDepartamento = $this->idDivision;
        if (!empty($this->idDepartamento)) {
            $this->departamento = Division::cargar($this->idDepartamento);
        }
    }

    public static function cargarPorCedula (string $cedula) : mixed{
        $bd = Database::getInstance();
        $bd->connect();

        $query = "SELECT
                t.*
                ,al.idTurno
                ,COALESCE(tu.nombre,'') as turno
                ,al.idDivision
                ,COALESCE(c.nombre,'') as cargo
                ,al.idCargo
                ,al.id as idAsignacionLaboral
            FROM
                trabajador AS t
            left JOIN asignacion_laboral AS al
            ON
                al.idTrabajador = t.id AND al.esActual = 1
            left JOIN turno as tu on al.idTurno = tu.id
            left JOIN cargo as c on c.id = al.idCargo

            WHERE
                cedula = :cedula;";
            //$query = "SELECT * FROM `trabajador` WHERE cedula = :cedula;";


        $consulta = $bd->pdo()->prepare($query);
        $consulta->execute([':cedula'=>$cedula]);
        $consulta->setFetchMode(PDO::FETCH_CLASS, "Trabajador");


        $bd->disconnect();

        if( $consulta->rowCount() == 0){
            return array();
        }

        return $consulta->fetch();
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
                throw new Exception("El campo 'Cedula' solo puede contener números y no puede tener mas de 8 digitos",self::SHOW_EXCEPTION );
            }

            if (empty(trim($this->nombre))) {
                throw new Exception("El campo 'Nombre' es obligatorio", self::SHOW_EXCEPTION );
            }
            if (!preg_match(REG_ALFABETICO, $this->nombre)) {
                throw new Exception("El campo 'Nombre' solo puede contener letras y números", self::SHOW_EXCEPTION);
            }
            if (empty(trim($this->apellido))) {
                throw new Exception("El campo 'Apellido' es obligatorio",  self::SHOW_EXCEPTION );
            }
            if (!preg_match(REG_ALFABETICO, $this->apellido)) {
                throw new Exception("El campo 'Apellido' solo puede contener letras y números",self::SHOW_EXCEPTION );
            }
            if (empty(trim($this->telefono))) {
                throw new Exception("El campo 'Teléfono' es obligatorio",self::SHOW_EXCEPTION );
            }
            if (!preg_match(REG_TELEFONO, $this->telefono)) {
                throw new Exception("El campo 'Teléfono' solo puede contener números", self::SHOW_EXCEPTION);
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
            if ( !isset($this->cedulaSeleccion) || empty(trim($this->cedulaSeleccion))) {
                throw new Exception("Error al obtener la cedula del trabajador seleccionado", self::SHOW_EXCEPTION);
            }
            if (!preg_match(REG_CEDULA, $this->cedulaSeleccion)) {
                throw new Exception("Error al obtener la cedula del trabajador seleccionado",self::SHOW_EXCEPTION );
            }
        }


        // valido base de datos

        if($control == self::REGISTRAR_TRABAJADOR){
            $trabajador = Trabajador::cargarPorCedula($this->cedula);
            $this->estado = "";

            if(!empty($trabajador) and ( $this->estado = $trabajador->getEstado() ) == self::TRABAJADOR_ACTIVO){
                throw new Exception("El trabajador con cedula $this->cedula ya existe en la base de datos", self::SHOW_EXCEPTION);
            }
            if(!empty($trabajador)){
                $this->id = $trabajador->id;
            }
            
        }
        if($control == self::ACTUALIZAR_TRABAJADOR || $control == self::ELIMINAR_TRABAJADOR){
            $trabajador = Trabajador::cargarPorCedula($this->cedulaSeleccion);
            if(empty($trabajador)){
                throw new Exception("El trabajador seleccionado no existe en la base de datos", self::SHOW_EXCEPTION);
            }



            if( $control == self::ACTUALIZAR_TRABAJADOR && $this->cedula != $this->cedulaSeleccion){
                $trabajador = Trabajador::cargarPorCedula($this->cedula);
                if(!empty($trabajador)){
                    throw new Exception("El trabajador con cedula $this->cedula ya existe en la base de datos", self::SHOW_EXCEPTION);
                }
            }
            
        }

        if($control == self::REGISTRAR_TRABAJADOR || $control == self::ACTUALIZAR_TRABAJADOR){

            // valida la existencia del departamento

            $departamento = Division::cargar($this->idDepartamento);
            if(empty($departamento)){
                throw new Exception("El departamento seleccionado no existe en la base de datos", self::SHOW_EXCEPTION);
            }

            // valida el turno 
            $turno = $this->turno;
            if(empty($turno)){
                throw new Exception("El turno seleccionado no es valido", self::SHOW_EXCEPTION);
            }
            //valida el cargo
            $cargo = $this->cargo;
            if(empty($cargo)){
                throw new Exception("El cargo seleccionado no es valido", self::SHOW_EXCEPTION);
            }
            
        }

        if($control == self::ELIMINAR_TRABAJADOR){

            // verifico la existencia de un usuario relacionado al trabajador
            // $usuario = Usuario::cargarPorCedula($this->cedulaSeleccion);
            // if(!empty($usuario)){
            //     throw new Exception("El trabajador seleccionado tiene un usuario asociado y no puede ser eliminado", self::SHOW_EXCEPTION);
            // }
        }

    }

    public function registrar($print = true) : Array
    {
        try {
            
            
            $this->esValido(self::REGISTRAR_TRABAJADOR);
            
            $this->db->connect();
            $this->beginTransaction();

            $parametros2 = array(
                "idTrabajador" => $this->id ?? null,
                "idDepartamento" => $this->idDepartamento,
                "idTurno" => $this->turno,
                "idCargo" => $this->cargo,
                "fechaIngreso" => $this->fechaIngreso
            );


            
            if($this->estado == self::TRABAJADOR_INACTIVO){
                $query = "UPDATE trabajador SET nombre = :nombre, apellido = :apellido, telefono = :telefono, fechaIngreso = :fechaIngreso, estado = 1 WHERE cedula = :cedula;";
                $parametros2["fechaIngreso"] = null;
            }
            else{
                $query = "INSERT INTO trabajador (cedula, nombre, apellido, telefono,fechaIngreso) VALUES (:cedula, :nombre, :apellido, :telefono, :fechaIngreso);";

            }

            $query2 = "CALL sp_gestionar_asignacion_laboral(:idTrabajador, :idDepartamento, :idTurno, :idCargo, :fechaIngreso);";
            

            $parametros = array(
                "cedula" => $this->cedula,
                "nombre" => $this->nombre,
                "apellido" => $this->apellido,
                "telefono" => $this->telefono,
                "fechaIngreso" => $this->fechaIngreso
            );

            // registro el nuevo trabajador
            $this->ejecutarStatement($query, $parametros);

            if($this->estado != self::TRABAJADOR_INACTIVO){
                $parametros2["idTrabajador"] = $this->db->pdo()->lastInsertId();
            }

            // registro la asignacion laboral
            $this->ejecutarStatement($query2, $parametros2);

            if(!$this->testHandler()){
                Bitacora::registrarTransaccion("Trabajador '".$this->getNombreCompleto()."' registrado", $this->db->pdo());
            }

            $this->commit();
            $this->db->disconnect();

            $resp = array(
                "success" => true,
                "message" => "Trabajador registrado con éxito"
            );
        } catch (\Throwable $th) {
            $this->disconectHandlerExeption();

            $resp = array(
                "success" => false,
                "message" => "Ocurrió un error al registrar al trabajador"
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
            $this->beginTransaction();

            $parametros = array(
                "cedula" => $this->cedula,
                "nombre" => $this->nombre,
                "apellido" => $this->apellido,
                "telefono" => $this->telefono,
                "fechaIngreso" => $this->fechaIngreso,
                "idTrabajador" => $this->id
            );

            $query = "UPDATE trabajador SET cedula = :cedula, nombre = :nombre, apellido = :apellido, telefono = :telefono, fechaIngreso = :fechaIngreso WHERE id = :idTrabajador;";

            $this->ejecutarStatement($query, $parametros);

            $parametros = array(
                "idTrabajador" => $this->id,
                "idDepartamento" => $this->idDepartamento,
                "idTurno" => $this->turno,
                "idCargo" => $this->cargo,
                "fechaIngreso" => null
            );

            $query = "CALL sp_gestionar_asignacion_laboral(:idTrabajador, :idDepartamento, :idTurno, :idCargo, :fechaIngreso);";

            $this->ejecutarStatement($query, $parametros);
            
            if(!$this->testHandler()) { // si no es una prueba crea la bitacora
                Bitacora::registrarTransaccion("Trabajador '".$this->getNombreCompleto()."' actualizado", $this->db->pdo());
            }

            $this->commit();
            $this->db->disconnect();

            $resp = array(
                "success" => true,
                "message" => "Trabajador actualizado con éxito"
            );
        } catch (\Throwable $th) {
            $this->disconectHandlerExeption();
            $resp = array(
                "success" => false,
                "message" => "Ocurrió un error al actualizar al trabajador"
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
            $this->beginTransaction();

            if( !$logicDelete ) {
                
                $query = "DELETE FROM trabajador WHERE cedula = :cedulaSeleccion;";
                $stmt = $this->prepare($query);
                $stmt->bindValue("cedulaSeleccion", $this->cedulaSeleccion);
                $stmt->execute();
            }
            else{

                $query = "UPDATE trabajador SET estado = 0 WHERE cedula = :cedulaSeleccion;";
                $this->ejecutarStatement($query, ["cedulaSeleccion" => $this->cedulaSeleccion]);

                $query = "UPDATE asignacion_laboral al join trabajador t on al.idTrabajador = t.id set al.fechaFin = CURRENT_TIMESTAMP() WHERE t.cedula = :cedulaSeleccion;";
                $this->ejecutarStatement($query, ["cedulaSeleccion" => $this->cedulaSeleccion]);
                

            }

            if(!$this->testHandler()) { // si no es una prueba crea la bitacora
                Bitacora::registrarTransaccion("Trabajador '".$this->cedulaSeleccion."' eliminado", $this->db->pdo());
            }

            

            $this->commit();
            $this->db->disconnect();

            $resp = array(
                "success" => true,
                "message" => "Trabajador eliminado con éxito"
            );
        } catch (\Throwable $th) {
            $this->disconectHandlerExeption();
            $resp = array(
                "success" => false,
                "message" => "Ocurrió un error al eliminar al trabajador"
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

    /**
     * Lista los trabajadores segun los filtros ingresados.
     * Si no se ingresan filtros, se devuelve la lista completa de trabajadores.
     * @param boolean $print si se imprime el resultado en formato json
     * @return array un array con la lista de trabajadores y un mensaje de estado
     */
    public function listraFiltro($print = true):array{
        try {
            $this->db->connect();
            $query = "SELECT t.*, c.id as idCargo, c.nombre as cargo, c.nivel as cargoNivel , tu.id as idTurno, tu.nombre as turno, d.id as idDepartamento, d.nombre as departamento FROM trabajador as t join asignacion_laboral al on al.idTrabajador = t.id and al.esActual = 1 join cargo c on c.id = al.idCargo join turno tu on tu.id = al.idTurno join division d on d.id = al.idDivision where ";
            $where ="";
            $list = [];
            $parametros = [];
            if(isset($this->cedula)){
                $list[] = "cedula LiKE :cedula";
                $parametros["cedula"] = "%".$this->cedula."%";

            }
            if(isset($this->nombre)){
                $list[] = "t.nombre LIKE :nombre";
                $parametros["nombre"] = "%".$this->nombre."%";
            }
            if(isset($this->apellido)){
                $list[] = "t.apellido LIKE :apellido";
                $parametros["apellido"] = "%".$this->apellido."%" ;
            }
            if(isset($this->telefono)){
                $list[] = "t.telefono LIKE :telefono";
                $parametros["telefono"] = "%".$this->telefono."%" ;
            }
            if(isset($this->cargo)){
                $list[] = "c.id = :cargo";
                $parametros["cargo"] = $this->cargo;
            }
            if(isset($this->turno)){
                $list[] = "tu.id = :turno";
                $parametros["turno"] = $this->turno;
            }
            if(isset($this->idDepartamento)){
                $list[] = "idDivision = :idDepartamento";
                $parametros["idDepartamento"] = $this->idDepartamento;
            }
            if(isset($this->cargoNivel)){
                $list[] = "c.nivel = :cargoNivel";
                $parametros["cargoNivel"] = $this->cargoNivel;
            }
            $list[] = "estado = 1";
            $where = implode(" AND ", $list);
            $query .= $where;

            $lista = $this->ejecutar($query, $parametros);
            $this->db->disconnect();

            $resp = array(
                "success" => true,
                "data" => $lista
            );
            
            
            
            
        } catch (\Throwable $th) {
            if(isset($this->db)) $this->db->disconnect();
            $resp = array(
                "success" => false,
                "message" => "Ocurrió un error al listar a los trabajadores"
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

    /**
     * Summary of listar
     * @param mixed $estado
     * @return Trabajador[]
     */
    public function listar ($estado = 1):array {
        
        try {
            $this->db->connect();
            $fetchMode = PDO::FETCH_CLASS;
            $fetchArg = Trabajador::class;
            $query = "SELECT
                t.*
                ,al.idTurno
                ,tu.nombre as turno
                ,al.idDivision
                ,c.nombre as cargo
                ,al.id as idAsignacionLaboral
            FROM
                trabajador AS t
            JOIN asignacion_laboral AS al
            ON
                al.idTrabajador = t.id AND al.esActual = 1
            JOIN turno as tu on al.idTurno = tu.id
            JOIN cargo as c on c.id = al.idCargo";

            $parametros = [];

            if($estado!= null){
                $query .=" WHERE estado = :estado ";
                $parametros["estado"] = $estado;
            }


            $resp = $this->ejecutar($query, $parametros, $fetchMode, $fetchArg);
            $this->db->disconnect();
            
        } catch (\Throwable $th) {

            if(isset($this->db) && $this->db->connected()) {
                $this->db->disconnect();
            }
            
            $resp = array();
            
            if(DEVELOPER_MODE) {
                debug($th->getMessage().":: Linea: ".$th->getLine());
            }
            

        }
        return $resp;
    }

    public function getTrayectoria() : array {
        $resp=array();
        if(!empty($this->id)) {
            $this->db->connect();
            $query = "SELECT
   	t.nombre as turno,
    c.nombre as cargo,
    d.nombre as division,
    DATE_FORMAT(al.fechaAsignacion, '%d/%m/%Y') as desde,
    if(al.esActual=1, 'Actual', DATE_FORMAT(al.fechaFin, '%d/%m/%Y')) as hasta
FROM
    asignacion_laboral AS al
JOIN division as d on d.id = al.idDivision
JOIN cargo as c on c.id = al.idCargo
JOIN turno as t on t.id = al.idTurno
WHERE
    al.idTrabajador = :id
ORDER BY
    al.esActual
DESC";
            $parametros = ["id" => $this->id];
            $resp = $this->ejecutar($query, $parametros);

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
    public function getCargo() : string {
        // TODO get desde el modelo
        return $this->cargo;
    }
    public function getTurno() : string {
        // TODO get desde el modelo
        return $this->turno;
    }
    public function getFechaIngreso() : string {
        return $this->fechaIngreso; 
    }
    public function getIdDepartamento() : int {
        return ($this->departamento instanceof Division) ? $this->departamento->id : $this->idDepartamento?? "";
    }
    public function getEstado() : string {
        return $this->estado;
    }
    /**
     * Retorna la antiguedad del trabajador en años
     * si es menos de 1 año retorna "menor a 1 año"
     * calculado desde la propiedad fechaIngreso
     * @return void
     */
    public function getAntiguedad() : string {
        $fechaIngreso = new DateTime($this->fechaIngreso);
        $hoy = new DateTime();
        $interval = $hoy->diff($fechaIngreso, true);
        $anios = $interval->y;
        $meses = $interval->m;
        $dias = $interval->d;
        $texto = "";
        if($anios > 0) {
            $texto .= $anios . (($anios > 1) ? " años " : " año ");
        }
        if($meses > 0) {
            $texto .= $meses . (($meses > 1) ? " meses " : " mes ");
        }
        if($dias > 0) {
            $texto .= $dias . (($dias > 1) ? " días " : " día ");
        }
        return $texto;
    }
    public function exporDataBase ():array{
        $resp = array();

        try {
            $this->db->connect();// || $this->db->connectUser(); // para las distintas base de datos
            $this->beginTransaction();

            $resp = $this->db->exportDatabase();

            if($this->getTestingMode()) {
                $this->rollBack();
                $this->beginTransaction();
            }

            $this->commit();

            $this->db->disconnect();
        } catch (\Throwable $th) {
            $this->disconectHandlerExeption();
            $resp["success"] = false;
            $resp["message"] = $th->getMessage();
        }
        return $resp;
    }

    public function importDatabase ($filePath) : array {
        $resp = array();
        $this->setTestingMode(true);
        try {
            $this->db->connect();// || $this->db->connectUser(); // para las distintas base de datos
            $this->beginTransaction();
            

            $resp = $this->db->importDatabase($filePath);
            if($this->getTestingMode()) {
                $this->rollBack();
                $this->beginTransaction();
            }
            $this->commit();
            $this->db->disconnect();
        } catch (\Throwable $th) {
            $this->disconectHandlerExeption();
            $resp["success"] = false;
            $resp["message"] = $th->getMessage();
            if(DEVELOPER_MODE) {
                $resp["trace"] = $th->getTraceAsString();
                $resp["line"] = $th->getLine();
            }
        }
        return $resp;
    }
}

/*
procedure sp_gestionar_asignacion_laboral:
BEGIN
    -- variables
    DECLARE v_current_id INT;
    DECLARE v_current_idDepartamento INT;
    DECLARE v_current_idTurno INT;
    DECLARE v_current_idCargo INT;

    -- Variables para capturar el SQLSTATE y el mensaje de error en el manejador de excepciones
    DECLARE v_sqlstate CHAR(5);
    DECLARE v_message_text VARCHAR(255);
    
    -- Intentar obtener la asignación laboral actual (donde esActual = 1) para el trabajador
    -- El bloqueo FOR UPDATE ayuda a prevenir condiciones de carrera si múltiples procesos
    -- intentan modificar la misma asignación simultáneamente.
    SELECT
        id,
        idDivision,
        idTurno,
        idCargo
    INTO
        v_current_id,
        v_current_idDepartamento,
        v_current_idTurno,
        v_current_idCargo
    FROM
        asignacion_laboral
    WHERE
        idTrabajador = p_idTrabajador AND esActual = 1;
    -- FOR UPDATE; -- Bloquea la fila seleccionada para evitar que otras transacciones la modifiquen.

    -- Verificar si se encontró una asignación laboral actual para el trabajador
    IF v_current_id IS NOT NULL THEN
        -- Si existe una asignación actual, verificar si los nuevos datos son diferentes
        IF (v_current_idDepartamento != p_idDepartamento OR
            v_current_idTurno != p_idTurno OR
            v_current_idCargo != p_idCargo) THEN

            -- Si los datos son diferentes, finalizar la asignación actual
            UPDATE asignacion_laboral
            SET fechaFin = CURRENT_TIMESTAMP()
            WHERE id = v_current_id;

            -- Insertar la nueva asignación laboral
            
            
            
            INSERT INTO asignacion_laboral (idTrabajador, idDivision, idTurno, idCargo, fechaAsignacion)
            VALUES (p_idTrabajador, p_idDepartamento, p_idTurno, p_idCargo, COALESCE(p_fechaAsignacion, CURRENT_TIMESTAMP() ) );

            -- Si los datos son los mismos, no hacer nada
        END IF;
    ELSE
        -- Si no hay una asignación actual para el trabajador, insertar la nueva asignación
        INSERT INTO asignacion_laboral (idTrabajador, idDivision, idTurno, idCargo, fechaAsignacion)
        VALUES (p_idTrabajador, p_idDepartamento, p_idTurno, p_idCargo, COALESCE(p_fechaAsignacion, CURRENT_TIMESTAMP() ));

    END IF;

END
 */