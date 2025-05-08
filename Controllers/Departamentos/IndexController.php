<?php
requiereAutenticacion();
requierePermiso(Modulo::DEPARTAMENTOS, Permiso::CONSULTAR);

$departamentoObj = new Departamento();
/** @var Departamento[] $departamentos */
$departamentos = $departamentoObj->listar();

foreach ($departamentos as $departamento) {
    if ($departamento->idDepartamento != null) {
        $departamentoPadreArray = array_filter($departamentos, fn($d) => $d->id == $departamento->idDepartamento);
        $departamento->departamentoPadre = reset($departamentoPadreArray);
    }
}

renderView();