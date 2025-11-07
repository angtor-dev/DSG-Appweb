<?php

use PhpParser\Node\Expr\Isset_;
abstract class Model
{
    public int $id;
    private bool $testingMode = false;
    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Retorna una instacia del modelo actual con un id especifico
     * 
     * @param int $id El id a buscar en la bd
     * @return null|self El modelo encontrado o null en caso de no haber coincidencias
     */
    public static function cargar(int $id, bool $userBD = false) : null|self
    {
        $bd = Database::getInstance();
        $table = strtolower(static::class);
        $query = "SELECT * FROM $table WHERE id = $id";
        $conexiones = [
            "desconectar_despues" => true,
            "auxiliar_pdo" => null
        ];

        $conexiones = $bd->conectarYmantener($userBD);
        //  if($userBD) $bd->connectUser();
        //  else $bd->connect();
        
        $stmt = $bd->pdo()->query($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, $table);

        if ($conexiones['desconectar_despues']) {
            $bd->disconnect();
        }

        if(isset($conexiones['auxiliar_pdo']) and $conexiones['auxiliar_pdo'] instanceof PDO){
            $bd->set_pdo($conexiones['auxiliar_pdo']);
        }

        if ($stmt->rowCount() == 0) {
            return null;
        }
        $resp = $stmt->fetch();
        $stmt = null;
        return $resp;
    }

    public function cargarUltimo(bool $userBD = false) : null|self
    {
        $items = $userBD ? $this->listarDBUser() : $this->listar();
        if (empty($items)) {
            return null;
        }
        return end($items);
    }

    /**
     * Retorna un array de objetos del modelo que lo instacía
     *
     * @param int|null $estado Si no se especifica, retorna todas las filas de la tabla.
     * Si se especifica, retorna las filas donde el estado sea igual al indicado.
     * @return array<self>
     **/
    public function listar(?int $estado = null): array
    {
        $table = strtolower(static::class);
        $query = "SELECT * FROM $table" . (isset($estado) ? " WHERE estado = :estado" : "");

            $this->db->connect();

        $stmt = $this->db->pdo()->prepare($query);
        if(Isset($estado)) $stmt->bindValue('estado', $estado);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, $table);

        $this->db->disconnect();

        if ($stmt->rowCount() == 0) {
            return array();
        }
        return $stmt->fetchAll();
    }

    public function listarDBUser(?int $estado = null): array
    {
        $table = strtolower(static::class);
        $query = "SELECT * FROM $table" . (isset($estado) ? " WHERE estado = :estado" : "");

        $this->db->connectUser();

        $stmt = $this->db->pdo()->prepare($query);
        if (isset($estado))
            $stmt->bindValue('estado', $estado);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, $table);


        $this->db->disconnect();

        if ($stmt->rowCount() == 0) {
            return array();
        }
        return $stmt->fetchAll();
    }

    /**
     * Retorna un array de objetos del modelo que lo instacía donde el id de la tabla
     * foranea coincida con el id del modelo
     * 
     * @param int $id El id del modelo actual
     * @param string $tablaForanea El nombre de la tabla con la que se relaciona el modelo
     * @param int|null $estatus Si se especifica, retorna las filas donde el estatus sea igual al indicado.
     * @return array<self>
     */
    public static function listarPorRelacion(int $id, string $tablaForanea, ?int $estado = null, bool $userBD = false) : array
    {
        $bd = Database::getInstance();
        $table = strtolower(static::class);
        $query = "SELECT * FROM $table WHERE id$tablaForanea = $id" . (isset($estado) ? " AND estado = $estado" : "");

        if($userBD){
            $bd->connectUser();
        }
        else{
            $bd->connect();
        }

        $stmt = $bd->pdo()->query($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, $table);

        $bd->disconnect();

        if ($stmt->rowCount() == 0) {
            return array();
        }
        $resp = $stmt->fetchAll();
        $stmt = null;
        return $resp;
    }

    /**
     * Retorna un array de objetos del modelo que lo instacía donde el id de la tabla
     * foranea coincida con el id del modelo en una relacion de muchos a muchos
     * 
     * @param int $id El id del modelo actual
     * @param string $tablaForanea el nombre de la tabla con la que se relaciona el modelo
     * @param string $tablaIntermediaria El nombre de la tabla intermediaria entre las relaciones
     * @return array<self>
     **/
    public static function listarPorRelacionIntermedia(
        int $id, string $tablaForanea, string $tablaIntermediaria) : array
    {
        $bd = Database::getInstance();
        $table = strtolower(static::class);
        $tablaIntermediaria = strtolower($tablaIntermediaria);
        $query = "SELECT t.* FROM $table AS t
            INNER JOIN $tablaIntermediaria AS ti ON t.id = ti.id$table
            WHERE ti.id$tablaForanea = $id";

            $bd->connect();

        $stmt = $bd->pdo()->query($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, $table);

        $bd->disconnect();

        if ($stmt->rowCount() == 0) {
            return array();
        }
        return $stmt->fetchAll();
    }

    /**
     * Elimina la instancia actual de la base de datos de forma logica o fisica
     * 
     * @param bool $eliminadoLogico Si se especifica, elimina de forma logica (modifica el estado a 0)
     *                              Si no se especifica, elimina de forma fisica (borra la fila)
     * @return bool True si se elimino correctamente, False si hubo un error
     */
    public function eliminar(bool $eliminadoLogico = true) : bool
    {
        $seElimino = false;
        $tabla = strtolower(get_class($this));
        $query = $eliminadoLogico
            ? "UPDATE $tabla set estado = 0 WHERE id = :id"
            : "DELETE FROM $tabla WHERE id = :id";

        try {
            $this->db->connect();
            $this->beginTransaction();

            $stmt = $this->prepare($query);
            $stmt->bindValue('id', $this->id);

            $stmt->execute();

            $this->testHandler();

            $this->commit();

            $this->db->disconnect();

            $seElimino = true;
        } catch (\PDOException $th) {
            $this->disconectHandlerExeption();
            $_SESSION['errores'][] = ($th->getCode() == '23000') 
                ? "Existen datos relacionados al item seleccionado." 
                : "Ha ocurrido un error al eliminar $tabla.";
            $seElimino = false;
        } catch (\Throwable $th) {
            $this->disconectHandlerExeption();
            //if (DEVELOPER_MODE) debug($th); // Eliminar esto al crear vista para errores
            $_SESSION['errores'][] = "Ha ocurrido un error al eliminar $tabla.";
            $seElimino = false;
        }
        return $seElimino;
    }
    public function eliminarDBUser(bool $eliminadoLogico = true) : bool
    {
        $tabla = strtolower(get_class($this));
        $query = $eliminadoLogico
            ? "UPDATE $tabla set estado = 0 WHERE id = :id"
            : "DELETE FROM $tabla WHERE id = :id";

        try {
            $this->db->connectUser();
            $this->beginTransaction();

            $stmt = $this->prepare($query);
            $stmt->bindValue('id', $this->id);

            $stmt->execute();

            $this->testHandler();
            $this->commit();

            $this->db->disconnect();

            return true;
        } catch (\PDOException $th) {
            $_SESSION['errores'][] = ($th->getCode() == '23000')
                ? "Existen datos relacionados al item seleccionado."
                : "Ha ocurrido un error al eliminar $tabla.";
            return false;
        } catch (\Throwable $th) {
            if (DEVELOPER_MODE)
                debug($th); // Eliminar esto al crear vista para errores
            $_SESSION['errores'][] = "Ha ocurrido un error al eliminar $tabla.";
            return false;
        }
    }
    /**
     * Ejecuta un query SQL con los parámetros proporcionados y devuelve los resultados con fetchAll.
     *
     * @param string $query El query SQL a ejecutar.
     * @param mixed ...$parametros Los parámetros para el query SQL.
     * @param mixed $fetchMode El modo de obtención de resultados. Por defecto es PDO::FETCH_ASSOC.
     * @param mixed $fetchArg1 Argumento opcional para el modo de obtención de resultados.
     * @param mixed $fetchArg2 Segundo argumento opcional para el modo de obtención de resultados.
     * @return array Un array con los resultados obtenidos del query.
     * @throws \Throwable Lanza una excepción si ocurre un error durante la ejecución del query.
     */
    protected function ejecutar(string $query, array $parametros = [], mixed $fetchMode = PDO::FETCH_ASSOC, mixed $fetchArg1 = null, mixed $fetchArg2 = null) : array
    {
        try {
            $stmt = $this->prepare($query);

            if(isset($fetchArg1) && isset($fetchArg2)) {
                $stmt->setFetchMode($fetchMode, $fetchArg1, $fetchArg2);
            } elseif(isset($fetchArg1)) {
                $stmt->setFetchMode($fetchMode, $fetchArg1);
            } else {
                $stmt->setFetchMode($fetchMode);
            }
            
            $stmt->execute($parametros);

            return $stmt->fetchAll();

        } catch (\Throwable $th) {
            throw $th;
        }
    }



    /**
     * Ejecuta un query SQL con los parámetros proporcionados y devuelve el PDOStatement.
     * 
     * para casos en los que se requiera hacer cosas como 
     * - $stmt->rowCount()
     * - $stmt->fetchColumn()
     *
     * @param string $query El query SQL a ejecutar.
     * @param mixed ...$parametros Los parámetros para el query SQL.
     * @param mixed $fetchMode El modo de obtención de resultados. Por defecto es PDO::FETCH_ASSOC.
     * @param mixed $fetchArg1 Argumento opcional para el modo de obtención de resultados.
     * @param mixed $fetchArg2 Segundo argumento opcional para el modo de obtención de resultados.
     * @return \PDOStatement El PDOStatement después de haber ejecutado el query.
     * @throws \Throwable Lanza una excepción si ocurre un error durante la ejecución del query.
     */
    protected function ejecutarStatement(string $query, array $parametros = [], mixed $fetchMode = PDO::FETCH_ASSOC, mixed $fetchArg1 = null, mixed $fetchArg2 = null) : \PDOStatement
    {
        try {
            $stmt = $this->prepare($query);

            if(isset($fetchArg1) && isset($fetchArg2)) {
                $stmt->setFetchMode($fetchMode, $fetchArg1, $fetchArg2);
            } elseif(isset($fetchArg1)) {
                $stmt->setFetchMode($fetchMode, $fetchArg1);
            } else {
                $stmt->setFetchMode($fetchMode);
            }
            $stmt->execute($parametros);

            return $stmt;

        } catch (\Throwable $th) {
            throw $th;
        }
    }


    /** Shorthand para PDO::query() */
    protected function query(string $query): PDOStatement
    {
        return $this->db->pdo()->query($query);
    }

    /** Shorthand para PDO::prepare() */
    protected function prepare(string $query): PDOStatement
    {
        return $this->db->pdo()->prepare($query);
    }
    /** shorthand para PDO::beginTransaction*/
    protected function beginTransaction() : void
    {
        if ($this->db->isTestConnection()) return;
        if(isset($this->db) and $this->db->connected()){
            if ($this->db->pdo()->inTransaction()) {
                return;
            }
            $this->db->pdo()->beginTransaction();
        }
        else {
            throw new Exception("La base de datos no esta conectada");
        }
    }

    public function beginTestTransaction() : void
    {
        $this->db->setTestConnection(true);
        $this->db->connect();
        if ($this->db->pdo()->inTransaction()) {
            throw new Exception("Ya hay una transacción en curso.");
        }
        $this->db->pdo()->beginTransaction();
    }

    public function stopTestTransaction() : void
    {
        $this->db->setTestConnection(false);
        if ($this->db->connected()) {
            if ($this->db->pdo()->inTransaction()) {
                $this->db->pdo()->rollBack();
            }
            $this->db->disconnect();
        }
    }

    /** shorthand para PDO::commit*/
    protected function commit() : void
    {
        if ($this->db->isTestConnection()) return;
        
        if(isset($this->db) and $this->db->connected() and $this->db->pdo()->inTransaction()){
            $this->db->pdo()->commit();
        }
    }
    protected function rollBack() : void
    {
        if ($this->db->isTestConnection()) return;
        if(isset($this->db) and $this->db->connected() and $this->db->pdo()->inTransaction()){
            $this->db->pdo()->rollBack();
        }
    }

    public function setTestingMode(bool $testingMode) : void
    {
        $this->testingMode = $testingMode;
    }
    public function getTestingMode() : bool
    {
        return $this->testingMode;
    }

    PUBLIC function set_id($value):void{
        // verifica si es un entero o se puede convertir a entero
        if(filter_var($value, FILTER_VALIDATE_INT) !== false){
            $this->id = (int)$value;
        }
    }
    /**
     * Si esta conectada y en una transaccion aplica un rollback y la desconecta
     * @return void
     */
    public function disconectHandlerExeption() : void
    {
        if ($this->db->isTestConnection()) return;
        if( isset($this->db) && $this->db->connected() ){
            
            if($this->db->pdo()->inTransaction()){
                $this->rollBack();
            }

            $this->db->disconnect();
        }
    }

    /**
     * verifica si el proceso es de pruebas 
     * si devuelve true el proceso es de pruebas
     * - se hace un rollBack y un nuevo beginTransaction
     * 
     * si devuelve false el proceso no es de pruebas
     * @return bool
     */
    public function testHandler() : bool
    {
        if ($this->db->isTestConnection()) {
            return true;
        }
        $testing = false;
        if($this->getTestingMode()) {
            $this->rollBack();
            $this->beginTransaction();
            $testing = true;
        }
        return $testing;
    }

    

    abstract public function setterArray(array $data):void;
    // TODO agregar a los setter array para que cuando se les pase un valor string se sanitize de los caracteres "<" y ">"
}