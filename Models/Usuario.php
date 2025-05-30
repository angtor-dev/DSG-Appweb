<?php

use function PHPUnit\Framework\throwException;
class Usuario extends Model
{
    public int|null $idRol = null;
    private string|null $correo = null;
    private int|string|null $estado = null;
    private string|null $clave = null;
    public Rol|int|null  $rol = null;
    public Trabajador|null $trabajador = null;
    private string|null $cedula = null;
    private string|null $idTrabajador = null;

    const REGISTRAR_USUARIO = 1;
    const ACTUALIZAR_USUARIO = 2;
    const ELIMINAR_USUARIO = 3;
    const SHOW_EXCEPTION = 1001;

    protected object $defaultMessages;

    
    
    
    public function __construct(
        string $correo = null,
        string $estado = null,
        Rol|int $rol = null,
        string $idTrabajador = null
    )
    {
        parent::__construct();
        $this->correo = $correo ?? $this->correo;
        $this->estado = $estado ?? $this->estado;
        $this->rol =  $rol ?? $this->rol;
        if (!empty($this->idRol)) {
            $this->rol = Rol::cargar($this->idRol);
        }
        if(!empty($this->idTrabajador)) {
            $this->trabajador = Trabajador::cargar($this->idTrabajador);
        }

        $this->defaultMessages = (object) [
            "registro" => "El usuario se ha registrado correctamente",
            "activado" => "El usuario se ha activado y registrado correctamente",
            "actualizar" => "El usuario se ha actualizado correctamente",
            "eliminar" => "El usuario se ha eliminado correctamente",
            "logicDelete" => "El usuario se ha desactivado correctamente",

            "errorRegistro" => "Ha ocurrido un error al registrar el usuario",
            "errorActualizar" => "Ha ocurrido un error al actualizar el usuario",
            "errorEliminar" => "Ha ocurrido un error al eliminar el usuario",
        ];
        

        
    }

    public static function login(string $correo, string $clave) : bool
    {
        if (empty($correo) || empty($clave)) {
            return false;
        }

        $usuario = Usuario::cargarPorCorreo($correo);

        if (is_null($usuario) || !$usuario->validarClave($clave)) {
            return false;
        }

        session_start();
        $_SESSION['usuario'] = $usuario;

        return true;
    }

    private function validarClave(string $clave) : bool
    {
        return password_verify($clave, $this->clave);
    }

    public static function cargarPorCorreo(string $correo, int $estado = 1) : Usuario | null
    {
        $bd = Database::getInstance();
        $query = "SELECT * FROM usuario WHERE correo = :correo AND estado = :estado";

        $bd->connect();
        
        $stmt = $bd->pdo()->prepare($query);
        $stmt->bindValue("correo", $correo);
        $stmt->bindValue("estado", $estado);

        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Usuario");

        $bd->disconnect();

        if ($stmt->rowCount() == 0) {
            return null;
        }
        return $stmt->fetch();
    }

    public static function cargarPorCedula(string $cedula, int $estado = 1) : Usuario | null
    {
        $bd = Database::getInstance();
        $query = "SELECT u.*, t.id as idTrabajador FROM Trabajador as t left join usuario as u on t.id = u.idTrabajador WHERE t.cedula = :cedula AND u.estado = :estado";

        $bd->connect();
        
        $stmt = $bd->pdo()->prepare($query);
        $stmt->bindValue("cedula", $cedula);
        $stmt->bindValue("estado", $estado);

        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Usuario");

        $bd->disconnect();

        if ($stmt->rowCount() == 0) {
            return null;
        }
        return $stmt->fetch();
    }


    

    /** @return array<self> */
    public static function listarPorRol(int $idRol, int $estado = null) : array
    {
        $bd = Database::getInstance();
        $query = "SELECT * FROM usuario WHERE idRol = $idRol" . (isset($estado) ? " AND estado = $estado" : "");

        $bd->connect();

        $stmt = $bd->pdo()->query($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Usuario");

        $bd->disconnect();

        if ($stmt->rowCount() == 0) {
            return array();
        }
        return $stmt->fetchAll();
    }

    public function registrar($print = true) : array
    {  
        
        
        
        
        
        try {
            $this->db->connect();
            $this->db->pdo()->beginTransaction();

            $validData = [];

            $this->esValido(self::REGISTRAR_USUARIO, $validData);

            $idRegistro = $validData["trabajador"]["id"];
            $registrarUpdate = $validData["registrar_Update"] ?? false;


            if($registrarUpdate){// cuando se intente registrar un usuario que este inactivo y se active
                $query = "UPDATE usuario SET idRol = :idRol, correo = :correo, clave = :clave, estado = 1 WHERE idTrabajador = :idTrabajador";
            }
            else{
                $query = "INSERT INTO usuario (idTrabajador, idRol, correo, clave)
                    VALUES (:idTrabajador, :idRol, :correo, :clave)";
            }

            
            
            $stmt = $this->prepare($query);
            $stmt->bindValue("idRol", $this->idRol);
            $stmt->bindValue("correo", $this->correo);
            $stmt->bindValue("clave", password_hash($this->clave, PASSWORD_DEFAULT));
            $stmt->bindValue("idTrabajador", $idRegistro);
            
            $stmt->execute();
            
            
            

            if($registrarUpdate){
                $last = $registrarUpdate;
            }
            else{
                $last = $this->db->pdo()->lastInsertId();
            }

            
            $respuesta = [
                "success" => true,
                "mensaje" => $this->defaultMessages->registro,
                "idInserted" => $last
            ];

            $bitacoraSMS = "Usuario '".$this->getCorreo()."' registrado";

            if($registrarUpdate){
                $bitacoraSMS = "Usuario '".$this->getCorreo()."' activado y actualizado";
                $respuesta["mensaje"] = $this->defaultMessages->activado;
            }


            
            Bitacora::registrarTransaccion($bitacoraSMS, $this->db->pdo());

            if($this->getTestingMode()) {
                $this->db->pdo()->rollBack();
                $this->db->pdo()->beginTransaction();
            }

            $this->db->pdo()->commit();

            $this->db->disconnect();
                
        } catch (\Throwable $th) {

            if( 
                isset($this->db) && $this->db->connected() &&
                $this->db->pdo() instanceof \PDO &&
                $this->db->pdo()->inTransaction()
            ){
                $this->db->pdo()->rollBack();
                $this->db->disconnect();
            }


            //$_SESSION['errores'][] = "Ocurrio un error al actualizar a el usuario";
            $respuesta = [
                "success" => false,
                "mensaje" => "Ocurrió un error al registrar el usuario",
                "idModificado" => null
            ];
            if($th instanceof Exception and $th->getCode() == self::SHOW_EXCEPTION) $respuesta['mensaje'] = $th->getMessage();

            if (DEVELOPER_MODE) $respuesta['consoleError'] = "`".addslashes($th->getMessage())." :: File:".addslashes($th->getFile())." :: Linea:".addslashes($th->getLine())."`";
            //if (DEVELOPER_MODE) $respuesta['consoleError'] = $th->getTraceAsString();
        }

        if($print) echo json_encode($respuesta);
        return $respuesta;
    }

    public function actualizarUsuario($print = true) : array
    {
            
        try {
            $this->db->connect();
            $this->db->pdo()->beginTransaction();

            $this->esValido(self::ACTUALIZAR_USUARIO);
            
            $query = "UPDATE usuario SET idRol = :idRol, 
                correo = :correo WHERE id = :id";

            $stmt = $this->prepare($query);
            $stmt->bindValue("idRol", $this->idRol);
            $stmt->bindValue("correo", $this->correo);
            $stmt->bindValue("id", $this->id);

            $stmt->execute();

            Bitacora::registrarTransaccion("Usuario '".$this->getCorreo()."' actualizado", $this->db->pdo());

            if($this->getTestingMode()) {
                $this->db->pdo()->rollBack();
                $this->db->pdo()->beginTransaction();
            }

            $this->db->pdo()->commit();

            $this->db->disconnect();

            $respuesta = [
                "success" => true,
                "idModificado" => $this->id,
                "mensaje" => "Usuario actualizado con exito"
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


            //$_SESSION['errores'][] = "Ocurrio un error al actualizar a el usuario";
            $respuesta = [
                "success" => false,
                "mensaje" => "Ocurrio un error al actualizar a el usuario",
                "idModificado" => null
            ];
            
            if($th instanceof Exception and $th->getCode() == self::SHOW_EXCEPTION) $respuesta['mensaje'] = $th->getMessage();

            //if (DEVELOPER_MODE) $respuesta['consoleError'] = "`".addslashes($th->getMessage())." :: File:".addslashes($th->getFile())." :: Linea:".addslashes($th->getLine())."`";
            if(DEVELOPER_MODE) $respuesta['consoleError'] = $th->getTraceAsString();
        }

        if($print) echo (json_encode($respuesta));

        return $respuesta;
    }

    public function eliminarUsuario($print = true, $logicDelete = false) : array
    {
        try {
            $this->db->connect();
            $this->db->pdo()->beginTransaction();
            $datosDevueltos = [];
            $this->esValido(self::ELIMINAR_USUARIO, $datosDevueltos);

            $query = "DELETE FROM usuario WHERE id = :id";

            if($logicDelete) {
                $query = "UPDATE usuario SET estado = 0 WHERE id = :id";
            }

            $stmt = $this->prepare($query);
            $stmt->bindValue("id", $this->id);
            $stmt->execute();

            if($this->getTestingMode()) {
                $this->db->pdo()->rollBack();
                $this->db->pdo()->beginTransaction();
            }

            Bitacora::registrarTransaccion("Usuario ('".$datosDevueltos['usuario']['correo']."') (".$datosDevueltos['usuario']['cedula'].") eliminado", $this->db->pdo());

            $this->db->pdo()->commit();
            $this->db->disconnect();

            $respuesta = [
                "success" => true,
                "idEliminado" => $this->id,
                "mensaje" => $this->defaultMessages->eliminar
            ];
            if($logicDelete) $respuesta['mensaje'] = $this->defaultMessages->logicDelete;
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
                "mensaje" => $this->defaultMessages->errorEliminar,
                "idEliminado" => null
            ];
            if($th instanceof Exception and $th->getCode() == self::SHOW_EXCEPTION) $respuesta['mensaje'] = $th->getMessage();

            if(DEVELOPER_MODE) $respuesta['consoleError'] = $th->getMessage();
            // si falla por la clave foranea
            if(!$logicDelete and preg_match("/a foreign key constraint fails/", $th->getMessage())){
                $respuesta = $this->eliminarUsuario(false, true);
            }
        }

        if($print) echo (json_encode($respuesta));

        return $respuesta;
    }

    public function mapearFormulario() : void
    {
        $this->cedula = trim($_POST['cedula'] ?? null );
        $this->idRol = $_POST['idRol'] ?? null;
        $this->correo = trim($_POST['correo'] ?? null) ;
        $this->clave = trim($_POST['clave'] ?? null);
        if(isset($_POST['id'])){
            $this->id = intval($_POST['id']);
        }
    }

    public function esValido($controlAction, array &$datosDevueltos = []) : void
    {

        $defaultValidationMessages = new class{

            public $correoRequerido = "El correo es requerido";
            public $correoInvalido = "El correo es invalido";
            public $rolRequerido = "El rol es requerido";
            public $rolInvalido = "El rol debe ser un número";
            public $usuarioNoSelected = "El usuario no fue seleccionado correctamente";
            public $userNoSelectedDelete = "El usuario a eliminar no fue seleccionado correctamente";
            public $usuarioPropioDelete = "No puedes eliminar tu propio usuario";
            public $claveRequerida = "La clave es requerida";
            public $calveInvalida = "La clave debe tener al menos 6 caracteres, una letra mayúscula, una letra minúscula y un número";
            public $cedulaRequerida = "La cedula es requerida";
            public $cedulaInvalida = "La cedula es invalida debe contener entre 7 y 8 dígitos";
            public $correoRegistradoUserActive = "El correo ya se encuentra registrado con un usuario activo";
            public $correoRegistradoUserInactive = "El correo ya se encuentra registrado con un usuario inactivo";
            public $cedulaNoTrabajador = "La cedula no pertenece a ningún trabajador";
            public $userNoExist = "El usuario no existe";
        };

        
        

        
        

        // validando campos

        if($controlAction == self::REGISTRAR_USUARIO || $controlAction == self::ACTUALIZAR_USUARIO) {
            
            
            if (empty(trim($this->correo))) {
                throw new \Exception($defaultValidationMessages->correoRequerido,self::SHOW_EXCEPTION);
            }
            
            if(!filter_var( $this->correo , FILTER_VALIDATE_EMAIL)){
                throw new \Exception($defaultValidationMessages->correoInvalido,self::SHOW_EXCEPTION);
            }


            if(empty(trim($this->idRol))){
                throw new \Exception($defaultValidationMessages->rolRequerido,self::SHOW_EXCEPTION);
                
            }
            else if(!is_numeric($this->idRol)){
                throw new \Exception($defaultValidationMessages->rolInvalido,self::SHOW_EXCEPTION);
            }
            // si se va a actualizar, la clave si puede estar vacia significa que no se modifico
            if($controlAction == self::ACTUALIZAR_USUARIO && empty($this->clave)){
                $this->clave = null;
            }
            else { // si no se esta actualizando o si esta llena la clave se valida

                if (!preg_match(REG_CLAVE, $this->clave)) {
                    throw new \Exception($defaultValidationMessages->calveInvalida,self::SHOW_EXCEPTION);
                }
            }
            if($controlAction == self::ACTUALIZAR_USUARIO and !preg_match("/^[0-9]+$/", $this->id)){
                throw new \Exception($defaultValidationMessages->usuarioNoSelected,self::SHOW_EXCEPTION);
            }
            if($controlAction == self::REGISTRAR_USUARIO){
                if(empty(trim($this->cedula))){
                    throw new \Exception($defaultValidationMessages->cedulaRequerida,self::SHOW_EXCEPTION);
                }
                else if(!preg_match(REG_CEDULA, $this->cedula)){
                    throw new \Exception($defaultValidationMessages->cedulaInvalida,self::SHOW_EXCEPTION);
                }
            }
        }
        else if($controlAction == self::ELIMINAR_USUARIO) {
            if(!preg_match("/^[0-9]+$/", $this->id)){
                throw new \Exception($defaultValidationMessages->userNoSelectedDelete,self::SHOW_EXCEPTION);
            }
            if ($this->id == $_SESSION['usuario']->id) {
                throw new \Exception($defaultValidationMessages->usuarioPropioDelete,self::SHOW_EXCEPTION);
            }
        }
        // valido en la base de datos



        if($controlAction == self::REGISTRAR_USUARIO || $controlAction == self::ACTUALIZAR_USUARIO) {
            $query = "SELECT u.*, t.cedula, t.id as idTrabajador FROM usuario AS u left join trabajador as t on u.idTrabajador = t.id WHERE correo = :correo";
            if($controlAction == self::ACTUALIZAR_USUARIO) {
                $query .= " AND u.id <> :id";
            }

            $stmt = $this->prepare($query);
            $stmt->bindValue("correo", $this->correo);
            if($controlAction == self::ACTUALIZAR_USUARIO) {
                $stmt->bindValue("id", $this->id);
            }

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                if($controlAction == self::REGISTRAR_USUARIO) {
                    if($user['estado'] != "0") {
                        throw new \Exception($defaultValidationMessages->correoRegistradoUserActive,self::SHOW_EXCEPTION);
                    }
                    else {
                        if($user['cedula'] == $this->cedula) {
                            $datosDevueltos["registrar_Update"] = $user['id'];
                            
                        }
                        else {
                            throw new \Exception($defaultValidationMessages->correoRegistradoUserInactive,self::SHOW_EXCEPTION);
                        }
                    }
                    
                }
                else if($controlAction == self::ACTUALIZAR_USUARIO) {
                    $sms = ($user['estado'] == "0") ? $defaultValidationMessages->correoRegistradoUserInactive : $defaultValidationMessages->correoRegistradoUserActive;
                    throw new \Exception($sms,self::SHOW_EXCEPTION);
                }
                
            }

        }
        if($controlAction == self::REGISTRAR_USUARIO) {

            // valido la cedula del trabajador
            ( $stmt =$this->prepare("SELECT * FROM trabajador as t WHERE cedula = :cedula") )->execute([':cedula'=>$this->cedula]);
            if($stmt->rowCount() == 0) {
                throw new \Exception($defaultValidationMessages->cedulaNoTrabajador,self::SHOW_EXCEPTION);
            }
            else {
                $datosDevueltos["trabajador"] = $stmt->fetch(\PDO::FETCH_ASSOC);
            }
        }
        if($controlAction == self::ACTUALIZAR_USUARIO || $controlAction == self::ELIMINAR_USUARIO) {
            // validar que el usuario existe
            ( $stmt = $this->prepare("SELECT u.*,t.cedula FROM usuario as u left join trabajador as t on u.idTrabajador = t.id WHERE u.id = :id") )->execute([':id'=>$this->id]);
            if($stmt->rowCount() == 0) {
                throw new \Exception($defaultValidationMessages->userNoExist,self::SHOW_EXCEPTION);
            }
            $datosDevueltos["usuario"] = $stmt->fetch(\PDO::FETCH_ASSOC);
        }

    }




















    // Getters
    public function getCorreo() : string {
        return $this->correo;
    }
    
    public function getEstado() : int {
        return $this->estado;
    }
    public function getNombre() : string {
        if($this->trabajador instanceof Trabajador) return $this->trabajador->getNombre();
        return "NO_NAME";
    }
    public function getNombreCompleto() : string {
        if($this->trabajador instanceof Trabajador) return $this->trabajador->getNombreCompleto();
        return null;
    }
    public function getTrabajador() : Trabajador {
        return $this->trabajador;
    }
}