<?php
require_once "Models/Departamento.php";
require_once "Models/Enums/Cargo.php";
require_once "Models/Enums/Turno.php";
class Trabajador extends Model
{
    public int $idDepartamento;
    private string $cedula;
    private string $nombre;
    private string $apellido;
    private string $telefono;
    private Cargo|string $cargo;
    private Turno|string $turno;
    public Departamento $departamento;

    public function __construct() {
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
        return is_string($this->turno) ? Turno::from($this->turno) : $this->turno;
    }
}