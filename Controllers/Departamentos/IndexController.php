<?php
requiereAutenticacion();
requierePermiso(Modulo::DEPARTAMENTOS, Permiso::CONSULTAR);
require_once "Models/Departamento.php";

/** @var Departamento[] $departamentos */
$departamentos = Departamento::listar();

foreach ($departamentos as $departamento) {
    if ($departamento->idDepartamento != null) {
        $departamentoPadreArray = array_filter($departamentos, fn($d) => $d->id == $departamento->idDepartamento);
        $departamento->departamentoPadre = reset($departamentoPadreArray);
    }
}

renderView();