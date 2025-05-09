<?php
// TODO agregar indice unico a la cedula del trabajador
// TODO agregar Alias a los trabajadores
// (para que Sir Reginald Pomposo siga siendo Chui )
class Trabajador extends Model
{
    public int $idDepartamento;
    private string $cedula;
    private string $nombre;
    private string $apellido;
    private string $telefono;
    private string $fechaIngreso;
    private Cargo|string $cargo;
    private Turno|string $turno;
    public Departamento $departamento;

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
            $this->cedula = $_POST['cedula'];
            $this->nombre = $_POST['nombre'];
            $this->apellido = $_POST['apellido'];
            $this->telefono = $_POST['telefono'];
            $this->cargo = $_POST['cargo'];
            $this->turno = $_POST['turno'];
            $this->idDepartamento = $_POST['departamento'];
            $this->fechaIngreso = $_POST['fecha_ingreso'];
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function esValido() : bool
    {
        if ( !isset($this->cedula) || empty(trim($this->cedula))) {
            $_SESSION['errores'][] = "El campo 'Cedula' es obligatorio";
            return false;
        }
        if (!preg_match(REG_CEDULA, $this->cedula)) {
            $_SESSION['errores'][] = "El campo 'Cedula' solo puede contener números";
            return false;
        }
        if (empty(trim($this->nombre))) {
            $_SESSION['errores'][] = "El campo 'Nombre' es obligatorio";
            return false;
        }
        if (!preg_match(REG_ALFABETICO, $this->nombre)) {
            $_SESSION['errores'][] = "El campo 'Nombre' solo puede contener letras y números";
            return false;
        }
        if (empty(trim($this->apellido))) {
            $_SESSION['errores'][] = "El campo 'Apellido' es obligatorio";
            return false;
        }
        if (!preg_match(REG_ALFABETICO, $this->apellido)) {
            $_SESSION['errores'][] = "El campo 'Apellido' solo puede contener letras y números";
            return false;
        }
        if (empty(trim($this->telefono))) {
            $_SESSION['errores'][] = "El campo 'Telefono' es obligatorio";
            return false;
        }
        if (!preg_match(REG_TELEFONO, $this->telefono)) {
            $_SESSION['errores'][] = "El campo 'Telefono' solo puede contener números";
            return false;
        }
        if (empty($this->cargo)) {
            $_SESSION['errores'][] = "El campo 'Cargo' es obligatorio";
            return false;
        }
        if (empty($this->turno)) {
            $_SESSION['errores'][] = "El campo 'Turno' es obligatorio";
            return false;
        }
        if (empty($this->idDepartamento)) {
            $_SESSION['errores'][] = "El campo 'Departamento' es obligatorio";
            return false;
        }
        return true;
    }

    public function registrar() : bool
    {
        $query = "INSERT INTO trabajador (cedula, nombre, apellido, telefono, cargo, turno, idDepartamento,fechaIngreso) VALUES (:cedula, :nombre, :apellido, :telefono, :cargo, :turno, :idDepartamento, :fechaIngreso);";
        try {
            $this->db->connect();

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

            $this->db->disconnect();

        return true;
        } catch (\Throwable $th) {
            $resp[] = $th->getMessage();
            $resp[] = $th->getCode();
            $resp[] = $th->getLine();
            debug($resp);
            $_SESSION['errores'][] = "Ocurrio un error al registrar al trabajador";

            return false;
        }

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
        return is_string($this->cargo) ? Cargo::from($this->cargo) : $this->cargo;
    }
    public function getTurno() : Turno {
        return is_string($this->turno) ? Turno::from(ucfirst($this->turno)) : $this->turno;
    }
}